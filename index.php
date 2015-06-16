<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->get('/', function () {
    echo "Hello";
});

$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});

$app->post('/gitlab/', function () {    
    $app->response->setStatus(503);
});

$app->run();
?>
