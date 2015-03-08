# GitLab JIRA Integration

## What
* [JIRA DVCS Connector Plugin](https://marketplace.atlassian.com/plugins/com.atlassian.jira.plugins.jira-bitbucket-connector-plugin) does not support gitlab.
* [GitLab Community Edition](http://doc.gitlab.com/ee/integration/jira.html) does not support Advanced JIRA Integration(EE only feature).

GitLab-JIRA-Integration is a PHP web app executed by gitlab web hooks and interact with JIRA using JIRA-REST API.
If you have questions contact to me or open an issue on GitHub.

## Requirements

- PHP > 5.4
- [php-jira-rest-client](https://github.com/lesstif/php-jira-rest-client)
- Atlassian JIRA 6 or above 
- Gitlab CE 6 or above

## Installation

```
composer require lesstif/gitlab-jira-integration dev-master
```

create config.jira.json file on your project root.
````json
{
    "host": "https://jira.example.com",
    "username": "username",
    "password": "password",
    "CURLOPT_SSL_VERIFYHOST": false,
    "CURLOPT_SSL_VERIFYPEER": false,
    "CURLOPT_VERBOSE": false,
    "LOG_FILE": "jira-rest-client.log",
    "LOG_LEVEL": "DEBUG"
}
````

create config.integration.json file on your project root.
````json
{
    "accept.host": [
        "localhost",
        "your-gitlab-host-here"
    ],
    "userid.mapping": {
        "gitlab-userid1": "jira-userid1",
        "gitlab-userid2": "jira-userid2",
        "gitlab-userid3": "jira-userid3"
    },
    "jira": {
        "transition": {
        	"message": "Issue KEYWORD with LINK_TO_COMMIT",
            "keyword": {
                "Resolved": [
                    "resolve",
                    "resolves",
                    "resolved",
                    "fix",
                    "fixed",
                    "fixes"
                ],
                "Closed": [
                    "close",
                    "closes",
                    "closed"
                ]
            }
            
        },
        "referencing": {
        	"message": "USER mentioned this issue in LINK_TO_COMMIT",
            "keyword": [
                "see",
                "ref",
                "refs"
            ]            
        }
    }
}
````

## Usage 

# License

Apache V2 License

# See Also
* [GitLab Web hooks](http://doc.gitlab.com/ce/web_hooks/web_hooks.html)
* [JIRA 6.2 REST API documentation](https://docs.atlassian.com/jira/REST/6.2/)
* [GitLab-EE Jira integration](http://doc.gitlab.com/ee/integration/jira.html)
* [Processing JIRA issues with commit messages](https://confluence.atlassian.com/display/Cloud/Processing+JIRA+issues+with+commit+messages)
