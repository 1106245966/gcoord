<?php

namespace Gcoord\Crs;

class BD09 {
    private static $BAIDU_FACTOR;

    private static function getBaiduFactor() {
        if (self::$BAIDU_FACTOR === null) {
            // 计算高精度的BAIDU_FACTOR: M_PI * 3000 / 180
            $pi = '3.14159265358979323846264338327950288419716939937510';
            $div = bcdiv('3000', '180', 20); // 3000/180 = 16.666...
            self::$BAIDU_FACTOR = bcmul($div, $pi, 20);
        }
        return self::$BAIDU_FACTOR;
    }

    // BD09转GCJ02
    public static function BD09ToGCJ02($coord) {
        list($lon, $lat) = $coord;

        // 使用高精度函数计算x和y
        $x = bcsub((string)$lon, '0.0065', 20);
        $y = bcsub((string)$lat, '0.006', 20);
        
        // 计算x² + y²
        $x_squared = bcmul($x, $x, 20);
        $y_squared = bcmul($y, $y, 20);
        $sum_squares = bcadd($x_squared, $y_squared, 20);
        $sqrt_sum = bcsqrt($sum_squares, 20);
        
        // 计算0.00002 * sin(y * BAIDU_FACTOR)
        $baidu_factor = self::getBaiduFactor();
        $y_baidu = bcmul($y, $baidu_factor, 20);
        $sin_val = sin((float)$y_baidu); // sin函数需要float参数
        $term1 = bcmul('0.00002', (string)$sin_val, 20);
        $z = bcsub($sqrt_sum, $term1, 20);
        
        // 计算theta
        $atan2_val = atan2((float)$y, (float)$x); // atan2需要float参数
        $x_baidu = bcmul($x, $baidu_factor, 20);
        $cos_val = cos((float)$x_baidu);
        $term2 = bcmul('0.000003', (string)$cos_val, 20);
        $theta = bcsub((string)$atan2_val, $term2, 20);
        
        // 计算newLon和newLat
        $cos_theta = cos((float)$theta);
        $newLon = bcmul($z, (string)$cos_theta, 20);
        $sin_theta = sin((float)$theta);
        $newLat = bcmul($z, (string)$sin_theta, 20);

        return [$newLon, $newLat];
    }

    // GCJ02转BD09
    public static function GCJ02ToBD09($coord) {
        list($lon, $lat) = $coord;

        $x = (string)$lon;
        $y = (string)$lat;
        
        $x_squared = bcmul($x, $x, 20);
        $y_squared = bcmul($y, $y, 20);
        $sum_squares = bcadd($x_squared, $y_squared, 20);
        $sqrt_sum = bcsqrt($sum_squares, 20);
        
        $baidu_factor = self::getBaiduFactor();
        $y_baidu = bcmul($y, $baidu_factor, 20);
        $sin_val = sin((float)$y_baidu);
        $term1 = bcmul('0.00002', (string)$sin_val, 20);
        $z = bcadd($sqrt_sum, $term1, 20);
        
        $atan2_val = atan2((float)$y, (float)$x);
        $x_baidu = bcmul($x, $baidu_factor, 20);
        $cos_val = cos((float)$x_baidu);
        $term2 = bcmul('0.000003', (string)$cos_val, 20);
        $theta = bcadd((string)$atan2_val, $term2, 20);
        
        $cos_theta = cos((float)$theta);
        $sin_theta = sin((float)$theta);
        
        $newLon = bcadd(bcmul($z, (string)$cos_theta, 20), '0.0065', 20);
        $newLat = bcadd(bcmul($z, (string)$sin_theta, 20), '0.006', 20);

        return [$newLon, $newLat];
    }
}