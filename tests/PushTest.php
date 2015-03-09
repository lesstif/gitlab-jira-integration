<?php

use GitLabJira\Push;
use GitLabJira\PushProcess;

class PushTest extends PHPUnit_Framework_TestCase 
{
	public function testDecode() 
	{
		$json = json_decode(file_get_contents('tests/push-reg-body.json'));

		$mapper = new JsonMapper();
		$push = $mapper->map($json, new Push());
		
		$this->assertEquals("Diaspora", $push->repository->name);
		$this->assertEquals("da1560886d4f094c3e6c9ef40349f7d38b5d27d7", $push->after);
		$this->assertEquals("fixed readme", $push->commits[1]->message);
		$this->assertEquals("GitLab dev user", $push->commits[1]->author->name);
		var_dump($push);
	}

	public function testPushProcess()
    {
		try {
			$push = new PushProcess(file_get_contents('tests/push-reg-body.json'));
			
			$push->jiraIntegrate();

			$this->assertTrue(true);
		} catch (JiraIntegrationException $e) {
			$this->assertTrue(FALSE, $e->getMessage());
		}
	}
	//
}

?>
