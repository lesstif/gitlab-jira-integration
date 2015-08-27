<?php

namespace App\Http\Controllers;

use Log;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App\GitlabUtil;
use App\HttpClient;

/**
 * gitlab project api
 * 
 */
class ProjectController extends BaseController
{

    /**
     * Get a list of all GitLab projects
     * 
     */
    public function allProjects()
    {    
        $client = new HttpClient();
        $body = $client->request('projects/all');

        dump($body);
    }

    /**
     * get a list of projects which owned by auth user.
     * @return [json] [array of Project]
     */
    public function ownedProjects()
    {
        $client = new HttpClient();
        $projects = $client->request('projects/owned');

        return $projects;
    }

    public function viewProject($id)
    {
        $client = new HttpClient();
        $project = $client->request('projects/' . $id);

        dump($project);
        return json_encode($project, JSON_PRETTY_PRINT);
    }

    public function addHookAllProjects(Request $request)
    {
        $projects = $this->ownedProjects();

        foreach($projects as $proj) {
            Log::info('add hook to project ' . $proj->name_with_namespace);
            $this->addOrEditProjectHooks($request, $proj->id);
        }
    }

    /**
     * add or edit project hook settings
     * @param [Request] $request HTTP Request
     *
     * @link(http://doc.gitlab.com/ce/api/projects.html#add-project-hook, link)
     */
    public function addOrEditProjectHooks(Request $request, $project_id = null)
    {
        $project = $request->json();

        if ($project_id == null)
            $id = $project->get('project_id');
        else 
            $id = $project_id;

        // hook url
        $url = $project->get('url');

        $gitUrl = sprintf('projects/%d/hooks', $id);

        $json['url'] = $url;
       
        $json['push_events'] = $project->get('push_events') ?: true;
        $json['issues_events'] = $project->get('issues_events') ?: false;
        $json['merge_requests_events'] = $project->get('merge_requests_events') ?: true;
        $json['tag_push_events'] = $project->get('tag_push_events') ?: false;

        //dump($json);
        $client = new HttpClient();

        $method = 'POST';
        $hookId = null;
        if ($this->hookHasUrl($id, $project->get('url'), $hookId) == true) {
            Log::info("project '$id' hook('$url') is already exist..");
            $method = 'PUT';
            $gitUrl .= '/' . $hookId;
        }

        $response = $client->send($gitUrl, $json, $method);

        return json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * get all project hooks
     * @param  [integer] $id project id
     * @return [type]     [description]
     */
    public function projectHooks($id)
    {
        $client = new HttpClient();

        $hooks = $client->request('projects/' . $id . '/hooks');
        return $hooks;
    }

    /**
     * check given hook's has same url.
     *
     * @param  [integer] $id project id
     * @param  [string] $url hookUrl
     * @return [boolean]     true: url already exist, false: none
     */
    private function hookHasUrl($id, $url, &$hookId)
    {    
        $hooks = $this->projectHooks($id);

        $rs = rtrim($url, '/');

        foreach($hooks as $h) {
            $ls = rtrim($h->url, '/');
            if ($ls === $rs) {
                $hookId = $h->id;
                return true;
            }
        }

        return false;
    }
}
