<?php

namespace App;

use Log;

class HttpClient
{
	private $dotenv;
	private $gitHost;
	private $gitToken;

	public function __construct($path = null)
	{
		if (empty($path))
			$path = base_path();

		$dotenv = \Dotenv::load($path);

        $this->gitHost  = str_replace("\"", "", getenv('GITLAB_HOST'));
        $this->gitToken = str_replace("\"", "", getenv('GITLAB_TOKEN'));
	}

	public function getUser($id)
	{
		return $this->request('users/' . $id);
	}

	/**
	 * fetch users list from gitlab.
	 * 
	 * @return type
	 */
	public function getAllUsers()
	{
		return $this->request('users');
	}

	/**
	 * performing gitlab api request
	 * 
	 * @param $uri API uri
	 * @return type json response
	 */
	public function request($uri)
	{
		$client = new \GuzzleHttp\Client([
            'base_uri' => $this->gitHost, 
            'timeout'  => 10.0, 
            'verify' => false,
            ]);

        $response = $client->get($this->gitHost . '/api/v3/' . $uri, [
            'query' => [
                'private_token' => $this->gitToken,
                'per_page' => 10000
            ],
        ]);

        if ($response->getStatusCode() != 200)
        {
        	throw GitlabException("Http request failed. status code : " 
        		. $response->getStatusCode() . " reason:" . $response->getReasonPhrase());        
        }

        return json_decode($response->getBody());
	}       
}