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

$USER_LIST = 'users.json';
// create gitlab user list
createUserList($USER_LIST, $app->log);

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
            // old gitlab does't set X-gitlab-Event Header.
            processPushHook($app);
            break;
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
        
        try {           
            $comment = new Comment();

            $body = "Issue solved with " . $commit['url'];

            $comment->setBody($body)
                ->setVisibility('role', 'Users');
            
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

/**
 * get gitlab username(aka 'lesstif') by id(int: 1)
 */
function getGitUserName($id, $username, $app)
{
    $users = loadGitLabUser();

    $u = $users[$id];
    if ( is_null($u))
    {
        $app->log->info("user($id) not found:");
    } else {
        return $u;
    }
}

function createUserList($userFile, $log)
{
     // fetch users list from gitlab and create file.
    $dotenv = new \Dotenv\Dotenv('.');
    $dotenv->load();

    $gitHost  = str_replace("\"", "", getenv('GITLAB_HOST'));
    $gitToken = str_replace("\"", "", getenv('GITLAB_TOKEN'));

    $client = new \GuzzleHttp\Client(['base_uri' => $gitHost, 'timeout'  => 10.0, 'verify' => false,]);

    $response = $client->get($gitHost . "/api/v3/users", [
        'query' => [
            'private_token' => $gitToken,
            'per_page' => 10000
        ],
    ]);

    $log->info("Status Code:" . $response->getStatusCode());

    if ($response->hasHeader('Content-Length')) {
        echo "It exists" . $response->getHeader('Content-Length');
    }

    $body = json_decode($response->getBody());
    
    $log->info("Gitlab Body:" . json_encode($response->getBody()));
    
    $users = [];

    foreach($body as $u)
    {        
        dump($u);/*
        $users[$u['id']] = [
            'name' => $u->name,
            'username' => $u->username,
            'state' => $u->state,
            ];
            */
    }

    $filesystem = new \League\Flysystem\Filesystem(new \League\Flysystem\Adapter\Local(__DIR__));

    $filesystem->put($userFile, json_encode($users, JSON_PRETTY_PRINT));

    return $users;
}

function loadGitLabUser($userFile, $log)
{
    $filesystem = new \League\Flysystem\Filesystem(new \League\Flysystem\Adapter\Local(__DIR__));

    if ($filesystem->has($userFile))
    {
        $users = $filesystem->read($userFile);

        return json_decode($users);
    }

    return createUserList($userFile);
}

?>
