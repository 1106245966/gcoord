<?php

namespace Gcoord;

require_once 'crs/EPSG3857.php';
require_once 'crs/GCJ02.php';
require_once 'crs/BD09.php';
require_once 'crs/BD09MC.php';
require_once 'helper.php';
require_once 'transform.php';

// 入口类
class Gcoord {
    public static function transform($input, $crsFrom, $crsTo, $isFloat = true) {
        return Transform::transform($input, $crsFrom, $crsTo, $isFloat);
    }
}