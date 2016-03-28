<?php

class RegexTest extends PHPUnit_Framework_TestCase 
{
    public function testIssueKey() 
    {
        $array = [
            "Issue test-123 resolved by " => 1,
            "AB-123 resolved by " => 1,
            "AB123 resolved by " => 0,
            "Issue test-123 prj-321 dummy str123 abcd-986 resolved by " => 3,
            ];

        $pattern = '([a-zA-Z]+-[0-9]+)';

        foreach($array as $subject => $matchCount)
        {
            $cnt = preg_match_all($pattern, $subject, $matches);

            dump($matches[0]);
            $this->assertEquals($matchCount, $cnt, $subject);
        }
    }

    public function testKeyword()
    {
        $array = [
            "Issue resolved by " => 1,
            "Issue resolves fix by " => 2,
            "ref test-123 Issue" => 1,
            ];

        $pattern = '(resolve|fix|see|ref)';
        
        foreach($array as $subject => $matchCount)
        {
            $cnt = preg_match_all($pattern, $subject, $matches);

            dump($matches[0]);
            $this->assertEquals($matchCount, $cnt, $subject);
        }
    }
    //
}

?>
