<?php

namespace App;

use Log;

class HttpClient
{
	private $dotenv;
	private $gitHost;
	private $gitToken;

	private $hookUrl;
	private $debug = false;

	public function __construct($path = null)
	{
		if (empty($path))
			$path = base_path();

		$dotenv = \Dotenv::load($path);

        $this->gitHost  = str_replace("\"", "", getenv('GITLAB_HOST'));
        $this->gitToken = str_replace("\"", "", getenv('GITLAB_TOKEN'));

        $this->hookUrl = str_replace("\"", "", getenv('HOOK_URL'));

        $bool = str_replace("\"", "", getenv('APP_DEBUG'));

        if (strtolower($bool) === 'true')
        	$this->debug = true;        
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

	/**
	 * get All gitlab projects
	 * 
	 * @return [type] [description]
	 */
	public function getAllProjects()
	{
		return $this->request('projects/all');
	}

	/**
	 * performing gitlab api request
	 *
	 * @param $uri API uri
	 * @param $body body data
	 * 
	 * @return type json response
	 */
	public function post($uri, $body)
	{
		$client = new \GuzzleHttp\Client([
            'base_uri' => $this->gitHost,
            'timeout'  => 10.0,
            'verify' => false,
            ]);
		
		$postData['headers'] = ['PRIVATE-TOKEN' => $this->gitToken];

		$postData['json'] = $body;

		if ($this->debug) {
			$postData['debug'] = fopen(base_path() . '/' . 'debug.txt', 'w');
		}		

		try{
			$response = $client->post($this->gitHost . '/api/v3/' . $uri, $postData);
		} catch (GuzzleHttp\Exception\ClientException $e) {
			dump($response);
		    echo $e->getRequest();
		    if ($e->hasResponse()) {
		        echo $e->getResponse();
		    }
		} 

        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201)
        {
        	throw new JiraIntegrationException("Http request failed. status code : "
        		. $response->getStatusCode() . " reason:" . $response->getReasonPhrase());
        }

        return json_decode($response->getBody());
	}

}