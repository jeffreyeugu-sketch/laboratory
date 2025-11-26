<?php
/**
 * Vista: Editar Departamento
 */
?>

<!-- Contenido Principal -->
<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/catalogos/departamentos') ?>">Departamentos</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Editar Departamento</h2>
            <p class="text-muted mb-0">
                Modificar datos de: <strong><?= htmlspecialchars($departamento['nombre']) ?></strong>
            </p>
        </div>
        <a href="<?= url('/catalogos/departamentos') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Datos del Departamento
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/catalogos/departamentos/actualizar/' . $departamento['id']) ?>" 
                          method="POST" 
                          id="formDepartamento">
                        
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
                                            <?= (isset($old['area_id']) ? $old['area_id'] : $departamento['area_id']) == $area['id'] ? 'selected' : '' ?>>
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
                                       value="<?= htmlspecialchars($old['codigo'] ?? $departamento['codigo']) ?>"
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
                                       value="<?= htmlspecialchars($old['orden'] ?? $departamento['orden'] ?? '0') ?>"
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
                                   value="<?= htmlspecialchars($old['nombre'] ?? $departamento['nombre']) ?>"
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
                                      maxlength="500"><?= htmlspecialchars($old['descripcion'] ?? $departamento['descripcion'] ?? '') ?></textarea>
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
                                       <?= (isset($old['activo']) ? $old['activo'] : $departamento['activo']) ? 'checked' : '' ?>>
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
                                <i class="fas fa-save me-2"></i>Actualizar Departamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna de información -->
        <div class="col-lg-4">
            <!-- Información del registro -->
            <div class="card shadow-sm border-secondary mb-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información del Registro
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">ID:</td>
                            <td><strong>#<?= $departamento['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Área:</td>
                            <td><?= htmlspecialchars($departamento['area_nombre'] ?? 'Sin área') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Código:</td>
                            <td><code><?= htmlspecialchars($departamento['codigo']) ?></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Orden:</td>
                            <td><?= $departamento['orden'] ?? 0 ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Creado:</td>
                            <td><?= isset($departamento['fecha_creacion']) ? date('d/m/Y H:i', strtotime($departamento['fecha_creacion'])) : 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Ayuda -->
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Ayuda
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        <strong>Área:</strong> El departamento debe pertenecer a un área específica del laboratorio.
                    </p>
                    <p class="small text-muted mb-2">
                        <strong>Código:</strong> Identificador único. No puede repetirse en el sistema.
                    </p>
                    <p class="small text-muted mb-0">
                        <strong>Orden:</strong> Determina el orden de aparición en listados y selectores.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Variables para detectar cambios
    let formChanged = false;
    const originalData = $('#formDepartamento').serialize();

    // Detectar cambios en el formulario
    $('#formDepartamento input, #formDepartamento textarea, #formDepartamento select').on('change input', function() {
        formChanged = ($('#formDepartamento').serialize() !== originalData);
    });

    // Advertir al salir si hay cambios sin guardar
    $(window).on('beforeunload', function() {
        if (formChanged) {
            return '¿Estás seguro de salir? Hay cambios sin guardar.';
        }
    });

    // No advertir al enviar el formulario
    $('#formDepartamento').on('submit', function() {
        $(window).off('beforeunload');
    });

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