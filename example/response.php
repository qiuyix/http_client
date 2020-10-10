<?php

require "../vendor/autoload.php";

use tingyu\HttpRequest\Method\Post;


/**
 * 发起请求后可以获取的响应内容
 * 1. 获取响应体内容
 * 2. 获取响应头部内容
 * 3. 获取响应状态码内容
 * 4. 获取响应cookie信息
 */


$handle = new Post();

/**
 * 1，发起请求并获取响应结果
 * @param string 请求地址
 * @param array|string 请求参数
 */
$responseBody = $handle->request("http://www.baidu.com/s", ["wd" => "php"]);  // 数组参数

/**
 * 2. 获取响应头部
 * @param bool 返回格式为数组或字符串 true原始字符串 false整理后数组, 默认 = false
 * @return mixed
 */
$responseHeader = $handle->getResponseHeader(false);
var_dump($responseHeader);

/**
 * 3. 获取响应状态码信息
 * @return int
 */
$responseStatusCode = $handle->getResponseStatusCode();

/**
 * 4. 获取响应cookie信息，如果需要更详细的cookie,可通过获取响应头部获取
 */
$responseCookie = $handle->getResponseCookie();
var_dump($responseCookie);

