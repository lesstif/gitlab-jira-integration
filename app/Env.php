<?php

namespace App;

use Log;

trait Env {
	private $dotenv;
	private $gitHost;
	private $gitToken;

	private $hookUrl;
	private $debug = false;
	private $verbose = false;

	public function envLoad($path = null)
	{
		if (empty($path))
			$path = base_path();

		$dotenv = \Dotenv::load($path);

        $this->gitHost  = str_replace("\"", "", getenv('GITLAB_HOST'));
        $this->gitToken = str_replace("\"", "", getenv('GITLAB_TOKEN'));

        $this->hookUrl = str_replace("\"", "", getenv('HOOK_URL'));

        $debug = str_replace("\"", "", getenv('APP_DEBUG'));

        if (strtolower($debug) === 'true') {
        	$this->debug = true;
        }

        $verbose = str_replace("\"", "", getenv('APP_VERBOSE'));

        if (strtolower($verbose) === 'true') {
        	$this->verbose = true;
        }
	}
 
 	public function isDebug()
 	{
 		return $this->debug;
 	}

 	public function isVerbose()
 	{
 		return $this->verbose;
 	}
}