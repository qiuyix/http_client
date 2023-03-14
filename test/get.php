<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use tingyu\HttpRequest\Method\Get;

try {

$request = new Get();
$response = $request->setCookie('123', '456')->setHeader('auth', 'bearer xxx')->setUserAgent("test")->setTimeOut(0, 200)->setCookie('789', '110')->do("http://localhost", ['a' => 'b', 'c'=> 'd']);
var_dump($response);
var_dump($request->getErrNo());
var_dump($request->getErrMsg());
} catch (\Exception $e) {
    var_dump($e->getMessage());
}

//$response = \tingyu\HttpRequest\HttpClient::Get("http://localhost", ['a' => 'b', 'c'=> 'd']);

//var_dump($response);