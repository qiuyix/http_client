<?php

require "../vendor/autoload.php";

use tingyu\HttpRequest\Method\Put;
use tingyu\HttpRequest\Request\Json;

$json = new Json();
$json->setData('{"msg":"success", "code":200}');

$handle = new Put($json);

$responseBody = $handle->request("localhost");
echo $responseBody;