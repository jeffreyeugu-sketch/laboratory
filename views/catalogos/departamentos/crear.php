<?php
/**
 * Vista: Crear Nuevo Departamento
 */
?>

<!-- Contenido Principal -->
<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/catalogos/departamentos') ?>">Departamentos</a></li>
            <li class="breadcrumb-item active">Nuevo</li>
        </ol>
    </nav>

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Nuevo Departamento</h2>
            <p class="text-muted mb-0">Registrar nuevo departamento del laboratorio</p>
        </div>
        <a href="<?= url('/catalogos/departamentos') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Datos del Departamento
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/catalogos/departamentos/guardar') ?>" method="POST" id="formDepartamento">
                        
                        <!-- Área (OBLIGATORIO) -->
                        <div class="mb-3">
                            <label for="area_id" class="form-label">
                                Área <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= isset($errores['area_id']) ? 'is-invalid' : '' ?>" 
                                    id="area_id" 
                                    name="area_id" 
                                    required>
                                <option value="">-- Seleccionar área --</option>
                                <?php foreach ($areas as $area): ?>
                                    <option value="<?= $area['id'] ?>" 
                                            <?= (isset($old['area_id']) && $old['area_id'] == $area['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($area['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errores['area_id'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errores['area_id'] ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Área a la que pertenece el departamento</small>
                        </div>

                        <div class="row">
                            <!-- Código (OBLIGATORIO) -->
                            <div class="col-md-6 mb-3">
                                <label for="codigo" class="form-label">
                                    Código <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control <?= isset($errores['codigo']) ? 'is-invalid' : '' ?>" 
                                       id="codigo" 
                                       name="codigo" 
                                       value="<?= htmlspecialchars($old['codigo'] ?? '') ?>"
                                       maxlength="20"
                                       style="text-transform: uppercase;"
                                       required>
                                <?php if (isset($errores['codigo'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errores['codigo'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Código único (máx. 20 caracteres)</small>
                            </div>

                            <!-- Orden -->
                            <div class="col-md-6 mb-3">
                                <label for="orden" class="form-label">Orden</label>
                                <input type="number" 
                                       class="form-control <?= isset($errores['orden']) ? 'is-invalid' : '' ?>" 
                                       id="orden" 
                                       name="orden" 
                                       value="<?= htmlspecialchars($old['orden'] ?? '0') ?>"
                                       min="0">
                                <?php if (isset($errores['orden'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errores['orden'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Orden de visualización</small>
                            </div>
                        </div>

                        <!-- Nombre (OBLIGATORIO) -->
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
                                   required>
                            <?php if (isset($errores['nombre'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errores['nombre'] ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Nombre del departamento (máx. 100 caracteres)</small>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control <?= isset($errores['descripcion']) ? 'is-invalid' : '' ?>" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3"
                                      maxlength="500"><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                            <?php if (isset($errores['descripcion'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errores['descripcion'] ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Descripción detallada (opcional, máx. 500 caracteres)</small>
                        </div>

                        <!-- Estado -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="activo" 
                                       name="activo"
                                       <?= isset($old['activo']) && $old['activo'] ? 'checked' : 'checked' ?>>
                                <label class="form-check-label" for="activo">
                                    <strong>Activo</strong>
                                    <small class="text-muted d-block">El departamento estará disponible en el sistema</small>
                                </label>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= url('/catalogos/departamentos') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Departamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna de ayuda -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">¿Qué es un Departamento?</h6>
                    <p class="small text-muted">
                        Los departamentos son subdivisiones dentro de las áreas del laboratorio 
                        que permiten una organización más específica de los estudios.
                    </p>
                    
                    <h6 class="fw-bold mt-3">Ejemplos por Área:</h6>
                    <ul class="small text-muted mb-0">
                        <li><strong>Química Clínica:</strong> Electrolitos, Lípidos, Glucosa</li>
                        <li><strong>Hematología:</strong> Coagulación, Citometría</li>
                        <li><strong>Microbiología:</strong> Bacteriología, Parasitología</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Convertir código a mayúsculas automáticamente
    $('#codigo').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validación del formulario
    $('#formDepartamento').on('submit', function(e) {
        let valid = true;
        
        // Validar área
        const areaId = $('#area_id').val();
        if (!areaId || areaId === '') {
            valid = false;
            $('#area_id').addClass('is-invalid');
        } else {
            $('#area_id').removeClass('is-invalid');
        }
        
        // Validar código
        const codigo = $('#codigo').val().trim();
        if (codigo.length === 0) {
            valid = false;
            $('#codigo').addClass('is-invalid');
        } else {
            $('#codigo').removeClass('is-invalid');
        }
        
        // Validar nombre
        const nombre = $('#nombre').val().trim();
        if (nombre.length === 0) {
            valid = false;
            $('#nombre').addClass('is-invalid');
        } else {
            $('#nombre').removeClass('is-invalid');
        }
        
        if (!valid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor completa todos los campos obligatorios'
            });
            return false;
        }
    });

    // Limpiar estilos de error al cambiar
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>