# GitLab JIRA Integration

## What
* [JIRA DVCS Connector Plugin](https://marketplace.atlassian.com/plugins/com.atlassian.jira.plugins.jira-bitbucket-connector-plugin) does not support gitlab.
* [GitLab Community Edition](http://doc.gitlab.com/ee/integration/jira.html) does not support Advanced JIRA Integration(EE only feature).

GitLab-JIRA-Integration is a small PHP standalone app executed by gitlab web hooks and interact with JIRA using JIRA-REST API.
If you have questions contact to me or open an issue on GitHub.

## How it works.
![How it works.](https://cloud.githubusercontent.com/assets/404534/8185075/f5241acc-147c-11e5-9961-32e241948ee9.png)

## Requirements

- PHP > 5.4
- [php-jira-rest-client](https://github.com/lesstif/php-jira-rest-client)
- Atlassian JIRA 6 or above 
- Gitlab CE 6 or above

## Installation

Install Composer
```
$ composer require lesstif/gitlab-jira-integration dev-master
```

copy .env.example file to `.env` in the root of your project.
```sh
$ cp .env.example .env
```

You would first define a Jira and Gitlab connection into `.env` configuration.
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

Next, copy config.integration.example.json to `config.integration.json` in the root of your project.
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
    }
}
````

## Usage 

Run PHP standalone web server on the gitlab-jira integration server. (eg: my-host.com).
```
php -S 0.0.0.0:9000
```

## Configuration

### gitlab configuration
- Choose  > **Project Settings** -> **Web Hooks**.
- Setting URL to your gitlab-jira integration's running Host. (eg: **http://my-host.com:9000/gitlab**)
![gitlab configuration.](https://cloud.githubusercontent.com/assets/404534/8201559/34dc5004-150d-11e5-9baf-6d7226cd8b84.png)

### Referencing JIRA isssues
- git commit with JIRA Issue Key(eg. TEST-123 or test-123)
- Gitlab-Jira-Integrator will automatically add a comment in specific JIRA Issue.

### Resolving or Closing JIRA isssues
- git commit with JIRA Issue Key and trigger keywords(eg. 'Closed TEST-123' or 'fix test-123')
- Gitlab-Jira-Integrator will automatically add a comment and closing(or fixing) directly in specific JIRA Issue by using trigger keywords(setting in config.integration.json) in commit message. 


# License

Apache V2 License

# See Also
* [GitLab Web hooks](http://doc.gitlab.com/ce/web_hooks/web_hooks.html)
* [JIRA 6.2 REST API documentation](https://docs.atlassian.com/jira/REST/6.2/)
* [GitLab-EE Jira integration](http://doc.gitlab.com/ee/integration/jira.html)
* [Processing JIRA issues with commit messages](https://confluence.atlassian.com/display/Cloud/Processing+JIRA+issues+with+commit+messages)

