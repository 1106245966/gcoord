<?php

namespace Gcoord;

use Gcoord\Crs\EPSG3857;
use Gcoord\Crs\GCJ02;
use Gcoord\Crs\BD09;
use Gcoord\Crs\BD09MC;
use Gcoord\Crs\WGS84;

class Transform {

    public static function getCrsMap()
    {
        return [
            CRSTypes::WGS84 => [
                'to' => [
                    CRSTypes::GCJ02 => \Closure::fromCallable(['\Gcoord\Crs\WGS84', 'WGS84ToGCJ02']),
                    CRSTypes::BD09 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'GCJ02ToBD09']),
                        \Closure::fromCallable(['\Gcoord\Crs\WGS84', 'WGS84ToGCJ02'])
                    ),
                    CRSTypes::BD09MC => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\BD09MC', 'BD09toBD09MC']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'GCJ02ToBD09']),
                        \Closure::fromCallable(['\Gcoord\Crs\WGS84', 'WGS84ToGCJ02'])
                    ),
                    CRSTypes::EPSG3857 => \Closure::fromCallable(['\Gcoord\Crs\EPSG3857', 'WGS84ToEPSG3857']),
                ],
            ],
            CRSTypes::GCJ02 => [
                'to' => [
                    CRSTypes::WGS84 => \Closure::fromCallable(['\Gcoord\Crs\GCJ02', 'GCJ02ToWGS84']),
                    CRSTypes::BD09 => \Closure::fromCallable(['\Gcoord\Crs\BD09', 'GCJ02ToBD09']),
                    CRSTypes::BD09MC => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\BD09MC', 'BD09toBD09MC']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'GCJ02ToBD09'])
                    ),
                    CRSTypes::EPSG3857 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\EPSG3857', 'WGS84ToEPSG3857']),
                        \Closure::fromCallable(['\Gcoord\Crs\GCJ02', 'GCJ02ToWGS84'])
                    ),
                ],
            ],
            CRSTypes::BD09 => [
                'to' => [
                    CRSTypes::WGS84 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\GCJ02', 'GCJ02ToWGS84']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'BD09ToGCJ02'])
                    ),
                    CRSTypes::GCJ02 => \Closure::fromCallable(['\Gcoord\Crs\BD09', 'BD09ToGCJ02']),
                    CRSTypes::EPSG3857 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\EPSG3857', 'WGS84ToEPSG3857']),
                        \Closure::fromCallable(['\Gcoord\Crs\GCJ02', 'GCJ02ToWGS84']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'BD09ToGCJ02'])
                    ),
                    CRSTypes::BD09MC => \Closure::fromCallable(['\Gcoord\Crs\BD09MC', 'BD09toBD09MC']),
                ],
            ],
            CRSTypes::EPSG3857 => [
                'to' => [
                    CRSTypes::WGS84 => \Closure::fromCallable(['\Gcoord\Crs\EPSG3857', 'EPSG3857ToWGS84']),
                    CRSTypes::GCJ02 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\WGS84', 'WGS84ToGCJ02']),
                        \Closure::fromCallable(['\Gcoord\Crs\EPSG3857', 'EPSG3857ToWGS84'])
                    ),
                    CRSTypes::BD09 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'GCJ02ToBD09']),
                        \Closure::fromCallable(['\Gcoord\Crs\WGS84', 'WGS84ToGCJ02']),
                        \Closure::fromCallable(['\Gcoord\Crs\EPSG3857', 'EPSG3857ToWGS84'])
                    ),
                    CRSTypes::BD09MC => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\BD09MC', 'BD09toBD09MC']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'GCJ02ToBD09']),
                        \Closure::fromCallable(['\Gcoord\Crs\WGS84', 'WGS84ToGCJ02']),
                        \Closure::fromCallable(['\Gcoord\Crs\EPSG3857', 'EPSG3857ToWGS84'])
                    ),
                ],
            ],
            CRSTypes::BD09MC => [
                'to' => [
                    CRSTypes::WGS84 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\GCJ02', 'GCJ02ToWGS84']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'BD09ToGCJ02']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09MC', 'BD09MCtoBD09'])
                    ),
                    CRSTypes::GCJ02 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'BD09ToGCJ02']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09MC', 'BD09MCtoBD09'])
                    ),
                    CRSTypes::EPSG3857 => Helper::compose(
                        \Closure::fromCallable(['\Gcoord\Crs\EPSG3857', 'WGS84ToEPSG3857']),
                        \Closure::fromCallable(['\Gcoord\Crs\GCJ02', 'GCJ02ToWGS84']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09', 'BD09ToGCJ02']),
                        \Closure::fromCallable(['\Gcoord\Crs\BD09MC', 'BD09MCtoBD09'])
                    ),
                    CRSTypes::BD09 => \Closure::fromCallable(['\Gcoord\Crs\BD09MC', 'BD09MCtoBD09']),
                ],
            ],
        ];
    }
    /**
     * 坐标转换核心方法
     * @param mixed $input 输入坐标或GeoJSON对象
     * @param string $crsFrom 源坐标系
     * @param string $crsTo 目标坐标系
     * @return mixed 转换后的结果
     */
    public static function transform($input, $crsFrom, $crsTo, $isFloat = true) {
        Helper::assert(!empty($input), '输入坐标不能为空');
        Helper::assert(!empty($crsFrom), '源坐标系不能为空');
        Helper::assert(!empty($crsTo), '目标坐标系不能为空');

        if ($crsFrom === $crsTo) {
            return $input;
        }

        $crsMap = self::getCrsMap();

        Helper::assert(isset($crsMap[$crsFrom]), '无效的源坐标系: ' . $crsFrom);
        $fromClass = $crsMap[$crsFrom];

        Helper::assert(isset($fromClass['to'][$crsTo]), '无效的目标坐标系: ' . $crsTo);

        // 处理字符串输入
        if (is_string($input)) {
            try {
                $input = json_decode($input, true);
            } catch (\Exception $e) {
                throw new \Error('无效的输入坐标: ' . $input);
            }
        }

        // 处理位置坐标
        $isPosition = false;
        if (isset($input[0]) && isset($input[1])) {
            Helper::assert(count($input) >= 2, '无效的输入坐标: ' . print_r($input, true));
            Helper::assert(Helper::isNumber($input[0]) && Helper::isNumber($input[1]), '无效的输入坐标: ' . print_r($input, true));
            $isPosition = true;
        }

        $convert = $fromClass['to'][$crsTo];

        if ($isPosition) {
            $value = call_user_func($convert, $input);
            if($isFloat){
                $value[0] = floatval($value[0]);
                $value[1] = floatval($value[1]);
            }
            return $value;
        }

        // 处理GeoJSON对象
        Helper::coordEach($input, function(&$coord) use ($convert, $isFloat) {
            list($x, $y) = call_user_func($convert, $coord);
            $coord[0] = $isFloat ? floatval($x) : $x;
            $coord[1] = $isFloat ? floatval($y) : $y;
        });

        return $input;
    }
}