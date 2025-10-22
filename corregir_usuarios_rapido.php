<?php
/**
 * Script de corrección rápida para usuarios administrativos
 * Corrige nombres vacíos y problemas de visualización
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Corrección Rápida de Usuarios Administrativos</h1>";

// Credenciales directas
$db_host = 'localhost';
$db_user = 'encuesta_capa';
$db_password = 'Malaga77';
$db_name = 'encuesta_capa';

try {
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    if ($mysqli->connect_error) {
        echo "<p>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa</p>";
    
    // ============================================
    // PASO 1: CORREGIR USUARIOS CON NOMBRES VACÍOS
    // ============================================
    
    echo "<h2>🔧 Corrigiendo usuarios con nombres vacíos</h2>";
    
    // Corregir usuario Coordinación
    $sql = "UPDATE usuarios SET usuario = 'Coordinación' WHERE tipo = 'adm' AND elim = 0 AND mail = 'coordinacion@capa.org.ar' AND (usuario = '' OR usuario IS NULL)";
    if ($mysqli->query($sql)) {
        echo "<p>✅ Usuario Coordinación corregido</p>";
    }
    
    // Corregir usuario admin
    $sql = "UPDATE usuarios SET usuario = 'admin' WHERE tipo = 'adm' AND elim = 0 AND mail = 'admin@capa.org.ar' AND (usuario = '' OR usuario IS NULL)";
    if ($mysqli->query($sql)) {
        echo "<p>✅ Usuario admin corregido</p>";
    }
    
    // ============================================
    // PASO 2: VERIFICAR USUARIOS FINALES
    // ============================================
    
    echo "<h2>👥 Usuarios administrativos finales</h2>";
    
    $result = $mysqli->query("
        SELECT did, usuario, mail, habilitado 
        FROM usuarios 
        WHERE tipo = 'adm' AND elim = 0 
        ORDER BY did
    ");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>did</th><th>usuario</th><th>mail</th><th>habilitado</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['did'] . "</td>";
        echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
        echo "<td>" . $row['habilitado'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // ============================================
    // PASO 3: CREAR LOGIN CORREGIDO
    // ============================================
    
    echo "<h2>🔐 Creando login corregido</h2>";
    
    $login_content = '<?php
session_start();

$usuarios_admin = [
    "liit" => "liit123",
    "Coordinación" => "para1857",
    "admin" => "admin123"
];

if (isset($_POST["usuario"]) && isset($_POST["password"])) {
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];
    
    if (isset($usuarios_admin[$usuario]) && $usuarios_admin[$usuario] === $password) {
        $_SESSION["ScapaUsuarioDid"] = 1;
        $_SESSION["ScapaUsuarioTipo"] = "adm";
        $_SESSION["ScapaUsuario"] = $usuario;
        
        echo "<p style=\"color: green;\">✅ Sesión iniciada: $usuario</p>";
        echo "<p><a href=\"usuarios/admUsuarios.php\">Ir a gestión de usuarios</a></p>";
    } else {
        echo "<p style=\"color: red;\">❌ Usuario o contraseña incorrectos</p>";
    }
} else {
    echo "<h1>🔐 Login</h1>";
    echo "<form method=\"POST\">";
    echo "<p>Usuario: <input type=\"text\" name=\"usuario\" required></p>";
    echo "<p>Contraseña: <input type=\"password\" name=\"password\" required></p>";
    echo "<p><button type=\"submit\">Iniciar Sesión</button></p>";
    echo "</form>";
    echo "<p><strong>Usuarios:</strong> liit, Coordinación, admin</p>";
    echo "<p><strong>Contraseñas:</strong> liit123, para1857, admin123</p>";
}
?>';
    
    if (file_put_contents('login_simple.php', $login_content)) {
        echo "<p>✅ login_simple.php creado</p>";
    }
    
    $mysqli->close();
    
    echo "<p style='color: green; font-weight: bold;'>🎉 ¡CORRECCIÓN COMPLETADA!</p>";
    echo "<p>💡 <strong>Próximo paso:</strong> Ve a login_simple.php e inicia sesión</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
