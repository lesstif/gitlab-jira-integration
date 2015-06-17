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
composer require lesstif/gitlab-jira-integration dev-master
```

copy .env.example file to .env in the root of your project and Add your application configuration to a .env.
```
JIRA_HOST="https://your-jira.host.com"
JIRA_USER="jira-username"
JIRA_PASS="jira-password"

GITLAB_HOST="https://your-gitlab.host.com"
GITLAB_TOKEN="gitlab-private-token-for-api"
```

copy config.integration.example.json to config.jira.json in the root of your project.
````json
{
    "accept.host": [
        "localhost",
        "your-gitlab-host-here"
    ],
    "transition": {
        "message": "[~%s] Issue %s with %s",
        "keyword": {
            "Resolved": [
                "resolve",
                "fix"
            ],
            "Closed": [
                "close"
            ]
        }
    },
    "referencing": {
        "message": "[~%s] mentioned this issue in %s",
        "keyword": [
            "see",
            "ref"
        ]
    }
}

````

## Usage 

Run PHP standalone web server on the gitlab-jira integration server. (Ex: my-host.com).
```
php -S 0.0.0.0:9000
```

## Configuration

### gitlab configuration
- Choose  > **Project Settings* -> **Web Hooks** .
- config URL to **http://tmy-host.com:9000/gitlab**
![gitlab configuration.](https://cloud.githubusercontent.com/assets/404534/8186689/b818862c-1486-11e5-9d9a-8d33c30e9cd3.png)
- push your change set.

# License

Apache V2 License

# See Also
* [GitLab Web hooks](http://doc.gitlab.com/ce/web_hooks/web_hooks.html)
* [JIRA 6.2 REST API documentation](https://docs.atlassian.com/jira/REST/6.2/)
* [GitLab-EE Jira integration](http://doc.gitlab.com/ee/integration/jira.html)
* [Processing JIRA issues with commit messages](https://confluence.atlassian.com/display/Cloud/Processing+JIRA+issues+with+commit+messages)

