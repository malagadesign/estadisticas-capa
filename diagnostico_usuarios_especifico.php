<?php
/**
 * Diagnóstico específico de gestión de usuarios
 * Identifica problemas específicos en la funcionalidad de usuarios
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico Específico de Gestión de Usuarios</h1>";
echo "<p>🔍 Analizando problemas específicos en la funcionalidad...</p>";

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
    // PASO 1: VERIFICAR ARCHIVOS CRÍTICOS
    // ============================================
    
    echo "<h2>📁 PASO 1: Verificación de archivos críticos</h2>";
    
    $archivos_criticos = [
        'usuarios/admUsuarios.php',
        'usuarios/admSocios.php', 
        'usuarios/ADM.php',
        'conector.php',
        'config.php'
    ];
    
    foreach ($archivos_criticos as $archivo) {
        if (file_exists($archivo)) {
            $tamaño = filesize($archivo);
            $contenido = file_get_contents($archivo);
            $es_basico = strpos($contenido, 'Archivo restaurado automáticamente') !== false;
            
            echo "<p>✅ $archivo - Existe ($tamaño bytes)";
            if ($es_basico) {
                echo " <span style='color: orange;'>⚠️ CONTENIDO BÁSICO</span>";
            } else {
                echo " <span style='color: green;'>✅ CONTENIDO COMPLETO</span>";
            }
            echo "</p>";
            
            // Verificar contenido específico
            if ($archivo == 'usuarios/ADM.php') {
                if (strpos($contenido, 'function mandarMailUsuario') !== false) {
                    echo "<p>✅ usuarios/ADM.php tiene funciones de email</p>";
                } else {
                    echo "<p>❌ usuarios/ADM.php NO tiene funciones de email</p>";
                }
            }
            
            if ($archivo == 'usuarios/admUsuarios.php') {
                if (strpos($contenido, 'function FguardarForm') !== false) {
                    echo "<p>✅ usuarios/admUsuarios.php tiene función de guardado</p>";
                } else {
                    echo "<p>❌ usuarios/admUsuarios.php NO tiene función de guardado</p>";
                }
            }
        } else {
            echo "<p>❌ $archivo - No encontrado</p>";
        }
    }
    
    // ============================================
    // PASO 2: VERIFICAR SESIÓN Y PERMISOS
    // ============================================
    
    echo "<h2>🔐 PASO 2: Verificación de sesión y permisos</h2>";
    
    // Iniciar sesión si no está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['ScapaUsuarioDid'])) {
        echo "<p>✅ Sesión activa - Usuario ID: " . $_SESSION['ScapaUsuarioDid'] . "</p>";
        echo "<p>✅ Tipo de usuario: " . ($_SESSION['ScapaUsuarioTipo'] ?? 'No definido') . "</p>";
        
        if ($_SESSION['ScapaUsuarioTipo'] == 'adm') {
            echo "<p>✅ Usuario tiene permisos de administrador</p>";
        } else {
            echo "<p>❌ Usuario NO tiene permisos de administrador</p>";
        }
    } else {
        echo "<p>❌ No hay sesión activa</p>";
        echo "<p>💡 <strong>Solución:</strong> Necesitas iniciar sesión como administrador</p>";
    }
    
    // ============================================
    // PASO 3: VERIFICAR ESTRUCTURA DE BASE DE DATOS
    // ============================================
    
    echo "<h2>📊 PASO 3: Verificación de estructura de base de datos</h2>";
    
    // Verificar tabla usuarios
    $result = $mysqli->query("DESCRIBE usuarios");
    $campos_requeridos = ['id', 'did', 'usuario', 'mail', 'tipo', 'habilitado', 'superado', 'elim'];
    $campos_encontrados = [];
    
    while ($row = $result->fetch_assoc()) {
        $campos_encontrados[] = $row['Field'];
    }
    
    echo "<p>📋 Campos encontrados en tabla usuarios:</p>";
    echo "<ul>";
    foreach ($campos_encontrados as $campo) {
        echo "<li>$campo</li>";
    }
    echo "</ul>";
    
    $campos_faltantes = array_diff($campos_requeridos, $campos_encontrados);
    if (empty($campos_faltantes)) {
        echo "<p>✅ Todos los campos requeridos están presentes</p>";
    } else {
        echo "<p>❌ Campos faltantes: " . implode(', ', $campos_faltantes) . "</p>";
    }
    
    // ============================================
    // PASO 4: VERIFICAR USUARIOS ADMINISTRATIVOS
    // ============================================
    
    echo "<h2>👥 PASO 4: Verificación de usuarios administrativos</h2>";
    
    $result = $mysqli->query("
        SELECT did, usuario, mail, habilitado, superado, elim 
        FROM usuarios 
        WHERE tipo = 'adm' AND elim = 0 
        ORDER BY did
    ");
    
    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
    
    echo "<p>📊 Usuarios administrativos encontrados: " . count($admins) . "</p>";
    
    if (count($admins) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th><th>superado</th><th>elim</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>" . $admin['did'] . "</td>";
            echo "<td>" . htmlspecialchars($admin['usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['mail']) . "</td>";
            echo "<td>" . $admin['habilitado'] . "</td>";
            echo "<td>" . $admin['superado'] . "</td>";
            echo "<td>" . $admin['elim'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No hay usuarios administrativos activos</p>";
    }
    
    // ============================================
    // PASO 5: VERIFICAR JAVASCRIPT Y FUNCIONES
    // ============================================
    
    echo "<h2>🔧 PASO 5: Verificación de JavaScript y funciones</h2>";
    
    // Verificar si hay funciones JavaScript en admUsuarios.php
    if (file_exists('usuarios/admUsuarios.php')) {
        $contenido = file_get_contents('usuarios/admUsuarios.php');
        
        $funciones_js = [
            'function Fcrear',
            'function Fmodificar',
            'function Fcompletar',
            'function FguardarForm',
            'doPostRequest'
        ];
        
        echo "<p>🔍 Verificando funciones JavaScript en usuarios/admUsuarios.php:</p>";
        foreach ($funciones_js as $funcion) {
            if (strpos($contenido, $funcion) !== false) {
                echo "<p>✅ $funcion - Encontrada</p>";
            } else {
                echo "<p>❌ $funcion - No encontrada</p>";
            }
        }
    }
    
    // ============================================
    // PASO 6: VERIFICAR ARCHIVOS DE DEPENDENCIAS
    // ============================================
    
    echo "<h2>📚 PASO 6: Verificación de archivos de dependencias</h2>";
    
    $archivos_dependencias = [
        'js/main.js',
        'js/plugins.js',
        'css/main.css',
        'css/bootstrap.min.css'
    ];
    
    foreach ($archivos_dependencias as $archivo) {
        if (file_exists($archivo)) {
            echo "<p>✅ $archivo - Existe</p>";
        } else {
            echo "<p>❌ $archivo - No encontrado</p>";
        }
    }
    
    // ============================================
    // PASO 7: RECOMENDACIONES ESPECÍFICAS
    // ============================================
    
    echo "<h2>💡 PASO 7: Recomendaciones específicas</h2>";
    
    $problemas_identificados = [];
    
    // Verificar si los archivos tienen contenido básico
    foreach ($archivos_criticos as $archivo) {
        if (file_exists($archivo)) {
            $contenido = file_get_contents($archivo);
            if (strpos($contenido, 'Archivo restaurado automáticamente') !== false) {
                $problemas_identificados[] = "Archivo $archivo tiene contenido básico";
            }
        }
    }
    
    // Verificar sesión
    if (!isset($_SESSION['ScapaUsuarioDid'])) {
        $problemas_identificados[] = "No hay sesión activa";
    } elseif ($_SESSION['ScapaUsuarioTipo'] != 'adm') {
        $problemas_identificados[] = "Usuario no tiene permisos de administrador";
    }
    
    if (empty($problemas_identificados)) {
        echo "<p>✅ No se identificaron problemas obvios</p>";
        echo "<p>💡 <strong>Posibles causas:</strong></p>";
        echo "<ul>";
        echo "<li>Errores de JavaScript en el navegador</li>";
        echo "<li>Problemas de permisos de archivos</li>";
        echo "<li>Errores en el código PHP</li>";
        echo "<li>Problemas de configuración del servidor</li>";
        echo "</ul>";
    } else {
        echo "<p>⚠️ <strong>Problemas identificados:</strong></p>";
        echo "<ul>";
        foreach ($problemas_identificados as $problema) {
            echo "<li>$problema</li>";
        }
        echo "</ul>";
    }
    
    // ============================================
    // PASO 8: SCRIPT DE PRUEBA
    // ============================================
    
    echo "<h2>🧪 PASO 8: Script de prueba</h2>";
    
    echo "<p>🔍 Probando funcionalidad básica...</p>";
    
    // Probar conexión a base de datos
    if ($mysqli->ping()) {
        echo "<p>✅ Conexión a base de datos activa</p>";
    } else {
        echo "<p>❌ Conexión a base de datos perdida</p>";
    }
    
    // Probar consulta básica
    $result = $mysqli->query("SELECT COUNT(*) as count FROM usuarios WHERE elim = 0");
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<p>✅ Consulta básica exitosa - $count usuarios activos</p>";
    } else {
        echo "<p>❌ Error en consulta básica: " . $mysqli->error . "</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎯 ¡DIAGNÓSTICO COMPLETADO!</p>";
    echo "<p>Revisa los problemas identificados arriba para resolver los issues de gestión de usuarios.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este diagnóstico identifica problemas específicos en la gestión de usuarios.</p>";
?>
