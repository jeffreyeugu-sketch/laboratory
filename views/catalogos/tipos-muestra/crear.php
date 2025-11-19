<?php
/**
 * Vista: Crear Tipo de Muestra
 */

$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old']);
?>

<!-- Encabezado -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Nuevo Tipo de Muestra</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= url('/catalogos/tipos-muestra') ?>">Tipos de Muestra</a></li>
                <li class="breadcrumb-item active">Nuevo</li>
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
<form method="POST" action="<?= url('/catalogos/tipos-muestra/guardar') ?>" id="formCrear">
    
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
                    <label class="form-label">Código <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="codigo" 
                           class="form-control <?= isset($errores['codigo']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['codigo'] ?? '') ?>"
                           placeholder="Ej: SUERO"
                           required>
                    <?php if (isset($errores['codigo'])): ?>
                        <div class="invalid-feedback"><?= $errores['codigo'] ?></div>
                    <?php endif; ?>
                    <small class="text-muted">Código único para identificar el tipo de muestra</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="nombre" 
                           class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                           placeholder="Ej: Suero"
                           required>
                    <?php if (isset($errores['nombre'])): ?>
                        <div class="invalid-feedback"><?= $errores['nombre'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" 
                              class="form-control" 
                              rows="3"
                              placeholder="Descripción del tipo de muestra..."><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Requisitos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-clipboard-check me-2 text-warning"></i>
                Requisitos de la Muestra
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="requiere_ayuno" 
                               id="requiere_ayuno"
                               <?= isset($old['requiere_ayuno']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="requiere_ayuno">
                            <strong>Requiere Ayuno</strong>
                        </label>
                    </div>
                    <small class="text-muted">Marcar si el paciente debe estar en ayunas</small>
                </div>
                
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="requiere_refrigeracion" 
                               id="requiere_refrigeracion"
                               <?= isset($old['requiere_refrigeracion']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="requiere_refrigeracion">
                            <strong>Requiere Refrigeración</strong>
                        </label>
                    </div>
                    <small class="text-muted">Marcar si la muestra debe mantenerse refrigerada</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Almacenamiento -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-thermometer-half me-2 text-info"></i>
                Condiciones de Almacenamiento
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tiempo de Estabilidad (horas)</label>
                    <input type="number" 
                           name="tiempo_estabilidad_horas" 
                           class="form-control" 
                           value="<?= htmlspecialchars($old['tiempo_estabilidad_horas'] ?? '24') ?>"
                           min="1"
                           max="168">
                    <small class="text-muted">Tiempo en horas que la muestra permanece estable</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Temperatura de Almacenamiento</label>
                    <input type="text" 
                           name="temperatura_almacenamiento" 
                           class="form-control" 
                           value="<?= htmlspecialchars($old['temperatura_almacenamiento'] ?? '') ?>"
                           placeholder="Ej: 2-8°C">
                    <small class="text-muted">Rango de temperatura recomendado</small>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Instrucciones de Recolección</label>
                    <textarea name="instrucciones_recoleccion" 
                              class="form-control" 
                              rows="4"
                              placeholder="Instrucciones detalladas sobre cómo recolectar la muestra..."><?= htmlspecialchars($old['instrucciones_recoleccion'] ?? '') ?></textarea>
                    <small class="text-muted">Pasos a seguir para la correcta recolección de la muestra</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-toggle-on me-2 text-success"></i>
                Estado
            </h5>
        </div>
        <div class="card-body">
            <div class="form-check form-switch">
                <input class="form-check-input" 
                       type="checkbox" 
                       name="activo" 
                       id="activo"
                       <?= (!isset($old['activo']) || $old['activo']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">
                    Tipo de muestra activo
                </label>
            </div>
            <small class="text-muted">Los tipos inactivos no aparecerán en los catálogos</small>
        </div>
    </div>

    <!-- Botones -->
    <div class="card shadow-sm">
        <div class="card-footer bg-white">
            <div class="row">
                <div class="col-md-6">
                    <a href="<?= url('/catalogos/tipos-muestra') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Cancelar
                    </a>
                </div>
                <div class="col-md-6 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Guardar Tipo de Muestra
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>

<!-- Script -->
<script>
let formModificado = false;

document.getElementById('formCrear').addEventListener('change', function() {
    formModificado = true;
});

document.getElementById('formCrear').addEventListener('submit', function() {
    formModificado = false;
});

window.addEventListener('beforeunload', function (e) {
    if (formModificado) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>