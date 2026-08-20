<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth']], function () use ($router) {

    // Dashboard
    $router->get('/api/sgd-solicitudes/dashboard', 'DashboardController@index');

    // Solicitudes
    $router->get('/api/sgd-solicitudes/listar', 'SolicitudController@listar');
    $router->get('/api/sgd-solicitudes/ver', 'SolicitudController@ver');
    $router->post('/api/sgd-solicitudes/crear', 'SolicitudController@crear');
    $router->put('/api/sgd-solicitudes/actualizar', 'SolicitudController@actualizar');
    $router->delete('/api/sgd-solicitudes/eliminar', 'SolicitudController@eliminar');
    $router->put('/api/sgd-solicitudes/aprobar-directivo', 'SolicitudController@aprobarDirectivo');
    $router->put('/api/sgd-solicitudes/rechazar-directivo', 'SolicitudController@rechazarDirectivo');
    $router->put('/api/sgd-solicitudes/firmar-rrhh', 'SolicitudController@firmarRrhh');
    $router->put('/api/sgd-solicitudes/rechazar-rrhh', 'SolicitudController@rechazarRrhh');
    $router->put('/api/sgd-solicitudes/firmar-alcalde', 'SolicitudController@firmarAlcalde');
    $router->put('/api/sgd-solicitudes/rechazar-alcalde', 'SolicitudController@rechazarAlcalde');
    $router->put('/api/sgd-solicitudes/actuar', 'SolicitudController@actuarFlujo');
    $router->put('/api/sgd-solicitudes/tras-visar', 'SolicitudController@trasVisar');
    $router->get('/api/sgd-solicitudes/pdf', 'SolicitudController@pdf');
    $router->get('/api/sgd-solicitudes/plantilla', 'SolicitudController@plantilla');

    // Catálogos / Admin
    $router->get('/api/sgd-solicitudes/cargos', 'AdminController@cargos');
    $router->post('/api/sgd-solicitudes/cargos', 'AdminController@crearCargo');
    $router->get('/api/sgd-solicitudes/departamentos', 'AdminController@departamentos');
    $router->post('/api/sgd-solicitudes/departamentos', 'AdminController@crearDepartamento');
    $router->put('/api/sgd-solicitudes/departamentos', 'AdminController@actualizarDepartamento');
    $router->get('/api/sgd-solicitudes/roles', 'AdminController@roles');
    $router->put('/api/sgd-solicitudes/roles', 'AdminController@actualizarRol');
    $router->get('/api/sgd-solicitudes/tipo-documentos/ver', 'AdminController@verTipoDocumento');
    $router->get('/api/sgd-solicitudes/tipo-documentos', 'AdminController@tipoDocumentos');
    $router->post('/api/sgd-solicitudes/tipo-documentos', 'AdminController@crearTipoDocumento');
    $router->put('/api/sgd-solicitudes/tipo-documentos', 'AdminController@actualizarTipoDocumento');
    $router->delete('/api/sgd-solicitudes/tipo-documentos', 'AdminController@eliminarTipoDocumento');
    $router->get('/api/sgd-solicitudes/buzones', 'AdminController@buzones');
    $router->get('/api/sgd-solicitudes/configuraciones', 'AdminController@configuraciones');
    $router->put('/api/sgd-solicitudes/configuraciones', 'AdminController@guardarConfiguraciones');
    $router->get('/api/sgd-solicitudes/usuarios-catalogo', 'AdminController@usuariosCatalogo');

    // RRHH saldos
    $router->get('/api/sgd-solicitudes/saldos', 'RrhhController@saldos');
    $router->post('/api/sgd-solicitudes/saldos/movimiento', 'RrhhController@movimiento');
});
