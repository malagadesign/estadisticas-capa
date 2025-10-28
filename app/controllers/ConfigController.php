<?php
/**
 * ConfigController - Gestión de Configuración (solo admin)
 * Mercados, Rubros, Familias, Artículos
 */

class ConfigController {
    
    /**
     * Vista principal - redirige a mercados
     */
    public function index() {
        View::redirect('/config/mercados');
    }
    
    // ============================================
    // MERCADOS
    // ============================================
    
    /**
     * Listar mercados
     */
    public function mercados() {
        if (!Session::isAdmin()) {
            View::forbidden();
        }
        
        $db = Database::getInstance();
        $mercados = $db->fetchAll(
            "SELECT * FROM mercados 
             WHERE superado = 0 AND elim = 0 
             ORDER BY nombre ASC"
        );
        
        View::render('config/mercados', [
            'title' => 'Mercados - CAPA',
            'mercados' => $mercados
        ]);
    }
    
    /**
     * Crear mercado
     */
    public function mercados_create() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $nombre = Request::clean(Request::post('nombre'));
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($nombre)) {
            View::json(['success' => false, 'message' => 'El nombre es requerido'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->insert(
                "INSERT INTO mercados (nombre, habilitado, superado, elim) 
                 VALUES (?, ?, 0, 0)",
                ['si', $nombre, $habilitado]
            );
            
            View::json(['success' => true, 'message' => 'Mercado creado correctamente']);
        } catch (Exception $e) {
            error_log("Error creando mercado: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al crear mercado'], 500);
        }
    }
    
    /**
     * Actualizar mercado
     */
    public function mercados_update() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        $nombre = Request::clean(Request::post('nombre'));
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($did) || empty($nombre)) {
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE mercados SET nombre = ?, habilitado = ? WHERE did = ?",
                ['sii', $nombre, $habilitado, $did]
            );
            
            View::json(['success' => true, 'message' => 'Mercado actualizado correctamente']);
        } catch (Exception $e) {
            error_log("Error actualizando mercado: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al actualizar'], 500);
        }
    }
    
    /**
     * Eliminar mercado (soft delete)
     */
    public function mercados_delete() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        
        if (empty($did)) {
            View::json(['success' => false, 'message' => 'ID requerido'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE mercados SET elim = 1 WHERE did = ?",
                ['i', $did]
            );
            
            View::json(['success' => true, 'message' => 'Mercado eliminado correctamente']);
        } catch (Exception $e) {
            error_log("Error eliminando mercado: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al eliminar'], 500);
        }
    }
    
    // ============================================
    // RUBROS
    // ============================================
    
    /**
     * Listar rubros
     */
    public function rubros() {
        if (!Session::isAdmin()) {
            View::forbidden();
        }
        
        $db = Database::getInstance();
        $rubros = $db->fetchAll(
            "SELECT * FROM rubros 
             WHERE superado = 0 AND elim = 0 
             ORDER BY nombre ASC"
        );
        
        View::render('config/rubros', [
            'title' => 'Rubros - CAPA',
            'rubros' => $rubros
        ]);
    }
    
    /**
     * Crear rubro
     */
    public function rubros_create() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $nombre = Request::clean(Request::post('nombre'));
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($nombre)) {
            View::json(['success' => false, 'message' => 'El nombre es requerido'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->insert(
                "INSERT INTO rubros (nombre, habilitado, superado, elim) 
                 VALUES (?, ?, 0, 0)",
                ['si', $nombre, $habilitado]
            );
            
            View::json(['success' => true, 'message' => 'Rubro creado correctamente']);
        } catch (Exception $e) {
            error_log("Error creando rubro: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al crear rubro'], 500);
        }
    }
    
    /**
     * Actualizar rubro
     */
    public function rubros_update() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        $nombre = Request::clean(Request::post('nombre'));
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($did) || empty($nombre)) {
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE rubros SET nombre = ?, habilitado = ? WHERE did = ?",
                ['sii', $nombre, $habilitado, $did]
            );
            
            View::json(['success' => true, 'message' => 'Rubro actualizado correctamente']);
        } catch (Exception $e) {
            error_log("Error actualizando rubro: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al actualizar'], 500);
        }
    }
    
    /**
     * Eliminar rubro (soft delete)
     */
    public function rubros_delete() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        
        if (empty($did)) {
            View::json(['success' => false, 'message' => 'ID requerido'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE rubros SET elim = 1 WHERE did = ?",
                ['i', $did]
            );
            
            View::json(['success' => true, 'message' => 'Rubro eliminado correctamente']);
        } catch (Exception $e) {
            error_log("Error eliminando rubro: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al eliminar'], 500);
        }
    }
    
    // ============================================
    // FAMILIAS
    // ============================================
    
    /**
     * Listar familias
     */
    public function familias() {
        if (!Session::isAdmin()) {
            View::forbidden();
        }
        
        $db = Database::getInstance();
        
        // Obtener familias con nombre de rubro
        $familias = $db->fetchAll(
            "SELECT f.*, r.nombre as rubro_nombre, r.habilitado as rubro_habilitado
             FROM familias f
             LEFT JOIN rubros r ON f.didRubro = r.did
             WHERE f.superado = 0 AND f.elim = 0
             ORDER BY r.nombre, f.nombre ASC"
        );
        
        // Obtener rubros activos para el dropdown
        $rubros = $db->fetchAll(
            "SELECT * FROM rubros 
             WHERE superado = 0 AND elim = 0 AND habilitado = 1
             ORDER BY nombre ASC"
        );
        
        View::render('config/familias', [
            'title' => 'Familias - CAPA',
            'familias' => $familias,
            'rubros' => $rubros
        ]);
    }
    
    /**
     * Crear familia
     */
    public function familias_create() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $nombre = Request::clean(Request::post('nombre'));
        $didRubro = Request::post('didRubro');
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($nombre) || empty($didRubro)) {
            View::json(['success' => false, 'message' => 'Nombre y rubro son requeridos'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->insert(
                "INSERT INTO familias (nombre, didRubro, habilitado, superado, elim) 
                 VALUES (?, ?, ?, 0, 0)",
                ['sii', $nombre, $didRubro, $habilitado]
            );
            
            View::json(['success' => true, 'message' => 'Familia creada correctamente']);
        } catch (Exception $e) {
            error_log("Error creando familia: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al crear familia'], 500);
        }
    }
    
    /**
     * Actualizar familia
     */
    public function familias_update() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        $nombre = Request::clean(Request::post('nombre'));
        $didRubro = Request::post('didRubro');
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($did) || empty($nombre) || empty($didRubro)) {
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE familias SET nombre = ?, didRubro = ?, habilitado = ? WHERE did = ?",
                ['siii', $nombre, $didRubro, $habilitado, $did]
            );
            
            View::json(['success' => true, 'message' => 'Familia actualizada correctamente']);
        } catch (Exception $e) {
            error_log("Error actualizando familia: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al actualizar'], 500);
        }
    }
    
    /**
     * Eliminar familia (soft delete)
     */
    public function familias_delete() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        
        if (empty($did)) {
            View::json(['success' => false, 'message' => 'ID requerido'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE familias SET elim = 1 WHERE did = ?",
                ['i', $did]
            );
            
            View::json(['success' => true, 'message' => 'Familia eliminada correctamente']);
        } catch (Exception $e) {
            error_log("Error eliminando familia: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al eliminar'], 500);
        }
    }
    
    // ============================================
    // ARTÍCULOS
    // ============================================
    
    /**
     * Listar artículos
     */
    public function articulos() {
        if (!Session::isAdmin()) {
            View::forbidden();
        }
        
        $db = Database::getInstance();
        
        // Obtener artículos con nombre de familia
        $articulos = $db->fetchAll(
            "SELECT a.*, f.nombre as familia_nombre, f.habilitado as familia_habilitado
             FROM articulos a
             LEFT JOIN familias f ON a.didFamilia = f.did
             WHERE a.superado = 0 AND a.elim = 0
             ORDER BY f.nombre, a.nombre ASC"
        );
        
        // Obtener familias activas para el dropdown
        $familias = $db->fetchAll(
            "SELECT * FROM familias 
             WHERE superado = 0 AND elim = 0 AND habilitado = 1
             ORDER BY nombre ASC"
        );
        
        View::render('config/articulos', [
            'title' => 'Artículos - CAPA',
            'articulos' => $articulos,
            'familias' => $familias
        ]);
    }
    
    /**
     * Crear artículo
     */
    public function articulos_create() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $nombre = Request::clean(Request::post('nombre'));
        $didFamilia = Request::post('didFamilia');
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($nombre) || empty($didFamilia)) {
            View::json(['success' => false, 'message' => 'Nombre y familia son requeridos'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->insert(
                "INSERT INTO articulos (nombre, didFamilia, habilitado, superado, elim) 
                 VALUES (?, ?, ?, 0, 0)",
                ['sii', $nombre, $didFamilia, $habilitado]
            );
            
            View::json(['success' => true, 'message' => 'Artículo creado correctamente']);
        } catch (Exception $e) {
            error_log("Error creando artículo: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al crear artículo'], 500);
        }
    }
    
    /**
     * Actualizar artículo
     */
    public function articulos_update() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        $nombre = Request::clean(Request::post('nombre'));
        $didFamilia = Request::post('didFamilia');
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($did) || empty($nombre) || empty($didFamilia)) {
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE articulos SET nombre = ?, didFamilia = ?, habilitado = ? WHERE did = ?",
                ['siii', $nombre, $didFamilia, $habilitado, $did]
            );
            
            View::json(['success' => true, 'message' => 'Artículo actualizado correctamente']);
        } catch (Exception $e) {
            error_log("Error actualizando artículo: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al actualizar'], 500);
        }
    }
    
    /**
     * Eliminar artículo (soft delete)
     */
    public function articulos_delete() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        
        if (empty($did)) {
            View::json(['success' => false, 'message' => 'ID requerido'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE articulos SET elim = 1 WHERE did = ?",
                ['i', $did]
            );
            
            View::json(['success' => true, 'message' => 'Artículo eliminado correctamente']);
        } catch (Exception $e) {
            error_log("Error eliminando artículo: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al eliminar'], 500);
        }
    }
    
    // ============================================
    // ENCUESTAS
    // ============================================
    
    /**
     * Listar encuestas
     */
    public function encuestas() {
        if (!Session::isAdmin()) {
            View::forbidden();
        }
        
        $db = Database::getInstance();
        $encuestas = $db->fetchAll(
            "SELECT * FROM encuestas 
             WHERE superado = 0 AND elim = 0 
             ORDER BY desdeText DESC"
        );
        
        View::render('config/encuestas', [
            'title' => 'Encuestas - CAPA',
            'encuestas' => $encuestas
        ]);
    }
    
    /**
     * Crear encuesta
     */
    public function encuestas_create() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $nombre = Request::clean(Request::post('nombre'));
        $desde = Request::post('desde'); // Formato YYYY-MM-DD
        $hasta = Request::post('hasta'); // Formato YYYY-MM-DD
        $habilitado = Request::post('habilitado', 1);
        
        // Log de depuración
        error_log("Datos recibidos - nombre: '$nombre', desde: '$desde', hasta: '$hasta', habilitado: '$habilitado'");
        
        if (empty($nombre) || empty($desde) || empty($hasta)) {
            error_log("Error: Campos vacíos - nombre: " . (empty($nombre) ? 'VACÍO' : 'OK') . 
                     ", desde: " . (empty($desde) ? 'VACÍO' : 'OK') . 
                     ", hasta: " . (empty($hasta) ? 'VACÍO' : 'OK'));
            View::json(['success' => false, 'message' => 'Todos los campos son requeridos'], 400);
        }
        
        try {
            $db = Database::getInstance();
            
            // Convertir fechas de YYYY-MM-DD a dd/mm/yyyy para desdeText y hastaText
            $desdeFormato = DateTime::createFromFormat('Y-m-d', $desde);
            $hastaFormato = DateTime::createFromFormat('Y-m-d', $hasta);
            
            if (!$desdeFormato || !$hastaFormato) {
                View::json(['success' => false, 'message' => 'Formato de fecha inválido'], 400);
            }
            
            $desdeText = $desdeFormato->format('d/m/Y');
            $hastaText = $hastaFormato->format('d/m/Y');
            
            // Validar que la fecha desde no sea mayor que hasta
            if ($desdeFormato > $hastaFormato) {
                View::json(['success' => false, 'message' => 'La fecha de inicio no puede ser mayor que la fecha de fin'], 400);
            }
            
            // Calcular próximo DID único (evita colisión con índice unique_did)
            $nextDidRow = $db->fetchOne("SELECT COALESCE(MAX(did), 0) + 1 AS nextDid FROM encuestas");
            $nextDid = (int)($nextDidRow['nextDid'] ?? 1);

            // Insertar seteando DID explícitamente
            $db->insert(
                "INSERT INTO encuestas (did, nombre, desdeText, hastaText, desde, hasta, habilitado, superado, elim, quien) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?)",
                ['isssssii', $nextDid, $nombre, $desdeText, $hastaText, $desde, $hasta, $habilitado, Session::get('user_id', 0)]
            );
            
            View::json(['success' => true, 'message' => 'Encuesta creada correctamente']);
        } catch (Exception $e) {
            error_log("Error creando encuesta: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al crear encuesta: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Actualizar encuesta
     */
    public function encuestas_update() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        $nombre = Request::clean(Request::post('nombre'));
        $desde = Request::post('desde'); // Formato YYYY-MM-DD
        $hasta = Request::post('hasta'); // Formato YYYY-MM-DD
        $habilitado = Request::post('habilitado', 1);
        
        if (empty($did) || empty($nombre) || empty($desde) || empty($hasta)) {
            View::json(['success' => false, 'message' => 'Datos incompletos'], 400);
        }
        
        try {
            $db = Database::getInstance();
            
            // Convertir fechas de YYYY-MM-DD a dd/mm/yyyy para desdeText y hastaText
            $desdeFormato = DateTime::createFromFormat('Y-m-d', $desde);
            $hastaFormato = DateTime::createFromFormat('Y-m-d', $hasta);
            
            if (!$desdeFormato || !$hastaFormato) {
                View::json(['success' => false, 'message' => 'Formato de fecha inválido'], 400);
            }
            
            $desdeText = $desdeFormato->format('d/m/Y');
            $hastaText = $hastaFormato->format('d/m/Y');
            
            // Validar que la fecha desde no sea mayor que hasta
            if ($desdeFormato > $hastaFormato) {
                View::json(['success' => false, 'message' => 'La fecha de inicio no puede ser mayor que la fecha de fin'], 400);
            }
            
            $db->query(
                "UPDATE encuestas SET nombre = ?, desdeText = ?, hastaText = ?, desde = ?, hasta = ?, habilitado = ? WHERE id = ?",
                ['sssssii', $nombre, $desdeText, $hastaText, $desde, $hasta, $habilitado, $did]
            );
            
            View::json(['success' => true, 'message' => 'Encuesta actualizada correctamente']);
        } catch (Exception $e) {
            error_log("Error actualizando encuesta: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Eliminar encuesta (soft delete)
     */
    public function encuestas_delete() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        
        if (empty($did)) {
            View::json(['success' => false, 'message' => 'ID requerido'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE encuestas SET elim = 1 WHERE id = ?",
                ['i', $did]
            );
            
            View::json(['success' => true, 'message' => 'Encuesta eliminada correctamente']);
        } catch (Exception $e) {
            error_log("Error eliminando encuesta: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al eliminar'], 500);
        }
    }
}
