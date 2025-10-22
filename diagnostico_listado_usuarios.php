<?php
/**
 * Diagnóstico específico para listado de usuarios administrativos
 * Identifica por qué no se muestran los usuarios en la página
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnóstico de Listado de Usuarios Administrativos</h1>";
echo "<p>🔍 Analizando por qué no se muestran los usuarios...</p>";

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
    // PASO 1: VERIFICAR CONSULTA SQL EXACTA
    // ============================================
    
    echo "<h2>📊 PASO 1: Verificando consulta SQL exacta</h2>";
    
    // Ejecutar la misma consulta que usa usuarios/admUsuarios.php
    $sql = "SELECT * FROM `usuarios` WHERE `tipo`='adm' AND `superado`=0 AND `elim`=0";
    echo "<p>🔍 Consulta SQL: <code>$sql</code></p>";
    
    $result = $mysqli->query($sql);
    
    if ($result === false) {
        echo "<p>❌ Error en consulta SQL: " . $mysqli->error . "</p>";
    } else {
        $usuarios_encontrados = [];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $usuarios_encontrados[] = $row;
        }
        
        echo "<p>📊 Usuarios encontrados: " . count($usuarios_encontrados) . "</p>";
        
        if (count($usuarios_encontrados) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th><th>superado</th><th>elim</th></tr>";
            foreach ($usuarios_encontrados as $usuario) {
                echo "<tr>";
                echo "<td>" . $usuario['did'] . "</td>";
                echo "<td>" . htmlspecialchars($usuario['usuario']) . "</td>";
                echo "<td>" . htmlspecialchars($usuario['mail']) . "</td>";
                echo "<td>" . $usuario['habilitado'] . "</td>";
                echo "<td>" . $usuario['superado'] . "</td>";
                echo "<td>" . $usuario['elim'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No se encontraron usuarios con la consulta SQL</p>";
        }
    }
    
    // ============================================
    // PASO 2: VERIFICAR TODOS LOS USUARIOS ADMIN
    // ============================================
    
    echo "<h2>👥 PASO 2: Verificando todos los usuarios administrativos</h2>";
    
    $sql_all = "SELECT * FROM usuarios WHERE tipo = 'adm'";
    $result_all = $mysqli->query($sql_all);
    
    echo "<p>🔍 Consulta SQL: <code>$sql_all</code></p>";
    
    if ($result_all) {
        $todos_usuarios = [];
        while ($row = $result_all->fetch_array(MYSQLI_ASSOC)) {
            $todos_usuarios[] = $row;
        }
        
        echo "<p>📊 Total usuarios administrativos: " . count($todos_usuarios) . "</p>";
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th><th>superado</th><th>elim</th></tr>";
        foreach ($todos_usuarios as $usuario) {
            $color = ($usuario['elim'] == 0) ? 'green' : 'red';
            echo "<tr style='color: $color;'>";
            echo "<td>" . $usuario['did'] . "</td>";
            echo "<td>" . htmlspecialchars($usuario['usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario['mail']) . "</td>";
            echo "<td>" . $usuario['habilitado'] . "</td>";
            echo "<td>" . $usuario['superado'] . "</td>";
            echo "<td>" . $usuario['elim'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p>💡 <strong>Leyenda:</strong> Verde = Activo, Rojo = Eliminado</p>";
    }
    
    // ============================================
    // PASO 3: VERIFICAR ARCHIVO usuarios/admUsuarios.php
    // ============================================
    
    echo "<h2>📁 PASO 3: Verificando archivo usuarios/admUsuarios.php</h2>";
    
    if (file_exists('usuarios/admUsuarios.php')) {
        $contenido = file_get_contents('usuarios/admUsuarios.php');
        
        // Buscar la consulta SQL en el archivo
        if (preg_match('/SELECT.*FROM.*usuarios.*WHERE.*tipo.*adm/s', $contenido, $matches)) {
            echo "<p>✅ Consulta SQL encontrada en el archivo:</p>";
            echo "<p><code>" . htmlspecialchars($matches[0]) . "</code></p>";
        } else {
            echo "<p>❌ No se encontró consulta SQL en el archivo</p>";
        }
        
        // Verificar si incluye conector.php
        if (strpos($contenido, "include('conector.php')") !== false) {
            echo "<p>✅ Archivo incluye conector.php correctamente</p>";
        } else {
            echo "<p>❌ Archivo NO incluye conector.php correctamente</p>";
        }
        
        // Verificar si tiene la estructura de tabla
        if (strpos($contenido, '<table class="table table-striped table-hover">') !== false) {
            echo "<p>✅ Archivo tiene estructura de tabla HTML</p>";
        } else {
            echo "<p>❌ Archivo NO tiene estructura de tabla HTML</p>";
        }
        
        // Verificar si tiene el bucle PHP
        if (strpos($contenido, 'while ($row = $stmt->fetch_array(MYSQLI_ASSOC))') !== false) {
            echo "<p>✅ Archivo tiene bucle PHP para mostrar usuarios</p>";
        } else {
            echo "<p>❌ Archivo NO tiene bucle PHP para mostrar usuarios</p>";
        }
        
    } else {
        echo "<p>❌ Archivo usuarios/admUsuarios.php no encontrado</p>";
    }
    
    // ============================================
    // PASO 4: CREAR ARCHIVO DE PRUEBA
    // ============================================
    
    echo "<h2>🧪 PASO 4: Creando archivo de prueba</h2>";
    
    $test_file_content = '<?php
/**
 * Archivo de prueba para mostrar usuarios administrativos
 */

// Incluir configuración
include("config.php");

// Establecer conexión
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

echo "<h1>🧪 Prueba de Usuarios Administrativos</h1>";

// Ejecutar consulta
$sql = "SELECT * FROM `usuarios` WHERE `tipo`=\'adm\' AND `superado`=0 AND `elim`=0";
echo "<p>🔍 Consulta SQL: <code>$sql</code></p>";

$stmt = $mysqli->query($sql);

if($stmt === false) {
    echo "<p style=\"color: red;\">❌ Error: " . $mysqli->error . "</p>";
} else {
    $usuarios = [];
    $did = 0;
    
    while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
        $did = $row[\'did\'];
        $usuario = $row[\'usuario\'];
        $mail = $row[\'mail\'];
        $habilitado = ($row[\'habilitado\'] == 1) ? \'Si\' : \'No\';
        
        $usuarios[] = $row;
        
        echo "<p>✅ Usuario encontrado: ID $did, Usuario: $usuario, Mail: $mail, Habilitado: $habilitado</p>";
    }
    
    $stmt->close();
    
    if ($did == 0){
        echo "<p style=\"color: red;\">❌ Sin datos encontrados</p>";
    } else {
        echo "<p style=\"color: green;\">✅ Total usuarios encontrados: " . count($usuarios) . "</p>";
    }
}

$mysqli->close();
?>';
    
    if (file_put_contents('test_usuarios_admin.php', $test_file_content)) {
        echo "<p>✅ test_usuarios_admin.php creado exitosamente</p>";
        echo "<p>💡 <strong>Prueba:</strong> Ve a test_usuarios_admin.php para ver si la consulta funciona</p>";
    } else {
        echo "<p>❌ Error al crear test_usuarios_admin.php</p>";
    }
    
    // ============================================
    // PASO 5: RECOMENDACIONES
    // ============================================
    
    echo "<h2>💡 PASO 5: Recomendaciones</h2>";
    
    if (count($usuarios_encontrados) > 0) {
        echo "<p>✅ La consulta SQL funciona correctamente</p>";
        echo "<p>💡 <strong>Problema probable:</strong> Error en el archivo usuarios/admUsuarios.php</p>";
        echo "<p>🔧 <strong>Solución:</strong> Verificar que el archivo tenga la estructura correcta</p>";
    } else {
        echo "<p>❌ La consulta SQL no encuentra usuarios</p>";
        echo "<p>💡 <strong>Problema probable:</strong> Usuarios marcados como eliminados o superados</p>";
        echo "<p>🔧 <strong>Solución:</strong> Verificar valores de elim y superado</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎯 ¡DIAGNÓSTICO COMPLETADO!</p>";
    echo "<p>Revisa los resultados arriba para identificar el problema específico.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Este diagnóstico identifica por qué no se muestran los usuarios administrativos.</p>";
?>
