<?php
$dias_restantes = 0;
if ($encuesta) {
    $fecha_actual = strtotime(date('Y-m-d'));
    $fecha_hasta = strtotime($encuesta['hasta']);
    $dias_restantes = ($fecha_hasta - $fecha_actual) / 86400;
}
?>

<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold" style="color: var(--capa-azul-oscuro);">
                <i class="fas fa-poll me-2"></i>
                <?= e($encuesta['nombre']) ?>
            </h1>
            <p class="text-muted">
                Período: <?= fecha_format($encuesta['desde']) ?> - <?= fecha_format($encuesta['hasta']) ?>
            </p>
            
            <?php if ($dias_restantes > 0): ?>
                <div class="alert alert-info">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Quedan <?= ceil($dias_restantes) ?> días</strong> para completar la encuesta
                </div>
            <?php elseif ($dias_restantes == 0): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡Último día!</strong> La encuesta finaliza hoy
                </div>
            <?php else: ?>
                <div class="alert alert-secondary">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta encuesta ya finalizó
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($isAdmin): ?>
        <!-- VISTA PARA ADMINISTRADORES -->
        <?php include __DIR__ . '/ultima-admin.php'; ?>
    <?php else: ?>
        <!-- VISTA PARA SOCIOS -->
        <?php include __DIR__ . '/ultima-socio.php'; ?>
    <?php endif; ?>
</div>


