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
    return view('home');
});
$router->get('/aproved/{hash}', 'ApiController@aproved_payment');
$router->get('/balance','ApiController@get_balance');

$router->post('/paycreate','ApiController@create_payment');
$router->get('/paystatus','ApiController@get_status_payment');
$router->post('/payexe','ApiController@pay_execute');

