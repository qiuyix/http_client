<?php

require "../vendor/autoload.php";

use tingyu\HttpRequest\Method\Patch;


/**
 * 发起http  get请求
 */


$handle = new Patch();

/**
 * 发起请求前的设置请求体内容
 */

/**
 * 设置请求头信息, 当参数不合法时会抛出异常
 * @param string 请求头键
 * @param string 请求头值
 * @throw \Exception
 */
$handle->setRequestHeader("authorization", "Bearer xxxx");

/**
 * 设置请求超时时间, 当参数值不合法时会抛出异常
 * @param int 超时时间，单位 “秒”
 * @throw \Exception
 */
$handle->setRequestTimeOut(30);

/**
 * 设置请求毫秒超时时间, 当参数值不合法时会抛出异常
 * @param int 超时时间，单位 “豪秒”
 * @throw \Exception
 */
$handle->setRequestTimeoutMs(30000);

/**
 * 设置请求cookie, 当参数不合法或键值为空时会抛出异常
 * @param string cookie 名
 * @param string cookie 值
 * @throw \Exception
 */
$handle->setRequestCookie('key', 'value');

/**
 * https请求的时候是否进行证书校验
 * @param bool false 不进行证书校验，true 进行证书校验，当为true，证书路径必传，否则抛异常
 * @param string 证书路径，建议传入绝对路径，否则可能导致路径错误
 * @throw \Exception
 */
$handle->setRequestSslVerifyPeer(true, 'cert file path');

/**
 * 设置请求 ua 标识，参数类型不正确将抛出异常
 * @param string 请求ua标识
 * @throw
 */
$handle->setRequestUserAgent("php server 1.0");

/**
 * 设置请求代理信息
 * @param string 代理ip
 * @param int 代理端口
 */
$handle->setRequestProxy("ip", 123);

/**
 * 请求携带ssl证书, 当证书路径找不到时，抛出异常
 * @param string ssl证书路径，后缀”pem“,推荐使用绝对路径
 * @param string ssl密钥路径，后缀”pem“推荐使用绝对路径
 * @throw \Exception
 */
$handle->setRequestCertificate('sslCertPart', 'sslKeyPath');


/**
 * 发起请求并获取响应结果
 * @param string 请求地址
 * @param array|string 请求参数
 * @return mixed 请求体
 */
//$responseBody = $handle->request("http://www.baidu.com");  // 无参数
//$responseBody = $handle->request("http://www.baidu.com/s", ["wd" => "php"]);  // 数组参数
$responseBody = $handle->request("http://www.baidu.com/s", "wd=php&keyword=mysql");  // 字符串参数

/**
 * 获取响应头部
 * @param bool 返回格式为数组或字符串 true原始字符串 false整理后数组, 默认 = false
 * @return mixed
 */
$responseHeader = $handle->getResponseHeader(false);
var_dump($responseHeader);

/**
 * 获取响应状态码信息
 * @return int
 */
$responseStatusCode = $handle->getResponseStatusCode();

/**
 * 获取响应cookie信息，如果需要更详细的cookie,可通过获取响应头部获取
 */
$responseCookie = $handle->getResponseCookie();
var_dump($responseCookie);

