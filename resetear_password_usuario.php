<?php
/**
 * Script para resetear la contraseña de un usuario específico
 */

require_once __DIR__ . '/v2/config/app.php';
require_once __DIR__ . '/v2/core/Database.php';

echo "<h1>🔐 Resetear Contraseña de Usuario</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    form { max-width: 500px; }
    .form-group { margin: 15px 0; }
    label { display: block; margin-bottom: 5px; font-weight: bold; }
    input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    button { background-color: #4A148C; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background-color: #3a0f6a; }
    .alert { padding: 15px; margin: 20px 0; border-radius: 5px; }
    .alert-success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
</style>";

try {
    $db = Database::getInstance();
    
    // Obtener lista de usuarios
    $usuarios = $db->fetchAll(
        "SELECT id, did, usuario, mail, tipo FROM usuarios WHERE elim = 0 ORDER BY usuario ASC"
    );
    
    // Si se envió el formulario, actualizar la contraseña
    if ($_POST['usuario_id'] ?? '') {
        $usuarioId = (int)$_POST['usuario_id'];
        $nuevaPassword = $_POST['nueva_password'];
        
        if (empty($nuevaPassword)) {
            echo "<div class='alert alert-danger'>❌ La contraseña no puede estar vacía</div>";
        } else {
            // Generar hash
            $passwordHash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
            
            // Actualizar en BD
            $db->query(
                "UPDATE usuarios SET psw = ? WHERE id = ?",
                ['si', $passwordHash, $usuarioId]
            );
            
            // Obtener nombre del usuario actualizado
            $user = $db->fetchOne("SELECT usuario FROM usuarios WHERE id = ?", ['i', $usuarioId]);
            
            echo "<div class='alert alert-success'>";
            echo "<h3>✅ Contraseña actualizada correctamente</h3>";
            echo "<p><strong>Usuario:</strong> {$user['usuario']}</p>";
            echo "<p><strong>Nueva contraseña:</strong> {$nuevaPassword}</p>";
            echo "<p><strong>Hash generado:</strong> " . substr($passwordHash, 0, 30) . "...</p>";
            echo "<p><strong>Longitud del hash:</strong> " . strlen($passwordHash) . " caracteres ✅</p>";
            echo "</div>";
        }
    }
    
    // Mostrar formulario
    echo "<form method='POST'>";
    echo "<div class='form-group'>";
    echo "<label for='usuario_id'>Seleccionar Usuario:</label>";
    echo "<select name='usuario_id' id='usuario_id' required>";
    echo "<option value=''>-- Seleccionar --</option>";
    
    foreach ($usuarios as $usuario) {
        $selected = ($_POST['usuario_id'] ?? '') == $usuario['id'] ? 'selected' : '';
        echo "<option value='{$usuario['id']}' {$selected}>{$usuario['usuario']} ({$usuario['mail']}) - {$usuario['tipo']}</option>";
    }
    
    echo "</select>";
    echo "</div>";
    
    echo "<div class='form-group'>";
    echo "<label for='nueva_password'>Nueva Contraseña:</label>";
    echo "<input type='text' name='nueva_password' id='nueva_password' placeholder='Ingrese la nueva contraseña' required>";
    echo "</div>";
    
    echo "<button type='submit'>🔐 Actualizar Contraseña</button>";
    echo "</form>";
    
    // Mostrar información de ayuda
    echo "<div class='alert alert-info'>";
    echo "<h3>ℹ️ Información:</h3>";
    echo "<ul>";
    echo "<li>La contraseña se guardará como hash bcrypt seguro</li>";
    echo "<li>Puedes usar letras, números y caracteres especiales</li>";
    echo "<li>La longitud recomendada es al menos 6 caracteres</li>";
    echo "<li>Después de actualizar, el usuario podrá hacer login con la nueva contraseña</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

