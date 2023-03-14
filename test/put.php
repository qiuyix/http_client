<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";


// post form
$request = new \tingyu\HttpRequest\Method\Put();
$response = $request->do("http://localhost", ['a'=> 1, 'b'=>2]);
echo PHP_EOL;
echo $response;
echo PHP_EOL;
echo PHP_EOL;



// post json string
$request = new \tingyu\HttpRequest\Method\Put();
$response = $request->do("http://localhost", '12222323');
echo PHP_EOL;
echo $response;
echo PHP_EOL;
echo PHP_EOL;