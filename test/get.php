<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use tingyu\HttpRequest\Method\Get;

$request = new Get();
$response = $request->do("http://localhost", ['a' => 'b', 'c'=> 'd']);
var_dump($response);