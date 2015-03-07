<?php

use GitLabJira\PushProcess;

class PushTest extends PHPUnit_Framework_TestCase 
{
	public function testGetProjectLists()
    {
    	//$this->markTestIncomplete();
		try {
			$push = new PushProcess();
	
		} catch (JiraIntegrationException $e) {
			$this->assertTrue(FALSE, $e->getMessage());
		}
	}
	//
}

?>
