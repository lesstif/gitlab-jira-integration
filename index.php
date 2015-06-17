<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim([
    'debug' => true,
    'mode' => 'development',
    'log.enabled' => true,
    'log.level' => \Slim\Log::DEBUG,
    'log.writer' => new \Slim\Logger\DateTimeFileWriter([
        'path' => '.',
        'name_format' => 'Y-m-d',
        'message_format' => '%label% - %date% - %message%'
        ])
    ]);

$app->get('/', function () {
    echo "Hello";    
});

// Get request headers as associative array
// Get request object
$req = $app->request;
$headers = $app->request->headers;

// Get the gitlab event type.
$eventType = $app->request->headers->get('X-Gitlab-Event');

$app->post('/gitlab/', function () use($eventType, $app) {
    $app->log->info('Event Type: ' . $eventType);

    switch($eventType) {
        case 'Push Hook':
            processPushHook($app);
            break;
    }
    
    $app->response->setStatus(503);
});

$app->log->info($req->getResourceUri () . ' connect from : ' . $req->getHost() . ' Event type:' . $eventType);

$app->run();

function processPushHook($app)
{
    $app->log->info('process Push Hook');
}

?>
