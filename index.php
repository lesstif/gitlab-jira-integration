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
        case 'Tag Push Hook':
            processTagHook($app);
            break;
        case 'Issue Hook':
            processIssueHook($app);
            break;
        case 'Note Hook':
            processCommentHook($app);
            break;
        case 'Merge Request Hook':
            processMergeReqHook($app);
            break;
        default:
            $app->response->setStatus(404);
            $app->response->setBody('Unknown eventType: ' . $eventType);
            return;            
    }    
});

$app->log->info($req->getResourceUri () . ' connect from : ' . $req->getHost() . ' Event type:' . $eventType);

$app->run();

function processPushHook($app)
{
    $app->log->info('processPushHook');
    $app->response->setStatus(200);
}

function processTagHook($app)
{
    $app->log->info('processTagHook');
    $app->response->setStatus(501);
    $app->response->setBody('Not Yet Implemented.');
}

function processIssueHook($app)
{
    $app->log->info('processIssueHook');
    $app->response->setStatus(501);
    $app->response->setBody('Not Yet Implemented.');
}

function processCommentHook($app)
{
    $app->log->info('processCommentHook');
    $app->response->setStatus(501);
    $app->response->setBody('Not Yet Implemented.');
}

function processMergeReqHook($app)
{
    $app->log->info('processMergeReqHook');
    $app->response->setStatus(501);
    $app->response->setBody('Not Yet Implemented.');
}

?>
