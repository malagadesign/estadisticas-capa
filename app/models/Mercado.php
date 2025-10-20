<?php
/**
 * Modelo: Mercado
 */
class Mercado {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los mercados activos
     */
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM mercados 
             WHERE superado = 0 
             AND elim = 0 
             AND habilitado = 1 
             ORDER BY nombre ASC"
        );
    }
    
    /**
     * Obtener mercado por ID
     */
    public function getById($did) {
        return $this->db->fetchOne(
            "SELECT * FROM mercados WHERE did = ? LIMIT 1",
            ['i', $did]
        );
    }
}

