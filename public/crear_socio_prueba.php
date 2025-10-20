<?php
// Script temporal para crear/actualizar socio de prueba
$mysqli = new mysqli('localhost', 'root', 'root', 'mlgcapa_enc', 8889);

if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

// Datos del socio de prueba
$usuario = 'socio_prueba';
$mail = 'socio@prueba.com';
$password = 'test123'; // Contraseña simple y corta

// Verificar si ya existe
$stmt = $mysqli->prepare("SELECT did FROM usuarios WHERE usuario = ? AND elim = 0");
$stmt->bind_param('s', $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Actualizar
    $row = $result->fetch_assoc();
    $did = $row['did'];
    
    $stmt2 = $mysqli->prepare("UPDATE usuarios SET psw = ?, mail = ?, habilitado = 1 WHERE did = ?");
    $stmt2->bind_param('ssi', $password, $mail, $did);
    $stmt2->execute();
    
    echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px;'>";
    echo "<h2 style='color: #155724;'>✅ Socio de prueba ACTUALIZADO</h2>";
} else {
    // Obtener el próximo did disponible
    $result_max = $mysqli->query("SELECT MAX(did) as max_did FROM usuarios");
    $row_max = $result_max->fetch_assoc();
    $new_did = ($row_max['max_did'] ?? 0) + 1;
    
    // Crear nuevo
    $stmt2 = $mysqli->prepare("INSERT INTO usuarios (did, usuario, mail, psw, tipo, habilitado, superado, elim, quien, alertadoUsuario, alertadoCapa) VALUES (?, ?, ?, ?, 'socio', 1, 0, 0, 0, 0, 0)");
    $stmt2->bind_param('isss', $new_did, $usuario, $mail, $password);
    $stmt2->execute();
    
    echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px;'>";
    echo "<h2 style='color: #155724;'>✅ Socio de prueba CREADO</h2>";
}

echo "<h3>Credenciales de acceso:</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; font-size: 16px;'>";
echo "<tr style='background: #9D4EDD; color: white;'><th>Campo</th><th>Valor</th></tr>";
echo "<tr><td><strong>Usuario:</strong></td><td><code style='background: #f5f5f5; padding: 5px;'>$usuario</code></td></tr>";
echo "<tr><td><strong>Contraseña:</strong></td><td><code style='background: #f5f5f5; padding: 5px;'>$password</code></td></tr>";
echo "<tr><td><strong>Email:</strong></td><td>$mail</td></tr>";
echo "<tr><td><strong>Tipo:</strong></td><td>Socio</td></tr>";
echo "</table>";

echo "<br><br>";
echo "<p><strong>URL de login:</strong> <a href='http://localhost:8888/capa/encuestas/v2/' target='_blank'>http://localhost:8888/capa/encuestas/v2/</a></p>";
echo "</div>";

$mysqli->close();
?>

