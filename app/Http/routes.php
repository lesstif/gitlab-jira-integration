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

// get a list of projects which are owned by the auth user.
$app->get('gitlab/projects/owned', [
    'as' => 'get-owned-project', 'uses' => 'ProjectController@ownedProjects'
]);

$app->get('gitlab/projects/view/{id}', [
    'as' => 'view-project', 'uses' => 'ProjectController@viewProject'
]);

// get all project in gitlab. ! admin only
$app->get('gitlab/projects/all', [
    'as' => 'get-all-project', 'uses' => 'ProjectController@allProjects'
]);

// Get a list of project hooks.
$app->get('gitlab/projects/hook/{id}', [
    'as' => 'get-project-hooks', 'uses' => 'ProjectController@projectHooks'
]);

$app->post('gitlab/projects/add-hook', [
    'as' => 'add-project-hook', 'uses' => 'ProjectController@addOrEditProjectHooks'
]);
