<?php

namespace tingyu\HttpRequest\Util;

class Json
{
    public static function IsJsonStr(string $str) {
        json_decode($str);
        return json_last_error() === JSON_ERROR_NONE;
    }
}