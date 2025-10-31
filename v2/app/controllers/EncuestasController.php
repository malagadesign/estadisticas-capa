<?php
/**
 * EncuestasController - Módulo de Encuestas
 */

// Cargar modelos
require_once __DIR__ . '/../models/Encuesta.php';
require_once __DIR__ . '/../models/Rubro.php';
require_once __DIR__ . '/../models/Familia.php';
require_once __DIR__ . '/../models/Articulo.php';
require_once __DIR__ . '/../models/Mercado.php';

class EncuestasController {
    
    /**
     * Ver última encuesta
     */
    public function ultima() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::redirect('/', 'Debe iniciar sesión', 'warning');
        }
        
        $userId = Session::userId();
        $userType = Session::userType();
        $isAdmin = Session::isAdmin();
        
        // Obtener última encuesta
        $encuestaModel = new Encuesta();
        $encuesta = $encuestaModel->getUltima();
        
        if (!$encuesta) {
            View::render('encuestas/no-activa', [
                'title' => 'Última Encuesta - CAPA',
                'message' => 'No hay encuestas activas en este momento'
            ]);
            return;
        }
        
        // Verificar si es editable
        $esEditable = $encuestaModel->isEditable($encuesta['did']);
        
        // Cargar rubros, familias, mercados (NO artículos - se cargan por demanda)
        $rubroModel = new Rubro();
        $familiaModel = new Familia();
        $mercadoModel = new Mercado();
        
        $rubros = $rubroModel->getAll();
        $familias = $familiaModel->getAll();
        // $articulos = NO se carga aquí - carga diferida via AJAX
        $mercados = $mercadoModel->getAll();
        
        // Debug: Log de carga
        error_log("DEBUG carga inicial - Rubros: " . count($rubros) . ", Familias: " . count($familias) . ", Mercados: " . count($mercados));
        
        // Organizar datos por jerarquía
        $rubrosArray = [];
        foreach ($rubros as $rubro) {
            $rubrosArray[$rubro['did']] = $rubro['nombre'];
        }
        
        $familiasArray = [];
        $familiasPorRubro = [];
        foreach ($familias as $familia) {
            $familiasArray[$familia['did']] = [
                'nombre' => $familia['nombre'],
                'rubroDid' => $familia['didRubro']
            ];
            $familiasPorRubro[$familia['didRubro']][] = $familia;
        }
        
        // NO precargar artículos - se cargarán por demanda via AJAX
        $articulosArray = [];
        $articulosPorFamilia = []; // Vacío - se carga por demanda
        
        $mercadosArray = [];
        foreach ($mercados as $mercado) {
            $mercadosArray[$mercado['did']] = $mercado['nombre'];
        }
        
        // Datos específicos según rol
        $articulosDeshabilitados = [];
        $montosYaCargados = [];
        $articulosNoIncluidos = [];
        
        if ($isAdmin) {
            // Admin: ver artículos no incluidos por socios
            $startTime = microtime(true);
            $articulosNoIncluidos = $encuestaModel->getArticulosNoIncluidosPorSocios();
            $elapsedTime = round((microtime(true) - $startTime) * 1000, 2);
            error_log("DEBUG admin - getArticulosNoIncluidosPorSocios(): Tardó {$elapsedTime}ms, cantidad: " . count($articulosNoIncluidos));
        } else {
            // Socio: cargar sus datos
            $articulosDeshabilitados = $encuestaModel->getArticulosDeshabilitadosPorSocio($userId);
            $montosYaCargados = $encuestaModel->getMontosYaCargados($encuesta['did'], $userId);
        }
        
        View::render('encuestas/ultima', [
            'title' => 'Última Encuesta - CAPA',
            'encuesta' => $encuesta,
            'esEditable' => $esEditable,
            'rubros' => $rubrosArray,
            'familias' => $familiasArray,
            'familiasPorRubro' => $familiasPorRubro,
            'articulos' => $articulosArray,
            'articulosPorFamilia' => $articulosPorFamilia,
            'mercados' => $mercadosArray,
            'articulosDeshabilitados' => $articulosDeshabilitados,
            'montosYaCargados' => $montosYaCargados,
            'articulosNoIncluidos' => $articulosNoIncluidos,
            'isAdmin' => $isAdmin
        ]);
    }
    
    /**
     * Guardar precio (AJAX)
     */
    public function guardarPrecio() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        // Solo socios pueden cargar precios
        if (Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'Los administradores no pueden cargar precios'], 403);
        }
        
        // Verificar CSRF
        $csrfToken = Request::post('csrf_token');
        if (!csrf_verify($csrfToken)) {
            View::json(['success' => false, 'message' => 'Token inválido'], 403);
        }
        
        // Obtener datos
        $encuestaDid = Request::post('encuestaDid');
        $articuloDid = Request::post('articuloDid');
        $mercadoDid = Request::post('mercadoDid');
        $tipo = Request::post('tipo', 'venta');
        $monto = Request::post('monto', 0);
        
        // Validar
        if (!$encuestaDid || !$articuloDid || !$mercadoDid) {
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        // Guardar
        try {
            $encuestaModel = new Encuesta();
            $encuestaModel->saveMonto(
                $encuestaDid,
                Session::userId(),
                $articuloDid,
                $mercadoDid,
                $tipo,
                $monto
            );
            
            View::json(['success' => true, 'message' => 'Precio guardado correctamente']);
        } catch (Exception $e) {
            error_log("Error guardando precio: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al guardar'], 500);
        }
    }
    
    /**
     * Guardar dato (cantidad o valor) - Tab 2 grilla (AJAX)
     */
    public function guardarDato() {
        error_log("DEBUG guardarDato - Inicio");
        
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            error_log("DEBUG guardarDato - No autenticado");
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        // Solo socios pueden cargar datos
        if (Session::isAdmin()) {
            error_log("DEBUG guardarDato - Admin no puede cargar");
            View::json(['success' => false, 'message' => 'Los administradores no pueden cargar datos'], 403);
        }
        
        // Verificar CSRF
        $csrfToken = Request::post('csrf_token');
        if (!csrf_verify($csrfToken)) {
            error_log("DEBUG guardarDato - Token inválido");
            View::json(['success' => false, 'message' => 'Token inválido'], 403);
        }
        
        // Obtener datos
        $encuestaDid = Request::post('encuestaDid');
        $articuloDid = Request::post('articuloDid');
        $canalDid = Request::post('canalDid'); // Mercado/Canal
        $tipoTexto = Request::post('tipo'); // 'cantidad' o 'valor'
        $monto = Request::post('monto', 0);
        
        error_log("DEBUG guardarDato - encuestaDid=$encuestaDid, articuloDid=$articuloDid, canalDid=$canalDid, tipoTexto=$tipoTexto, monto=$monto");
        
        // Validar
        if (!$encuestaDid || !$articuloDid || !$canalDid || !$tipoTexto) {
            error_log("DEBUG guardarDato - Datos incompletos");
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        // Convertir tipo texto a número: cantidad=1, valor=2
        $tipoNum = ($tipoTexto === 'cantidad' || $tipoTexto === '1') ? 1 : 2;
        error_log("DEBUG guardarDato - tipo convertido: $tipoNum");
        
        // Guardar
        try {
            $encuestaModel = new Encuesta();
            $encuestaModel->saveMonto(
                $encuestaDid,
                Session::userId(),
                $articuloDid,
                $canalDid,
                $tipoNum,
                $monto
            );
            
            error_log("DEBUG guardarDato - OK");
            View::json(['success' => true, 'message' => 'Dato guardado correctamente']);
        } catch (Exception $e) {
            error_log("Error guardando dato: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Toggle artículo (AJAX)
     */
    public function toggleArticulo() {
        error_log("DEBUG toggleArticulo - Inicio");
        
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            error_log("DEBUG toggleArticulo - No autenticado");
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        // Solo socios pueden configurar artículos
        if (Session::isAdmin()) {
            error_log("DEBUG toggleArticulo - Admin no puede configurar");
            View::json(['success' => false, 'message' => 'Los administradores no pueden configurar artículos'], 403);
        }
        
        // Verificar CSRF
        $csrfToken = Request::post('csrf_token');
        error_log("DEBUG toggleArticulo - CSRF token recibido: " . substr($csrfToken, 0, 20) . "...");
        if (!csrf_verify($csrfToken)) {
            error_log("DEBUG toggleArticulo - Token inválido");
            View::json(['success' => false, 'message' => 'Token inválido'], 403);
        }
        
        // Obtener datos
        $articuloDid = Request::post('articuloDid');
        $userId = Session::userId();
        error_log("DEBUG toggleArticulo - ArticuloDid: $articuloDid, UserId: $userId");
        
        if (!$articuloDid) {
            error_log("DEBUG toggleArticulo - Artículo no especificado");
            View::json(['success' => false, 'message' => 'Artículo no especificado'], 400);
        }
        
        // Toggle
        try {
            $encuestaModel = new Encuesta();
            error_log("DEBUG toggleArticulo - Llamando a toggleArticuloSocio");
            $nuevoEstado = $encuestaModel->toggleArticuloSocio($userId, $articuloDid);
            error_log("DEBUG toggleArticulo - Nuevo estado: $nuevoEstado");
            
            View::json([
                'success' => true,
                'habilitado' => $nuevoEstado,
                'message' => $nuevoEstado ? 'Artículo habilitado' : 'Artículo deshabilitado'
            ]);
        } catch (Exception $e) {
            error_log("ERROR toggle artículo: " . $e->getMessage());
            error_log("ERROR stack trace: " . $e->getTraceAsString());
            View::json(['success' => false, 'message' => 'Error al actualizar'], 500);
        }
    }
    
    /**
     * Encuestas anteriores
     */
    public function anteriores() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::redirect('/', 'Debe iniciar sesión', 'warning');
        }
        
        $encuestaModel = new Encuesta();
        $encuestas = $encuestaModel->getAll();
        
        View::render('encuestas/anteriores', [
            'title' => 'Encuestas Anteriores - CAPA',
            'encuestas' => $encuestas
        ]);
    }
    
    /**
     * Upload Excel (TODO: implementar)
     */
    public function uploadExcel() {
        View::json(['success' => false, 'message' => 'Funcionalidad en desarrollo'], 501);
    }
    
    /**
     * Cargar artículos por familia (Carga diferida - AJAX)
     */
    public function getArticulosPorFamilia() {
        // Verificar autenticación
        if (!Session::isLoggedIn()) {
            View::json(['success' => false, 'message' => 'No autenticado'], 401);
        }
        
        $familiaDid = Request::get('familiaDid');
        
        if (!$familiaDid) {
            View::json(['success' => false, 'message' => 'Familia no especificada'], 400);
        }
        
        try {
            $articuloModel = new Articulo();
            $articulos = $articuloModel->getByFamilia($familiaDid);
            
            // Si es socio, obtener también los artículos deshabilitados
            $articulosDeshabilitados = [];
            if (!Session::isAdmin()) {
                $encuestaModel = new Encuesta();
                $articulosDeshabilitados = $encuestaModel->getArticulosDeshabilitadosPorSocio(Session::userId());
            }
            
            // Formatear respuesta
            $response = [];
            foreach ($articulos as $articulo) {
                $deshabilitado = isset($articulosDeshabilitados[$articulo['did']]);
                $response[] = [
                    'did' => $articulo['did'],
                    'nombre' => $articulo['nombre'],
                    'didFamilia' => $articulo['didFamilia'],
                    'deshabilitado' => $deshabilitado
                ];
            }
            
            View::json(['success' => true, 'articulos' => $response]);
        } catch (Exception $e) {
            error_log("Error cargando artículos por familia: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al cargar artículos'], 500);
        }
    }
}

