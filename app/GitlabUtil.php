<?php

namespace App;

use Log;

define("USER_LIST", 'users.json');

class GitlabUtil
{
    /**
     * get gitlab username (aka 'lesstif') by id(int: 1)
     * 
     * @param type $id 
     * @return type
     */
    public function getGitUserName($id)
    {
        $users = $this->loadGitLabUser();

        dd($users);

        $u = $users->{$id};
        if ( is_null($u))
        {
            Log::info("user($id) not found:");
        } else {
            return $u;
        }
    }
    
    /**
      * Description
      * @return type
      */ 
    public function createUserList()
    {
         // fetch users list from gitlab and create file.
        $dotenv = \Dotenv::load(base_path());
        $gitHost  = str_replace("\"", "", getenv('GITLAB_HOST'));
        $gitToken = str_replace("\"", "", getenv('GITLAB_TOKEN'));
        $client = new \GuzzleHttp\Client(
            ['base_uri' => $gitHost, 'timeout'  => 10.0, 'verify' => false,]
            );

        $response = $client->get($gitHost . "/api/v3/users", [
            'query' => [
                'private_token' => $gitToken,
                'per_page' => 10000
            ],
        ]);

        if ($response->getStatusCode() != 200)
        {
            Log::erro("Gitlab Get Users Status Code:" . $response->getStatusCode());
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
        $filesystem = new \League\Flysystem\Filesystem(
            new \League\Flysystem\Adapter\Local(storage_path())
            );
        $filesystem->put(USER_LIST, json_encode($users, JSON_PRETTY_PRINT));
        return $users;
    }
    
    /**
     * load gitlab user data from file.
     * 
     * @return type user list(json encoding)
     */
    public function loadGitLabUser()
    {
        $filesystem = new \League\Flysystem\Filesystem(
            new \League\Flysystem\Adapter\Local(storage_path())
            );
        if ($filesystem->has(USER_LIST))
        {
            $users = $filesystem->read(USER_LIST);
            return json_decode($users);
        }
       
        // if file not exist, create user list.
        return $this->createUserList(USER_LIST);
    }
}