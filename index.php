<?php

require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\Transition;

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

define("USER_LIST", 'users.json');

// create gitlab user list
createUserList(USER_LIST, $app->log);

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

    $u = getGitUserName(2, $app);
    foreach($hook['commits'] as $commit)
    {
        $app->log->info('Commit : ' . json_encode($commit, JSON_PRETTY_PRINT));
        
        $issueKey = extractIssueKey($commit['message']);
        if (empty($issueKey))
            continue;

        $transitionName = needTransition($commit['message'], $message);

        try {
            if (empty($transitionName))
            {
                $comment = new Comment();

                $body = sprintf($message, $u->username, $commit['url']);

                $comment->setBody($body);
                
                $issueService = new IssueService();
                $ret = $issueService->addComment($issueKey, $comment);
            } else //need issue transition
            {
                $transition = new Transition();
                $transition->setTransitionName($transitionName);
                $body = sprintf($message, $u->username, $transitionName, $commit['url']);
                $transition->setCommentBody($body);

                $issueService = new IssueService();

                $issueService->transition($issueKey, $transition);
            }
            
        } catch (JIRAException $e) {
             $app->log->error("add Comment Failed : " . $e->getMessage());
        }
    }    

    $app->response->setStatus(200);
}

function needTransition($subject, &$message)
{
    $string = file_get_contents('config.integration.json');
    $config = json_decode($string);

    foreach($config->transition->keywords as $key)
    {
        $cnt = preg_match_all($key[1],  $subject, $matches);
        if ($cnt > 0)
        {
            // matched. get keyword('Resolved', 'Closed')
            $message = $config->transition->message;
            return $key[0];
        }
    }

    $message = $config->referencing->message;

    return null;
}

function extractIssueKey($subject)
{
    $pattern = '([a-zA-Z]+-[0-9]+)';

    $cnt = preg_match_all($pattern, $subject, $matches);

    if ($cnt == 0)
        return null;

    // return only first matched key.
    return $matches[0][0];
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
function getGitUserName($id, $app)
{
    $users = loadGitLabUser($app->log);

    $u = $users->{$id};
    if ( is_null($u))
    {
        $app->log->info("user($id) not found:");
    } else {
        return $u;
    }
}

function createUserList($log)
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

    if ($response->getStatusCode() != 200)
    {
        $log->error("Gitlab Get Users Status Code:" . $response->getStatusCode());
        return ;    
    }    

    $body = json_decode($response->getBody());
        
    $users = [];

    foreach($body as $u)
    {        
        $users[$u->id] = [
            'name' => $u->name,
            'username' => $u->username,
            'state' => $u->state,
            ];
    }

    $filesystem = new \League\Flysystem\Filesystem(new \League\Flysystem\Adapter\Local(__DIR__));

    $filesystem->put(USER_LIST, json_encode($users, JSON_PRETTY_PRINT));

    return $users;
}

function loadGitLabUser($log)
{
    $filesystem = new \League\Flysystem\Filesystem(new \League\Flysystem\Adapter\Local(__DIR__));

    if ($filesystem->has(USER_LIST))
    {
        $users = $filesystem->read(USER_LIST);

        return json_decode($users);
    }

    return createUserList(USER_LIST);
}

?>
