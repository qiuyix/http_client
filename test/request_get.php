<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use tingyu\HttpRequest\Method\Get;

$request = new Get();
$response = $request->do("https://api.tianpanshiye.com", []);
var_dump($response);