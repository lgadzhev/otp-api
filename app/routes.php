<?php

use Bramus\Router\Router;

$router = new Router();

$router->options(API_VER . '/.*',
	'App\Http\Controllers\OptionsController@crossOrigin');

$router->mount(API_VER . '/public', function () use ($router) {

	$router->post('/register', 'App\Http\Controllers\RegistrationController@store');
	$router->post('/verify-phone', 'App\Http\Controllers\RegistrationController@verifyPhone');
	$router->post('/new-code', 'App\Http\Controllers\RegistrationController@newCode');
});

$router->run();
