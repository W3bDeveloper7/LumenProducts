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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// API route group
$router->group(['prefix' => 'api'], function () use ($router) {

    $router->post('register', 'AuthController@register');

    $router->post('login', 'AuthController@login');

    $router->group(['middleware'=>['auth:api', 'auth_type:1'],'prefix' => 'admin'], function () use ($router) {
        $router->get('/pull-products', 'ProductController@pullProducts');

    });

    $router->group(['middleware'=>'auth:api'], function () use ($router) {
        $router->get('/products', 'ProductController@index');
        $router->get('/products/profitable', 'ProductController@profit');
        $router->get('/products/expensive', 'ProductController@expensive');
    });

});
