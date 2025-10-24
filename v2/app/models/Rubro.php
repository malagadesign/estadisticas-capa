<?php
/**
 * Modelo: Rubro
 */
class Rubro {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los rubros activos
     */
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM rubros 
             WHERE superado = 0 
             AND elim = 0 
             AND habilitado = 1 
             ORDER BY nombre ASC"
        );
    }
    
    /**
     * Obtener rubro por ID
     */
    public function getById($did) {
        return $this->db->fetchOne(
            "SELECT * FROM rubros WHERE did = ? LIMIT 1",
            ['i', $did]
        );
    }
}

