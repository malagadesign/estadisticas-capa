<?php
/**
 * Modelo: Familia
 */
class Familia {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todas las familias activas
     */
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM familias 
             WHERE superado = 0 
             AND elim = 0 
             AND habilitado = 1 
             ORDER BY nombre ASC"
        );
    }
    
    /**
     * Obtener familias por rubro
     */
    public function getByRubro($rubroDid) {
        return $this->db->fetchAll(
            "SELECT * FROM familias 
             WHERE didRubro = ? 
             AND superado = 0 
             AND elim = 0 
             AND habilitado = 1 
             ORDER BY nombre ASC",
            ['i', $rubroDid]
        );
    }
}

