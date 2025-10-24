<?php
/**
 * USUARIOS.PHP - Gestión de usuarios directa
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

if (!Session::isLoggedIn() || Session::get('user_type') !== 'adm') {
    header('Location: index-working.php');
    exit;
}

$db = Database::getInstance();

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $usuario = trim($_POST['usuario'] ?? '');
        $mail = trim($_POST['mail'] ?? '');
        $habilitado = isset($_POST['habilitado']) ? 1 : 0;
        
        if (empty($usuario) || empty($mail)) {
            $error = 'Usuario y email son requeridos';
        } else {
            try {
                // Verificar que no exista
                $existe = $db->fetchOne(
                    "SELECT COUNT(*) as total FROM usuarios WHERE usuario = ? AND elim = 0",
                    ['s', $usuario]
                );
                
                if ($existe['total'] > 0) {
                    $error = 'El usuario ya existe';
                } else {
                    // Crear usuario
                    $password = 'temp_' . uniqid();
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                    
                    $db->insert(
                        "INSERT INTO usuarios (usuario, mail, psw, tipo, habilitado, superado, elim) VALUES (?, ?, ?, 'adm', ?, 0, 0)",
                        ['sssi', $usuario, $mail, $passwordHash, $habilitado]
                    );
                    
                    $success = 'Usuario creado correctamente';
                }
            } catch (Exception $e) {
                $error = 'Error al crear usuario: ' . $e->getMessage();
            }
        }
    }
    
    if ($action === 'toggle') {
        $did = (int)($_POST['did'] ?? 0);
        $habilitado = (int)($_POST['habilitado'] ?? 0);
        
        if ($did > 0) {
            try {
                $db->query(
                    "UPDATE usuarios SET habilitado = ? WHERE did = ?",
                    ['ii', $habilitado, $did]
                );
                $success = 'Estado actualizado correctamente';
            } catch (Exception $e) {
                $error = 'Error al actualizar estado: ' . $e->getMessage();
            }
        }
    }
}

// Obtener usuarios administrativos
$usuarios = $db->fetchAll(
    "SELECT * FROM usuarios WHERE tipo = 'adm' AND superado = 0 AND elim = 0 ORDER BY usuario ASC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - CAPA Encuestas v2.0</title>
    
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
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-capa">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-chart-line me-2"></i>CAPA Encuestas v2.0
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-capa">
                        <i class="fas fa-users me-2"></i>Gestión de Usuarios Administrativos
                    </h1>
                    <button class="btn btn-capa" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                        <i class="fas fa-plus me-2"></i>Nuevo Usuario
                    </button>
                </div>
                
                <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                </div>
                <?php endif; ?>
                
                <!-- Tabla de usuarios -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($usuarios)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No hay usuarios administrativos
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?= $usuario['did'] ?></span></td>
                                        <td><strong><?= htmlspecialchars($usuario['usuario']) ?></strong></td>
                                        <td><?= htmlspecialchars($usuario['mail']) ?></td>
                                        <td>
                                            <?php if ($usuario['habilitado'] == 1): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Habilitado
                                            </span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-ban me-1"></i>Deshabilitado
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario['did'] != Session::get('user_id')): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="did" value="<?= $usuario['did'] ?>">
                                                <input type="hidden" name="habilitado" value="<?= $usuario['habilitado'] == 1 ? 0 : 1 ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-<?= $usuario['habilitado'] == 1 ? 'warning' : 'success' ?>" 
                                                        onclick="return confirm('¿Está seguro?')">
                                                    <i class="fas fa-<?= $usuario['habilitado'] == 1 ? 'ban' : 'check' ?>"></i>
                                                    <?= $usuario['habilitado'] == 1 ? 'Deshabilitar' : 'Habilitar' ?>
                                                </button>
                                            </form>
                                            <?php else: ?>
                                            <span class="text-muted">Tu usuario</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para crear usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-capa text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Usuario Administrativo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="mail" name="mail" required>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="habilitado" name="habilitado" checked>
                            <label class="form-check-label" for="habilitado">
                                Usuario habilitado
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-capa">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
