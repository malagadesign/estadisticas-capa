<?php
/**
 * Database - Wrapper para MySQLi con prepared statements
 */
class Database {
    private static $instance = null;
    private $mysqli;
    
    /**
     * Constructor privado (Singleton)
     */
    private function __construct() {
        $this->mysqli = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASSWORD,
            DB_NAME,
            DB_PORT
        );
        
        if ($this->mysqli->connect_error) {
            error_log("Database connection failed: " . $this->mysqli->connect_error);
            throw new Exception("Error de conexión con la base de datos");
        }
        
        $this->mysqli->set_charset("utf8mb4");
    }
    
    /**
     * Obtener instancia única
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Query con prepared statements
     * 
     * @param string $sql Query SQL con ? para placeholders
     * @param array $params ['tipo', valor1, valor2, ...]
     * @return mysqli_result|bool
     */
    public function query($sql, $params = []) {
        $stmt = $this->mysqli->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->mysqli->error . " SQL: " . $sql);
            throw new Exception("Error en la consulta");
        }
        
        if (!empty($params)) {
            $types = array_shift($params);
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error . " SQL: " . $sql);
            // Propagar el detalle del error para facilitar el diagnóstico en entorno controlado
            throw new Exception($stmt->error ?: "Error ejecutando la consulta");
        }
        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Insert y devolver ID
     */
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->mysqli->insert_id;
    }
    
    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        
        if ($result === false || $result === true) {
            return [];
        }
        
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    /**
     * Fetch una sola row
     */
    public function fetchOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        
        if ($result === false || $result === true) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    /**
     * Count rows
     */
    public function count($sql, $params = []) {
        $result = $this->query($sql, $params);
        
        if ($result === false || $result === true) {
            return 0;
        }
        
        return $result->num_rows;
    }
    
    /**
     * Escape string (fallback si no se pueden usar prepared statements)
     */
    public function escape($string) {
        return $this->mysqli->real_escape_string($string);
    }
    
    /**
     * Obtener conexión mysqli (para casos especiales)
     */
    public function getConnection() {
        return $this->mysqli;
    }
}

