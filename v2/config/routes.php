<?php
/**
 * Definición de rutas de la aplicación V2
 */

// ===============================================
// RUTAS PÚBLICAS (sin autenticación)
// ===============================================
$router->get('/', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');

// ===============================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ===============================================

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Logout
$router->get('/logout', 'AuthController@logout');

// ===============================================
// ENCUESTAS (Admin y Socios)
// ===============================================
$router->get('/encuestas/ultima', 'EncuestasController@ultima');
$router->get('/encuestas/anteriores', 'EncuestasController@anteriores');
$router->post('/encuestas/guardar-precio', 'EncuestasController@guardarPrecio');
$router->post('/encuestas/upload-excel', 'EncuestasController@uploadExcel');
$router->post('/encuestas/toggle-articulo', 'EncuestasController@toggleArticulo');

// ===============================================
// USUARIOS (Solo Admin) - MIGRADO DE V1
// ===============================================
$router->get('/usuarios', 'UsuariosController@index');
$router->get('/usuarios/administrativos', 'UsuariosController@administrativos');
$router->get('/usuarios/socios', 'UsuariosController@socios');
$router->post('/usuarios/create', 'UsuariosController@create');
$router->post('/usuarios/update', 'UsuariosController@update');
$router->post('/usuarios/toggle', 'UsuariosController@toggle');
$router->post('/usuarios/delete', 'UsuariosController@delete');

// ===============================================
// CONFIGURACIÓN (Solo Admin)
// ===============================================

// Mercados
$router->get('/config/mercados', 'ConfigController@mercados');
$router->post('/config/mercados/create', 'ConfigController@mercados_create');
$router->post('/config/mercados/update', 'ConfigController@mercados_update');
$router->post('/config/mercados/delete', 'ConfigController@mercados_delete');

// Rubros
$router->get('/config/rubros', 'ConfigController@rubros');
$router->post('/config/rubros/create', 'ConfigController@rubros_create');
$router->post('/config/rubros/update', 'ConfigController@rubros_update');
$router->post('/config/rubros/delete', 'ConfigController@rubros_delete');

// Familias
$router->get('/config/familias', 'ConfigController@familias');
$router->post('/config/familias/create', 'ConfigController@familias_create');
$router->post('/config/familias/update', 'ConfigController@familias_update');
$router->post('/config/familias/delete', 'ConfigController@familias_delete');

// Artículos
$router->get('/config/articulos', 'ConfigController@articulos');
$router->post('/config/articulos/create', 'ConfigController@articulos_create');
$router->post('/config/articulos/update', 'ConfigController@articulos_update');
$router->post('/config/articulos/delete', 'ConfigController@articulos_delete');

// Encuestas
$router->get('/config/encuestas', 'ConfigController@encuestas');
$router->post('/config/encuestas/create', 'ConfigController@encuestas_create');
$router->post('/config/encuestas/update', 'ConfigController@encuestas_update');
$router->post('/config/encuestas/delete', 'ConfigController@encuestas_delete');

// ===============================================
// CUENTA (Todos)
// ===============================================
$router->get('/cuenta/cambiar-password', 'CuentaController@cambiarPassword');
$router->post('/cuenta/update-password', 'CuentaController@updatePassword');

// ===============================================
// API (AJAX)
// ===============================================
$router->get('/api/familias/:idRubro', 'ApiController@familiasPorRubro');
$router->get('/api/articulos/:idFamilia', 'ApiController@articulosPorFamilia');
