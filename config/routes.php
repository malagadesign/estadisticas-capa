<?php
/**
 * Definición de rutas de la aplicación V2
 */

// ===============================================
// RUTAS PÚBLICAS (sin autenticación)
// ===============================================
$router->get('/', 'AuthController@showLogin');
$router->get('/log', 'AuthController@showLogin'); // Login por hash (mantiene compatibilidad v1)
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
$router->post('/encuestas/guardar-dato', 'EncuestasController@guardarDato'); // Nueva: grilla Tab 2
$router->post('/encuestas/upload-excel', 'EncuestasController@uploadExcel');
$router->post('/encuestas/toggle-articulo', 'EncuestasController@toggleArticulo');
$router->get('/encuestas/articulos', 'EncuestasController@getArticulosPorFamilia'); // Nueva: carga diferida
$router->get('/encuestas/seguimiento', 'EncuestasController@seguimiento'); // Nueva: seguimiento admin
$router->post('/encuestas/enviar-recordatorios', 'EncuestasController@enviarRecordatorios'); // Nueva: enviar recordatorios

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
$router->get('/panel/config/mercados', 'ConfigController@mercados');
$router->post('/panel/config/mercados/create', 'ConfigController@mercados_create');
$router->post('/panel/config/mercados/update', 'ConfigController@mercados_update');
$router->post('/panel/config/mercados/delete', 'ConfigController@mercados_delete');

// Rubros
$router->get('/panel/config/rubros', 'ConfigController@rubros');
$router->post('/panel/config/rubros/create', 'ConfigController@rubros_create');
$router->post('/panel/config/rubros/update', 'ConfigController@rubros_update');
$router->post('/panel/config/rubros/delete', 'ConfigController@rubros_delete');

// Familias
$router->get('/panel/config/familias', 'ConfigController@familias');
$router->post('/panel/config/familias/create', 'ConfigController@familias_create');
$router->post('/panel/config/familias/update', 'ConfigController@familias_update');
$router->post('/panel/config/familias/delete', 'ConfigController@familias_delete');

// Artículos
$router->get('/panel/config/articulos', 'ConfigController@articulos');
$router->post('/panel/config/articulos/create', 'ConfigController@articulos_create');
$router->post('/panel/config/articulos/update', 'ConfigController@articulos_update');
$router->post('/panel/config/articulos/delete', 'ConfigController@articulos_delete');

// Encuestas
$router->get('/panel/config/encuestas', 'ConfigController@encuestas');
$router->post('/panel/config/encuestas/create', 'ConfigController@encuestas_create');
$router->post('/panel/config/encuestas/update', 'ConfigController@encuestas_update');
$router->post('/panel/config/encuestas/delete', 'ConfigController@encuestas_delete');
$router->post('/panel/config/encuestas/notificar', 'ConfigController@encuestas_notificar');

// Notificaciones (Plantillas de Email)
$router->get('/panel/config/notificaciones', 'ConfigController@notificaciones');
$router->post('/panel/config/notificaciones/update', 'ConfigController@notificaciones_update');

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
