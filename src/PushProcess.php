<?php

namespace GitLabJira;

/**
 * process gitlab push hook
 * 
 * @package gitlab-jira-integration
 * @author KwangSeob Jeong
 */
class PushProcess {

	/** before commit hash
	 * @var string
	 * 
	 */
    public $before;

    /** after commit hash
	 * @var string
	 * 
	 */
    public $after;

     /** ref : "refs/heads/master",
	 * @var string
	 * 
	 */
    public $ref;

     /** gitlab userid(not string)
	 * @var int
	 * 
	 */
    public $user_id;

    /** user_name
	 * @var string
	 * 
	 */
    public $user_name;

    /** user email
	 * @var string
	 * 
	 */
    public $user_email;

    /** gitlab project id(not string)
	 * @var int
	 * 
	 */
    public $project_id;

 	/** repository info
	 * @var GitLabJira\Repository
	 * 
	 */
    public $repository;

	/** repository info
	 * @var CommitList[\GitLabJira\Commit]
	 * 
	 */
    public $commits;

   	/** total commit count 
	 * @var int
	 * 
	 */
    public $total_commits_count;
}

?>