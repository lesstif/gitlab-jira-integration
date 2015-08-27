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

    /**
     * add or edit project hook settings
     * @param [Request] $request HTTP Request
     *
     * @link(http://doc.gitlab.com/ce/api/projects.html#add-project-hook, link)
     */
    public function addOrEditProjectHooks(Request $request)
    {
        $project = $request->json();

        $gitUrl = sprintf('projects/%d/hooks', $project->get('project_id'));

        $hooks = $this->projectHooks($project->get('project_id'));
        foreach ($hooks as $hook) {
            # already registed...
            if ($hook->url === $project->get('url')) {
                continue;
            }
        }

        $json['url'] = $project->get('url');
       
        $json['push_events'] = $project->get('push_events') ?: true;
        $json['issues_events'] = $project->get('issues_events') ?: false;
        $json['merge_requests_events'] = $project->get('merge_requests_events') ?: true;
        $json['tag_push_events'] = $project->get('tag_push_events') ?: false;

        $client = new HttpClient();

        $response = $client->post($gitUrl, $json);

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

        $response = $client->request('projects/' . $id . '/hooks');
        return $response;
    }

}
