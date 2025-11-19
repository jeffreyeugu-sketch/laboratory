<?php
/**
 * Vista: Crear Nueva Área
 * Formulario para registrar una nueva área del laboratorio
 */

$old = $_SESSION['old'] ?? [];
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['old'], $_SESSION['errores']);
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $data['titulo'] ?? 'Nueva Área' ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <?php if (isset($data['breadcrumb'])): ?>
                        <?php foreach ($data['breadcrumb'] as $item): ?>
                            <li class="breadcrumb-item">
                                <?php if (!empty($item['url'])): ?>
                                    <a href="<?= $item['url'] ?>"><?= $item['nombre'] ?></a>
                                <?php else: ?>
                                    <?= $item['nombre'] ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-md-8 offset-md-2">
                
                <!-- Card del formulario -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sitemap mr-2"></i>
                            Datos del Área
                        </h3>
                    </div>
                    
                    <form action="/catalogos/areas/guardar" method="POST" id="formArea">
                        <div class="card-body">
                            
                            <!-- Código -->
                            <div class="form-group">
                                <label for="codigo">
                                    Código <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control <?= isset($errores['codigo']) ? 'is-invalid' : '' ?>" 
                                       id="codigo" 
                                       name="codigo" 
                                       value="<?= htmlspecialchars($old['codigo'] ?? '') ?>"
                                       maxlength="20"
                                       required
                                       placeholder="Ej: QC, HEM, MICRO">
                                <small class="form-text text-muted">
                                    Código único del área (máximo 20 caracteres)
                                </small>
                                <?php if (isset($errores['codigo'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $errores['codigo'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Nombre -->
                            <div class="form-group">
                                <label for="nombre">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : '' ?>" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                                       maxlength="100"
                                       required
                                       placeholder="Ej: Química Clínica">
                                <small class="form-text text-muted">
                                    Nombre del área (máximo 100 caracteres)
                                </small>
                                <?php if (isset($errores['nombre'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $errores['nombre'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control <?= isset($errores['descripcion']) ? 'is-invalid' : '' ?>" 
                                          id="descripcion" 
                                          name="descripcion" 
                                          rows="3"
                                          maxlength="500"
                                          placeholder="Descripción del área..."><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                                <small class="form-text text-muted">
                                    Descripción opcional del área (máximo 500 caracteres)
                                </small>
                                <?php if (isset($errores['descripcion'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $errores['descripcion'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Estado -->
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="activo" 
                                           name="activo"
                                           <?= (!isset($old['activo']) || $old['activo']) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="activo">
                                        Área activa
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Las áreas inactivas no aparecerán en los catálogos
                                </small>
                            </div>
                            
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href=" <?= url('catalogos/areas') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i>
                                        Cancelar
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>
                                        Guardar Área
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                </div>
                
            </div>
        </div>
        
    </div>
</section>

<!-- Scripts específicos -->
<script>
$(document).ready(function() {
    
    // Validación del formulario
    $('#formArea').on('submit', function(e) {
        let valido = true;
        let errores = [];
        
        // Validar código
        const codigo = $('#codigo').val().trim();
        if (!codigo) {
            errores.push('El código es requerido');
            $('#codigo').addClass('is-invalid');
            valido = false;
        } else if (codigo.length > 20) {
            errores.push('El código no puede tener más de 20 caracteres');
            $('#codigo').addClass('is-invalid');
            valido = false;
        } else {
            $('#codigo').removeClass('is-invalid');
        }
        
        // Validar nombre
        const nombre = $('#nombre').val().trim();
        if (!nombre) {
            errores.push('El nombre es requerido');
            $('#nombre').addClass('is-invalid');
            valido = false;
        } else if (nombre.length > 100) {
            errores.push('El nombre no puede tener más de 100 caracteres');
            $('#nombre').addClass('is-invalid');
            valido = false;
        } else {
            $('#nombre').removeClass('is-invalid');
        }
        
        // Validar descripción
        const descripcion = $('#descripcion').val().trim();
        if (descripcion.length > 500) {
            errores.push('La descripción no puede tener más de 500 caracteres');
            $('#descripcion').addClass('is-invalid');
            valido = false;
        } else {
            $('#descripcion').removeClass('is-invalid');
        }
        
        // Si hay errores, mostrarlos
        if (!valido) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Errores de validación',
                html: errores.join('<br>'),
            });
        }
    });
    
    // Limpiar clases de error al escribir
    $('#codigo, #nombre, #descripcion').on('input', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Auto-generar código desde nombre (opcional)
    $('#nombre').on('blur', function() {
        const codigo = $('#codigo').val().trim();
        if (!codigo) {
            const nombre = $(this).val().trim();
            // Tomar iniciales o primeras 3-5 letras
            const codigoSugerido = nombre.substring(0, 5).toUpperCase().replace(/\s/g, '');
            $('#codigo').val(codigoSugerido);
        }
    });
    
    // Convertir código a mayúsculas
    $('#codigo').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});
</script>

<style>
.text-danger {
    color: #dc3545;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
}
</style>
