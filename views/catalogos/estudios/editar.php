<?php
/**
 * Vista: Editar Estudio
 */

// Limpiar variables de sesión para errores
$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old']);
?>

<!-- Encabezado -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Editar Estudio</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/catalogos/estudios') ?>">Estudios</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Errores -->
<?php if (!empty($errores)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Errores de Validación</h5>
    <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Formulario -->
<form method="POST" action="<?= url('/catalogos/estudios/actualizar/' . $estudio['id']) ?>" id="formEditarEstudio">
    
    <!-- Información General -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                Información General
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Código Interno <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="codigo_interno" 
                           class="form-control <?= isset($errores['codigo_interno']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['codigo_interno'] ?? $estudio['codigo_interno']) ?>"
                           required>
                    <?php if (isset($errores['codigo_interno'])): ?>
                        <div class="invalid-feedback"><?= $errores['codigo_interno'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Código LOINC</label>
                    <input type="text" 
                           name="codigo_loinc" 
                           class="form-control" 
                           value="<?= htmlspecialchars($old['codigo_loinc'] ?? $estudio['codigo_loinc'] ?? '') ?>"
                           placeholder="Opcional">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Nombre del Estudio <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="nombre" 
                           class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['nombre'] ?? $estudio['nombre']) ?>"
                           required>
                    <?php if (isset($errores['nombre'])): ?>
                        <div class="invalid-feedback"><?= $errores['nombre'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Nombre Corto</label>
                    <input type="text" 
                           name="nombre_corto" 
                           class="form-control" 
                           value="<?= htmlspecialchars($old['nombre_corto'] ?? $estudio['nombre_corto'] ?? '') ?>">
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" 
                              class="form-control" 
                              rows="3"><?= htmlspecialchars($old['descripcion'] ?? $estudio['descripcion'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Clasificación -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-tags me-2 text-success"></i>
                Clasificación y Metodología
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Área <span class="text-danger">*</span></label>
                    <select name="area_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($areas as $area): ?>
                        <option value="<?= $area['id'] ?>" 
                                <?= ($estudio['area_id'] == $area['id']) ? 'selected' : '' ?>>
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
                                <?= ($estudio['tipo_muestra_id'] == $tipo['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tipo['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Metodología</label>
                    <select name="metodologia_id" class="form-select">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($metodologias as $met): ?>
                        <option value="<?= $met['id'] ?>" 
                                <?= ($estudio['metodologia_id'] == $met['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($met['nombre']) ?>
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
                                <?= ($estudio['departamento_id'] == $depto['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($depto['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Días de Proceso</label>
                    <input type="number" 
                           name="dias_proceso" 
                           class="form-control" 
                           value="<?= $old['dias_proceso'] ?? $estudio['dias_proceso'] ?? 1 ?>"
                           min="1" max="30">
                </div>
                
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="activo" 
                               id="activo"
                               <?= ($old['activo'] ?? $estudio['activo']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activo">
                            Estudio Activo
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subrogación -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-exchange-alt me-2 text-warning"></i>
                Subrogación
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="es_subrogado" 
                               id="es_subrogado"
                               <?= ($old['es_subrogado'] ?? $estudio['es_subrogado']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="es_subrogado">
                            <strong>Estudio Subrogado</strong>
                        </label>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <label class="form-label">Laboratorio de Referencia</label>
                    <select name="laboratorio_referencia_id" class="form-select">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($laboratorios as $lab): ?>
                        <option value="<?= $lab['id'] ?>" 
                                <?= ($estudio['laboratorio_referencia_id'] == $lab['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lab['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicaciones -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-clipboard-list me-2 text-info"></i>
                Indicaciones para el Paciente
            </h5>
        </div>
        <div class="card-body">
            <textarea name="indicaciones_paciente" 
                      class="form-control" 
                      rows="4"><?= htmlspecialchars($old['indicaciones_paciente'] ?? $estudio['indicaciones_paciente'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Botones -->
    <div class="card shadow-sm">
        <div class="card-footer bg-white">
            <div class="row">
                <div class="col-md-6">
                    <a href="<?= url('/catalogos/estudios') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Cancelar
                    </a>
                </div>
                <div class="col-md-6 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Actualizar Estudio
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>

<!-- Script para confirmación al salir sin guardar -->
<script>
let formModificado = false;

document.getElementById('formEditarEstudio').addEventListener('change', function() {
    formModificado = true;
});

document.getElementById('formEditarEstudio').addEventListener('submit', function() {
    formModificado = false;
});

window.addEventListener('beforeunload', function (e) {
    if (formModificado) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>