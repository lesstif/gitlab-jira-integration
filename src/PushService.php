<?php

namespace GitLabJira;

use \GitLabJira\JiraIntegrationException;
use \GitLabJira\RootClass;

/**
 * process gitlab push hook
 * 
 * @package gitlab-jira-integration
 * @author KwangSeob Jeong
 */
class PushProcess extends RootClass {

	public function __construct($jsonRequestBody) {
		$json = json_decode($jsonRequestBody);

		$mapper = new JsonMapper();
		$this->push = $mapper->map($json, new Push());
	}

	/** Push json data
	 * @var \GitLabJira\Push
	 * 
	 */
	private $push;

    private function refJiraIssue() 
    {
    	//USER mentioned this issue in LINK_TO_THE_MENTION
    }

    /**
     * Change Jira issues status directly if commit message to have trigger keywords.
     * 
     */ 
    private function transitionJiraIssue()
    {
    	$this->log->addDebug("changeJiraIssueStatus");
    }

    // 
    public function jiraIntegrate()
    {	
    	$this->log->addDebug("jiraIntegrate");

    	throw new JiraIntegrationException("not yet implemented");
    }
}

?>