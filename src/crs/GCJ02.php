<?php

namespace Gcoord\Crs;

class GCJ02 {
    const A = '6378245';
    const EE = '0.006693421622965823';
    const PI = '3.14159265358979323846264338327950288419716939937510';

    // 大致判断是否在中国境内
    private static function isInChinaBbox($lon, $lat) {
        return $lon >= 72.004 && $lon <= 137.8347 && $lat >= 0.8293 && $lat <= 55.8271;
    }

    // 转换纬度
    private static function transformLat($x, $y) {
        // 分步计算初始项 - 简化版
        $term1 = bcadd('-100', bcmul('2', $x, 20), 20);
        $term2 = bcadd($term1, bcmul('3', $y, 20), 20);
        $term3 = bcadd($term2, bcmul(bcmul('0.2', $y, 20), $y, 20), 20);
        $term4_part1 = bcmul(bcmul('0.1', $x, 20), $y, 20);
        $absX = bccomp($x, '0') < 0 ? bcmul($x, '-1', 20) : $x;
        $term4_part2 = bcmul('0.2', bcsqrt($absX, 20), 20);
        $term4 = bcadd($term4_part1, $term4_part2, 20);
        $ret = bcadd($term3, $term4, 20);

        // 计算三角函数项
        $angle1 = bcmul(bcmul('6', $x, 20), self::PI, 20);
        $sin1 = sin((float)$angle1);
        $angle2 = bcmul(bcmul('2', $x, 20), self::PI, 20);
        $sin2 = sin((float)$angle2);
        $trigTerm1 = bcadd(bcmul('20', $sin1, 20), bcmul('20', $sin2, 20), 20);
        $ret = bcadd($ret, bcmul($trigTerm1, bcdiv('2', '3', 20), 20), 20);

        $angle3 = bcmul($y, self::PI, 20);
        $sin3 = sin((float)$angle3);
        $angle4 = bcmul(bcdiv($y, '3', 20), self::PI, 20);
        $sin4 = sin((float)$angle4);
        $trigTerm2 = bcadd(bcmul('20', $sin3, 20), bcmul('40', $sin4, 20), 20);
        $ret = bcadd($ret, bcmul($trigTerm2, bcdiv('2', '3', 20), 20), 20);

        $angle5 = bcmul(bcdiv($y, '12', 20), self::PI, 20);
        $sin5 = sin((float)$angle5);
        $angle6 = bcmul(bcdiv($y, '30', 20), self::PI, 20);
        $sin6 = sin((float)$angle6);
        $trigTerm3 = bcadd(bcmul('160', $sin5, 20), bcmul('320', $sin6, 20), 20);
        $ret = bcadd($ret, bcmul($trigTerm3, bcdiv('2', '3', 20), 20), 20);
        return $ret;
    }

    // 转换经度
    private static function transformLon($x, $y) {
        // 分步计算初始项 - 简化版
        $term1 = bcadd('300', $x, 20);
        $term2 = bcadd($term1, bcmul('2', $y, 20), 20);
        $term3 = bcadd($term2, bcmul(bcmul('0.1', $x, 20), $x, 20), 20);
        $term4_part1 = bcmul(bcmul('0.1', $x, 20), $y, 20);
        $absX = bccomp($x, '0') < 0 ? bcmul($x, '-1', 20) : $x;
        $term4_part2 = bcmul('0.1', bcsqrt($absX, 20), 20);
        $term4 = bcadd($term4_part1, $term4_part2, 20);
        $ret = bcadd($term3, $term4, 20);

        // 计算三角函数项
        $angle1 = bcmul(bcmul('6', $x, 20), self::PI, 20);
        $sin1 = sin((float)$angle1);
        $angle2 = bcmul(bcmul('2', $x, 20), self::PI, 20);
        $sin2 = sin((float)$angle2);
        $trigTerm1 = bcadd(bcmul('20', $sin1, 20), bcmul('20', $sin2, 20), 20);
        $ret = bcadd($ret, bcmul($trigTerm1, bcdiv('2', '3', 20), 20), 20);

        $angle3 = bcmul($x, self::PI, 20);
        $sin3 = sin((float)$angle3);
        $angle4 = bcmul(bcdiv($x, '3', 20), self::PI, 20);
        $sin4 = sin((float)$angle4);
        $trigTerm2 = bcadd(bcmul('20', $sin3, 20), bcmul('40', $sin4, 20), 20);
        $ret = bcadd($ret, bcmul($trigTerm2, bcdiv('2', '3', 20), 20), 20);

        $angle5 = bcmul(bcdiv($x, '12', 20), self::PI, 20);
        $sin5 = sin((float)$angle5);
        $angle6 = bcmul(bcdiv($x, '30', 20), self::PI, 20);
        $sin6 = sin((float)$angle6);
        $trigTerm3 = bcadd(bcmul('150', $sin5, 20), bcmul('300', $sin6, 20), 20);
        $ret = bcadd($ret, bcmul($trigTerm3, bcdiv('2', '3', 20), 20), 20);
        return $ret;
    }

    // 计算偏移量
    private static function delta($lon, $lat) {
        $dLon = self::transformLon(bcsub($lon, 105, 20), bcsub($lat, 35, 20));
        $dLat = self::transformLat(bcsub($lon, 105, 20), bcsub($lat, 35, 20));

        $radLat = bcmul(bcdiv($lat, 180, 20), self::PI, 20);
        $magic = sin((float)$radLat);
        $magic = bcsub('1', bcmul(bcmul(self::EE, $magic, 20), $magic, 20), 20);
        $sqrtMagic = bcsqrt($magic, 20);

        $denominatorLon = bcmul(bcmul(bcdiv(self::A, $sqrtMagic, 20), cos((float)$radLat), 20), self::PI, 20);
        $dLon = bcdiv(bcmul($dLon, 180, 20), $denominatorLon, 20);

        $numeratorLat = bcmul(self::A, bcsub('1', self::EE, 20), 20);
        $denominatorLatPart = bcmul($magic, $sqrtMagic, 20);
        $denominatorLat = bcmul(bcdiv($numeratorLat, $denominatorLatPart, 20), self::PI, 20);
        $dLat = bcdiv(bcmul($dLat, 180, 20), $denominatorLat, 20);

        return [$dLon, $dLat];
    }

    // WGS84转GCJ02
    public static function WGS84ToGCJ02($coord) {
        list($lon, $lat) = $coord;

        if (!self::isInChinaBbox($lon, $lat)) {
            return [$lon, $lat];
        }

        $d = self::delta($lon, $lat);
        return [bcadd($lon, $d[0], 20), bcadd($lat, $d[1], 20)];
    }

    // GCJ02转WGS84（迭代法）
    public static function GCJ02ToWGS84($coord) {
        list($lon, $lat) = $coord;

        if (!self::isInChinaBbox($lon, $lat)) {
            return [$lon, $lat];
        }

        $wgsLon = $lon;
        $wgsLat = $lat;
        $epsilon = '0.000001';

        do {
            $tempPoint = self::WGS84ToGCJ02([$wgsLon, $wgsLat]);
            $dx = bcsub($tempPoint[0], $lon, 20);
            $dy = bcsub($tempPoint[1], $lat, 20);
            $wgsLon = bcsub($wgsLon, $dx, 20);
            $wgsLat = bcsub($wgsLat, $dy, 20);
        $absDx = bccomp($dx, '0') < 0 ? bcmul($dx, '-1', 20) : $dx;
        $absDy = bccomp($dy, '0') < 0 ? bcmul($dy, '-1', 20) : $dy;
        } while (bccomp($absDx, $epsilon, 20) > 0 || bccomp($absDy, $epsilon, 20) > 0);

        return [$wgsLon, $wgsLat];
    }
}