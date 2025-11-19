<?php
/**
 * Vista: Editar Área
 * Formulario para modificar datos de un área existente
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

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $data['titulo'] ?? 'Editar Área' ?></h1>
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
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-2"></i>
                            Editar Área
                        </h3>
                    </div>
                    
                    <form action="/catalogos/areas/actualizar/<?= $area['id'] ?>" method="POST" id="formArea">
                        <div class="card-body">
                            
                            <!-- ID (oculto pero visible para referencia) -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>ID del Área:</strong> <?= $area['id'] ?>
                            </div>
                            
                            <!-- Código -->
                            <div class="form-group">
                                <label for="codigo">
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
                            <div class="form-group">
                                <label for="nombre">
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
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
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
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="activo" 
                                           name="activo"
                                           <?= $activo ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="activo">
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
                                            <i class="far fa-calendar-plus mr-1"></i>
                                            <strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($area['created_at'])) ?>
                                        </small>
                                    </div>
                                    <?php if (isset($area['updated_at'])): ?>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="far fa-calendar-check mr-1"></i>
                                                <strong>Modificado:</strong> <?= date('d/m/Y H:i', strtotime($area['updated_at'])) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="/catalogos/areas" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i>
                                        Cancelar
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save mr-1"></i>
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
    
    // Convertir código a mayúsculas
    $('#codigo').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
    
    // Confirmación de cambios
    let formModificado = false;
    
    $('#formArea :input').on('change', function() {
        formModificado = true;
    });
    
    $('a[href="/catalogos/areas"]').on('click', function(e) {
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
                    window.location.href = '/catalogos/areas';
                }
            });
        }
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

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}
</style>
