<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use Log;

use App\GitlabUtil;
use App\HttpClient;

/**
 * process gitlab user API.
 *
 * @package default
 */
class UserController extends BaseController
{
    use \App\Env;

	private $userList = 'users.json';

    public function __construct($path = null)
    {
        $this->envLoad($path);
    }

	/**
     * get gitlab username (aka 'lesstif') by id(int: 1)
     *
     * @param type $id
     * @return type
     */
    public function getGitUser($id)
    {
        $users = $this->loadGitLabUser();

        //dd($users);
        $u = array_get($users, $id);
        if ( is_null($u))
        {
            Log::debug("user($id) not found: fetching..");
            // get user info
            $u = $this->getUser($id);
        }

        return $u;
    }

    /**
     * get a single user.
     * @param type $id gitlab userid(int)
     * @return type
     */
    private function getUser($id)
    {
        $client = new HttpClient();
        $u = $client->getUser($id);

        $user = [
        	'name' => $u->name,
        	// username is same jira user id
        	'username' => $u->username,
        	'state' => $u->state,
        	];

        $users = $this->loadGitLabUser();

        $users = array_add($users, $id, $user);

        \Storage::put($this->userList, json_encode($users, JSON_PRETTY_PRINT));
        return $user;
    }

    //
    public function createUserList()
    {
        // fetch users list from gitlab and create file.
    	$client = new HttpClient();
        $body = $client->getAllUsers();

        $users = [] ;
        foreach($body as $u)
        {
            $users[$u->id] = [
                'name' => $u->name,
                // username is same jira user id
                'username' => $u->username,
                'state' => $u->state,
                ];
        }

        \Storage::put($this->userList, json_encode($users, JSON_PRETTY_PRINT));
        return $users;
    }

    /**
     * load gitlab user data from file.
     *
     * @return type user list(json encoding)
     */
    public function loadGitLabUser()
    {
        if (\Storage::has($this->userList))
        {
            $users = \Storage::read($this->userList);
            return json_decode($users, true);
        }

        // if file not exist, create empty list.
        return [];
    }
}
