<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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


$router->group(['middleware' => ['auth']], function () use ($router){

$router->get('/api/sgd-tipodoc/ver_todos', 'TipoDocumentoController@ver_todos'); 
$router->post('/api/sgd-tipodoc/crear', 'TipoDocumentoController@crear');
$router->get('/api/sgd-tipodoc/ver', 'TipoDocumentoController@ver');
$router->put('/api/sgd-tipodoc/actualizar', 'TipoDocumentoController@actualizar');
    
});


//$router->get('/api/sgd-tipodoc', function () use ($router) {
//    return "prueba lumen tipo documentos";
//});
