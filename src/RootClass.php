<?php

namespace GitLabJira;

require 'vendor/autoload.php';

use \Monolog\Logger as Logger;
use \Monolog\Handler\StreamHandler;

use \Noodlehaus\Config as Config;

class RootClass {

	protected $config;

	/** @var Monolog instance */
	protected $log;

	public function __construct()
    {	
    	$this->config = Config::load('config.jira.json', 'config.integration.json');

    	  // create logger      
        $this->log =  new Logger('GitLabJira');
    	$this->log->pushHandler(new StreamHandler(
    		$this->config->get('LOG_FILE', 'jira-rest-client.log'), 
    		$this->config->get('LOG_LEVEL', Logger::INFO)
    		));    	
    }
}

?>
