<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use tingyu\HttpRequest\Method\Post;

$request = new Post();
$response = $request->do("https://api.tianpanshiye.com", []);
var_dump($response);