<?php
/**
 * Modelo: Encuesta
 */
class Encuesta {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener última encuesta activa
     */
    public function getUltima() {
        return $this->db->fetchOne(
            "SELECT * FROM encuestas 
             WHERE superado = 0 
             AND elim = 0 
             ORDER BY hasta DESC 
             LIMIT 1"
        );
    }
    
    /**
     * Obtener todas las encuestas (historial)
     */
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM encuestas 
             WHERE superado = 0 
             AND elim = 0 
             ORDER BY hasta DESC"
        );
    }
    
    /**
     * Verificar si una encuesta es editable
     */
    public function isEditable($encuestaDid) {
        $encuesta = $this->db->fetchOne(
            "SELECT hasta FROM encuestas WHERE did = ? LIMIT 1",
            ['i', $encuestaDid]
        );
        
        if (!$encuesta) return false;
        
        $fecha_actual = strtotime(date('Y-m-d'));
        $fecha_hasta = strtotime($encuesta['hasta']);
        
        return $fecha_hasta >= $fecha_actual;
    }
    
    /**
     * Obtener artículos deshabilitados por un socio
     */
    public function getArticulosDeshabilitadosPorSocio($usuarioDid) {
        $result = $this->db->fetchAll(
            "SELECT didArticulo FROM articulosUsuarios 
             WHERE didUsuario = ? 
             AND habilitado = 0 
             AND superado = 0 
             AND elim = 0",
            ['i', $usuarioDid]
        );
        
        $articulos = [];
        foreach ($result as $row) {
            $articulos[$row['didArticulo']] = true;
        }
        
        return $articulos;
    }
    
    /**
     * Obtener montos ya cargados para una encuesta y usuario
     */
    public function getMontosYaCargados($encuestaDid, $usuarioDid) {
        $result = $this->db->fetchAll(
            "SELECT didArticulo, didMercado, tipo, monto 
             FROM articulosMontos 
             WHERE didEncuesta = ? 
             AND didUsuario = ? 
             AND superado = 0 
             AND elim = 0",
            ['ii', $encuestaDid, $usuarioDid]
        );
        
        $montos = [];
        foreach ($result as $row) {
            $key = "{$row['didArticulo']}-{$row['didMercado']}-{$row['tipo']}";
            $montos[$key] = $row['monto'];
        }
        
        return $montos;
    }
    
    /**
     * Guardar/actualizar monto
     */
    public function saveMonto($encuestaDid, $usuarioDid, $articuloDid, $mercadoDid, $tipo, $monto) {
        error_log("DEBUG saveMonto - inicio: encuestaDid=$encuestaDid, usuarioDid=$usuarioDid, articuloDid=$articuloDid, mercadoDid=$mercadoDid, tipo=$tipo, monto=$monto");
        
        // Verificar si ya existe (buscar por campos únicos)
        $exists = $this->db->fetchOne(
            "SELECT id FROM articulosMontos 
             WHERE didEncuesta = ? 
             AND didUsuario = ? 
             AND didArticulo = ? 
             AND didMercado = ? 
             AND tipo = ? 
             AND superado = 0 
             AND elim = 0 
             LIMIT 1",
            ['iiiii', $encuestaDid, $usuarioDid, $articuloDid, $mercadoDid, $tipo]
        );
        
        if ($exists) {
            error_log("DEBUG saveMonto - UPDATE id={$exists['id']}");
            // Update
            $this->db->query(
                "UPDATE articulosMontos 
                 SET monto = ? 
                 WHERE id = ?",
                ['di', $monto, $exists['id']]
            );
        } else {
            error_log("DEBUG saveMonto - INSERT");
            // Insert
            $this->db->insert(
                "INSERT INTO articulosMontos 
                 (didEncuesta, didUsuario, didArticulo, didMercado, tipo, monto, superado, elim) 
                 VALUES (?, ?, ?, ?, ?, ?, 0, 0)",
                ['iiiisd', $encuestaDid, $usuarioDid, $articuloDid, $mercadoDid, $tipo, $monto]
            );
        }
        
        error_log("DEBUG saveMonto - OK");
        return true;
    }
    
    /**
     * Toggle artículo para un socio
     */
    public function toggleArticuloSocio($usuarioDid, $articuloDid) {
        error_log("DEBUG toggleArticuloSocio - Inicio: usuarioDid=$usuarioDid, articuloDid=$articuloDid");
        
        // Verificar si ya existe
        $exists = $this->db->fetchOne(
            "SELECT id, habilitado FROM articulosUsuarios 
             WHERE didUsuario = ? 
             AND didArticulo = ? 
             AND superado = 0 
             AND elim = 0 
             LIMIT 1",
            ['ii', $usuarioDid, $articuloDid]
        );
        
        error_log("DEBUG toggleArticuloSocio - Exists: " . ($exists ? "Sí (id={$exists['id']}, habilitado={$exists['habilitado']})" : "No"));
        
        if ($exists) {
            // Toggle habilitado
            $nuevoEstado = $exists['habilitado'] == 1 ? 0 : 1;
            error_log("DEBUG toggleArticuloSocio - Updating: id={$exists['id']}, nuevoEstado=$nuevoEstado");
            $this->db->query(
                "UPDATE articulosUsuarios 
                 SET habilitado = ? 
                 WHERE id = ?",
                ['ii', $nuevoEstado, $exists['id']]
            );
            error_log("DEBUG toggleArticuloSocio - Update exitoso");
            return $nuevoEstado;
        } else {
            // Primero, marcar como superado los anteriores (soft delete)
            error_log("DEBUG toggleArticuloSocio - Marcando anteriores como superado=1");
            $this->db->query(
                "UPDATE articulosUsuarios 
                 SET superado = 1 
                 WHERE didArticulo = ? 
                 AND didUsuario = ? 
                 AND superado = 0",
                ['ii', $articuloDid, $usuarioDid]
            );
            
            // Crear nuevo registro
            error_log("DEBUG toggleArticuloSocio - Insertando nuevo registro");
            $idInsertado = $this->db->insert(
                "INSERT INTO articulosUsuarios 
                 (didUsuario, didArticulo, habilitado, superado, elim, quien) 
                 VALUES (?, ?, 0, 0, 0, ?)",
                ['iii', $usuarioDid, $articuloDid, $usuarioDid]
            );
            
            error_log("DEBUG toggleArticuloSocio - Id insertado: $idInsertado");
            error_log("DEBUG toggleArticuloSocio - Retornando 0");
            return 0;
        }
    }
    
    /**
     * Obtener consolidado con cantidad de socios (para admin)
     */
    public function getConsolidadoAdmin($encuestaDid) {
        // Primero obtener lista de socios activos
        $sociosActivos = $this->db->fetchAll(
            "SELECT did FROM usuarios 
             WHERE TRIM(tipo) = 'socio' 
             AND superado = 0 
             AND elim = 0 
             AND habilitado = 1"
        );
        
        if (empty($sociosActivos)) {
            return [];
        }
        
        $sociosIds = array_column($sociosActivos, 'did');
        $sociosIn = implode(',', $sociosIds);
        
        // Obtener suma de montos Y count de socios por artículo/mercado/tipo
        $result = $this->db->fetchAll(
            "SELECT 
                am.didArticulo, 
                am.didMercado, 
                am.tipo,
                SUM(am.monto) AS monto,
                COUNT(DISTINCT am.didUsuario) AS socios
             FROM articulosMontos am
             WHERE am.didEncuesta = ? 
             AND am.superado = 0 
             AND am.elim = 0 
             AND am.monto > 0 
             AND am.didUsuario IN ({$sociosIn})
             GROUP BY am.didArticulo, am.didMercado, am.tipo",
            ['i', $encuestaDid]
        );
        
        $consolidado = [];
        foreach ($result as $row) {
            $key = "{$row['didArticulo']}-{$row['didMercado']}-{$row['tipo']}";
            $consolidado[$key] = [
                'monto' => $row['monto'],
                'socios' => $row['socios']
            ];
        }
        
        return $consolidado;
    }
    
    /**
     * Obtener artículos no incluidos por socios (para admin)
     */
    public function getArticulosNoIncluidosPorSocios() {
        return $this->db->fetchAll(
            "SELECT au.didArticulo, a.nombre as articuloNombre, 
                    f.nombre as familiaNombre, r.nombre as rubroNombre,
                    u.usuario as socioNombre
             FROM articulosUsuarios au
             INNER JOIN articulos a ON au.didArticulo = a.did
             INNER JOIN familias f ON a.didFamilia = f.did
             INNER JOIN rubros r ON f.didRubro = r.did
             INNER JOIN usuarios u ON au.didUsuario = u.did
             WHERE au.habilitado = 0 
             AND au.superado = 0 
             AND au.elim = 0
             ORDER BY r.nombre, f.nombre, a.nombre"
        );
    }
}

