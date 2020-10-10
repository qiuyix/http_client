<?php

// 引入文件，该代码仅供参考，具体视情况而定
require "../vendor/autoload.php";

use tingyu\HttpRequest\Method\Post;
use tingyu\HttpRequest\Request\Form;

/**
 * 准备表单数据
 * @para array 表单数据，支持多次调用，重复键值将覆盖调
 */
$form = new Form();
$form->setData(['language'=>'php']);
// 上传文件
$form->setUploadFile('img', __DIR__ . '/../logs.txt', 'aaa');

$handle = new Post($form);

/**
 * 发起请求并获取响应结果
 * @param string 请求地址
 * @param array|string 请求参数
 * @return mixed 请求体
 */
$responseBody = $handle->request("localhost");  // 字符串参数
echo $responseBody;