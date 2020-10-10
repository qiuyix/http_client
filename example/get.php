<?php

require "../vendor/autoload.php";

use tingyu\HttpRequest\Method\Get;


/**
 * 发起http  get请求
 */


$handle = new Get();



/**
 * 发起请求并获取响应结果
 * @param string 请求地址
 * @param array|string  uri 请求参数， 支持数组或以组装好的字符串
 * @return mixed 请求体
 */
$responseBody = $handle->request("localhost", ["wd" => "php"]);  // 数组参数
//$responseBody = $handle->request("localhost", "wd=php&keyword=mysql");  // 字符串参数
var_dump($responseBody);