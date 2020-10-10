<?php

require "../vendor/autoload.php";

use tingyu\HttpRequest\Method\Post;


/**
 * 发起请求前可以设置的请求体内容
 * 1. 设置请求头信息
 * 2. 设置请求超时时间
 * 3. 设置请求超时毫秒时间
 * 4. 设置请求cookie信息
 * 5. 设置https正式否实进行证书验证，默认不进行证书验证
 * 6. 设置请求 user-agent 标识
 * 7. 设置请求代理信息
 * 8。设置请求ssl证书信息
 */


$handle = new Post();


/**
 * 1. 设置请求头信息, 当参数不合法时会抛出异常
 * @param string 请求头键
 * @param string 请求头值
 * @throw \Exception
 */
$handle->setRequestHeader("authorization", "Bearer xxxx");

/**
 * 2. 设置请求超时时间, 当参数值不合法时会抛出异常
 * @param int 超时时间，单位 “秒”
 * @throw \Exception
 */
$handle->setRequestTimeOut(30);

/**
 * 3. 设置请求毫秒超时时间, 当参数值不合法时会抛出异常
 * @param int 超时时间，单位 “豪秒”
 * @throw \Exception
 */
$handle->setRequestTimeoutMs(30000);

/**
 * 4. 设置请求cookie, 当参数不合法或键值为空时会抛出异常
 * @param string cookie 名
 * @param string cookie 值
 * @throw \Exception
 */
$handle->setRequestCookie('key', 'value');

/**
 * 5. 设置https请求的时候是否进行证书校验
 * @param bool false 不进行证书校验，true 进行证书校验，当为true，证书路径必传，否则抛异常
 * @param string 证书路径，建议传入绝对路径，否则可能导致路径错误
 * @throw \Exception
 */
$handle->setRequestSslVerifyPeer(true, 'cert file path');

/**
 * 6. 设置请求 user-agent 标识，参数类型不正确将抛出异常
 * @param string 请求ua标识
 * @throw
 */
$handle->setRequestUserAgent("php server 1.0");

/**
 * 7. 设置请求代理信息
 * @param string 代理ip
 * @param int 代理端口
 */
$handle->setRequestProxy("ip", 123);

/**
 * 8. 请求携带ssl证书, 当证书路径找不到时，抛出异常
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
$responseBody = $handle->request("http://www.baidu.com/s", ["wd" => "php"]);  // 数组参数



