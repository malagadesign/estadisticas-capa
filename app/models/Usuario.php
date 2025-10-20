<?php
/**
 * Modelo: Usuario
 */
class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM usuarios 
             WHERE superado = 0 
             AND elim = 0 
             ORDER BY usuario ASC"
        );
    }
    
    /**
     * Obtener usuarios por tipo
     */
    public function getByTipo($tipo) {
        return $this->db->fetchAll(
            "SELECT * FROM usuarios 
             WHERE tipo = ? 
             AND superado = 0 
             AND elim = 0 
             ORDER BY usuario ASC",
            ['s', $tipo]
        );
    }
    
    /**
     * Obtener usuario por ID
     */
    public function getById($did) {
        return $this->db->fetchOne(
            "SELECT * FROM usuarios WHERE did = ? LIMIT 1",
            ['i', $did]
        );
    }
    
    /**
     * Crear usuario
     */
    public function create($usuario, $password, $mail, $tipo) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        return $this->db->insert(
            "INSERT INTO usuarios 
             (usuario, psw, mail, tipo, habilitado, superado, elim) 
             VALUES (?, ?, ?, ?, 1, 0, 0)",
            ['ssss', $usuario, $passwordHash, $mail, $tipo]
        );
    }
    
    /**
     * Actualizar usuario
     */
    public function update($did, $mail, $habilitado) {
        return $this->db->query(
            "UPDATE usuarios 
             SET mail = ?, habilitado = ? 
             WHERE did = ?",
            ['sii', $mail, $habilitado, $did]
        );
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword($did, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        
        return $this->db->query(
            "UPDATE usuarios 
             SET psw = ?, hash = '' 
             WHERE did = ?",
            ['si', $passwordHash, $did]
        );
    }
    
    /**
     * Toggle habilitado
     */
    public function toggleHabilitado($did) {
        $user = $this->getById($did);
        if (!$user) return false;
        
        $nuevoEstado = $user['habilitado'] == 1 ? 0 : 1;
        
        $this->db->query(
            "UPDATE usuarios SET habilitado = ? WHERE did = ?",
            ['ii', $nuevoEstado, $did]
        );
        
        return $nuevoEstado;
    }
}

