<?php

require "../vendor/autoload.php";

use tingyu\HttpRequest\Method\Delete;
use tingyu\HttpRequest\Request\Xml;

/**
 * 发起http  get请求
 */
$xml = new Xml();
$xml->setData('<xml><head>HttpClient</head><body>1333</body></xml>');

$handle = new Delete($xml);

$responseBody = $handle->request("localhost");
echo $responseBody;
