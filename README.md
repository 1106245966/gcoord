# Gcoord

**gcoord**(**g**eographic **coord**inates)是一个处理地理坐标系的JS库，用来修正百度地图、高德地图及其它互联网地图坐标系不统一的问题。

支持转换坐标数组和 GeoJSON 数据，无外部依赖，本库是Node.js库gcoord的PHP移植版本，支持多种坐标系转换。

更多信息可以阅读[地理坐标系](https://github.com/hujiulong/gcoord/wiki/%E5%9C%B0%E7%90%86%E5%9D%90%E6%A0%87%E7%B3%BB)

本代码参考自[hujiulong/gcoord](https://github.com/hujiulong/gcoord)转换移植的PHP版本

## 安装
通过npm安装:
```bash
composer require 11062/gcoord
```

## 引入
```php
use Gcoord\Gcoord;
use Gcoord\CRSTypes;
```

## 使用
例如从手机的GPS得到一个经纬度坐标，需要将其展示在百度地图上，则应该将当前坐标从[WGS-84](https://github.com/hujiulong/gcoord/wiki/%E5%9C%B0%E7%90%86%E5%9D%90%E6%A0%87%E7%B3%BB#wgs-84---%E4%B8%96%E7%95%8C%E5%A4%A7%E5%9C%B0%E6%B5%8B%E9%87%8F%E7%B3%BB%E7%BB%9F)坐标系转换为[BD-09](https://github.com/hujiulong/gcoord/wiki/%E5%9C%B0%E7%90%86%E5%9D%90%E6%A0%87%E7%B3%BB#bd-09---%E7%99%BE%E5%BA%A6%E5%9D%90%E6%A0%87%E7%B3%BB)坐标系
```php
// WGS84转GCJ02
$wgs84Coord = [116.403988, 39.914266];
$gcj02Coord = Gcoord::transform($wgs84Coord, CRSTypes::WGS84, CRSTypes::GCJ02);
echo "WGS84转GCJ02结果：" . print_r($gcj02Coord, true) . "\n\n";

// GCJ02转BD09
$bd09Coord = Gcoord::transform($gcj02Coord, CRSTypes::GCJ02, CRSTypes::BD09);
echo "GCJ02转BD09结果：" . print_r($bd09Coord, true) . "\n\n";

// BD09转BD09MC
$bd09MCCoord = Gcoord::transform($bd09Coord, CRSTypes::BD09, CRSTypes::BD09MC);
echo "BD09转BD09MC结果：" . print_r($bd09MCCoord, true) . "\n\n";

// EPSG3857转WGS84
$epsg3857Coord = Gcoord::transform([13378583.21, 3573214.56], CRSTypes::EPSG3857, CRSTypes::WGS84);
echo "EPSG3857转WGS84结果：" . print_r($epsg3857Coord, true) . "\n\n";

// GeoJSON对象转换
$geojson = [
    'type' => 'Feature',
    'geometry' => [
    'type' => 'Point',
    'coordinates' => [116.403988, 39.914266]
],
'properties' => ['name' => '测试点']
];
$transformedGeojson = Gcoord::transform($geojson, CRSTypes::WGS84, CRSTypes::GCJ02);
echo "GeoJSON转换结果：" . json_encode($transformedGeojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
```
同时gcoord还可以转换GeoJSON对象的坐标系，详细使用方式可以参考[API](#api)

## API

### Gcoord::transform(input, from, to, isFloat=true)
进行坐标转换

**参数**
-   `input` **[GeoJSON][GeoJSON] | Array&lt;number>** GeoJSON对象，或GeoJSON字符串，或经纬度数组
-   `from` **[CRSTypes](#CRSTypes)** 当前坐标系
-   `to` **[CRSTypes](#CRSTypes)** 目标坐标系
-   `isFloat` **boolean** 默认值为：true，是否返回浮点，如果为true就是返回浮点类型，如果为false则返回php的高精度字符串的值

**返回值**

**[GeoJSON][GeoJSON] | Array&lt;number>**

返回数组或GeoJSON数组（由输入决定），**注意：当输入为GeoJSON时，transform会改变输入对象**

### CRSTypes
CRSTypes为坐标系，目标支持以下几种坐标系

| CRS                | 坐标格式   | 说明    |
| --------           | --------- | ----- |
| CRSTypes::WGS84       | [lng,lat] | WGS-84坐标系，GPS设备获取的经纬度坐标   |
| CRSTypes::GCJ02       | [lng,lat] | GCJ-02坐标系，google中国地图、soso地图、aliyun地图、mapabc地图和高德地图所用的经纬度坐标   |
| CRSTypes::BD09        | [lng,lat] | BD-09坐标系，百度地图采用的经纬度坐标    |
| CRSTypes::BD09LL      | [lng,lat] | 同BD09  |
| CRSTypes::BD09MC      | [x,y]     | BD-09米制坐标，百度地图采用的米制坐标，单位：米  |
| CRSTypes::BD09Meter   | [x,y]     | 同BD09MC |
| CRSTypes::Baidu       | [lng,lat] | 百度坐标系，BD-09坐标系别名，同BD-09  |
| CRSTypes::BMap        | [lng,lat] | 百度地图，BD-09坐标系别名，同BD-09  |
| CRSTypes::AMap        | [lng,lat] | 高德地图，同GCJ-02  |
| CRSTypes::WebMercator | [x,y]     | Web Mercator投影，墨卡托投影，同EPSG3857，单位：米 |
| CRSTypes::WGS1984     | [lng,lat] | WGS-84坐标系别名，同WGS-84  |
| CRSTypes::EPSG4326    | [lng,lat] | WGS-84坐标系别名，同WGS-84  |
| CRSTypes::EPSG3857    | [x,y]     | Web Mercator投影，同WebMercator，单位：米  |
| CRSTypes::EPSG900913  | [x,y]     | Web Mercator投影，同WebMercator，单位：米  |

**支持更多坐标系？**
gcoord的目标是处理web地图中的坐标，目前支持的坐标系已经能满足绝大部分要求了，同时gcoord也能保持轻量。


[GeoJSON]: https://tools.ietf.org/html/rfc7946#page-6