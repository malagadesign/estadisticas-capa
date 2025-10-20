<?php
/**
 * Script de Migración de Contraseñas
 * Convierte contraseñas de texto plano a bcrypt
 * 
 * IMPORTANTE: Ejecutar UNA SOLA VEZ
 * Fecha: 8 de Octubre, 2025
 */

// Configuración directa de base de datos (para evitar conflictos con conector.php)
$db_config = [
    'host' => 'localhost',
    'port' => 8889,
    'user' => 'root',
    'password' => 'root',
    'database' => 'mlgcapa_enc'
];

// Conectar a la base de datos
$mysqli = new mysqli(
    $db_config['host'],
    $db_config['user'],
    $db_config['password'],
    $db_config['database'],
    $db_config['port']
);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error . "\n");
}

$mysqli->set_charset("utf8");

// Verificar que se ejecute desde terminal o con parámetro especial
if (php_sapi_name() !== 'cli' && !isset($_GET['ejecutar_migracion_passwords'])) {
    die('Este script debe ejecutarse desde terminal o con el parámetro ?ejecutar_migracion_passwords=SI_ESTOY_SEGURO');
}

// Verificación adicional de seguridad
if (isset($_GET['ejecutar_migracion_passwords']) && $_GET['ejecutar_migracion_passwords'] !== 'SI_ESTOY_SEGURO') {
    die('Parámetro de confirmación incorrecto');
}

echo "===========================================\n";
echo "MIGRACIÓN DE CONTRASEÑAS A BCRYPT\n";
echo "===========================================\n\n";

// Verificar si ya existe la columna password_hash
$result = $mysqli->query("SHOW COLUMNS FROM usuarios LIKE 'password_hash'");
if ($result->num_rows == 0) {
    echo "Agregando columna 'password_hash' a la tabla usuarios...\n";
    $mysqli->query("ALTER TABLE usuarios ADD COLUMN password_hash VARCHAR(255) NULL AFTER psw");
    echo "✓ Columna agregada\n\n";
} else {
    echo "✓ La columna 'password_hash' ya existe\n\n";
}

// Obtener todos los usuarios con contraseña en texto plano
$query = "SELECT id, did, usuario, psw, tipo FROM usuarios WHERE superado = 0 AND elim = 0";
$result = $mysqli->query($query);

if (!$result) {
    die("Error en consulta: " . $mysqli->error . "\n");
}

$total = $result->num_rows;
$migrados = 0;
$errores = 0;

echo "Usuarios a migrar: $total\n";
echo "Iniciando migración...\n\n";

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $did = $row['did'];
    $usuario = $row['usuario'];
    $password_plano = $row['psw'];
    $tipo = $row['tipo'];
    
    // No migrar si ya tiene hash
    if (strlen($password_plano) > 60 && strpos($password_plano, '$2y$') === 0) {
        echo "⊘ Usuario '{$usuario}' (ID: {$id}) ya tiene hash bcrypt\n";
        continue;
    }
    
    // Generar hash con bcrypt (cost 12 para mayor seguridad)
    $password_hash = password_hash($password_plano, PASSWORD_BCRYPT, ['cost' => 12]);
    
    if ($password_hash === false) {
        echo "✗ ERROR: No se pudo hashear password para '{$usuario}' (ID: {$id})\n";
        $errores++;
        continue;
    }
    
    // Actualizar en la base de datos usando prepared statement
    $stmt = $mysqli->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $password_hash, $id);
    
    if ($stmt->execute()) {
        echo "✓ Migrado: '{$usuario}' (ID: {$id}, Tipo: {$tipo})\n";
        $migrados++;
    } else {
        echo "✗ ERROR al actualizar '{$usuario}' (ID: {$id}): " . $stmt->error . "\n";
        $errores++;
    }
    
    $stmt->close();
}

echo "\n===========================================\n";
echo "RESUMEN DE MIGRACIÓN\n";
echo "===========================================\n";
echo "Total de usuarios: $total\n";
echo "Migrados exitosamente: $migrados\n";
echo "Errores: $errores\n";
echo "===========================================\n\n";

if ($migrados > 0) {
    echo "⚠️  IMPORTANTE:\n";
    echo "1. Las contraseñas ahora están hasheadas de forma segura\n";
    echo "2. NO ejecutes este script nuevamente\n";
    echo "3. Las contraseñas en texto plano (columna 'psw') se mantienen por seguridad\n";
    echo "   pero el sistema usará 'password_hash' de ahora en adelante\n";
    echo "4. Una vez verificado que todo funciona, puedes eliminar la columna 'psw'\n\n";
}

$mysqli->close();

echo "✓ Migración completada\n";
?>
