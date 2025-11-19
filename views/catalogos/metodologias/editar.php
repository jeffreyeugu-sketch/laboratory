<?php
/**
 * Vista: Editar Metodología
 */
?>

<!-- Contenido Principal -->
<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/catalogos/metodologias') ?>">Metodologías</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Editar Metodología</h2>
            <p class="text-muted mb-0">
                Modificar datos de: <strong><?= htmlspecialchars($metodologia['nombre']) ?></strong>
            </p>
        </div>
        <a href="<?= url('/catalogos/metodologias') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Datos de la Metodología
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/catalogos/metodologias/actualizar/' . $metodologia['id']) ?>" 
                          method="POST" 
                          id="formMetodologia">
                        
                        <!-- Nombre (OBLIGATORIO) -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : '' ?>" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="<?= htmlspecialchars($old['nombre'] ?? $metodologia['nombre']) ?>"
                                   maxlength="100"
                                   required>
                            <?php if (isset($errores['nombre'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errores['nombre'] ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Nombre de la metodología (máx. 100 caracteres)</small>
                        </div>

                        <div class="row">
                            <!-- Código (OPCIONAL) -->
                            <div class="col-md-4 mb-3">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" 
                                       class="form-control <?= isset($errores['codigo']) ? 'is-invalid' : '' ?>" 
                                       id="codigo" 
                                       name="codigo" 
                                       value="<?= htmlspecialchars($old['codigo'] ?? $metodologia['codigo'] ?? '') ?>"
                                       maxlength="20"
                                       style="text-transform: uppercase;">
                                <?php if (isset($errores['codigo'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errores['codigo'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Código único (opcional)</small>
                            </div>

                            <!-- Abreviatura (OPCIONAL) -->
                            <div class="col-md-4 mb-3">
                                <label for="abreviatura" class="form-label">Abreviatura</label>
                                <input type="text" 
                                       class="form-control <?= isset($errores['abreviatura']) ? 'is-invalid' : '' ?>" 
                                       id="abreviatura" 
                                       name="abreviatura" 
                                       value="<?= htmlspecialchars($old['abreviatura'] ?? $metodologia['abreviatura'] ?? '') ?>"
                                       maxlength="20"
                                       style="text-transform: uppercase;">
                                <?php if (isset($errores['abreviatura'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errores['abreviatura'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Abreviatura (opcional)</small>
                            </div>

                            <!-- Orden -->
                            <div class="col-md-4 mb-3">
                                <label for="orden" class="form-label">Orden</label>
                                <input type="number" 
                                       class="form-control <?= isset($errores['orden']) ? 'is-invalid' : '' ?>" 
                                       id="orden" 
                                       name="orden" 
                                       value="<?= htmlspecialchars($old['orden'] ?? $metodologia['orden'] ?? '0') ?>"
                                       min="0">
                                <?php if (isset($errores['orden'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $errores['orden'] ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Orden de visualización</small>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control <?= isset($errores['descripcion']) ? 'is-invalid' : '' ?>" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="3"
                                      maxlength="500"><?= htmlspecialchars($old['descripcion'] ?? $metodologia['descripcion'] ?? '') ?></textarea>
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
                                       <?= (isset($old['activo']) ? $old['activo'] : $metodologia['activo']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activo">
                                    <strong>Activo</strong>
                                    <small class="text-muted d-block">La metodología estará disponible para asignar a estudios</small>
                                </label>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= url('/catalogos/metodologias') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar Metodología
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
                            <td><strong>#<?= $metodologia['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Código:</td>
                            <td><code><?= htmlspecialchars($metodologia['codigo'] ?? 'N/A') ?></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Abreviatura:</td>
                            <td><strong><?= htmlspecialchars($metodologia['abreviatura'] ?? 'N/A') ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Orden:</td>
                            <td><?= $metodologia['orden'] ?? 0 ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Creado:</td>
                            <td><?= isset($metodologia['fecha_creacion']) ? date('d/m/Y H:i', strtotime($metodologia['fecha_creacion'])) : 'N/A' ?></td>
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
                        <strong>Código:</strong> Identificador único (opcional). Si está asignado, 
                        debe ser único en el sistema.
                    </p>
                    <p class="small text-muted mb-2">
                        <strong>Abreviatura:</strong> Forma corta del nombre para reportes (opcional).
                    </p>
                    <p class="small text-muted mb-0">
                        <strong>Orden:</strong> Controla el orden de aparición en listados y selectores.
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
    const originalData = $('#formMetodologia').serialize();

    // Detectar cambios en el formulario
    $('#formMetodologia input, #formMetodologia textarea').on('change input', function() {
        formChanged = ($('#formMetodologia').serialize() !== originalData);
    });

    // Advertir al salir si hay cambios sin guardar
    $(window).on('beforeunload', function() {
        if (formChanged) {
            return '¿Estás seguro de salir? Hay cambios sin guardar.';
        }
    });

    // No advertir al enviar el formulario
    $('#formMetodologia').on('submit', function() {
        $(window).off('beforeunload');
    });

    // Convertir código y abreviatura a mayúsculas automáticamente
    $('#codigo, #abreviatura').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validación del formulario
    $('#formMetodologia').on('submit', function(e) {
        const nombre = $('#nombre').val().trim();
        
        if (nombre.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El nombre es obligatorio'
            });
            $('#nombre').addClass('is-invalid');
            return false;
        }
    });

    // Limpiar estilos de error al escribir
    $('input, textarea').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>