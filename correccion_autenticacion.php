<?php
/**
 * CORRECCIÓN DEL SISTEMA DE AUTENTICACIÓN
 * Maneja contraseñas en texto plano y con hash
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔐 CORRECCIÓN DEL SISTEMA DE AUTENTICACIÓN</h1>";
echo "<p>🔍 Corrigiendo el problema de credenciales incorrectas...</p>";

// ============================================
// PASO 1: CONECTAR A BASE DE DATOS
// ============================================

echo "<h2>🔗 PASO 1: Conectando a base de datos</h2>";

try {
    $mysqli = new mysqli('localhost', 'encuesta_capa', 'Malaga77', 'encuesta_capa');
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa a la base de datos</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    exit;
}

// ============================================
// PASO 2: VERIFICAR USUARIOS EXISTENTES
// ============================================

echo "<h2>👥 PASO 2: Verificando usuarios existentes</h2>";

$sql_usuarios = "SELECT did, usuario, psw, tipo, habilitado FROM usuarios WHERE tipo = 'adm' AND elim = 0 ORDER BY did";
$result_usuarios = $mysqli->query($sql_usuarios);

if ($result_usuarios) {
    echo "<p>📊 Usuarios administrativos encontrados:</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>DID</th><th>Usuario</th><th>Tipo Contraseña</th><th>Habilitado</th></tr>";
    
    $usuarios_texto_plano = [];
    
    while ($row = $result_usuarios->fetch_assoc()) {
        $tipo_password = 'Hash';
        if (strlen($row['psw']) < 60) {
            $tipo_password = 'Texto Plano';
            $usuarios_texto_plano[] = $row;
        }
        
        echo "<tr>";
        echo "<td>" . $row['did'] . "</td>";
        echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
        echo "<td>" . $tipo_password . "</td>";
        echo "<td>" . ($row['habilitado'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if (count($usuarios_texto_plano) > 0) {
        echo "<p>⚠️ <strong>Problema identificado:</strong> " . count($usuarios_texto_plano) . " usuarios tienen contraseñas en texto plano</p>";
    } else {
        echo "<p>✅ Todos los usuarios tienen contraseñas con hash</p>";
    }
    
} else {
    echo "<p>❌ Error al consultar usuarios: " . $mysqli->error . "</p>";
}

// ============================================
// PASO 3: CREAR LOGIN.PHP CORREGIDO
// ============================================

echo "<h2>🔧 PASO 3: Creando login.php corregido</h2>";

$login_corregido_content = '<?php
/**
 * LOGIN.PHP CORREGIDO - Maneja contraseñas en texto plano y con hash
 */

require_once __DIR__ . \'/config/app.php\';
require_once __DIR__ . \'/core/Database.php\';
require_once __DIR__ . \'/core/Session.php\';
require_once __DIR__ . \'/core/Request.php\';

Session::start();

if ($_SERVER[\'REQUEST_METHOD\'] === \'POST\') {
    $usuario = Request::post(\'usuario\');
    $password = Request::post(\'password\');
    
    if (empty($usuario) || empty($password)) {
        echo json_encode([\'success\' => false, \'message\' => \'Usuario y contraseña requeridos\']);
        exit;
    }
    
    try {
        $db = Database::getInstance();
        
        // Buscar usuario
        $user = $db->fetchOne(
            "SELECT * FROM usuarios WHERE usuario = ? AND elim = 0 AND superado = 0 LIMIT 1",
            [\'s\', $usuario]
        );
        
        if ($user) {
            $password_valid = false;
            
            // Verificar contraseña - manejar tanto texto plano como hash
            if (strlen($user[\'psw\']) >= 60) {
                // Contraseña con hash
                $password_valid = password_verify($password, $user[\'psw\']);
            } else {
                // Contraseña en texto plano (compatibilidad con sistema viejo)
                $password_valid = ($password === $user[\'psw\']);
            }
            
            if ($password_valid) {
                // Login exitoso
                Session::set(\'user_id\', $user[\'did\']);
                Session::set(\'user_name\', $user[\'usuario\']);
                Session::set(\'user_type\', $user[\'tipo\']);
                Session::set(\'user_logged\', true);
                
                echo json_encode([\'success\' => true, \'message\' => \'Login exitoso\']);
            } else {
                echo json_encode([\'success\' => false, \'message\' => \'Credenciales incorrectas\']);
            }
        } else {
            echo json_encode([\'success\' => false, \'message\' => \'Usuario no encontrado\']);
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode([\'success\' => false, \'message\' => \'Error interno\']);
    }
} else {
    echo json_encode([\'success\' => false, \'message\' => \'Método no permitido\']);
}
?>';

if (file_put_contents('v2/login.php', $login_corregido_content)) {
    echo "<p>✅ Archivo v2/login.php corregido</p>";
} else {
    echo "<p>❌ Error al corregir v2/login.php</p>";
}

// ============================================
// PASO 4: CREAR INDEX-WORKING.PHP CORREGIDO
// ============================================

echo "<h2>📄 PASO 4: Creando index-working.php corregido</h2>";

$index_corregido_content = '<?php
/**
 * CAPA Encuestas v2.0 - Entry Point Funcional CORREGIDO
 * Versión que funciona sin routing complejo de Apache
 */

// Configuración básica
ini_set(\'display_errors\', \'1\');
error_reporting(E_ALL);

// Cargar configuración
require_once __DIR__ . \'/config/app.php\';

// Cargar clases del core
require_once __DIR__ . \'/core/Router.php\';
require_once __DIR__ . \'/core/Database.php\';
require_once __DIR__ . \'/core/View.php\';
require_once __DIR__ . \'/core/Request.php\';
require_once __DIR__ . \'/core/Session.php\';

// Iniciar sesión
Session::start();

// Verificar si es una petición AJAX o POST
if ($_SERVER[\'REQUEST_METHOD\'] === \'POST\' || !empty($_POST)) {
    // Procesar peticiones POST directamente
    $router = new Router();
    require_once __DIR__ . \'/config/routes.php\';
    
    try {
        $router->dispatch(
            Request::url(),
            Request::method()
        );
    } catch (Exception $e) {
        error_log("Dispatch error: " . $e->getMessage());
        echo json_encode([\'error\' => $e->getMessage()]);
    }
    exit;
}

// Para peticiones GET, mostrar la página principal
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAPA Encuestas v2.0</title>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --capa-azul-oscuro: #001A4D;
            --capa-purpura: #9D4EDD;
            --capa-purpura-claro: #C084FC;
            --capa-purpura-oscuro: #6B21A8;
        }
        
        .bg-capa {
            background: linear-gradient(135deg, var(--capa-azul-oscuro) 0%, var(--capa-purpura) 100%);
        }
        
        .btn-capa {
            background-color: var(--capa-purpura);
            border-color: var(--capa-purpura);
            color: white;
        }
        
        .btn-capa:hover {
            background-color: var(--capa-purpura-oscuro);
            border-color: var(--capa-purpura-oscuro);
            color: white;
        }
        
        .text-capa {
            color: var(--capa-purpura);
        }
        
        .card-capa {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--capa-purpura);
            box-shadow: 0 0 0 0.2rem rgba(157, 78, 221, 0.25);
        }
    </style>
</head>
<body class="bg-capa min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card card-capa">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h3 text-capa mb-3">
                                <i class="fas fa-chart-line me-2"></i>
                                CAPA Encuestas v2.0
                            </h1>
                            <p class="text-muted">Sistema moderno de gestión de encuestas</p>
                        </div>
                        
                        <?php
                        // Verificar si hay sesión activa
                        if (Session::isLoggedIn()) {
                            // Usuario logueado - mostrar dashboard
                            $userType = Session::get(\'user_type\');
                            $userName = Session::get(\'user_name\');
                            
                            echo "<div class=\"alert alert-success\">";
                            echo "<h5><i class=\"fas fa-check-circle me-2\"></i>¡Bienvenido!</h5>";
                            echo "<p>Usuario: <strong>$userName</strong></p>";
                            echo "<p>Tipo: <strong>" . ($userType === \'adm\' ? \'Administrador\' : \'Socio\') . "</strong></p>";
                            echo "</div>";
                            
                            echo "<div class=\"d-grid gap-2\">";
                            echo "<a href=\"dashboard.php\" class=\"btn btn-capa btn-lg\">";
                            echo "<i class=\"fas fa-tachometer-alt me-2\"></i>Ir al Dashboard";
                            echo "</a>";
                            
                            if ($userType === \'adm\') {
                                echo "<a href=\"usuarios.php\" class=\"btn btn-outline-capa\">";
                                echo "<i class=\"fas fa-users me-2\"></i>Gestión de Usuarios";
                                echo "</a>";
                                
                                echo "<a href=\"config.php\" class=\"btn btn-outline-capa\">";
                                echo "<i class=\"fas fa-cog me-2\"></i>Configuración";
                                echo "</a>";
                            }
                            
                            echo "<a href=\"logout.php\" class=\"btn btn-outline-secondary\">";
                            echo "<i class=\"fas fa-sign-out-alt me-2\"></i>Cerrar Sesión";
                            echo "</a>";
                            echo "</div>";
                            
                        } else {
                            // Usuario no logueado - mostrar formulario de login
                            ?>
                            <form id="loginForm" method="POST" action="login.php">
                                <?= csrf_field() ?>
                                
                                <div class="mb-3">
                                    <label for="usuario" class="form-label">
                                        <i class="fas fa-user me-2"></i>Usuario
                                    </label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" 
                                           placeholder="Ingrese su usuario" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Contraseña
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Ingrese su contraseña" required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-capa btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-4 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Sistema moderno con Bootstrap 5 y diseño responsive
                                </small>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-white-50">
                        CAPA - Cámara Argentina de Productores Avícolas<br>
                        Versión 2.0 - Sistema Moderno
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Manejar formulario de login
        document.getElementById(\'loginForm\')?.addEventListener(\'submit\', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const btn = this.querySelector(\'button[type="submit"]\');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = \'<span class="spinner-border spinner-border-sm me-2"></span>Iniciando...\';
            
            fetch(\'login.php\', {
                method: \'POST\',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(\'Error: \' + (data.message || \'Credenciales incorrectas\'));
                }
            })
            .catch(error => {
                console.error(\'Error:\', error);
                alert(\'Error de conexión\');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });
    </script>
</body>
</html>
';

if (file_put_contents('v2/index-working.php', $index_corregido_content)) {
    echo "<p>✅ Archivo v2/index-working.php corregido</p>";
} else {
    echo "<p>❌ Error al corregir v2/index-working.php</p>";
}

// ============================================
// PASO 5: CREAR SCRIPT DE MIGRACIÓN DE CONTRASEÑAS
// ============================================

echo "<h2>🔐 PASO 5: Creando script de migración de contraseñas</h2>";

$migracion_passwords_content = '<?php
/**
 * MIGRACIÓN DE CONTRASEÑAS A HASH
 * Convierte contraseñas en texto plano a hash seguro
 */

error_reporting(E_ALL);
ini_set(\'display_errors\', 1);

echo "<h1>🔐 MIGRACIÓN DE CONTRASEÑAS A HASH</h1>";
echo "<p>🔍 Convirtiendo contraseñas en texto plano a hash seguro...</p>";

// Conectar a base de datos
try {
    $mysqli = new mysqli(\'localhost\', \'encuesta_capa\', \'Malaga77\', \'encuesta_capa\');
    
    if ($mysqli->connect_error) {
        echo "<p style=\'color: red;\'>❌ Error de conexión: " . $mysqli->connect_error . "</p>";
        exit;
    }
    
    echo "<p>✅ Conexión exitosa a la base de datos</p>";
    
} catch (Exception $e) {
    echo "<p style=\'color: red;\'>❌ Error: " . $e->getMessage() . "</p>";
    exit;
}

// Buscar usuarios con contraseñas en texto plano
$sql = "SELECT did, usuario, psw FROM usuarios WHERE LENGTH(psw) < 60 AND elim = 0";
$result = $mysqli->query($sql);

if ($result) {
    $usuarios_migrar = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios_migrar[] = $row;
    }
    
    if (count($usuarios_migrar) > 0) {
        echo "<p>⚠️ Encontrados " . count($usuarios_migrar) . " usuarios con contraseñas en texto plano:</p>";
        
        echo "<table border=\'1\' style=\'border-collapse: collapse; width: 100%;\'>";
        echo "<tr><th>DID</th><th>Usuario</th><th>Contraseña Actual</th><th>Estado</th></tr>";
        
        foreach ($usuarios_migrar as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario[\'did\'] . "</td>";
            echo "<td>" . htmlspecialchars($usuario[\'usuario\']) . "</td>";
            echo "<td>" . htmlspecialchars($usuario[\'psw\']) . "</td>";
            
            // Convertir a hash
            $password_hash = password_hash($usuario[\'psw\'], PASSWORD_BCRYPT);
            
            $sql_update = "UPDATE usuarios SET psw = ? WHERE did = ?";
            $stmt_update = $mysqli->prepare($sql_update);
            $stmt_update->bind_param(\'si\', $password_hash, $usuario[\'did\']);
            
            if ($stmt_update->execute()) {
                echo "<td style=\'color: green;\'>✅ Migrado</td>";
            } else {
                echo "<td style=\'color: red;\'>❌ Error</td>";
            }
            $stmt_update->close();
            
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p style=\'color: green; font-weight: bold;\'>🎉 Migración completada</p>";
        
    } else {
        echo "<p>✅ No hay usuarios con contraseñas en texto plano</p>";
    }
} else {
    echo "<p>❌ Error al consultar usuarios: " . $mysqli->error . "</p>";
}

$mysqli->close();

echo "<hr>";
echo "<p><strong>📝 Nota:</strong> Las contraseñas han sido convertidas a hash seguro.</p>";
echo "<p><strong>🔒 Seguridad:</strong> Ahora el sistema es más seguro.</p>";
?>';

if (file_put_contents('migracion_passwords.php', $migracion_passwords_content)) {
    echo "<p>✅ Archivo migracion_passwords.php creado</p>";
} else {
    echo "<p>❌ Error al crear migracion_passwords.php</p>";
}

// ============================================
// PASO 6: RESUMEN Y RECOMENDACIONES
// ============================================

echo "<h2>📋 PASO 6: Resumen y recomendaciones</h2>";

echo "<p style=\"color: green; font-weight: bold;\">🎉 CORRECCIÓN DE AUTENTICACIÓN COMPLETADA</p>";

echo "<h3>🔧 Problema identificado:</h3>";
echo "<p>El sistema nuevo usa <code>password_verify()</code> pero los usuarios existentes tienen contraseñas en texto plano.</p>";

echo "<h3>✅ Solución implementada:</h3>";
echo "<ol>";
echo "<li><strong>Login.php corregido:</strong> Maneja tanto contraseñas en texto plano como con hash</li>";
echo "<li><strong>Index-working.php mejorado:</strong> Diseño más moderno y funcional</li>";
echo "<li><strong>Script de migración:</strong> Para convertir contraseñas a hash seguro</li>";
echo "</ol>";

echo "<h3>🔗 Archivos corregidos:</h3>";
echo "<ul>";
echo "<li><a href=\"v2/login.php\">v2/login.php</a> - Sistema de autenticación corregido</li>";
echo "<li><a href=\"v2/index-working.php\">v2/index-working.php</a> - Página principal mejorada</li>";
echo "<li><a href=\"migracion_passwords.php\">migracion_passwords.php</a> - Migración de contraseñas</li>";
echo "</ul>";

echo "<h3>🚀 Próximos pasos:</h3>";
echo "<ol>";
echo "<li><strong>Probar login:</strong> Accede a <a href=\"v2/index-working.php\">v2/index-working.php</a></li>";
echo "<li><strong>Verificar funcionamiento:</strong> Usar credenciales existentes</li>";
echo "<li><strong>Migrar contraseñas (opcional):</strong> Ejecutar <a href=\"migracion_passwords.php\">migracion_passwords.php</a></li>";
echo "<li><strong>Confirmar funcionamiento:</strong> Probar gestión de usuarios</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>💡 El sistema ahora maneja ambos tipos de contraseñas automáticamente</strong></p>";
echo "<p><strong>🎨 El diseño moderno se mantiene intacto</strong></p>";
?>
