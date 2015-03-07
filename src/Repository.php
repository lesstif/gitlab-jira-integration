<?php

namespace GitLabJira;

class Repository {
	
	/** Repository name
	 * @var string
	 * 
	 */
	public $name;

	/** git remote repository url
	 * @var string
	 * 
	 */
	public $url;

	/** 
	 * @var string
	 * 
	 */
	public $description;
    
    /** project home page
	 * @var string
	 * 
	 */
    public $homepage;
    
    /**
	 * @var string
	 * 
	 */
    public $git_http_url;
    
    /**
	 * @var string
	 * 
	 */
    public $git_ssh_url;
    
    /**
	 * @var int
	 * 
	 */
    public $visibility_level;
}

?>

