<?php
/**
 * Modelo: Articulo
 */
class Articulo {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los artículos activos
     */
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM articulos 
             WHERE superado = 0 
             AND elim = 0 
             AND habilitado = 1 
             ORDER BY nombre ASC"
        );
    }
    
    /**
     * Obtener artículos por familia
     */
    public function getByFamilia($familiaDid) {
        return $this->db->fetchAll(
            "SELECT * FROM articulos 
             WHERE didFamilia = ? 
             AND superado = 0 
             AND elim = 0 
             AND habilitado = 1 
             ORDER BY nombre ASC",
            ['i', $familiaDid]
        );
    }
    
    /**
     * Obtener artículo por ID
     */
    public function getById($did) {
        return $this->db->fetchOne(
            "SELECT * FROM articulos WHERE did = ? LIMIT 1",
            ['i', $did]
        );
    }
}

