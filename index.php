<?php

require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Comment;

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
    $json = $app->request->getBody();

    $hook = json_decode($json, true);

    $app->log->debug('processPushHook : ' . json_encode($hook, JSON_PRETTY_PRINT));

    $issueKey = "TEST-960";

    foreach($hook['commits'] as $commit)
    {
        $app->log->info('Commit : ' . json_encode($commit, JSON_PRETTY_PRINT));
        //dump($commit);
        
        try {           
            $comment = new Comment();

            $body = "Issue solved with " . $commit['url'];

            $comment->setBody($body)
                ->setVisibility('role', 'Users');
            ;

            $issueService = new IssueService();
            $ret = $issueService->addComment($issueKey, $comment);
            
        } catch (JIRAException $e) {
            $this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
        }
    }    


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
