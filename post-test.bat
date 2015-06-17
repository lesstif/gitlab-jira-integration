@ECHO ON

curl -v -d @tests/push-reg-body.json -H "Content-Type: application/json" -H "X-Gitlab-Event: Push Hook"  http://localhost:9000/gitlab
