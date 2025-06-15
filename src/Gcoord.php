<?php

namespace Gcoord;

// 入口类
class Gcoord {
    public static function transform($input, $crsFrom, $crsTo, $isFloat = true) {
        return Transform::transform($input, $crsFrom, $crsTo, $isFloat);
    }
}