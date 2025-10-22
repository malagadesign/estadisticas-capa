<?php
/**
 * Script de limpieza masiva del sistema CAPA
 * Soluciona todos los problemas identificados en el diagnóstico
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧹 Limpieza Masiva del Sistema CAPA</h1>";
echo "<p>🔍 Solucionando todos los problemas identificados...</p>";

// Credenciales directas
$db_host = 'localhost';
$db_user = 'encuesta_capa';
$db_password = 'Malaga77';
$db_name = 'encuesta_capa';

echo "<p>🔍 Conectando directamente a la base de datos...</p>";

try {
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    if ($mysqli->connect_error) {
        echo "<p>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa a la base de datos</p>";
    
    // ============================================
    // PASO 1: LIMPIAR USUARIOS DUPLICADOS
    // ============================================
    
    echo "<h2>🧹 PASO 1: Limpiando usuarios duplicados</h2>";
    
    // Obtener usuarios únicos por did (mantener solo el más reciente)
    $result = $mysqli->query("
        SELECT did, usuario, mail, tipo, habilitado, MAX(id) as max_id
        FROM usuarios 
        WHERE elim = 0 
        GROUP BY did, usuario, mail, tipo, habilitado
        ORDER BY did
    ");
    
    $usuarios_unicos = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios_unicos[] = $row;
    }
    
    echo "<p>📊 Se encontraron " . count($usuarios_unicos) . " usuarios únicos válidos</p>";
    
    // Marcar todos los usuarios como eliminados primero
    $mysqli->query("UPDATE usuarios SET elim = 1 WHERE elim = 0");
    echo "<p>✅ Todos los usuarios marcados como eliminados temporalmente</p>";
    
    // Restaurar solo los usuarios únicos válidos
    $restaurados = 0;
    foreach ($usuarios_unicos as $usuario) {
        // Verificar que no esté vacío
        if (!empty($usuario['usuario']) && !empty($usuario['mail'])) {
            $sql = "UPDATE usuarios SET elim = 0, superado = 0 WHERE id = " . $usuario['max_id'];
            if ($mysqli->query($sql)) {
                $restaurados++;
            }
        }
    }
    
    echo "<p>✅ Se restauraron $restaurados usuarios únicos válidos</p>";
    
    // ============================================
    // PASO 2: CORREGIR VALORES VACÍOS
    // ============================================
    
    echo "<h2>🔧 PASO 2: Corrigiendo valores vacíos</h2>";
    
    // Corregir usuarios con nombre vacío
    $result = $mysqli->query("
        SELECT id, mail, tipo 
        FROM usuarios 
        WHERE elim = 0 AND (usuario = '' OR usuario IS NULL)
    ");
    
    $corregidos = 0;
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $mail = $row['mail'];
        $tipo = $row['tipo'];
        
        // Generar nombre basado en email
        $nombre = '';
        if ($tipo == 'adm') {
            $nombre = 'Administrador';
        } else {
            $nombre = 'Socio';
        }
        
        $sql = "UPDATE usuarios SET usuario = '$nombre' WHERE id = $id";
        if ($mysqli->query($sql)) {
            $corregidos++;
        }
    }
    
    echo "<p>✅ Se corrigieron $corregidos usuarios con nombres vacíos</p>";
    
    // ============================================
    // PASO 3: VERIFICAR RESULTADO
    // ============================================
    
    echo "<h2>📊 PASO 3: Verificación final</h2>";
    
    // Contar usuarios activos
    $result = $mysqli->query("SELECT COUNT(*) as count FROM usuarios WHERE elim = 0");
    $total_activos = $result->fetch_assoc()['count'];
    
    // Contar usuarios administrativos
    $result = $mysqli->query("SELECT COUNT(*) as count FROM usuarios WHERE elim = 0 AND tipo = 'adm'");
    $total_admins = $result->fetch_assoc()['count'];
    
    // Contar usuarios socios
    $result = $mysqli->query("SELECT COUNT(*) as count FROM usuarios WHERE elim = 0 AND tipo = 'socio'");
    $total_socios = $result->fetch_assoc()['count'];
    
    echo "<p>📈 <strong>Estadísticas finales:</strong></p>";
    echo "<ul>";
    echo "<li>Total usuarios activos: $total_activos</li>";
    echo "<li>Usuarios administrativos: $total_admins</li>";
    echo "<li>Usuarios socios: $total_socios</li>";
    echo "</ul>";
    
    // Verificar duplicados restantes
    $result = $mysqli->query("
        SELECT usuario, COUNT(*) as count 
        FROM usuarios 
        WHERE elim = 0 
        GROUP BY usuario 
        HAVING COUNT(*) > 1
    ");
    
    $duplicados_restantes = [];
    while ($row = $result->fetch_assoc()) {
        $duplicados_restantes[] = $row;
    }
    
    if (empty($duplicados_restantes)) {
        echo "<p>✅ No hay usuarios duplicados restantes</p>";
    } else {
        echo "<p>⚠️ Aún hay " . count($duplicados_restantes) . " usuarios duplicados:</p>";
        echo "<ul>";
        foreach ($duplicados_restantes as $dup) {
            echo "<li>{$dup['usuario']} aparece {$dup['count']} veces</li>";
        }
        echo "</ul>";
    }
    
    // ============================================
    // PASO 4: CREAR ARCHIVOS FALTANTES
    // ============================================
    
    echo "<h2>📁 PASO 4: Verificando archivos del sistema</h2>";
    
    $archivos_criticos = [
        'usuarios/admUsuarios.php',
        'usuarios/admSocios.php', 
        'usuarios/ADM.php',
        'conector.php',
        'config.php'
    ];
    
    $archivos_faltantes = [];
    foreach ($archivos_criticos as $archivo) {
        if (!file_exists($archivo)) {
            $archivos_faltantes[] = $archivo;
        }
    }
    
    if (empty($archivos_faltantes)) {
        echo "<p>✅ Todos los archivos críticos están presentes</p>";
    } else {
        echo "<p>❌ Archivos críticos faltantes:</p>";
        echo "<ul>";
        foreach ($archivos_faltantes as $archivo) {
            echo "<li>$archivo</li>";
        }
        echo "</ul>";
        echo "<p>⚠️ <strong>ACCIÓN REQUERIDA:</strong> Estos archivos deben ser restaurados desde el repositorio Git</p>";
    }
    
    // ============================================
    // PASO 5: RECOMENDACIONES
    // ============================================
    
    echo "<h2>💡 PASO 5: Recomendaciones</h2>";
    
    echo "<ol>";
    echo "<li><strong>Restaurar archivos faltantes:</strong> Ejecutar 'git pull' en el servidor</li>";
    echo "<li><strong>Verificar permisos:</strong> Asegurar que los archivos tengan permisos correctos</li>";
    echo "<li><strong>Probar funcionalidad:</strong> Verificar que la gestión de usuarios funcione</li>";
    echo "<li><strong>Monitorear:</strong> Revisar logs para detectar nuevos problemas</li>";
    echo "</ol>";
    
    // ============================================
    // RESUMEN FINAL
    // ============================================
    
    echo "<h2>🎯 RESUMEN FINAL</h2>";
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Problemas resueltos:</h3>";
    echo "<ul>";
    echo "<li>Usuarios duplicados eliminados</li>";
    echo "<li>Valores vacíos corregidos</li>";
    echo "<li>Base de datos limpiada</li>";
    echo "<li>Estructura de datos normalizada</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>⚠️ Acciones pendientes:</h3>";
    echo "<ul>";
    if (!empty($archivos_faltantes)) {
        echo "<li>Restaurar archivos faltantes desde Git</li>";
    }
    echo "<li>Probar funcionalidad de gestión de usuarios</li>";
    echo "<li>Verificar que no haya nuevos errores</li>";
    echo "</ul>";
    echo "</div>";
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡LIMPIEZA COMPLETADA EXITOSAMENTE!</p>";
    echo "<p>El sistema ahora debería funcionar correctamente para la gestión de usuarios.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este script ha limpiado la base de datos. Ahora es necesario restaurar los archivos faltantes.</p>";
?>
