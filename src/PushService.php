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
class PushService extends RootClass {

	public function __construct($jsonRequestBody) {
        parent::__construct();

		$json = json_decode($jsonRequestBody);

		$mapper = new \JsonMapper();
		$this->push = $mapper->map($json, new Push());
	}

	/** Push json data
	 * @var \GitLabJira\Push
	 * 
	 */
	public $push;

    private function parsingCommitMessage($commitMessage) 
    {
        $issuePattern = "([a-zA-Z]{2,}-[0-9]{1,})";

    	//USER mentioned this issue in LINK_TO_THE_MENTION
        $keywords = $this->config->get('jira.transition.keyword');

        /*
        foreach ($keywords as $transitName => $keywordArray) {
            $reg = "(" . implode("|", $keywordArray) . ")[ \t,]+" . $issuePattern;
            print($transitName) . "\n";
        }
        */

        preg_match_all('/(?:close|fix(?:ed)?|fixes)[\t ,]((?P<k>([A-Z]{2,}-[0-9]{1,})))/i', $commitMessage, $result, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($result[0]); $i++) {
            # Matched text = $result[0][$i];
            print("Fetch Keyword " . $result[0][$i]) . "\n";
        }
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

        foreach ($this->push->commits as $commit) {
            print "Commit message = $commit->message\n";

            $this->parsingCommitMessage($commit->message);
        }
        
    	//throw new JiraIntegrationException("not yet implemented");
    }
}

?>