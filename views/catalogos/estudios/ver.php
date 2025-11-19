<?php
/**
 * Vista: Detalle de Estudio
 */
?>

<!-- Encabezado -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Detalle del Estudio</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/catalogos/estudios') ?>">Estudios</a></li>
                <li class="breadcrumb-item active">Detalle</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('/catalogos/estudios/editar/' . $estudio['id']) ?>" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>
            Editar
        </a>
        <a href="<?= url('/catalogos/estudios') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Volver
        </a>
    </div>
</div>

<!-- Información General -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>
            Información General
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Código Interno</label>
                <p class="fw-bold"><?= htmlspecialchars($estudio['codigo_interno']) ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Código LOINC</label>
                <p class="fw-bold"><?= htmlspecialchars($estudio['codigo_loinc'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Nombre</label>
                <p class="fw-bold"><?= htmlspecialchars($estudio['nombre']) ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Nombre Corto</label>
                <p class="fw-bold"><?= htmlspecialchars($estudio['nombre_corto'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-12 mb-3">
                <label class="text-muted small">Descripción</label>
                <p><?= nl2br(htmlspecialchars($estudio['descripcion'] ?? 'Sin descripción')) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Clasificación -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-tags me-2"></i>
            Clasificación
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Área</label>
                <p class="fw-bold">
                    <span class="badge bg-info"><?= htmlspecialchars($estudio['area_nombre'] ?? 'N/A') ?></span>
                </p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Tipo de Muestra</label>
                <p class="fw-bold"><?= htmlspecialchars($estudio['tipo_muestra_nombre'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Metodología</label>
                <p class="fw-bold"><?= htmlspecialchars($estudio['metodologia_nombre'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Departamento</label>
                <p class="fw-bold"><?= htmlspecialchars($estudio['departamento_nombre'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Días de Proceso</label>
                <p class="fw-bold"><?= $estudio['dias_proceso'] ?? 1 ?> día(s)</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Estado</label>
                <p>
                    <?php if ($estudio['activo']): ?>
                        <span class="badge bg-success">Activo</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactivo</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Subrogación -->
<?php if ($estudio['es_subrogado']): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-exchange-alt me-2"></i>
            Subrogación
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Laboratorio de Referencia</label>
                <p class="fw-bold"><?= htmlspecialchars($estudio['laboratorio_referencia_nombre'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Tipo</label>
                <p><span class="badge bg-warning text-dark">Estudio Subrogado</span></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Indicaciones -->
<?php if (!empty($estudio['indicaciones_paciente'])): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-clipboard-list me-2"></i>
            Indicaciones para el Paciente
        </h5>
    </div>
    <div class="card-body">
        <p><?= nl2br(htmlspecialchars($estudio['indicaciones_paciente'])) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Auditoría -->
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>
            Información de Auditoría
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Creado</label>
                <p><?= date('d/m/Y H:i', strtotime($estudio['created_at'])) ?></p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Última Actualización</label>
                <p><?= date('d/m/Y H:i', strtotime($estudio['updated_at'])) ?></p>
            </div>
        </div>
    </div>
</div>