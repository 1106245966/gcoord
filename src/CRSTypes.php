<?php

namespace Gcoord;

class CRSTypes
{
    // WGS84
    const WGS84 = 'WGS84';
    const WGS1984 = self::WGS84;
    const EPSG4326 = self::WGS84;

    // GCJ02
    const GCJ02 = 'GCJ02';
    const AMap = self::GCJ02;

    // BD09
    const BD09 = 'BD09';
    const BD09LL = self::BD09;
    const Baidu = self::BD09;
    const BMap = self::BD09;

    // BD09MC
    const BD09MC = 'BD09MC';
    const BD09Meter = self::BD09MC;

    // EPSG3857
    const EPSG3857 = 'EPSG3857';
    const EPSG900913 = self::EPSG3857;
    const EPSG102100 = self::EPSG3857;
    const WebMercator = self::EPSG3857;
    const WM = self::EPSG3857;
}