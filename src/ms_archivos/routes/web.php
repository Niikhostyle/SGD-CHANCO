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
    $router->put('/api/sgd-archivos/generar_archivo_pdf', 'ArchivoController@generar_archivo_pdf');
    $router->get('/api/sgd-archivos/generar_vista_previa', 'ArchivoController@generar_vista_previa');
    $router->put('/api/sgd-archivos/generar_folio', 'ArchivoController@generar_folio'); 
});

/*
$router->get('/api/sgd-archivos', function () use ($router) {
    //return $router->app->version();
    return "prueba lumen archivos";
});
*/