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

<!-- Agregar JS específico para encuestas -->
<?php if (!$isAdmin && $esEditable): ?>
<script>
// Datos de la encuesta
const encuestaDid = <?= $encuesta['did'] ?>;
const csrfToken = '<?= csrf_token() ?>';

// Datos ya cargados
const montosYaCargados = <?= json_encode($montosYaCargados) ?>;

// Cargar montos previos en los inputs
document.addEventListener('DOMContentLoaded', function() {
    for (let key in montosYaCargados) {
        const input = document.querySelector(`input[data-key="${key}"]`);
        if (input) {
            input.value = montosYaCargados[key];
        }
    }
});

// Guardar precio
async function guardarPrecio(articuloDid, mercadoDid, tipo) {
    const key = `${articuloDid}-${mercadoDid}-${tipo}`;
    const input = document.querySelector(`input[data-key="${key}"]`);
    
    if (!input) return;
    
    const monto = input.value;
    
    try {
        const response = await fetchCapa('<?= route('/encuestas/guardar-precio') ?>', {
            method: 'POST',
            body: JSON.stringify({
                csrf_token: csrfToken,
                encuestaDid: encuestaDid,
                articuloDid: articuloDid,
                mercadoDid: mercadoDid,
                tipo: tipo,
                monto: monto
            })
        });
        
        if (response.success) {
            input.classList.add('is-valid');
            setTimeout(() => input.classList.remove('is-valid'), 2000);
        } else {
            showToast(response.message || 'Error al guardar', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al guardar el precio', 'danger');
    }
}

// Toggle artículo
async function toggleArticulo(articuloDid, checkbox) {
    try {
        const response = await fetchCapa('<?= route('/encuestas/toggle-articulo') ?>', {
            method: 'POST',
            body: JSON.stringify({
                csrf_token: csrfToken,
                articuloDid: articuloDid
            })
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            // Actualizar estado del checkbox
            checkbox.checked = response.habilitado == 1;
        } else {
            showToast(response.message || 'Error al actualizar', 'danger');
            // Revertir checkbox
            checkbox.checked = !checkbox.checked;
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al actualizar el artículo', 'danger');
        checkbox.checked = !checkbox.checked;
    }
}
</script>
<?php endif; ?>

