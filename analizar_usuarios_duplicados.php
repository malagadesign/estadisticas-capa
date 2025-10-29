<?php
/**
 * Script para analizar usuarios duplicados en la base de datos
 * Identifica problemas y sugiere soluciones
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/v2/config/app.php';
require_once __DIR__ . '/v2/core/Database.php';

echo "<h1>🔍 Análisis de Usuarios Duplicados - CAPA Encuestas</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4A148C; color: white; }
    .alert { padding: 15px; margin: 20px 0; border-radius: 5px; }
    .alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .alert-warning { background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; }
    .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    .alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
</style>";

try {
    $db = Database::getInstance();
    
    // ============================================
    // 1. COLUMNAS DE LA TABLA USUARIOS
    // ============================================
    echo "<h2>📊 Columnas de la tabla 'usuarios' y su propósito:</h2>";
    echo "<table>";
    echo "<tr><th>Columna</th><th>Propósito</th><th>Uso en v2</th></tr>";
    echo "<tr><td><code>id</code></td><td>Primary Key (AUTO_INCREMENT)</td><td>✅ Usado internamente</td></tr>";
    echo "<tr><td><code>did</code></td><td>ID único visible al usuario (índice único)</td><td>✅ Usado para CRUD</td></tr>";
    echo "<tr><td><code>usuario</code></td><td>Nombre del usuario</td><td>✅ Mostrado en tabla</td></tr>";
    echo "<tr><td><code>mail</code></td><td>Email del usuario</td><td>✅ Mostrado en tabla</td></tr>";
    echo "<tr><td><code>psw</code></td><td>Hash de contraseña</td><td>✅ Usado para login</td></tr>";
    echo "<tr><td><code>tipo</code></td><td>'adm' o 'socio'</td><td>✅ Filtro de búsqueda</td></tr>";
    echo "<tr><td><code>habilitado</code></td><td>1 = activo, 0 = deshabilitado</td><td>✅ Toggle en tabla</td></tr>";
    echo "<tr><td><code>superado</code></td><td>1 = desactualizado/antiguo, 0 = vigente</td><td>✅ Filtro WHERE</td></tr>";
    echo "<tr><td><code>elim</code></td><td>1 = eliminado (soft delete), 0 = activo</td><td>✅ Filtro WHERE</td></tr>";
    echo "<tr><td><code>quien</code></td><td>ID del usuario que creó/modificó el registro</td><td>⚠️ Audit log (no usado en v2)</td></tr>";
    echo "<tr><td><code>hash</code></td><td>Token aleatorio para sesiones/emails</td><td>⚠️ Preparado para uso futuro</td></tr>";
    echo "<tr><td><code>alertadoUsuario</code></td><td>Flag de notificación al usuario</td><td>❌ No usado</td></tr>";
    echo "<tr><td><code>alertadoCapa</code></td><td>Flag de notificación a CAPA</td><td>❌ No usado</td></tr>";
    echo "<tr><td><code>autofecha</code></td><td>Timestamp de creación</td><td>❌ No usado</td></tr>";
    echo "</table>";
    
    echo "<div class='alert alert-info'>";
    echo "<strong>💡 Resumen:</strong><br>";
    echo "<ul>";
    echo "<li><code>superado</code> y <code>elim</code>: filtros principales para no mostrar registros obsoletos/borrados</li>";
    echo "<li><code>quien</code>: auditoría (quién creó el registro), no crítico</li>";
    echo "<li><code>alertadoUsuario</code> y <code>alertadoCapa</code>: no se usan actualmente</li>";
    echo "</ul>";
    echo "</div>";
    
    // ============================================
    // 2. USUARIOS DUPLICADOS POR NOMBRE
    // ============================================
    echo "<h2>🔍 2. Usuarios duplicados por 'usuario' (nombre)</h2>";
    
    $duplicados_nombre = $db->fetchAll(
        "SELECT usuario, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids, GROUP_CONCAT(did ORDER BY id) as dids
         FROM usuarios 
         WHERE elim = 0
         GROUP BY usuario 
         HAVING COUNT(*) > 1
         ORDER BY count DESC, usuario ASC"
    );
    
    if (empty($duplicados_nombre)) {
        echo "<div class='alert alert-success'><strong>✅ No hay usuarios duplicados por nombre</strong></div>";
    } else {
        echo "<div class='alert alert-warning'>";
        echo "<strong>⚠️ Se encontraron " . count($duplicados_nombre) . " usuarios con nombres duplicados</strong>";
        echo "</div>";
        
        echo "<table>";
        echo "<tr><th>Usuario</th><th>Duplicados</th><th>IDs</th><th>DIDs</th><th>Acción sugerida</th></tr>";
        
        foreach ($duplicados_nombre as $dup) {
            $ids = explode(',', $dup['ids']);
            $dids = explode(',', $dup['dids']);
            
            // Obtener detalles de cada duplicado
            $detalles = $db->fetchAll(
                "SELECT id, did, usuario, mail, tipo, habilitado, superado, elim, autofecha
                 FROM usuarios 
                 WHERE usuario = ? AND elim = 0
                 ORDER BY autofecha DESC",
                ['s', $dup['usuario']]
            );
            
            echo "<tr>";
            echo "<td><strong>{$dup['usuario']}</strong></td>";
            echo "<td>{$dup['count']}</td>";
            echo "<td>" . implode(', ', $ids) . "</td>";
            echo "<td>" . implode(', ', $dids) . "</td>";
            echo "<td>";
            
            // Determinar cuál debería quedarse
            $quedarse = null;
            $marcar_superado = [];
            
            foreach ($detalles as $idx => $det) {
                if ($idx === 0) {
                    $quedarse = $det;
                    echo "<strong>✅ Mantener (más reciente)</strong><br>";
                    echo "- ID: {$det['id']}, DID: {$det['did']}<br>";
                    echo "- Email: {$det['mail']}<br>";
                    echo "- Fecha: {$det['autofecha']}<br>";
                } else {
                    $marcar_superado[] = $det['did'];
                    echo "<span style='color: red;'>❌ Marcar como superado</span><br>";
                    echo "- ID: {$det['id']}, DID: {$det['did']}<br>";
                }
            }
            
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // ============================================
    // 3. USUARIOS DUPLICADOS POR EMAIL
    // ============================================
    echo "<h2>📧 3. Usuarios duplicados por 'mail' (email)</h2>";
    
    $duplicados_email = $db->fetchAll(
        "SELECT mail, COUNT(*) as count, GROUP_CONCAT(usuario ORDER BY id) as usuarios, GROUP_CONCAT(id ORDER BY id) as ids
         FROM usuarios 
         WHERE elim = 0
         GROUP BY mail 
         HAVING COUNT(*) > 1
         ORDER BY count DESC, mail ASC"
    );
    
    if (empty($duplicados_email)) {
        echo "<div class='alert alert-success'><strong>✅ No hay emails duplicados</strong></div>";
    } else {
        echo "<div class='alert alert-warning'>";
        echo "<strong>⚠️ Se encontraron " . count($duplicados_email) . " emails duplicados</strong>";
        echo "</div>";
        
        echo "<table>";
        echo "<tr><th>Email</th><th>Duplicados</th><th>Usuarios</th><th>Acción sugerida</th></tr>";
        
        foreach ($duplicados_email as $dup) {
            echo "<tr>";
            echo "<td><strong>{$dup['mail']}</strong></td>";
            echo "<td>{$dup['count']}</td>";
            echo "<td>{$dup['usuarios']}</td>";
            echo "<td><strong>⚠️ Revisar manualmente - mismo email usado por múltiples usuarios</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // ============================================
    // 4. USUARIOS CON DID DUPLICADO
    // ============================================
    echo "<h2>🔢 4. Usuarios con 'did' duplicado</h2>";
    
    $duplicados_did = $db->fetchAll(
        "SELECT did, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids, GROUP_CONCAT(usuario ORDER BY id) as usuarios
         FROM usuarios 
         WHERE elim = 0
         GROUP BY did 
         HAVING COUNT(*) > 1
         ORDER BY count DESC, did ASC"
    );
    
    if (empty($duplicados_did)) {
        echo "<div class='alert alert-success'><strong>✅ No hay DIDs duplicados (correcto)</strong></div>";
    } else {
        echo "<div class='alert alert-danger'>";
        echo "<strong>❌ PROBLEMA CRÍTICO: Se encontraron " . count($duplicados_did) . " DIDs duplicados</strong><br>";
        echo "El campo 'did' debe ser único. Esto causará problemas en las operaciones CRUD.";
        echo "</div>";
        
        echo "<table>";
        echo "<tr><th>DID</th><th>Duplicados</th><th>IDs</th><th>Usuarios</th><th>Acción sugerida</th></tr>";
        
        foreach ($duplicados_did as $dup) {
            echo "<tr>";
            echo "<td><strong>{$dup['did']}</strong></td>";
            echo "<td>{$dup['count']}</td>";
            echo "<td>{$dup['ids']}</td>";
            echo "<td>{$dup['usuarios']}</td>";
            echo "<td><strong>🔧 Asignar nuevo DID a duplicados (MÁX+1)</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // ============================================
    // 5. ESTADÍSTICAS GENERALES
    // ============================================
    echo "<h2>📊 5. Estadísticas generales</h2>";
    
    $stats = $db->fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE elim = 0");
    $stats_adm = $db->fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'adm' AND superado = 0 AND elim = 0");
    $stats_socios = $db->fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'socio' AND superado = 0 AND elim = 0");
    $stats_superados = $db->fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE superado = 1 AND elim = 0");
    $stats_eliminados = $db->fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE elim = 1");
    
    echo "<table>";
    echo "<tr><th>Métrica</th><th>Valor</th></tr>";
    echo "<tr><td>Total usuarios activos (<code>elim=0</code>)</td><td><strong>{$stats['total']}</strong></td></tr>";
    echo "<tr><td>Administradores activos (<code>tipo='adm', superado=0, elim=0</code>)</td><td><strong>{$stats_adm['total']}</strong></td></tr>";
    echo "<tr><td>Socios activos (<code>tipo='socio', superado=0, elim=0</code>)</td><td><strong>{$stats_socios['total']}</strong></td></tr>";
    echo "<tr><td>Usuarios superados (<code>superado=1, elim=0</code>)</td><td>{$stats_superados['total']}</td></tr>";
    echo "<tr><td>Usuarios eliminados (<code>elim=1</code>)</td><td>{$stats_eliminados['total']}</td></tr>";
    echo "</table>";
    
    // ============================================
    // 6. RECOMENDACIONES
    // ============================================
    echo "<h2>💡 Recomendaciones</h2>";
    
    echo "<div class='alert alert-info'>";
    echo "<h3>Filtros recomendados para mostrar usuarios en la tabla:</h3>";
    echo "<pre><code>SELECT * FROM usuarios 
WHERE superado = 0 
  AND elim = 0 
ORDER BY did DESC, usuario ASC</code></pre>";
    
    echo "<h3>Explicación:</h3>";
    echo "<ul>";
    echo "<li><strong><code>superado = 0</code></strong>: Excluye registros obsoletos/antiguos</li>";
    echo "<li><strong><code>elim = 0</code></strong>: Excluye registros eliminados (soft delete)</li>";
    echo "<li><strong><code>ORDER BY did DESC</code></strong>: Usuarios más recientes primero</li>";
    echo "</ul>";
    echo "</div>";
    
    if (!empty($duplicados_nombre) || !empty($duplicados_did)) {
        echo "<div class='alert alert-warning'>";
        echo "<h3>⚠️ Acciones recomendadas:</h3>";
        echo "<ol>";
        echo "<li><strong>Marcar como 'superado'</strong> los registros duplicados más antiguos</li>";
        echo "<li><strong>Verificar</strong> que cada <code>did</code> sea único</li>";
        echo "<li><strong>Revisar</strong> emails duplicados manualmente</li>";
        echo "<li><strong>Probar</strong> las operaciones CRUD después de las correcciones</li>";
        echo "</ol>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>❌ Error:</strong> " . $e->getMessage();
    echo "</div>";
}

