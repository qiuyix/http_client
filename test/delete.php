<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use tingyu\HttpRequest\Method\Delete;

// post form
$request = new Delete();
$response = $request->do("http://localhost", ['a'=> 1, 'b'=>2]);
echo PHP_EOL;
echo $response;
echo PHP_EOL;



// post json string
$request = new Delete();
$response = $request->do("http://localhost", json_encode(['a'=> 1, 'b'=>2]));
echo PHP_EOL;
echo $response;
echo PHP_EOL;