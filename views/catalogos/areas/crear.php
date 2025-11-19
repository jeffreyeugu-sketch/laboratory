<?php
/**
 * Vista: Crear Nueva Área
 */

$old = $_SESSION['old'] ?? [];
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['old'], $_SESSION['errores']);
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Contenido Principal -->
<div class="container-fluid py-4">
    
    <!-- Encabezado -->
    <div class="mb-4">
        <h2 class="mb-1">Nueva Área</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                <li class="breadcrumb-item">Catálogos</li>
                <li class="breadcrumb-item"><a href="<?= url('/catalogos/areas') ?>">Áreas</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </nav>
    </div>
    
    <!-- Formulario -->
    <div class="row">
        <div class="col-md-8 offset-md-2">
            
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sitemap me-2"></i>
                        Datos del Área
                    </h5>
                </div>
                
                <form action="<?= url('/catalogos/areas/guardar') ?>" method="POST" id="formArea">
                    <div class="card-body">
                        
                        <!-- Código -->
                        <div class="mb-3">
                            <label for="codigo" class="form-label">
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
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
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
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
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
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="activo" 
                                       name="activo"
                                       <?= (!isset($old['activo']) || $old['activo']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">
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
                                <a href="<?= url('/catalogos/areas') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Cancelar
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
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

<!-- Scripts -->
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
        } else {
            $('#codigo').removeClass('is-invalid');
        }
        
        // Validar nombre
        const nombre = $('#nombre').val().trim();
        if (!nombre) {
            errores.push('El nombre es requerido');
            $('#nombre').addClass('is-invalid');
            valido = false;
        } else {
            $('#nombre').removeClass('is-invalid');
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