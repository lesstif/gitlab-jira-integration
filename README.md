# GitLab JIRA Integration

## What
* [JIRA DVCS Connector Plugin](https://marketplace.atlassian.com/plugins/com.atlassian.jira.plugins.jira-bitbucket-connector-plugin) does not support gitlab.
* [GitLab Community Edition]i(http://doc.gitlab.com/ee/integration/jira.html) does not support Advanced JIRA Integration(EE Only).

GitLab-JIRA-Integration is a PHP script run with gitlab web hooks and performing JIRA issue tracker.

## Requirements

- PHP > 5.4 (although pull request to support 2 are welcome)
- [php-jira-rest-client](https://github.com/lesstif/php-jira-rest-client)
- Atlassian JIRA 6.2 or above 
- Gitlab CE 6.7 or above

## Installation

## Usage 

# License

Apache V2 License

# See Also
* [GitLab Web hooks](http://doc.gitlab.com/ce/web_hooks/web_hooks.html)
* [JIRA 6.2 REST API documentation](https://docs.atlassian.com/jira/REST/6.2/)
* [GitLab-EE Jira integration](http://doc.gitlab.com/ee/integration/jira.html)
* [Processing JIRA issues with commit messages](https://confluence.atlassian.com/display/Cloud/Processing+JIRA+issues+with+commit+messages)
