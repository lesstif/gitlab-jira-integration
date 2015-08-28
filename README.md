# GitLab JIRA Integration

## What is it?
* [JIRA DVCS Connector Plugin](https://marketplace.atlassian.com/plugins/com.atlassian.jira.plugins.jira-bitbucket-connector-plugin) does not support gitlab.
* [GitLab Community Edition](http://doc.gitlab.com/ee/integration/jira.html) does not support Advanced JIRA Integration(EE only feature).

GitLab-JIRA-Integration is a small PHP standalone app executed by gitlab web hooks and interact with JIRA using JIRA-REST API.
If you have questions contact to me or open an issue on GitHub.

## How it works.
![How it works.](https://cloud.githubusercontent.com/assets/404534/8185075/f5241acc-147c-11e5-9961-32e241948ee9.png)

## Requirements

- PHP >= 5.5.9
- Lumen framework >= 5.1
- [php-jira-rest-client](https://github.com/lesstif/php-jira-rest-client)
- Atlassian JIRA 6 or above
- Gitlab CE 6 or above

## Installation

1. Download and Install PHP Composer.
	``` sh
	curl -sS https://getcomposer.org/installer | php
	```

2. clonning gitlab-jira-intergration project
	```sh
	$ git clone https://github.com/lesstif/gitlab-jira-integration.git
	```

3. Run the composer install command.
	```sh
	$ composer install
	```

4. Now you need define your a Jira and Gitlab connection info into `.env` configuration.
	```
	JIRA_HOST="https://your-jira.host.com"
	JIRA_USER="jira-username"
	JIRA_PASS="jira-password"
	GITLAB_HOST="https://your-gitlab.host.com"
	GITLAB_TOKEN="gitlab-private-token-for-api"
	```

**Tip:**  In the following steps, you will generate your private token for API.
- login gitlab and click on **Profile Settings**
- Click on **Account**
- Here, You can find your private token.
![Private Token](https://cloud.githubusercontent.com/assets/404534/8210509/555cf47e-154d-11e5-83da-84f6f96b4fae.png)

Next, copy config.integration.example.json to `config.integration.json`.

```sh
$ cp config.integration.example.json config.integration.json
```

Here is the default configuration, for interact with Jira.
````json
{
    "accept.host": [
        "localhost",
        "your-gitlab-host-here"
    ],
    "transition": {
        "message": "[~%s] Issue %s with %s",
        "keywords": [
            [
                "Resolved",
                "[resolve|fix]"
            ],
            [
                "Closed",
                "[close]"
            ]
        ]
    },
    "referencing": {
        "message": "[~%s] mentioned this issue in %s"
    },
    "merging": {
        "message": "[~%s] COMMIT_MESSAGE with %s"
    }
}
````

#### transition
- **message** : "[~%s] issue %s with %s" : Converted to "**User** Issue **Resolved** with **Commit URL**"
- **keywords**: if commit message had second element(eg: resolve or fix),then issue status transition to first element.(eg : Resolved)


## Usage

Run PHP standalone web server on the gitlab-jira integration server. (eg: my-host.com).
```
php artisan serve --host 0.0.0.0 --port 9000
```

## Configuration

### gitlab web hook configuration
- Choose  > **Project Settings** -> **Web Hooks**.
- Setting URL to your gitlab-jira integration's running Host. (eg: **http://my-host.com:9000/gitlab/hook**)
![gitlab configuration.](https://cloud.githubusercontent.com/assets/404534/8638183/7f7951c2-28ed-11e5-987f-5258f1bc2bec.png)

**Tip:**  If you decide to change the hook receiving URI from the default, Open the app/Http/routes.php file in a text editor and find this line:
```php
$app->post('gitlab/hook',[
	'as' => 'hook', 'uses' => 'GitlabController@hookHandler'
]);
```
change to 'gitlab/hook' to desired the URI (eg: 'gitlab/my-hook-receiver')

### auto gitlab webhook configuration
1. modify 'url' field and save to hook.json

	```json
	{
    "project_id": 5,
    "url": "https://localhost:9000/gitlab/hook/",
    "push_events": true,
    "issues_events": false,
    "merge_requests_events": true,
    "tag_push_events": true
	}
	```
2. modify url(ttp://my-host.com:9000/) to your url and running curl command

	```sh
	curl -X POST @hook.json http://my-host.com:9000/gitlab/projects/add-hook-all-projects
	```

3. login gitlab and goto Choose  > **Project Settings** -> **Web Hooks**.  Then confirm your web hook settings.

## Checking Installation  
1.  To get started, let's running a curl command on your command line.

	```sh
	curl http://myhost.com:9000/gitlab/user/list
	```
You can see response json data including gitlab user list and created user list file to 'storage/app/users.json'.
	```json
{
    "1234": {
        "name": "KwangSeob Jeong",
        "username": "lesstif",
        "state": "active"
    }
}
	 ```

## Usage

### Referencing JIRA isssues
- git commit with JIRA Issue Key(eg. TEST-123 or test-123)
- Gitlab-Jira-Integrator will automatically add a comment in specific JIRA Issue.

### Resolving or Closing JIRA isssues
- git commit with JIRA Issue Key and trigger keywords(eg. 'Closed TEST-123' or 'fix test-123')
- Gitlab-Jira-Integrator will automatically add a comment and closing(or fixing) directly in specific JIRA Issue by using trigger keywords(setting in config.integration.json) in commit message.

### Issue Time Tracking
not yet implemented.


# License

Apache V2 License

# See Also
* [GitLab Web hooks](http://doc.gitlab.com/ce/web_hooks/web_hooks.html)
* [JIRA 6.2 REST API documentation](https://docs.atlassian.com/jira/REST/6.2/)
* [GitLab-EE Jira integration](http://doc.gitlab.com/ee/integration/jira.html)
* [Processing JIRA issues with commit messages](https://confluence.atlassian.com/display/Cloud/Processing+JIRA+issues+with+commit+messages)

