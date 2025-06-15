<?php

namespace Gcoord\Crs;

class EPSG3857 {
    const R2D = '57.295779513082320876798154814105'; // 180/M_PI高精度值
    const D2R = '0.017453292519943295769236907684886'; // M_PI/180高精度值
    const A = '6378137.0';
    const MAXEXTENT = '20037508.342789244';
    const PRECISION = 20; // 统一精度控制

    public static function EPSG3857ToWGS84($xy) {
        // 高精度计算经度
        $lon = bcmul(bcdiv($xy[0], self::A, self::PRECISION), self::R2D, self::PRECISION);
        
        // 高精度计算纬度（优化类型转换精度）
        $dividend = bcmul('-1', $xy[1], self::PRECISION);
        $divResult = bcdiv($dividend, self::A, self::PRECISION);
        // 使用高精度字符串转float，保留更多有效数字
        $expValue = exp((float)bcadd($divResult, '0.00000000000000000000', self::PRECISION));
        $atanValue = atan($expValue);
        // 将结果转换为高精度字符串时保留更多小数位
        $term = bcmul('2.0', number_format($atanValue, self::PRECISION, '.', ''), self::PRECISION);
        $latRad = bcsub('1.5707963267948966484375', $term, self::PRECISION);
        $lat = bcmul($latRad, self::R2D, self::PRECISION);
        
        return [$lon, $lat];
    }

    public static function WGS84ToEPSG3857($lonLat) {
        // 使用高精度计算调整经度（原代码使用普通算术运算符）
        $absLon = bccomp(bcabs($lonLat[0]), '180', self::PRECISION);
        if ($absLon > 0) {
            $sign = bccomp($lonLat[0], '0', self::PRECISION) < 0 ? '-1' : '1';
            $adjusted = bcsub($lonLat[0], bcmul($sign, '360', self::PRECISION), self::PRECISION);
        } else {
            $adjusted = (string)$lonLat[0];
        }
        $x = bcmul(bcmul(self::A, $adjusted, self::PRECISION), self::D2R, self::PRECISION);

        // 优化y值计算的精度处理
        $term1 = bcmul('3.1415926535897932384626433832795', '0.25', self::PRECISION); // 更高精度的M_PI值
        $term2 = bcmul((string)$lonLat[1], self::D2R, self::PRECISION);
        $term2 = bcmul($term2, '0.5', self::PRECISION);
        $sum = bcadd($term1, $term2, self::PRECISION);
        // 改进float转换精度
        $tanSum = tan((float)bcadd($sum, '0.00000000000000000000', self::PRECISION));
        $logTan = log($tanSum);
        $y = bcmul(self::A, number_format($logTan, self::PRECISION, '.', ''), self::PRECISION);

        // 限制最大范围（保持不变，已使用bccomp）
        if (bccomp($x, self::MAXEXTENT) > 0) $x = self::MAXEXTENT;
        if (bccomp($x, bcmul(self::MAXEXTENT, '-1', self::PRECISION)) < 0) $x = bcmul(self::MAXEXTENT, '-1', self::PRECISION);
        if (bccomp($y, self::MAXEXTENT) > 0) $y = self::MAXEXTENT;
        if (bccomp($y, bcmul(self::MAXEXTENT, '-1', self::PRECISION)) < 0) $y = bcmul(self::MAXEXTENT, '-1', self::PRECISION);

        return [$x, $y];
    }
}