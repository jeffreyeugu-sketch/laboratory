<?php
/**
 * Vista: Editar Área
 */

$area = $data['area'] ?? null;
$old = $_SESSION['old'] ?? [];
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['old'], $_SESSION['errores']);

// Usar datos viejos si existen (validación falló), sino los del área
$codigo = $old['codigo'] ?? $area['codigo'] ?? '';
$nombre = $old['nombre'] ?? $area['nombre'] ?? '';
$descripcion = $old['descripcion'] ?? $area['descripcion'] ?? '';
$activo = isset($old['activo']) ? $old['activo'] : ($area['activo'] ?? 1);
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Contenido Principal -->
<div class="container-fluid py-4">
    
    <!-- Encabezado -->
    <div class="mb-4">
        <h2 class="mb-1">Editar Área</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
                <li class="breadcrumb-item">Catálogos</li>
                <li class="breadcrumb-item"><a href="<?= url('/catalogos/areas') ?>">Áreas</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>
    
    <!-- Formulario -->
    <div class="row">
        <div class="col-md-8 offset-md-2">
            
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Área
                    </h5>
                </div>
                
                <form action="<?= url('/catalogos/areas/actualizar/' . $area['id']) ?>" method="POST" id="formArea">
                    <div class="card-body">
                        
                        <!-- ID (oculto pero visible para referencia) -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>ID del Área:</strong> <?= $area['id'] ?>
                        </div>
                        
                        <!-- Código -->
                        <div class="mb-3">
                            <label for="codigo" class="form-label">
                                Código <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= isset($errores['codigo']) ? 'is-invalid' : '' ?>" 
                                   id="codigo" 
                                   name="codigo" 
                                   value="<?= htmlspecialchars($codigo) ?>"
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
                                   value="<?= htmlspecialchars($nombre) ?>"
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
                                      placeholder="Descripción del área..."><?= htmlspecialchars($descripcion) ?></textarea>
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
                                       <?= $activo ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">
                                    Área activa
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Las áreas inactivas no aparecerán en los catálogos
                            </small>
                        </div>
                        
                        <!-- Información de auditoría -->
                        <?php if (isset($area['created_at'])): ?>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="far fa-calendar-plus me-1"></i>
                                        <strong>Creado:</strong> <?= formatDate($area['created_at'], 'd/m/Y H:i') ?>
                                    </small>
                                </div>
                                <?php if (isset($area['updated_at'])): ?>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="far fa-calendar-check me-1"></i>
                                            <strong>Modificado:</strong> <?= formatDate($area['updated_at'], 'd/m/Y H:i') ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                    
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?= url('/catalogos/areas') ?>" class="btn btn-secondary" id="btnCancelar">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Cancelar
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>
                                    Actualizar Área
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
    
    // Convertir código a mayúsculas
    $('#codigo').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
    
    // Confirmación de cambios sin guardar
    let formModificado = false;
    
    $('#formArea :input').on('change', function() {
        formModificado = true;
    });
    
    $('#btnCancelar').on('click', function(e) {
        if (formModificado) {
            e.preventDefault();
            Swal.fire({
                title: '¿Descartar cambios?',
                text: 'Hay cambios sin guardar en el formulario',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, descartar',
                cancelButtonText: 'Seguir editando'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= url('/catalogos/areas') ?>';
                }
            });
        }
    });
});
</script>