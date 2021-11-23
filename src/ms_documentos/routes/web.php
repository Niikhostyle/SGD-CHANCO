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
    $router->get('/api/sgd-documentos/listarFavoritos', 'DocumentoController@listarFavoritos');
    $router->post('/api/sgd-documentos/crear', 'DocumentoController@crear');
    $router->put('/api/sgd-documentos/actualizar', 'DocumentoController@actualizar');
    $router->put('/api/sgd-documentos/enviar', 'DocumentoController@enviar');
    $router->get('/api/sgd-documentos/ver', 'DocumentoController@ver');
    $router->put('/api/sgd-documentos/actualizar_estado', 'DocumentoController@actualizar_estado');
    $router->put('/api/sgd-documentos/archivar', 'DocumentoController@archivar');
    $router->put('/api/sgd-documentos/derivar', 'DocumentoController@derivar');

});

//$router->get('/api/sgd-documentos', function () use ($router) {
    //return $router->app->version();
//    return "prueba lumen documentos";
//});
