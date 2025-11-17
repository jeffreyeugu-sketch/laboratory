<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('pacientes') ?>">Pacientes</a></li>
                <li class="breadcrumb-item active">Nuevo Paciente</li>
            </ol>
        </nav>
        <h2 class="mb-1">
            <i class="fas fa-user-plus text-primary me-2"></i>
            Nuevo Paciente
        </h2>
        <p class="text-muted">Registrar nuevo paciente en el sistema</p>
    </div>
</div>

<?php if (isset($_SESSION['duplicados']) && !empty($_SESSION['duplicados'])): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Pacientes Similares Encontrados</h5>
    <p>Se encontraron pacientes con datos similares:</p>
    <ul class="mb-0">
        <?php foreach ($_SESSION['duplicados'] as $dup): ?>
        <li>
            <strong><?= e($dup['nombre_completo']) ?></strong> - 
            Expediente: <?= e($dup['expediente']) ?> - 
            Edad: <?= e($dup['edad']) ?> años
            <a href="<?= url('pacientes/ver/' . $dup['id']) ?>" target="_blank" class="ms-2">Ver detalle</a>
        </li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
    unset($_SESSION['duplicados']);
endif; 
?>

<?php if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Errores en el formulario</h5>
    <ul class="mb-0">
        <?php foreach ($_SESSION['errores'] as $error): ?>
        <li><?= e($error) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
    unset($_SESSION['errores']);
endif; 
?>

<form action="<?= url('pacientes/guardar') ?>" method="POST" id="formPaciente">
    
    <!-- Datos Personales -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-user me-2 text-primary"></i>
            Datos Personales
        </h5>
        
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nombre(s) <span class="text-danger">*</span></label>
                <input type="text" 
                       name="nombres" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['nombres'] ?? '') ?>"
                       required>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                <input type="text" 
                       name="apellido_paterno" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['apellido_paterno'] ?? '') ?>"
                       required>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Apellido Materno</label>
                <input type="text" 
                       name="apellido_materno" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['apellido_materno'] ?? '') ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                <input type="date" 
                       name="fecha_nacimiento" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['fecha_nacimiento'] ?? '') ?>"
                       max="<?= date('Y-m-d') ?>"
                       required>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Sexo <span class="text-danger">*</span></label>
                <select name="sexo" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option value="M" <?= (($_SESSION['old']['sexo'] ?? '') == 'M') ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= (($_SESSION['old']['sexo'] ?? '') == 'F') ? 'selected' : '' ?>>Femenino</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Estado Civil</label>
                <select name="estado_civil" class="form-select">
                    <option value="">Seleccionar...</option>
                    <option value="soltero" <?= (($_SESSION['old']['estado_civil'] ?? '') == 'soltero') ? 'selected' : '' ?>>Soltero(a)</option>
                    <option value="casado" <?= (($_SESSION['old']['estado_civil'] ?? '') == 'casado') ? 'selected' : '' ?>>Casado(a)</option>
                    <option value="union_libre" <?= (($_SESSION['old']['estado_civil'] ?? '') == 'union_libre') ? 'selected' : '' ?>>Unión Libre</option>
                    <option value="divorciado" <?= (($_SESSION['old']['estado_civil'] ?? '') == 'divorciado') ? 'selected' : '' ?>>Divorciado(a)</option>
                    <option value="viudo" <?= (($_SESSION['old']['estado_civil'] ?? '') == 'viudo') ? 'selected' : '' ?>>Viudo(a)</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">CURP</label>
                <input type="text" 
                       name="curp" 
                       class="form-control text-uppercase" 
                       value="<?= e($_SESSION['old']['curp'] ?? '') ?>"
                       maxlength="18"
                       pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}"
                       placeholder="AAAA000000HXXXXX00">
                <small class="text-muted">18 caracteres</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Ocupación</label>
                <input type="text" 
                       name="ocupacion" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['ocupacion'] ?? '') ?>">
            </div>
        </div>
    </div>
    
    <!-- Contacto -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-phone me-2 text-success"></i>
            Información de Contacto
        </h5>
        
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" 
                       name="telefono" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['telefono'] ?? '') ?>"
                       placeholder="(000) 000-0000">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" 
                       name="email" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['email'] ?? '') ?>"
                       placeholder="ejemplo@correo.com">
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
            <div class="col-md-8">
                <label class="form-label">Calle</label>
                <input type="text" 
                       name="calle" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['calle'] ?? '') ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">No. Ext.</label>
                <input type="text" 
                       name="numero_exterior" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['numero_exterior'] ?? '') ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">No. Int.</label>
                <input type="text" 
                       name="numero_interior" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['numero_interior'] ?? '') ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Colonia</label>
                <input type="text" 
                       name="colonia" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['colonia'] ?? '') ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Ciudad</label>
                <input type="text" 
                       name="ciudad" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['ciudad'] ?? '') ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <input type="text" 
                       name="estado" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['estado'] ?? '') ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">C.P.</label>
                <input type="text" 
                       name="codigo_postal" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['codigo_postal'] ?? '') ?>"
                       maxlength="5"
                       pattern="[0-9]{5}">
            </div>
        </div>
    </div>
    
    <!-- Contacto de Emergencia -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
            Contacto de Emergencia
        </h5>
        
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nombre Completo</label>
                <input type="text" 
                       name="nombre_contacto_emergencia" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['nombre_contacto_emergencia'] ?? '') ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input type="tel" 
                       name="telefono_contacto_emergencia" 
                       class="form-control" 
                       value="<?= e($_SESSION['old']['telefono_contacto_emergencia'] ?? '') ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Parentesco</label>
                <select name="parentesco_contacto_emergencia" class="form-select">
                    <option value="">Seleccionar...</option>
                    <option value="Padre" <?= (($_SESSION['old']['parentesco_contacto_emergencia'] ?? '') == 'Padre') ? 'selected' : '' ?>>Padre</option>
                    <option value="Madre" <?= (($_SESSION['old']['parentesco_contacto_emergencia'] ?? '') == 'Madre') ? 'selected' : '' ?>>Madre</option>
                    <option value="Esposo(a)" <?= (($_SESSION['old']['parentesco_contacto_emergencia'] ?? '') == 'Esposo(a)') ? 'selected' : '' ?>>Esposo(a)</option>
                    <option value="Hijo(a)" <?= (($_SESSION['old']['parentesco_contacto_emergencia'] ?? '') == 'Hijo(a)') ? 'selected' : '' ?>>Hijo(a)</option>
                    <option value="Hermano(a)" <?= (($_SESSION['old']['parentesco_contacto_emergencia'] ?? '') == 'Hermano(a)') ? 'selected' : '' ?>>Hermano(a)</option>
                    <option value="Otro" <?= (($_SESSION['old']['parentesco_contacto_emergencia'] ?? '') == 'Otro') ? 'selected' : '' ?>>Otro</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Observaciones -->
    <div class="stat-card mb-4">
        <h5 class="mb-3">
            <i class="fas fa-sticky-note me-2 text-secondary"></i>
            Observaciones
        </h5>
        
        <div class="row">
            <div class="col-12">
                <textarea name="observaciones" 
                          class="form-control" 
                          rows="3" 
                          placeholder="Notas adicionales sobre el paciente..."><?= e($_SESSION['old']['observaciones'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
    
    <!-- Botones -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="<?= url('pacientes') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Guardar Paciente
                </button>
            </div>
        </div>
    </div>
    
</form>

<?php 
    // Limpiar datos old después de mostrar el formulario
    unset($_SESSION['old']);
?>

<script>
// Convertir CURP a mayúsculas automáticamente
document.querySelector('input[name="curp"]').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});

// Validación adicional del formulario
document.getElementById('formPaciente').addEventListener('submit', function(e) {
    const fechaNac = new Date(document.querySelector('input[name="fecha_nacimiento"]').value);
    const hoy = new Date();
    
    if (fechaNac > hoy) {
        e.preventDefault();
        alert('La fecha de nacimiento no puede ser futura');
        return false;
    }
    
    // Calcular edad
    const edad = Math.floor((hoy - fechaNac) / (365.25 * 24 * 60 * 60 * 1000));
    
    if (edad > 120) {
        e.preventDefault();
        if (!confirm('La edad calculada es mayor a 120 años. ¿Deseas continuar?')) {
            return false;
        }
    }
});
</script>

<style>
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .text-danger {
        color: #dc3545;
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 1rem;
    }
</style>
