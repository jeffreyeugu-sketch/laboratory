<?php
/**
 * Vista: Ver Detalles del Laboratorio de Referencia (Solo lectura)
 */
?>

<!-- Contenido Principal -->
<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/catalogos/laboratorios-referencia') ?>">Laboratorios de Referencia</a></li>
            <li class="breadcrumb-item active">Detalle</li>
        </ol>
    </nav>

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><?= htmlspecialchars($laboratorio['nombre']) ?></h2>
            <p class="text-muted mb-0">
                <code><?= htmlspecialchars($laboratorio['codigo']) ?></code>
                <?php if ($laboratorio['activo']): ?>
                    <span class="badge bg-success ms-2">Activo</span>
                <?php else: ?>
                    <span class="badge bg-secondary ms-2">Inactivo</span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <a href="<?= url('/catalogos/laboratorios-referencia/editar/' . $laboratorio['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="<?= url('/catalogos/laboratorios-referencia') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            
            <!-- DATOS GENERALES -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Datos Generales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Código:</label>
                            <p class="fw-bold"><code><?= htmlspecialchars($laboratorio['codigo']) ?></code></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nombre:</label>
                            <p class="fw-bold"><?= htmlspecialchars($laboratorio['nombre']) ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label text-muted">Razón Social:</label>
                            <p><?= htmlspecialchars($laboratorio['razon_social'] ?? 'No especificada') ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">RFC:</label>
                            <p><?= htmlspecialchars($laboratorio['rfc'] ?? 'No especificado') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DIRECCIÓN -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Dirección
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($laboratorio['direccion']) || !empty($laboratorio['ciudad'])): ?>
                        <div class="mb-2">
                            <strong>Dirección:</strong><br>
                            <?= htmlspecialchars($laboratorio['direccion'] ?? 'No especificada') ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Ciudad:</strong><br>
                                <?= htmlspecialchars($laboratorio['ciudad'] ?? 'No especificada') ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Estado:</strong><br>
                                <?= htmlspecialchars($laboratorio['estado'] ?? 'N/A') ?>
                            </div>
                            <div class="col-md-3">
                                <strong>C.P.:</strong><br>
                                <?= htmlspecialchars($laboratorio['codigo_postal'] ?? 'N/A') ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0"><em>No hay información de dirección registrada</em></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- CONTACTO -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-phone me-2"></i>Información de Contacto
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">Contacto General</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <strong>Teléfono:</strong><br>
                            <span class="ms-4"><?= htmlspecialchars($laboratorio['telefono'] ?? 'No especificado') ?></span>
                        </div>
                        <div class="col-md-6">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <strong>Email:</strong><br>
                            <span class="ms-4"><?= htmlspecialchars($laboratorio['email'] ?? 'No especificado') ?></span>
                        </div>
                    </div>

                    <?php if (!empty($laboratorio['contacto_nombre'])): ?>
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Persona de Contacto</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <i class="fas fa-user text-success me-2"></i>
                                <strong>Nombre:</strong><br>
                                <span class="ms-4"><?= htmlspecialchars($laboratorio['contacto_nombre']) ?></span>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-phone text-success me-2"></i>
                                <strong>Teléfono:</strong><br>
                                <span class="ms-4"><?= htmlspecialchars($laboratorio['contacto_telefono'] ?? 'N/A') ?></span>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-envelope text-success me-2"></i>
                                <strong>Email:</strong><br>
                                <span class="ms-4"><?= htmlspecialchars($laboratorio['contacto_email'] ?? 'N/A') ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- OBSERVACIONES -->
            <?php if (!empty($laboratorio['observaciones'])): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sticky-note me-2"></i>Observaciones
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($laboratorio['observaciones'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            
            <!-- Información del Registro -->
            <div class="card shadow-sm border-secondary mb-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información del Registro
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">ID:</td>
                            <td><strong>#<?= $laboratorio['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado:</td>
                            <td>
                                <?php if ($laboratorio['activo']): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Entrega:</td>
                            <td><strong><?= $laboratorio['dias_entrega_promedio'] ?? 3 ?> días</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Creado:</td>
                            <td class="small"><?= isset($laboratorio['fecha_creacion']) ? date('d/m/Y H:i', strtotime($laboratorio['fecha_creacion'])) : 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Modificado:</td>
                            <td class="small"><?= isset($laboratorio['fecha_modificacion']) ? date('d/m/Y H:i', strtotime($laboratorio['fecha_modificacion'])) : 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card shadow-sm border-warning mb-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Estudios Subrogados:</span>
                        <span class="badge bg-primary fs-6"><?= $estadisticas['total_estudios'] ?? 0 ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Estudios Activos:</span>
                        <span class="badge bg-success fs-6"><?= $estadisticas['estudios_activos'] ?? 0 ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Estudios Inactivos:</span>
                        <span class="badge bg-secondary fs-6"><?= ($estadisticas['total_estudios'] ?? 0) - ($estadisticas['estudios_activos'] ?? 0) ?></span>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= url('/catalogos/laboratorios-referencia/editar/' . $laboratorio['id']) ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Laboratorio
                        </a>
                        <button type="button" class="btn btn-outline-<?= $laboratorio['activo'] ? 'secondary' : 'success' ?>" id="btnCambiarEstado">
                            <i class="fas fa-power-off me-2"></i>
                            <?= $laboratorio['activo'] ? 'Desactivar' : 'Activar' ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script para cambiar estado -->
<script>
$(document).ready(function() {
    
    $('#btnCambiarEstado').on('click', function() {
        const labId = <?= $laboratorio['id'] ?>;
        const labNombre = '<?= htmlspecialchars($laboratorio['nombre']) ?>';
        const estadoActual = <?= $laboratorio['activo'] ? 'true' : 'false' ?>;
        const nuevoEstadoTexto = estadoActual ? 'desactivar' : 'activar';
        
        Swal.fire({
            title: '¿Estás seguro?',
            html: `¿Deseas ${nuevoEstadoTexto} el laboratorio:<br><strong>${labNombre}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: estadoActual ? '#6c757d' : '#28a745',
            cancelButtonColor: '#3085d6',
            confirmButtonText: `Sí, ${nuevoEstadoTexto}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= url('/catalogos/laboratorios-referencia/cambiar-estado/') ?>' + labId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cambiar el estado'
                    });
                });
            }
        });
    });
    
});
</script>