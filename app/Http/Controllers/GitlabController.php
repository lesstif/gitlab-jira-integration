<?php

namespace App\Http\Controllers;

use Log;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\Transition;
use JiraRestApi\Configuration\DotEnvConfiguration;

class GitlabController extends BaseController
{
    use \App\Env;

    public function __construct($path = null)
    {
        $this->envLoad($path);
    }

    /**
     * process request from gitlab webhook.
     *
     * @param  Request  $request
     * @return Response
     */
    public function hookHandler(Request $request)
    {
        $clientIp = !empty($request->header('X-Forwarded-For')) ?: $request->ip();
        Log::debug('hook received from ' . $clientIp);

        if ($this->isVerbose()) {
            dump($request);
        }

        $eventType = $request->headers->get('X-Gitlab-Event') ;
        if (is_null($eventType))
            $eventType = 'Push Hook';

         // for debugging purpose.
        \Storage::put(str_replace(' ', '-', $eventType) . ".json",
            json_encode($request->json()->all(), JSON_PRETTY_PRINT));

        Log::info('eventType : ' . $eventType);

        if ($eventType == 'Push Hook')
        {
            return $this->pushHook($request);
        }
        elseif ($eventType == 'Tag Push Hook')
        {
            return $this->tagPushHook($request);
        }
        elseif ($eventType == 'Issue Hook'){
            return $this->issueHook($request);
        }
        elseif ($eventType == 'Note Hook')
        {
            return $this->noteHook($request);
        }
        elseif ($eventType == 'Merge Request Hook'){
            return $this->mergeRequestHook($request);
        }

        abort(500, 'Unknown Hook type : ' . $eventType);
    }

    private function pushHook(Request $request)
    {
        $userController = new UserController();

        $hook = $request->json();

        // call UserController's method using IoC.
        $user = \App::make('App\Http\Controllers\UserController')
            ->{'getGitUser'}($hook->get('user_id'));

        $commits = $hook->get('commits');

        // empty commit logs. stop processing.
        if (is_null($commits) || count($commits) == 0) {
            Log::info("Empty commit logs. " . json_encode($hook->all(), JSON_PRETTY_PRINT));

            return response()->json([
                'result' => 'Ok',
                'issue_count' => 0
            ]);
        }

        $issueCount = 0;
        foreach($commits as $commit)
        {
            Log::debug('Commit : ' . json_encode($commit, JSON_PRETTY_PRINT));

            $issueKey = $this->extractIssueKey($commit['message']);
            if (empty($issueKey)) {
                Log::debug('Can\'t found issue Key in commit message : ' . $commit['message']);
                continue;
            }

            Log::debug("Found found issue Key($issueKey) in commit message : " . $commit['message']);

            $issueCount++;

            $transitionName = $this->needTransition($commit['message'], $message);
            try {
                if (empty($transitionName))
                {
                    $comment = new Comment();
                    $body = sprintf($message, $user['username'], $commit['url']);
                    $comment->setBody($body);

                    $issueService = new IssueService(new DotEnvConfiguration(base_path()));
                    $ret = $issueService->addComment($issueKey, $comment);
                } else //need issue transition
                {
                    $transition = new Transition();
                    $transition->setTransitionName($transitionName);
                    $body = sprintf($message, $user['username'], $transitionName, $commit['url']);
                    $transition->setCommentBody($body);
                    $issueService = new IssueService(new DotEnvConfiguration(base_path()));
                    $issueService->transition($issueKey, $transition);
                }

            } catch (JIRAException $e) {
                 Log::error("add Comment Failed : " . $e->getMessage());
            }
        }

        return response()->json([
            'result' => 'Ok',
            'issue_count' => $issueCount
            ]);
    }

    private function needTransition($subject, &$message)
    {
        //$filesystem = new \League\Flysystem\Filesystem(new \League\Flysystem\Adapter\Local(__DIR__));
        $string = file_get_contents(base_path() . DIRECTORY_SEPARATOR  . 'config.integration.json');
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

        // Merge branch 'feature\/test-961' into develop\n",
        $cnt = preg_match("/Merge\s+branch\s+'feature/i",  $subject, $matches);
        if ($cnt > 0) {
            $message =  str_replace("COMMIT_MESSAGE", $subject, $config->merging->message);
        }else {
            $message = $config->referencing->message;
        }
        return null;
    }

    private function extractIssueKey($subject)
    {
        $pattern = '([a-zA-Z]+-[0-9]+)';
        $cnt = preg_match_all($pattern, $subject, $matches);
        if ($cnt == 0)
            return null;
        // return only first matched key.
        return $matches[0][0];
    }

    private function tagHook(Request $req)
    {
        abort(500, 'Not Yet Implemented.');
    }

    private function issueHook(Request $req)
    {
        abort(500, 'Not Yet Implemented.');
    }

    private function noteHook(Request $req)
    {
         abort(500, 'Not Yet Implemented.');
    }

    private function mergeReqHook(Request $req)
    {
       abort(500, 'Not Yet Implemented.');
    }


}
