<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\BuzonController;

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


$router->get('/api/sgd-buzones/listar_todos', 'BuzonController@listar_todos');     //ver todos         :: OK
$router->post('/api/sgd-buzones/crear', 'BuzonController@crear');           //crear
//$router->put('/api/sgd-buzones/buzon/{id}', 'BuzonController@actualizar');  //actualizar/editar
$router->put('/api/sgd-buzones/actualizar', 'BuzonController@actualizar');  //actualizar/editar
$router->delete('/api/sgd-buzones/eliminar', 'BuzonController@eliminar'); //eliminar
//$router->get('/api/sgd-buzones/buzon/{id}', 'BuzonController@ver');         //ver
//$router->get('/api/sgd-buzones/ver/{id}', 'BuzonController@ver');         //ver
$router->get('/api/sgd-buzones/ver', 'BuzonController@ver');         //ver


/*
$router->get('/api/sgd-buzones', function () use ($router) {
    //return $router->app->version();
    return "prueba lumen buzones.";
});
*/