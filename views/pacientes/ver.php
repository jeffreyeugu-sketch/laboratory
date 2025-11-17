<?php
/**
 * Vista: Detalle del Paciente
 * Muestra información completa del paciente y su historial
 */
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('pacientes') ?>">Pacientes</a></li>
                <li class="breadcrumb-item active">Detalle del Paciente</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-user text-primary me-2"></i>
                    <?= e($paciente['nombre_completo']) ?>
                </h2>
                <p class="text-muted mb-0">
                    <span class="badge bg-secondary me-2">Expediente: <?= e($paciente['expediente']) ?></span>
                    <span class="badge bg-info">Edad: <?= e($paciente['edad']) ?> años</span>
                    <?php if ($paciente['sexo'] == 'M'): ?>
                        <span class="badge bg-primary"><i class="fas fa-mars me-1"></i>Masculino</span>
                    <?php elseif ($paciente['sexo'] == 'F'): ?>
                        <span class="badge bg-danger"><i class="fas fa-venus me-1"></i>Femenino</span>
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <a href="<?= url('pacientes/editar/' . $paciente['id']) ?>" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
                <a href="<?= url('ordenes/crear?paciente_id=' . $paciente['id']) ?>" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Nueva Orden
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Columna Izquierda: Información del Paciente -->
    <div class="col-md-6">
        
        <!-- Datos Personales -->
        <div class="stat-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-user me-2 text-primary"></i>
                Datos Personales
            </h5>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="40%" class="text-muted"><strong>Expediente:</strong></td>
                    <td><span class="badge bg-secondary"><?= e($paciente['expediente']) ?></span></td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Nombre:</strong></td>
                    <td><?= e($paciente['nombres']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Apellido Paterno:</strong></td>
                    <td><?= e($paciente['apellido_paterno']) ?></td>
                </tr>
                <?php if (!empty($paciente['apellido_materno'])): ?>
                <tr>
                    <td class="text-muted"><strong>Apellido Materno:</strong></td>
                    <td><?= e($paciente['apellido_materno']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-muted"><strong>Fecha de Nacimiento:</strong></td>
                    <td><?= formatDate($paciente['fecha_nacimiento']) ?> (<?= e($paciente['edad']) ?> años)</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Sexo:</strong></td>
                    <td>
                        <?php if ($paciente['sexo'] == 'M'): ?>
                            <i class="fas fa-mars text-primary me-1"></i> Masculino
                        <?php elseif ($paciente['sexo'] == 'F'): ?>
                            <i class="fas fa-venus text-danger me-1"></i> Femenino
                        <?php else: ?>
                            <i class="fas fa-genderless me-1"></i> Otro
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if (!empty($paciente['curp'])): ?>
                <tr>
                    <td class="text-muted"><strong>CURP:</strong></td>
                    <td><span class="badge bg-dark"><?= e($paciente['curp']) ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($paciente['estado_civil'])): ?>
                <tr>
                    <td class="text-muted"><strong>Estado Civil:</strong></td>
                    <td><?= ucfirst(str_replace('_', ' ', e($paciente['estado_civil']))) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($paciente['ocupacion'])): ?>
                <tr>
                    <td class="text-muted"><strong>Ocupación:</strong></td>
                    <td><?= e($paciente['ocupacion']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-muted"><strong>Fecha de Registro:</strong></td>
                    <td><?= formatDateTime($paciente['fecha_registro']) ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Información de Contacto -->
        <div class="stat-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-phone text-success me-2"></i>
                Información de Contacto
            </h5>
            <table class="table table-sm table-borderless">
                <?php if (!empty($paciente['telefono'])): ?>
                <tr>
                    <td width="40%" class="text-muted"><strong>Teléfono:</strong></td>
                    <td>
                        <a href="tel:<?= e($paciente['telefono']) ?>">
                            <i class="fas fa-phone me-1"></i>
                            <?= e($paciente['telefono']) ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                
                <?php if (!empty($paciente['celular'])): ?>
                <tr>
                    <td class="text-muted"><strong>Celular:</strong></td>
                    <td>
                        <a href="tel:<?= e($paciente['celular']) ?>">
                            <i class="fas fa-mobile-alt me-1"></i>
                            <?= e($paciente['celular']) ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                
                <?php if (!empty($paciente['email'])): ?>
                <tr>
                    <td class="text-muted"><strong>Email:</strong></td>
                    <td>
                        <a href="mailto:<?= e($paciente['email']) ?>">
                            <i class="fas fa-envelope me-1"></i>
                            <?= e($paciente['email']) ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                
                <?php if (empty($paciente['telefono']) && empty($paciente['celular']) && empty($paciente['email'])): ?>
                <tr>
                    <td colspan="2" class="text-muted">No hay información de contacto registrada</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <!-- Dirección -->
        <div class="stat-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                Dirección
            </h5>
            
            <?php if (!empty($paciente['calle']) || !empty($paciente['ciudad'])): ?>
                <table class="table table-sm table-borderless">
                    <?php if (!empty($paciente['calle'])): ?>
                    <tr>
                        <td width="40%" class="text-muted"><strong>Calle:</strong></td>
                        <td><?= e($paciente['calle']) ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if (!empty($paciente['numero_exterior'])): ?>
                    <tr>
                        <td class="text-muted"><strong>Número Exterior:</strong></td>
                        <td><?= e($paciente['numero_exterior']) ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if (!empty($paciente['numero_interior'])): ?>
                    <tr>
                        <td class="text-muted"><strong>Número Interior:</strong></td>
                        <td><?= e($paciente['numero_interior']) ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if (!empty($paciente['colonia'])): ?>
                    <tr>
                        <td class="text-muted"><strong>Colonia:</strong></td>
                        <td><?= e($paciente['colonia']) ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if (!empty($paciente['codigo_postal'])): ?>
                    <tr>
                        <td class="text-muted"><strong>Código Postal:</strong></td>
                        <td><?= e($paciente['codigo_postal']) ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if (!empty($paciente['ciudad'])): ?>
                    <tr>
                        <td class="text-muted"><strong>Ciudad:</strong></td>
                        <td><?= e($paciente['ciudad']) ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if (!empty($paciente['estado'])): ?>
                    <tr>
                        <td class="text-muted"><strong>Estado:</strong></td>
                        <td><?= e($paciente['estado']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            <?php else: ?>
                <p class="text-muted mb-0">Dirección no registrada</p>
            <?php endif; ?>
        </div>
        
        <!-- Contacto de Emergencia -->
        <?php if (!empty($paciente['nombre_contacto_emergencia'])): ?>
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
                <?php if (!empty($paciente['telefono_contacto_emergencia'])): ?>
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
                <?php if (!empty($paciente['parentesco_contacto_emergencia'])): ?>
                <tr>
                    <td class="text-muted"><strong>Parentesco:</strong></td>
                    <td><?= e($paciente['parentesco_contacto_emergencia']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Observaciones -->
        <?php if (!empty($paciente['observaciones'])): ?>
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
                <span class="badge bg-primary"><?= count($historialOrdenes ?? []) ?> órdenes</span>
            </div>
            
            <?php if (empty($historialOrdenes)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay órdenes registradas para este paciente</p>
                    <a href="<?= url('ordenes/crear?paciente_id=' . $paciente['id']) ?>" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Crear Primera Orden
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Estudios</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historialOrdenes as $orden): ?>
                            <tr>
                                <td>
                                    <strong><?= e($orden['folio']) ?></strong>
                                </td>
                                <td>
                                    <small><?= formatDate($orden['fecha_registro']) ?></small>
                                </td>
                                <td>
                                    <small><?= e($orden['num_estudios']) ?> estudio(s)</small>
                                </td>
                                <td>
                                    <strong><?= formatMoney($orden['total']) ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $estatusBadge = [
                                        'registrada' => 'secondary',
                                        'en_proceso' => 'info',
                                        'parcial' => 'warning',
                                        'validada' => 'primary',
                                        'liberada' => 'success',
                                        'entregada' => 'dark',
                                        'cancelada' => 'danger'
                                    ];
                                    $badge = $estatusBadge[$orden['estatus']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($orden['estatus']) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= url('ordenes/ver/' . $orden['id']) ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Ver orden">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Botones de Acción -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between">
            <a href="<?= url('pacientes') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Lista
            </a>
            <div>
                <a href="<?= url('ordenes/crear?paciente_id=' . $paciente['id']) ?>" class="btn btn-success me-2">
                    <i class="fas fa-plus me-2"></i>Nueva Orden
                </a>
                <a href="<?= url('pacientes/editar/' . $paciente['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Editar Paciente
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.stat-card h5 {
    font-size: 1.1rem;
    font-weight: 600;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

.table-borderless td {
    padding: 0.5rem 0;
}

.badge {
    font-size: 0.85rem;
    padding: 0.35em 0.65em;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
