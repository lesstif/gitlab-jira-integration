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

    /**
     * add or edit project hook settings
     * @param [integer] $projectId gitlab project id
     * @param [string] $hookUrl hookUrl (required) - The hook URL
     * @param [array] $[events] push_events - Trigger hook on push events, (default: true)
     *                          issues_events - Trigger hook on issues events(default: false), 
     *                          merge_requests_events - Trigger hook on merge_requests events (default: true)
     *                          tag_push_events - Trigger hook on push_tag events, (default: false)
     *
     * @link(http://doc.gitlab.com/ce/api/projects.html#add-project-hook, link)
     */
    public function addOrEditProjectHooks(Request $request)
    {
        $project = $request->json();       

        $gitUrl = sprintf('projects/%d/hooks', $project->get('project_id'));

        $data = ['url'  => $project->get('url')];

        $data['push_events'] = $project->get('push_events') ?: true;
        $data['issues_events'] = $project->get('issues_events') ?: false;
        $data['merge_requests_events'] = $project->get('merge_requests_events') ?: true;
        $data['tag_push_events'] = $project->get('tag_push_events') ?: false;

        $client = new HttpClient();

        $response = $client->post($gitUrl, $data);

        dump($response);        
    }
}
