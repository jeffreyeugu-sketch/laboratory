<?php
/**
 * Vista: catalogos/laboratorios-referencia/editar.php
 * Formulario para editar laboratorio de referencia
 */
?>

<!-- Encabezado -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="mb-1">
            <i class="fas fa-hospital text-primary me-2"></i>
            Editar Laboratorio de Referencia
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('catalogos/laboratorios-referencia') ?>">Laboratorios de Referencia</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Mostrar errores -->
<?php if (isset($_SESSION['errores'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Error!</strong>
        <ul class="mb-0">
            <?php foreach($_SESSION['errores'] as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['errores']); ?>
<?php endif; ?>

<!-- Formulario -->
<form action="<?= url('catalogos/laboratorios-referencia/actualizar/' . $laboratorio['id']) ?>" method="POST">
    
    <!-- Información General -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-info-circle me-2 text-primary"></i>
            Información General
        </h5>
        
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Código <span class="text-danger">*</span></label>
                <input type="text" 
                       name="codigo" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['codigo'] ?? $laboratorio['codigo']) ?>"
                       required>
                <small class="text-muted">Código único del laboratorio</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Nombre del Laboratorio <span class="text-danger">*</span></label>
                <input type="text" 
                       name="nombre" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['nombre'] ?? $laboratorio['nombre']) ?>"
                       required>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Días de Entrega Promedio</label>
                <input type="number" 
                       name="dias_entrega_promedio" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['dias_entrega_promedio'] ?? $laboratorio['dias_entrega_promedio']) ?>"
                       min="1"
                       max="30">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Razón Social</label>
                <input type="text" 
                       name="razon_social" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['razon_social'] ?? $laboratorio['razon_social']) ?>">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">RFC</label>
                <input type="text" 
                       name="rfc" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['rfc'] ?? $laboratorio['rfc']) ?>"
                       maxlength="13">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="activo" 
                           id="activo"
                           <?= ($laboratorio['activo'] || isset($_SESSION['old']['activo'])) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="activo">Activo</label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dirección -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-map-marker-alt me-2 text-info"></i>
            Dirección
        </h5>
        
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Dirección</label>
                <input type="text" 
                       name="direccion" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['direccion'] ?? $laboratorio['direccion']) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Ciudad</label>
                <input type="text" 
                       name="ciudad" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['ciudad'] ?? $laboratorio['ciudad']) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <input type="text" 
                       name="estado" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['estado'] ?? $laboratorio['estado']) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Código Postal</label>
                <input type="text" 
                       name="codigo_postal" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['codigo_postal'] ?? $laboratorio['codigo_postal']) ?>"
                       maxlength="10">
            </div>
        </div>
    </div>
    
    <!-- Contacto -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-address-book me-2 text-success"></i>
            Información de Contacto
        </h5>
        
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nombre del Contacto</label>
                <input type="text" 
                       name="contacto_nombre" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['contacto_nombre'] ?? $laboratorio['contacto_nombre']) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Teléfono del Contacto</label>
                <input type="text" 
                       name="contacto_telefono" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['contacto_telefono'] ?? $laboratorio['contacto_telefono']) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Email del Contacto</label>
                <input type="email" 
                       name="contacto_email" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['contacto_email'] ?? $laboratorio['contacto_email']) ?>">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Teléfono del Laboratorio</label>
                <input type="text" 
                       name="telefono" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['telefono'] ?? $laboratorio['telefono']) ?>">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Email del Laboratorio</label>
                <input type="email" 
                       name="email" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['email'] ?? $laboratorio['email']) ?>">
            </div>
        </div>
    </div>
    
    <!-- Observaciones -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-clipboard me-2 text-warning"></i>
            Observaciones
        </h5>
        
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" 
                          class="form-control" 
                          rows="4"><?= e($_SESSION['old']['observaciones'] ?? $laboratorio['observaciones']) ?></textarea>
            </div>
        </div>
    </div>
    
    <!-- Botones -->
    <div class="stat-card">
        <div class="d-flex justify-content-between">
            <a href="<?= url('catalogos/laboratorios-referencia') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                Actualizar Laboratorio
            </button>
        </div>
    </div>
</form>

<?php unset($_SESSION['old']); ?>
