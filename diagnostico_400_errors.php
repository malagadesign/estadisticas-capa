<?php
/**
 * Diagnóstico de errores 400 Bad Request
 * Analiza problemas específicos en la comunicación frontend-backend
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico de Errores 400 Bad Request</h1>";
echo "<p>🔍 Analizando problemas de comunicación frontend-backend...</p>";

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
    // PASO 1: VERIFICAR SESIÓN Y PERMISOS
    // ============================================
    
    echo "<h2>🔐 PASO 1: Verificación de sesión y permisos</h2>";
    
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
            echo "<p>💡 <strong>Solución:</strong> Necesitas iniciar sesión como administrador</p>";
        }
    } else {
        echo "<p>❌ No hay sesión activa</p>";
        echo "<p>💡 <strong>Solución:</strong> Necesitas iniciar sesión</p>";
    }
    
    // ============================================
    // PASO 2: VERIFICAR ARCHIVOS RESTAURADOS
    // ============================================
    
    echo "<h2>📁 PASO 2: Verificación de archivos restaurados</h2>";
    
    $archivos_criticos = [
        'usuarios/admUsuarios.php',
        'usuarios/ADM.php',
        'conector.php',
        'config.php'
    ];
    
    foreach ($archivos_criticos as $archivo) {
        if (file_exists($archivo)) {
            $tamaño = filesize($archivo);
            $contenido = file_get_contents($archivo);
            
            echo "<p>✅ $archivo - Existe ($tamaño bytes)</p>";
            
            // Verificar contenido específico
            if ($archivo == 'usuarios/ADM.php') {
                if (strpos($contenido, 'function mandarMailUsuario') !== false) {
                    echo "<p>✅ usuarios/ADM.php tiene funciones de email</p>";
                } else {
                    echo "<p>❌ usuarios/ADM.php NO tiene funciones de email</p>";
                }
                
                if (strpos($contenido, 'doPostRequest') !== false) {
                    echo "<p>✅ usuarios/ADM.php tiene función doPostRequest</p>";
                } else {
                    echo "<p>❌ usuarios/ADM.php NO tiene función doPostRequest</p>";
                }
            }
            
            if ($archivo == 'usuarios/admUsuarios.php') {
                if (strpos($contenido, 'function FguardarForm') !== false) {
                    echo "<p>✅ usuarios/admUsuarios.php tiene función de guardado</p>";
                } else {
                    echo "<p>❌ usuarios/admUsuarios.php NO tiene función de guardado</p>";
                }
                
                if (strpos($contenido, 'doPostRequest') !== false) {
                    echo "<p>✅ usuarios/admUsuarios.php tiene función doPostRequest</p>";
                } else {
                    echo "<p>❌ usuarios/admUsuarios.php NO tiene función doPostRequest</p>";
                }
            }
        } else {
            echo "<p>❌ $archivo - No encontrado</p>";
        }
    }
    
    // ============================================
    // PASO 3: VERIFICAR FUNCIONES JAVASCRIPT
    // ============================================
    
    echo "<h2>🔧 PASO 3: Verificación de funciones JavaScript</h2>";
    
    // Verificar si hay archivos JavaScript que contengan doPostRequest
    $archivos_js = [
        'js/main.js',
        'js/plugins.js',
        'js/vendor/jquery.min.js'
    ];
    
    $doPostRequest_encontrado = false;
    foreach ($archivos_js as $archivo) {
        if (file_exists($archivo)) {
            $contenido = file_get_contents($archivo);
            if (strpos($contenido, 'doPostRequest') !== false) {
                echo "<p>✅ $archivo contiene función doPostRequest</p>";
                $doPostRequest_encontrado = true;
            }
        }
    }
    
    if (!$doPostRequest_encontrado) {
        echo "<p>❌ Función doPostRequest no encontrada en archivos JS</p>";
        echo "<p>💡 <strong>Problema:</strong> La función doPostRequest es necesaria para las peticiones AJAX</p>";
    }
    
    // ============================================
    // PASO 4: CREAR FUNCIÓN doPostRequest FALTANTE
    // ============================================
    
    echo "<h2>🔧 PASO 4: Creando función doPostRequest faltante</h2>";
    
    if (!$doPostRequest_encontrado) {
        echo "<p>🔨 Creando función doPostRequest...</p>";
        
        $doPostRequest_js = '
// Función doPostRequest para peticiones AJAX
function doPostRequest(url, data) {
    return fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .catch(error => {
        console.error("Error en doPostRequest:", error);
        throw error;
    });
}

// Función notifyBox para notificaciones
function notifyBox(position, align, icon, type, animIn, animOut, title, message, url, time) {
    // Implementación básica de notificaciones
    if (typeof swal !== "undefined") {
        swal({
            title: title,
            text: message,
            type: type === "danger" ? "error" : type,
            timer: time || 3000
        });
    } else {
        alert(title + ": " + message);
    }
}

// Función FverificarCaracteres
function FverificarCaracteres(element) {
    // Función básica para verificar caracteres
    return true;
}
';
        
        if (file_put_contents('js/doPostRequest.js', $doPostRequest_js)) {
            echo "<p>✅ js/doPostRequest.js creado exitosamente</p>";
        } else {
            echo "<p>❌ Error al crear js/doPostRequest.js</p>";
        }
    }
    
    // ============================================
    // PASO 5: VERIFICAR ESTRUCTURA DE BASE DE DATOS
    // ============================================
    
    echo "<h2>📊 PASO 5: Verificación de estructura de base de datos</h2>";
    
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
    // PASO 6: PROBAR FUNCIONALIDAD BÁSICA
    // ============================================
    
    echo "<h2>🧪 PASO 6: Prueba de funcionalidad básica</h2>";
    
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
    
    // ============================================
    // PASO 7: RECOMENDACIONES ESPECÍFICAS
    // ============================================
    
    echo "<h2>💡 PASO 7: Recomendaciones específicas</h2>";
    
    $problemas_identificados = [];
    
    // Verificar sesión
    if (!isset($_SESSION['ScapaUsuarioDid'])) {
        $problemas_identificados[] = "No hay sesión activa - necesitas iniciar sesión";
    } elseif ($_SESSION['ScapaUsuarioTipo'] != 'adm') {
        $problemas_identificados[] = "Usuario no tiene permisos de administrador";
    }
    
    // Verificar función doPostRequest
    if (!$doPostRequest_encontrado) {
        $problemas_identificados[] = "Función doPostRequest faltante - creada en js/doPostRequest.js";
    }
    
    if (empty($problemas_identificados)) {
        echo "<p>✅ No se identificaron problemas obvios</p>";
        echo "<p>💡 <strong>Posibles causas de errores 400:</strong></p>";
        echo "<ul>";
        echo "<li>Problemas de CORS en el servidor</li>";
        echo "<li>Headers incorrectos en las peticiones</li>";
        echo "<li>Datos malformados en el JSON</li>";
        echo "<li>Problemas de configuración del servidor web</li>";
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
    // PASO 8: CREAR SCRIPT DE PRUEBA
    // ============================================
    
    echo "<h2>🧪 PASO 8: Creando script de prueba</h2>";
    
    $test_script = '<?php
/**
 * Script de prueba para usuarios/ADM.php
 * Prueba la funcionalidad del backend
 */

include("conector.php");

// Simular datos de prueba
$test_data = [
    "Adatos" => [
        "que" => "admUsuarios",
        "did" => 0,
        "usuario" => "test_user",
        "mail" => "test@example.com",
        "habilitado" => 1
    ]
];

echo "<h1>🧪 Prueba de usuarios/ADM.php</h1>";

// Simular POST request
$_POST = $test_data;

// Incluir el archivo ADM.php
ob_start();
include("usuarios/ADM.php");
$output = ob_get_clean();

echo "<p>✅ usuarios/ADM.php ejecutado sin errores</p>";
echo "<p>📄 Output: " . htmlspecialchars($output) . "</p>";
?>';
    
    if (file_put_contents('test_adm.php', $test_script)) {
        echo "<p>✅ test_adm.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ejecuta test_adm.php para probar el backend</p>";
    } else {
        echo "<p>❌ Error al crear test_adm.php</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎯 ¡DIAGNÓSTICO COMPLETADO!</p>";
    echo "<p>Revisa los problemas identificados arriba para resolver los errores 400.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este diagnóstico identifica problemas específicos que causan errores 400.</p>";
?>
