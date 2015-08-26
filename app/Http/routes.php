<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->welcome();
});

$app->post('gitlab/hook', [
	'as' => 'hook', 'uses' => 'GitlabController@hookHandler'
]);

// lumen route doesn't have any() method
$app->post('gitlab/user/list', [
    'as' => 'create-user', 'uses' => 'UserController@createUserList'
]);
$app->get('gitlab/user/list', [
    'as' => 'create-user', 'uses' => 'UserController@createUserList'
]);

$app->get('gitlab/user/view/{id}', [
    'as' => 'get-user', 'uses' => 'UserController@getGitUser'
]);
