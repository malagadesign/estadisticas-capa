<?php
/**
 * Archivo para mostrar encuestas anteriores
 * Versión segura - 8 de Octubre, 2025
 */

// Incluir conector seguro
require_once __DIR__ . '/../conector.php';

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['ScapaUsuarioTipo']) || $_SESSION['ScapaUsuarioTipo'] != 'adm') {
    header("Location: ../login-register.php");
    exit();
}

// Consulta para obtener encuestas anteriores
$query = "SELECT * FROM encuestas WHERE superado = 0 AND elim = 0 ORDER BY autofecha DESC";
$result = $mysqli->query($query);

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Encuestas Anteriores</h4>
                </div>
                <div class="card-body">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['did']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($row['desde']); ?></td>
                                            <td><?php echo htmlspecialchars($row['hasta']); ?></td>
                                            <td>
                                                <?php if ($row['habilitado'] == 1): ?>
                                                    <span class="badge badge-success">Activa</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Inactiva</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="?qh=ultimoadm&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                                    Ver Detalles
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            No hay encuestas anteriores disponibles.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Cerrar conexión
if (isset($result)) {
    $result->close();
}
?>
