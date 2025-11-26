<?php
/**
 * Vista: Crear Nuevo Laboratorio de Referencia
 */
?>

<!-- Contenido Principal -->
<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/dashboard') ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/catalogos/laboratorios-referencia') ?>">Laboratorios de Referencia</a></li>
            <li class="breadcrumb-item active">Nuevo</li>
        </ol>
    </nav>

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Nuevo Laboratorio de Referencia</h2>
            <p class="text-muted mb-0">Registrar nuevo laboratorio externo</p>
        </div>
        <a href="<?= url('/catalogos/laboratorios-referencia') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <form action="<?= url('/catalogos/laboratorios-referencia/guardar') ?>" method="POST" id="formLaboratorio">
                
                <!-- SECCIÓN 1: DATOS GENERALES -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Datos Generales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Código -->
                            <div class="col-md-3 mb-3">
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
                                    <div class="invalid-feedback"><?= $errores['codigo'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Nombre -->
                            <div class="col-md-9 mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : '' ?>" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                                       maxlength="150"
                                       required>
                                <?php if (isset($errores['nombre'])): ?>
                                    <div class="invalid-feedback"><?= $errores['nombre'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Razón Social -->
                            <div class="col-md-8 mb-3">
                                <label for="razon_social" class="form-label">Razón Social</label>
                                <input type="text" 
                                       class="form-control <?= isset($errores['razon_social']) ? 'is-invalid' : '' ?>" 
                                       id="razon_social" 
                                       name="razon_social" 
                                       value="<?= htmlspecialchars($old['razon_social'] ?? '') ?>"
                                       maxlength="200">
                                <?php if (isset($errores['razon_social'])): ?>
                                    <div class="invalid-feedback"><?= $errores['razon_social'] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- RFC -->
                            <div class="col-md-4 mb-3">
                                <label for="rfc" class="form-label">RFC</label>
                                <input type="text" 
                                       class="form-control <?= isset($errores['rfc']) ? 'is-invalid' : '' ?>" 
                                       id="rfc" 
                                       name="rfc" 
                                       value="<?= htmlspecialchars($old['rfc'] ?? '') ?>"
                                       maxlength="15"
                                       style="text-transform: uppercase;">
                                <?php if (isset($errores['rfc'])): ?>
                                    <div class="invalid-feedback"><?= $errores['rfc'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: DIRECCIÓN -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Dirección
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Dirección -->
                            <div class="col-md-12 mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="direccion" 
                                       name="direccion" 
                                       value="<?= htmlspecialchars($old['direccion'] ?? '') ?>"
                                       maxlength="255">
                            </div>
                        </div>

                        <div class="row">
                            <!-- Ciudad -->
                            <div class="col-md-4 mb-3">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="ciudad" 
                                       name="ciudad" 
                                       value="<?= htmlspecialchars($old['ciudad'] ?? '') ?>"
                                       maxlength="100">
                            </div>

                            <!-- Estado -->
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="estado" 
                                       name="estado" 
                                       value="<?= htmlspecialchars($old['estado'] ?? '') ?>"
                                       maxlength="100">
                            </div>

                            <!-- Código Postal -->
                            <div class="col-md-4 mb-3">
                                <label for="codigo_postal" class="form-label">Código Postal</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="codigo_postal" 
                                       name="codigo_postal" 
                                       value="<?= htmlspecialchars($old['codigo_postal'] ?? '') ?>"
                                       maxlength="10">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: CONTACTO -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-phone me-2"></i>Información de Contacto
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Contacto General -->
                        <h6 class="border-bottom pb-2 mb-3">Contacto General</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="<?= htmlspecialchars($old['telefono'] ?? '') ?>"
                                       maxlength="20">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control <?= isset($errores['email']) ? 'is-invalid' : '' ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                       maxlength="100">
                                <?php if (isset($errores['email'])): ?>
                                    <div class="invalid-feedback"><?= $errores['email'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Persona de Contacto -->
                        <h6 class="border-bottom pb-2 mb-3 mt-3">Persona de Contacto</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="contacto_nombre" class="form-label">Nombre</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contacto_nombre" 
                                       name="contacto_nombre" 
                                       value="<?= htmlspecialchars($old['contacto_nombre'] ?? '') ?>"
                                       maxlength="150">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="contacto_telefono" class="form-label">Teléfono</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contacto_telefono" 
                                       name="contacto_telefono" 
                                       value="<?= htmlspecialchars($old['contacto_telefono'] ?? '') ?>"
                                       maxlength="20">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="contacto_email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control <?= isset($errores['contacto_email']) ? 'is-invalid' : '' ?>" 
                                       id="contacto_email" 
                                       name="contacto_email" 
                                       value="<?= htmlspecialchars($old['contacto_email'] ?? '') ?>"
                                       maxlength="100">
                                <?php if (isset($errores['contacto_email'])): ?>
                                    <div class="invalid-feedback"><?= $errores['contacto_email'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 4: INFORMACIÓN OPERATIVA -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-warning">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Información Operativa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Días de entrega -->
                            <div class="col-md-3 mb-3">
                                <label for="dias_entrega_promedio" class="form-label">Días de Entrega Promedio</label>
                                <input type="number" 
                                       class="form-control <?= isset($errores['dias_entrega_promedio']) ? 'is-invalid' : '' ?>" 
                                       id="dias_entrega_promedio" 
                                       name="dias_entrega_promedio" 
                                       value="<?= htmlspecialchars($old['dias_entrega_promedio'] ?? '3') ?>"
                                       min="0">
                                <?php if (isset($errores['dias_entrega_promedio'])): ?>
                                    <div class="invalid-feedback"><?= $errores['dias_entrega_promedio'] ?></div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Tiempo promedio en días</small>
                            </div>

                            <!-- Estado activo -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="activo" 
                                           name="activo"
                                           <?= isset($old['activo']) && $old['activo'] ? 'checked' : 'checked' ?>>
                                    <label class="form-check-label" for="activo">
                                        <strong>Laboratorio Activo</strong>
                                    </label>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="col-md-12 mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" 
                                          id="observaciones" 
                                          name="observaciones" 
                                          rows="3"><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
                                <small class="form-text text-muted">Notas adicionales sobre el laboratorio</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="<?= url('/catalogos/laboratorios-referencia') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Guardar Laboratorio
                            </button>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Convertir a mayúsculas
    $('#codigo, #rfc').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validación del formulario
    $('#formLaboratorio').on('submit', function(e) {
        let valid = true;
        
        const codigo = $('#codigo').val().trim();
        if (codigo.length === 0) {
            valid = false;
            $('#codigo').addClass('is-invalid');
        } else {
            $('#codigo').removeClass('is-invalid');
        }
        
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
            // Scroll al primer error
            $('html, body').animate({
                scrollTop: $('.is-invalid:first').offset().top - 100
            }, 500);
            return false;
        }
    });

    // Limpiar estilos de error
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>