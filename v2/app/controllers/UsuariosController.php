<?php
/**
 * UsuariosController - Gestión de Usuarios (solo admin)
 */

require_once __DIR__ . '/../models/Usuario.php';

class UsuariosController {
    
    /**
     * Vista principal - redirige a administrativos
     */
    public function index() {
        View::redirect('/usuarios/administrativos');
    }
    
    /**
     * Usuarios Administrativos
     */
    public function administrativos() {
        if (!Session::isAdmin()) {
            View::forbidden();
        }
        
        $db = Database::getInstance();
        // Usar TRIM para evitar problemas por espacios en 'tipo' y ordenar por did descendente (usuarios recientes primero)
        $usuarios = $db->fetchAll(
            "SELECT * FROM usuarios 
             WHERE TRIM(tipo) = 'adm' 
             AND superado = 0 
             AND elim = 0 
             ORDER BY did DESC, usuario ASC"
        );
        
        View::render('usuarios/administrativos', [
            'title' => 'Usuarios Administrativos - CAPA',
            'usuarios' => $usuarios
        ]);
    }
    
    /**
     * Usuarios Socios
     */
    public function socios() {
        if (!Session::isAdmin()) {
            View::forbidden();
        }
        
        $db = Database::getInstance();
        
        // Nota: En la BD el tipo es 'socio' (5 letras), no 'soc'
        // Filtrar por superado=0 y elim=0 para no mostrar duplicados
        $usuarios = $db->fetchAll(
            "SELECT * FROM usuarios 
             WHERE TRIM(tipo) = 'socio' 
             AND superado = 0 
             AND elim = 0 
             ORDER BY did DESC, usuario ASC"
        );
        
        $mercados = $db->fetchAll(
            "SELECT * FROM mercados 
             WHERE superado = 0 AND elim = 0 AND habilitado = 1 
             ORDER BY nombre ASC"
        );
        
        View::render('usuarios/socios', [
            'title' => 'Usuarios Socios - CAPA',
            'usuarios' => $usuarios,
            'mercados' => $mercados
        ]);
    }
    
    /**
     * Crear usuario
     */
    public function create() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        // Debug: Log de datos recibidos
        $allData = Request::postAll();
        error_log("UsuariosController::create - Datos recibidos: " . json_encode($allData));
        
        $tipo = Request::post('tipo'); // 'adm' o 'socio'
        $usuario = Request::post('usuario');
        $mail = Request::post('mail');
        $password = Request::post('password');
        $habilitado = Request::post('habilitado', 1);
        
        error_log("UsuariosController::create - Parseado - tipo: '$tipo', usuario: '$usuario', mail: '$mail', habilitado: '$habilitado'");
        
        // Limpiar después de obtener
        $usuario = Request::clean($usuario);
        $mail = Request::clean($mail);
        
        // Validaciones
        if (empty($usuario) || empty($mail)) {
            error_log("UsuariosController::create - Validación falló - usuario: " . (empty($usuario) ? 'VACIO' : 'OK') . ", mail: " . (empty($mail) ? 'VACIO' : 'OK'));
            View::json(['success' => false, 'message' => 'Usuario y email son requeridos'], 400);
        }
        
        // Contraseña opcional - si no se proporciona, generar una automática
        if (empty($password)) {
            $password = 'temp_' . uniqid(); // Contraseña temporal
        }
        
        // Si no vino tipo (por ejemplo, request parcial), asumir 'adm' en esta vista
        if (empty($tipo)) {
            $tipo = 'adm';
        }
        if (!in_array($tipo, ['adm', 'socio'])) {
            View::json(['success' => false, 'message' => 'Tipo de usuario inválido'], 400);
        }
        
        // Validar email
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            View::json(['success' => false, 'message' => 'Email inválido'], 400);
        }
        
        // Validar que el usuario no exista
        $db = Database::getInstance();
        $existe = $db->fetchOne(
            "SELECT COUNT(*) as total FROM usuarios WHERE usuario = ? AND elim = 0",
            ['s', $usuario]
        );
        
        if ($existe['total'] > 0) {
            View::json(['success' => false, 'message' => 'El nombre de usuario ya existe'], 400);
        }
        
        try {
            // Hash de contraseña y token auxiliar (columna hash)
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $auxHash = bin2hex(random_bytes(16));
            
            // Calcular próximo DID único (evita colisión con índice unique_did)
            $nextDidRow = $db->fetchOne("SELECT COALESCE(MAX(did), 0) + 1 AS nextDid FROM usuarios");
            $nextDid = (int)($nextDidRow['nextDid'] ?? 1);
            
            // Insertar seteando DID explícitamente (incluye columna hash)
            $db->insert(
                "INSERT INTO usuarios (did, usuario, mail, psw, `hash`, tipo, habilitado, superado, elim, quien) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?)",
                ['isssssii', $nextDid, $usuario, $mail, $passwordHash, $auxHash, $tipo, $habilitado, Session::get('user_id', 0)]
            );
            
            View::json(['success' => true, 'message' => 'Usuario creado correctamente']);
        } catch (Exception $e) {
            error_log("Error creando usuario: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al crear usuario'], 500);
        }
    }
    
    /**
     * Actualizar usuario
     */
    public function update() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        // Debug
        $allData = Request::postAll();
        error_log("UsuariosController::update - Datos recibidos: " . json_encode($allData));
        
        $did = Request::post('did');
        $usuario = Request::post('usuario');
        $mail = Request::post('mail');
        $password = Request::post('password');
        $habilitado = Request::post('habilitado', 1);
        
        error_log("UsuariosController::update - Parseado - did: '$did', usuario: '$usuario', mail: '$mail', habilitado: '$habilitado'");
        
        // Limpiar después de obtener
        $usuario = Request::clean($usuario);
        $mail = Request::clean($mail);
        
        // Validar campos básicos
        if (empty($usuario) || empty($mail)) {
            error_log("UsuariosController::update - Validación falló - usuario: " . (empty($usuario) ? 'VACIO' : 'OK') . ", mail: " . (empty($mail) ? 'VACIO' : 'OK'));
            View::json(['success' => false, 'message' => 'Usuario y email son requeridos'], 400);
        }
        
        // Validar did para edición - convertir a entero
        $did = is_numeric($did) ? (int)$did : null;
        if ($did === null || $did < 0) {
            error_log("UsuariosController::update - Validación falló - did inválido: '$did'");
            View::json(['success' => false, 'message' => 'ID requerido para editar'], 400);
        }
        
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            View::json(['success' => false, 'message' => 'Email inválido'], 400);
        }
        
        try {
            $db = Database::getInstance();
            
            // Si hay nueva contraseña, actualizarla
            if (!empty($password)) {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $db->query(
                    "UPDATE usuarios 
                     SET usuario = ?, mail = ?, psw = ?, habilitado = ? 
                     WHERE did = ?",
                    ['sssii', $usuario, $mail, $passwordHash, $habilitado, $did]
                );
            } else {
                // Sin cambio de contraseña
                $db->query(
                    "UPDATE usuarios 
                     SET usuario = ?, mail = ?, habilitado = ? 
                     WHERE did = ?",
                    ['ssii', $usuario, $mail, $habilitado, $did]
                );
            }
            
            View::json(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        } catch (Exception $e) {
            error_log("Error actualizando usuario: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al actualizar'], 500);
        }
    }
    
    /**
     * Habilitar/Deshabilitar usuario
     */
    public function toggle() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        // Debug
        $allData = Request::postAll();
        error_log("UsuariosController::toggle - Datos recibidos: " . json_encode($allData));
        
        $did = Request::post('did');
        $habilitado = Request::post('habilitado', 0);
        
        // Convertir a entero para validación
        $did = is_numeric($did) ? (int)$did : null;
        
        error_log("UsuariosController::toggle - Parseado - did: '$did' (tipo: " . gettype($did) . "), habilitado: '$habilitado'");
        
        // Validar: did debe ser un número mayor o igual a 0 (0 es válido para usuarios con did=0)
        if ($did === null || $did < 0) {
            error_log("UsuariosController::toggle - Validación falló - did inválido: '$did'");
            View::json(['success' => false, 'message' => 'ID requerido'], 400);
        }
        
        // Prevenir que el admin se deshabilite a sí mismo
        if ($did == Session::get('user_id')) {
            View::json(['success' => false, 'message' => 'No puede deshabilitarse a sí mismo'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE usuarios SET habilitado = ? WHERE did = ?",
                ['ii', $habilitado, $did]
            );
            
            $mensaje = $habilitado ? 'Usuario habilitado' : 'Usuario deshabilitado';
            View::json(['success' => true, 'message' => $mensaje]);
        } catch (Exception $e) {
            error_log("Error toggle usuario: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al actualizar estado'], 500);
        }
    }
    
    /**
     * Eliminar usuario (soft delete)
     */
    public function delete() {
        if (!Session::isAdmin()) {
            View::json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $did = Request::post('did');
        
        // Convertir a entero para validación
        $did = is_numeric($did) ? (int)$did : null;
        
        if ($did === null || $did < 0) {
            error_log("UsuariosController::delete - Validación falló - did inválido: '$did'");
            View::json(['success' => false, 'message' => 'ID requerido'], 400);
        }
        
        // Prevenir que el admin se elimine a sí mismo
        if ($did == Session::get('user_id')) {
            View::json(['success' => false, 'message' => 'No puede eliminarse a sí mismo'], 400);
        }
        
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE usuarios SET elim = 1 WHERE did = ?",
                ['i', $did]
            );
            
            View::json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } catch (Exception $e) {
            error_log("Error eliminando usuario: " . $e->getMessage());
            View::json(['success' => false, 'message' => 'Error al eliminar'], 500);
        }
    }
}


