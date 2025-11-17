<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('pacientes') ?>">Pacientes</a></li>
                <li class="breadcrumb-item active"><?= e($paciente['nombre_completo']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- Header del Paciente -->
<div class="stat-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="me-4">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 700;">
                        <?= strtoupper(substr($paciente['nombres'], 0, 1) . substr($paciente['apellido_paterno'], 0, 1)) ?>
                    </div>
                </div>
                <div>
                    <h2 class="mb-1"><?= e($paciente['nombre_completo']) ?></h2>
                    <p class="text-muted mb-2">
                        <span class="badge bg-secondary me-2">Exp. <?= e($paciente['expediente']) ?></span>
                        <span class="me-3">
                            <i class="fas fa-birthday-cake me-1"></i>
                            <?= calcularEdad($paciente['fecha_nacimiento']) ?> años
                        </span>
                        <span class="me-3">
                            <i class="fas fa-<?= $paciente['sexo'] == 'M' ? 'mars' : 'venus' ?> me-1"></i>
                            <?= $paciente['sexo'] == 'M' ? 'Masculino' : 'Femenino' ?>
                        </span>
                        <?php if ($paciente['telefono']): ?>
                        <span class="me-3">
                            <i class="fas fa-phone me-1"></i>
                            <?= e($paciente['telefono']) ?>
                        </span>
                        <?php endif; ?>
                    </p>
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Registrado: <?= formatearFecha($paciente['fecha_registro'], 'd/m/Y H:i') ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= url('pacientes/editar/' . $paciente['id']) ?>" class="btn btn-warning text-white me-2">
                <i class="fas fa-edit me-1"></i>
                Editar
            </a>
            <a href="<?= url('ordenes/crear?paciente_id=' . $paciente['id']) ?>" class="btn btn-success">
                <i class="fas fa-file-medical me-1"></i>
                Nueva Orden
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Columna Izquierda: Información Personal -->
    <div class="col-md-6">
        
        <!-- Datos Personales -->
        <div class="stat-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-user text-primary me-2"></i>
                Datos Personales
            </h5>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="40%" class="text-muted"><strong>Nombre Completo:</strong></td>
                    <td><?= e($paciente['nombre_completo']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Fecha de Nacimiento:</strong></td>
                    <td><?= formatearFecha($paciente['fecha_nacimiento']) ?> (<?= calcularEdad($paciente['fecha_nacimiento']) ?> años)</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Sexo:</strong></td>
                    <td>
                        <?php if ($paciente['sexo'] == 'M'): ?>
                            <span class="badge bg-info">Masculino</span>
                        <?php else: ?>
                            <span class="badge" style="background-color: #ec4899;">Femenino</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($paciente['curp']): ?>
                <tr>
                    <td class="text-muted"><strong>CURP:</strong></td>
                    <td><code><?= e($paciente['curp']) ?></code></td>
                </tr>
                <?php endif; ?>
                <?php if ($paciente['estado_civil']): ?>
                <tr>
                    <td class="text-muted"><strong>Estado Civil:</strong></td>
                    <td><?= ucfirst(str_replace('_', ' ', $paciente['estado_civil'])) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($paciente['ocupacion']): ?>
                <tr>
                    <td class="text-muted"><strong>Ocupación:</strong></td>
                    <td><?= e($paciente['ocupacion']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <!-- Contacto -->
        <div class="stat-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-phone text-success me-2"></i>
                Contacto
            </h5>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="40%" class="text-muted"><strong>Teléfono:</strong></td>
                    <td>
                        <?php if ($paciente['telefono']): ?>
                            <a href="tel:<?= e($paciente['telefono']) ?>">
                                <i class="fas fa-phone me-1"></i>
                                <?= e($paciente['telefono']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No registrado</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Email:</strong></td>
                    <td>
                        <?php if ($paciente['email']): ?>
                            <a href="mailto:<?= e($paciente['email']) ?>">
                                <i class="fas fa-envelope me-1"></i>
                                <?= e($paciente['email']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No registrado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Dirección -->
        <div class="stat-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-map-marker-alt text-info me-2"></i>
                Dirección
            </h5>
            <?php 
            $tieneDireccion = !empty($paciente['calle']) || !empty($paciente['colonia']) || !empty($paciente['ciudad']);
            ?>
            <?php if ($tieneDireccion): ?>
                <p class="mb-0">
                    <?php if ($paciente['calle']): ?>
                        <?= e($paciente['calle']) ?>
                        <?= $paciente['numero_exterior'] ? ' #' . e($paciente['numero_exterior']) : '' ?>
                        <?= $paciente['numero_interior'] ? ' Int. ' . e($paciente['numero_interior']) : '' ?>
                        <br>
                    <?php endif; ?>
                    
                    <?php if ($paciente['colonia']): ?>
                        Col. <?= e($paciente['colonia']) ?><br>
                    <?php endif; ?>
                    
                    <?php if ($paciente['ciudad']): ?>
                        <?= e($paciente['ciudad']) ?>
                        <?= $paciente['estado'] ? ', ' . e($paciente['estado']) : '' ?>
                        <br>
                    <?php endif; ?>
                    
                    <?php if ($paciente['codigo_postal']): ?>
                        C.P. <?= e($paciente['codigo_postal']) ?>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <p class="text-muted mb-0">Dirección no registrada</p>
            <?php endif; ?>
        </div>
        
        <!-- Contacto de Emergencia -->
        <?php if ($paciente['nombre_contacto_emergencia']): ?>
        <div class="stat-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                Contacto de Emergencia
            </h5>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="40%" class="text-muted"><strong>Nombre:</strong></td>
                    <td><?= e($paciente['nombre_contacto_emergencia']) ?></td>
                </tr>
                <?php if ($paciente['telefono_contacto_emergencia']): ?>
                <tr>
                    <td class="text-muted"><strong>Teléfono:</strong></td>
                    <td>
                        <a href="tel:<?= e($paciente['telefono_contacto_emergencia']) ?>">
                            <i class="fas fa-phone me-1"></i>
                            <?= e($paciente['telefono_contacto_emergencia']) ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($paciente['parentesco_contacto_emergencia']): ?>
                <tr>
                    <td class="text-muted"><strong>Parentesco:</strong></td>
                    <td><?= e($paciente['parentesco_contacto_emergencia']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Observaciones -->
        <?php if ($paciente['observaciones']): ?>
        <div class="stat-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-sticky-note text-secondary me-2"></i>
                Observaciones
            </h5>
            <p class="mb-0"><?= nl2br(e($paciente['observaciones'])) ?></p>
        </div>
        <?php endif; ?>
        
    </div>
    
    <!-- Columna Derecha: Historial de Órdenes -->
    <div class="col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-history text-secondary me-2"></i>
                    Historial de Órdenes
                </h5>
                <span class="badge bg-primary"><?= count($historialOrdenes) ?> órdenes</span>
            </div>
            
            <?php if (empty($historialOrdenes)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay órdenes registradas para este paciente</p>
                    <a href="<?= url('ordenes/crear?paciente_id=' . $paciente['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Crear Primera Orden
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Estatus</th>
                                <th>Total</th>
                                <th width="80">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historialOrdenes as $orden): ?>
                            <tr>
                                <td>
                                    <code><?= e($orden['folio']) ?></code>
                                </td>
                                <td>
                                    <small><?= formatearFecha($orden['fecha_registro'], 'd/m/Y') ?></small>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = [
                                        'registrada' => 'bg-secondary',
                                        'en_proceso' => 'bg-info',
                                        'completada' => 'bg-success',
                                        'cancelada' => 'bg-danger'
                                    ][$orden['estatus']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $orden['estatus'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= formatearMoneda($orden['total']) ?></strong>
                                </td>
                                <td>
                                    <a href="<?= url('ordenes/ver/' . $orden['id']) ?>" 
                                       class="btn btn-sm btn-info" 
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Resumen de órdenes -->
                <div class="border-top pt-3 mt-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fas fa-file-medical fa-2x text-primary"></i>
                            </div>
                            <h4 class="mb-0"><?= count($historialOrdenes) ?></h4>
                            <small class="text-muted">Total Órdenes</small>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                            <h4 class="mb-0">
                                <?= count(array_filter($historialOrdenes, fn($o) => in_array($o['estatus'], ['registrada', 'en_proceso']))) ?>
                            </h4>
                            <small class="text-muted">Pendientes</small>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                            <h4 class="mb-0">
                                <?= count(array_filter($historialOrdenes, fn($o) => $o['estatus'] == 'completada')) ?>
                            </h4>
                            <small class="text-muted">Completadas</small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .table-borderless td {
        padding: 0.5rem 0;
    }
    
    code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
    }
</style>
