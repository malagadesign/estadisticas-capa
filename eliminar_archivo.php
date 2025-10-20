<?php
/**
 * Script de Eliminación Segura de Archivos
 * Uso: Subir a la misma carpeta donde está el archivo a eliminar
 * Ejecutar desde navegador: http://tudominio.com/ruta/eliminar_archivo.php
 * 
 * IMPORTANTE: Este script se auto-elimina después de ejecutar
 */

// Configuración de seguridad
$password = 'eliminar2024'; // Cambiá esto por una contraseña segura
$archivos_a_eliminar = [
    'shell.php',           // Archivo principal a eliminar
    // Podés agregar más archivos aquí:
    // 'otro_archivo.php',
    // 'backdoor.php',
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminador de Archivos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d9534f;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #bee5eb;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background: #d9534f;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background: #c9302c;
        }
        .file-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .file-list ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗑️ Eliminador de Archivos Sospechosos</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar contraseña
            if (!isset($_POST['password']) || $_POST['password'] !== $password) {
                echo '<div class="error"><strong>❌ Error:</strong> Contraseña incorrecta</div>';
            } else {
                // Contraseña correcta, proceder con eliminación
                $directorio_actual = __DIR__;
                $resultados = [];
                $todos_eliminados = true;
                
                foreach ($archivos_a_eliminar as $archivo) {
                    $ruta_completa = $directorio_actual . '/' . $archivo;
                    
                    if (file_exists($ruta_completa)) {
                        if (unlink($ruta_completa)) {
                            $resultados[] = "✅ <strong>$archivo</strong> eliminado exitosamente";
                        } else {
                            $resultados[] = "❌ <strong>$archivo</strong> NO se pudo eliminar (permisos?)";
                            $todos_eliminados = false;
                        }
                    } else {
                        $resultados[] = "⚠️ <strong>$archivo</strong> no existe en esta carpeta";
                    }
                }
                
                // Mostrar resultados
                foreach ($resultados as $resultado) {
                    if (strpos($resultado, '✅') !== false) {
                        echo '<div class="success">' . $resultado . '</div>';
                    } elseif (strpos($resultado, '❌') !== false) {
                        echo '<div class="error">' . $resultado . '</div>';
                    } else {
                        echo '<div class="info">' . $resultado . '</div>';
                    }
                }
                
                // Auto-eliminarse si todo salió bien
                if ($todos_eliminados && isset($_POST['auto_delete'])) {
                    echo '<div class="warning"><strong>🔄 Auto-eliminación...</strong></div>';
                    
                    // Esperar 3 segundos y auto-eliminarse
                    echo '<script>
                        setTimeout(function() {
                            window.location.href = "?self_destruct=1";
                        }, 3000);
                    </script>';
                    echo '<div class="info">Este script se eliminará en 3 segundos...</div>';
                }
            }
        }
        
        // Auto-eliminación
        if (isset($_GET['self_destruct']) && $_GET['self_destruct'] == '1') {
            $este_archivo = __FILE__;
            if (unlink($este_archivo)) {
                echo '<div class="success"><strong>✅ ¡Listo!</strong> Este script se eliminó correctamente.</div>';
                echo '<div class="info">Ya podés cerrar esta ventana.</div>';
                exit();
            } else {
                echo '<div class="error"><strong>❌ Error:</strong> No se pudo auto-eliminar. Eliminá manualmente: ' . basename($este_archivo) . '</div>';
            }
        }
        ?>
        
        <?php if (!isset($_POST['password']) || $_POST['password'] !== $password): ?>
        <div class="file-list">
            <strong>📁 Directorio actual:</strong><br>
            <code><?php echo htmlspecialchars(__DIR__); ?></code>
            
            <br><br>
            <strong>🗂️ Archivos a eliminar:</strong>
            <ul>
                <?php foreach ($archivos_a_eliminar as $archivo): ?>
                    <?php 
                    $existe = file_exists(__DIR__ . '/' . $archivo);
                    $icono = $existe ? '🔴' : '⚪';
                    $estado = $existe ? '(existe)' : '(no existe)';
                    ?>
                    <li><?php echo $icono; ?> <code><?php echo htmlspecialchars($archivo); ?></code> <?php echo $estado; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="warning">
            <strong>⚠️ ADVERTENCIA:</strong> Esta acción es irreversible. Asegurate de que querés eliminar estos archivos.
        </div>
        
        <form method="POST">
            <label for="password"><strong>Contraseña:</strong></label>
            <input type="password" name="password" id="password" placeholder="Ingresá la contraseña" required autofocus>
            
            <label style="display: block; margin: 15px 0;">
                <input type="checkbox" name="auto_delete" value="1" checked>
                Auto-eliminar este script después de ejecutar
            </label>
            
            <button type="submit">🗑️ Eliminar Archivos</button>
        </form>
        
        <div class="info" style="margin-top: 20px; font-size: 13px;">
            <strong>💡 Instrucciones:</strong><br>
            1. Subí este archivo a la misma carpeta donde está el archivo a eliminar<br>
            2. Accedé desde el navegador<br>
            3. Ingresá la contraseña: <code><?php echo $password; ?></code><br>
            4. El script eliminará los archivos y se auto-eliminará
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
