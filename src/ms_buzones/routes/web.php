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

$router->group(['middleware' => ['auth']], function () use ($router){
    $router->get('/api/sgd-buzones/listar_todos', 'BuzonController@listar_todos');
    $router->post('/api/sgd-buzones/crear', 'BuzonController@crear');  
    $router->put('/api/sgd-buzones/actualizar', 'BuzonController@actualizar');  
    $router->delete('/api/sgd-buzones/eliminar', 'BuzonController@eliminar'); 
    $router->get('/api/sgd-buzones/ver', 'BuzonController@ver');           
});


       

/*
$router->get('/api/sgd-buzones', function () use ($router) {
    //return $router->app->version();
    return "prueba lumen buzones.";
});
*/