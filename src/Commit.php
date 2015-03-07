<?php

namespace GitLabJira;

class Commit {

	/** commit hash id
	 * @var string
	 * 
	 */
	public $id;

	/** commit hash
	 * @var string
	 * 
	 */
	public $message;

	/** commit timestamp : "2011-12-12T14:27:31+02:00"
	 * @var string
	 * 
	 */
	public $timestamp;

	/** commit code url : "http://example.com/mike/diaspora/commit/b6568db1bc1dcd7f8b4d5a946b0b91f9dacd7327"
	 * @var string
	 * 
	 */
	public $url;

	/** committer name and email
	 * @var array
	 * 
	 */
	public $author;
}

?>