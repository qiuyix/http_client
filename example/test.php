<?php

$str = 'Set-Cookie: BAIDUID=D1D97945B6964C185A54AAC3B9C70799:FG=1; expires=Thu, 31-Dec-37 23:55:55 GMT; max-age=2147483647; path= domain=.baidu.com';

if (preg_match_all('/([^(;|=|:|\s*)]+)=([^;]+)/', $str, $cookie)) {
    var_dump($cookie);
}