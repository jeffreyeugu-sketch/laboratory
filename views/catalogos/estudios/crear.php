<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('catalogos/estudios') ?>">Catálogo de Estudios</a></li>
                <li class="breadcrumb-item active">Nuevo Estudio</li>
            </ol>
        </nav>
        <h2 class="mb-1">
            <i class="fas fa-vial text-primary me-2"></i>
            Nuevo Estudio
        </h2>
        <p class="text-muted">Registrar nuevo estudio de laboratorio</p>
    </div>
</div>

<?php if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Errores en el formulario</h5>
    <ul class="mb-0">
        <?php foreach ($_SESSION['errores'] as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
    unset($_SESSION['errores']);
endif; 
?>

<form action="<?= url('catalogos/estudios/guardar') ?>" method="POST" id="formEstudio">
    
    <!-- Información Básica -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-info-circle me-2 text-primary"></i>
            Información Básica
        </h5>
        
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Código Interno <span class="text-danger">*</span></label>
                <input type="text" 
                       name="codigo_interno" 
                       class="form-control" 
                       value="<?= htmlspecialchars($_SESSION['old']['codigo_interno'] ?? '') ?>"
                       placeholder="Ej: QC001"
                       required>
                <small class="text-muted">Código único del estudio</small>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Código LOINC</label>
                <input type="text" 
                       name="codigo_loinc" 
                       class="form-control" 
                       value="<?= htmlspecialchars($_SESSION['old']['codigo_loinc'] ?? '') ?>"
                       placeholder="Opcional">
                <small class="text-muted">Código de nomenclatura estándar</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Nombre del Estudio <span class="text-danger">*</span></label>
                <input type="text" 
                       name="nombre" 
                       class="form-control" 
                       value="<?= htmlspecialchars($_SESSION['old']['nombre'] ?? '') ?>"
                       placeholder="Ej: Glucosa en Sangre"
                       required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Nombre Corto</label>
                <input type="text" 
                       name="nombre_corto" 
                       class="form-control" 
                       value="<?= htmlspecialchars($_SESSION['old']['nombre_corto'] ?? '') ?>"
                       placeholder="Ej: GLUCOSA">
                <small class="text-muted">Nombre abreviado para reportes</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" 
                          class="form-control" 
                          rows="3"
                          placeholder="Descripción opcional del estudio"><?= htmlspecialchars($_SESSION['old']['descripcion'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Clasificación -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-tags me-2 text-success"></i>
            Clasificación y Metodología
        </h5>
        
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Área <span class="text-danger">*</span></label>
                <select name="area_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($areas as $area): ?>
                    <option value="<?= $area['id'] ?>" 
                            <?= (($_SESSION['old']['area_id'] ?? '') == $area['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($area['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Tipo de Muestra <span class="text-danger">*</span></label>
                <select name="tipo_muestra_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($tipos_muestra as $tipo): ?>
                    <option value="<?= $tipo['id'] ?>" 
                            <?= (($_SESSION['old']['tipo_muestra_id'] ?? '') == $tipo['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tipo['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Metodología</label>
                <select name="metodologia_id" class="form-select">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($metodologias as $metodologia): ?>
                    <option value="<?= $metodologia['id'] ?>" 
                            <?= (($_SESSION['old']['metodologia_id'] ?? '') == $metodologia['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($metodologia['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Departamento</label>
                <select name="departamento_id" class="form-select">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($departamentos as $depto): ?>
                    <option value="<?= $depto['id'] ?>" 
                            <?= (($_SESSION['old']['departamento_id'] ?? '') == $depto['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($depto['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Volumen Requerido</label>
                <input type="text" 
                       name="volumen_requerido" 
                       class="form-control" 
                       value="<?= htmlspecialchars($_SESSION['old']['volumen_requerido'] ?? '') ?>"
                       placeholder="Ej: 5 ml">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Días de Proceso</label>
                <input type="number" 
                       name="dias_proceso" 
                       class="form-control" 
                       value="<?= htmlspecialchars($_SESSION['old']['dias_proceso'] ?? '1') ?>"
                       min="1"
                       max="30">
            </div>
        </div>
    </div>

    <!-- Subrogación -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-exchange-alt me-2 text-warning"></i>
            Subrogación (Laboratorio de Referencia)
        </h5>
        
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="es_subrogado" 
                           id="es_subrogado"
                           <?= isset($_SESSION['old']['es_subrogado']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="es_subrogado">
                        <strong>Este estudio se envía a laboratorio externo</strong>
                    </label>
                </div>
            </div>
            
            <div class="col-md-8">
                <label class="form-label">Laboratorio de Referencia</label>
                <select name="laboratorio_referencia_id" class="form-select" id="laboratorio_referencia">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($laboratorios as $lab): ?>
                    <option value="<?= $lab['id'] ?>" 
                            <?= (($_SESSION['old']['laboratorio_referencia_id'] ?? '') == $lab['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lab['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Indicaciones -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-clipboard-list me-2 text-info"></i>
            Indicaciones para el Paciente
        </h5>
        
        <div class="row">
            <div class="col-12">
                <textarea name="indicaciones_paciente" 
                          class="form-control" 
                          rows="4"
                          placeholder="Indicaciones de preparación para el estudio (ayuno, restricciones, etc.)"><?= htmlspecialchars($_SESSION['old']['indicaciones_paciente'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Estado -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-toggle-on me-2 text-secondary"></i>
            Estado
        </h5>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="activo" 
                           id="activo"
                           checked>
                    <label class="form-check-label" for="activo">
                        <strong>Estudio activo</strong>
                    </label>
                </div>
                <small class="text-muted">Los estudios inactivos no aparecerán al crear órdenes</small>
            </div>
        </div>
    </div>

    <!-- Botones -->
    <div class="d-flex justify-content-between mb-4">
        <a href="<?= url('catalogos/estudios') ?>" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i>
            Cancelar
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>
            Guardar Estudio
        </button>
    </div>
</form>

<?php unset($_SESSION['old']); ?>

<script>
// Habilitar/deshabilitar laboratorio de referencia según checkbox
document.getElementById('es_subrogado').addEventListener('change', function() {
    document.getElementById('laboratorio_referencia').disabled = !this.checked;
});

// Inicializar estado
document.addEventListener('DOMContentLoaded', function() {
    const esSubrogado = document.getElementById('es_subrogado').checked;
    document.getElementById('laboratorio_referencia').disabled = !esSubrogado;
});
</script>
