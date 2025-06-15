<?php

namespace Gcoord;

class Helper {
    /**
     * 断言函数
     * @param bool $condition 条件
     * @param string|null $msg 错误信息
     * @throws \Error
     */
    public static function assert($condition, $msg = null) {
        if (!$condition) {
            throw new \Error($msg ?? 'Assertion failed');
        }
    }

    /**
     * 检查是否为对象
     * @param mixed $input 输入值
     * @return bool
     */
    public static function isObject($input) {
        return is_object($input) && get_class($input) === 'stdClass';
    }

    /**
     * 检查是否为数组
     * @param mixed $input 输入值
     * @return bool
     */
    public static function isArray($input) {
        return is_array($input);
    }

    /**
     * 检查是否为数字
     * @param mixed $input 输入值
     * @return bool
     */
    public static function isNumber($input) {
        return is_numeric($input) && $input !== null && !self::isArray($input);
    }

    /**
     * 检查是否为字符串
     * @param mixed $input 输入值
     * @return bool
     */
    public static function isString($input) {
        return is_string($input);
    }

    /**
     * 组合函数（类似compose）
     * @param callable[] $funcs 函数数组
     * @return callable
     */
    public static function compose(...$funcs) {
        return function(...$args) use ($funcs) {
            $result = $funcs[count($funcs)-1](...$args);
            for ($i = count($funcs)-2; $i >= 0; $i--) {
                $result = $funcs[$i]($result);
            }
            return $result;
        };
    }

    /**
     * 遍历GeoJSON坐标（类似coordEach）
     * @param mixed $geojson GeoJSON对象
     * @param callable $callback 回调函数
     * @param bool $excludeWrapCoord 是否排除闭合坐标
     */
    public static function coordEach(&$geojson, $callback, $excludeWrapCoord = false) {
        if ($geojson === null) return;

        $coordIndex = 0;
        $type = isset($geojson['type']) ? $geojson['type'] : '';
        $isFeatureCollection = $type === 'FeatureCollection';
        $isFeature = $type === 'Feature';
        $stop = $isFeatureCollection ? count($geojson['features']) : 1;

        for ($featureIndex = 0; $featureIndex < $stop; $featureIndex++) {
            if ($isFeatureCollection) {
                $geometryMaybeCollection = &$geojson['features'][$featureIndex]['geometry'];
            }else{
                if ($isFeature) {
                    $geometryMaybeCollection = &$geojson['geometry'];
                } else {
                    $geometryMaybeCollection = &$geojson;
                }
            }
            $isGeometryCollection = isset($geometryMaybeCollection['type']) && $geometryMaybeCollection['type'] === 'GeometryCollection';
            $stopG = $isGeometryCollection ? count($geometryMaybeCollection['geometries']) : 1;

            for ($geomIndex = 0; $geomIndex < $stopG; $geomIndex++) {
                $multiFeatureIndex = 0;
                $geometryIndex = 0;
                if ($isGeometryCollection) {
                    $geometry = &$geometryMaybeCollection['geometries'][$geomIndex];
                } else {
                    $geometry = &$geometryMaybeCollection;
                }

                if ($geometry === null) continue;
                $geomType = isset($geometry['type']) ? $geometry['type'] : '';
                $wrapShrink = $excludeWrapCoord && in_array($geomType, ['Polygon', 'MultiPolygon']) ? 1 : 0;

                switch ($geomType) {
                    case 'Point':
                        $coords = &$geometry['coordinates'];
                        if ($callback($coords, $coordIndex, $featureIndex, $multiFeatureIndex, $geometryIndex) === false) return;
                        $coordIndex++;
                        $multiFeatureIndex++;
                        break;
                    case 'LineString':
                    case 'MultiPoint':
                        $coords = $geometry['coordinates'];
                        foreach ($coords as $j => &$coord) {
                            if ($callback($coord, $coordIndex, $featureIndex, $multiFeatureIndex, $geometryIndex) === false) return;
                            $coordIndex++;
                            if ($geomType === 'MultiPoint') $multiFeatureIndex++;
                        }
                        unset($coord); // 解除引用
                        if ($geomType === 'LineString') $multiFeatureIndex++;
                        break;
                    case 'Polygon':
                    case 'MultiLineString':
                        $coords = $geometry['coordinates'];
                        foreach ($coords as $j => &$ringOrLine) {
                            $length = count($ringOrLine) - $wrapShrink;
                            foreach ($ringOrLine as $k => &$coord) {
                                if ($k >= $length) break;
                                if ($callback($coord, $coordIndex, $featureIndex, $multiFeatureIndex, $geometryIndex) === false) return;
                                $coordIndex++;
                            }
                            unset($coord); // 解除引用
                            if ($geomType === 'MultiLineString') $multiFeatureIndex++;
                            if ($geomType === 'Polygon') $geometryIndex++;
                        }
                        unset($ringOrLine); // 解除引用
                        if ($geomType === 'Polygon') $multiFeatureIndex++;
                        break;
                    case 'MultiPolygon':
                        $coords = $geometry['coordinates'];
                        foreach ($coords as $j => &$polygon) {
                            $geometryIndex = 0;
                            foreach ($polygon as $k => &$ring) {
                                $length = count($ring) - $wrapShrink;
                                foreach ($ring as $l => &$coord) {
                                    if ($l >= $length) break;
                                    if ($callback($coord, $coordIndex, $featureIndex, $multiFeatureIndex, $geometryIndex) === false) return;
                                    $coordIndex++;
                                }
                                unset($coord); // 解除引用
                                $geometryIndex++;
                            }
                            unset($ring); // 解除引用
                            $multiFeatureIndex++;
                        }
                        unset($polygon); // 解除引用
                        break;
                    case 'GeometryCollection':
                        foreach ($geometry['geometries'] as $j => &$subGeometry) {
                            if (self::coordEach($subGeometry, $callback, $excludeWrapCoord) === false) return;
                        }
                        unset($subGeometry); // 解除引用
                        break;
                    default:
                        throw new \Exception('Unknown Geometry Type');
                }
            }
        }
    }
}