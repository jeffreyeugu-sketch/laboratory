<?php
/**
 * Vista: catalogos/laboratorios-referencia/ver.php
 * Ver detalles de un laboratorio de referencia
 */
?>

<!-- Encabezado -->
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="mb-1">
            <i class="fas fa-hospital text-primary me-2"></i>
            <?= e($laboratorio['nombre']) ?>
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('catalogos/laboratorios-referencia') ?>">Laboratorios de Referencia</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </nav>
    </div>
    <div class="col-md-4 text-end">
        <a href="<?= url('catalogos/laboratorios-referencia/editar/' . $laboratorio['id']) ?>" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>
            Editar
        </a>
        <a href="<?= url('catalogos/laboratorios-referencia') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Volver
        </a>
    </div>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 text-muted">Estudios Asignados</p>
                    <h3 class="text-primary mb-0"><?= $estadisticas['total_estudios'] ?></h3>
                </div>
                <div>
                    <i class="fas fa-flask fa-3x text-primary" style="opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 text-muted">Órdenes del Mes</p>
                    <h3 class="text-success mb-0"><?= $estadisticas['ordenes_mes'] ?></h3>
                </div>
                <div>
                    <i class="fas fa-file-medical fa-3x text-success" style="opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 text-muted">Días de Entrega</p>
                    <h3 class="text-info mb-0"><?= $laboratorio['dias_entrega_promedio'] ?></h3>
                    <small class="text-muted">Promedio</small>
                </div>
                <div>
                    <i class="fas fa-clock fa-3x text-info" style="opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información General -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card h-100">
            <h5 class="mb-3">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                Información General
            </h5>
            
            <div class="row mb-3">
                <div class="col-4 text-muted">Código:</div>
                <div class="col-8"><strong><?= e($laboratorio['codigo']) ?></strong></div>
            </div>
            
            <div class="row mb-3">
                <div class="col-4 text-muted">Nombre:</div>
                <div class="col-8"><strong><?= e($laboratorio['nombre']) ?></strong></div>
            </div>
            
            <?php if($laboratorio['razon_social']): ?>
            <div class="row mb-3">
                <div class="col-4 text-muted">Razón Social:</div>
                <div class="col-8"><?= e($laboratorio['razon_social']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($laboratorio['rfc']): ?>
            <div class="row mb-3">
                <div class="col-4 text-muted">RFC:</div>
                <div class="col-8"><?= e($laboratorio['rfc']) ?></div>
            </div>
            <?php endif; ?>
            
            <div class="row mb-3">
                <div class="col-4 text-muted">Estado:</div>
                <div class="col-8">
                    <?php if($laboratorio['activo']): ?>
                        <span class="badge bg-success">Activo</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactivo</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-4 text-muted">Registro:</div>
                <div class="col-8">
                    <small><?= date('d/m/Y H:i', strtotime($laboratorio['fecha_creacion'])) ?></small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="stat-card h-100">
            <h5 class="mb-3">
                <i class="fas fa-map-marker-alt me-2 text-info"></i>
                Dirección
            </h5>
            
            <?php if($laboratorio['direccion']): ?>
            <div class="row mb-3">
                <div class="col-4 text-muted">Dirección:</div>
                <div class="col-8"><?= e($laboratorio['direccion']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($laboratorio['ciudad']): ?>
            <div class="row mb-3">
                <div class="col-4 text-muted">Ciudad:</div>
                <div class="col-8"><?= e($laboratorio['ciudad']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($laboratorio['estado']): ?>
            <div class="row mb-3">
                <div class="col-4 text-muted">Estado:</div>
                <div class="col-8"><?= e($laboratorio['estado']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($laboratorio['codigo_postal']): ?>
            <div class="row">
                <div class="col-4 text-muted">C.P.:</div>
                <div class="col-8"><?= e($laboratorio['codigo_postal']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if(!$laboratorio['direccion'] && !$laboratorio['ciudad'] && !$laboratorio['estado']): ?>
            <p class="text-muted">Sin información de dirección</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Información de Contacto -->
<div class="stat-card mb-4">
    <h5 class="mb-3">
        <i class="fas fa-address-book me-2 text-success"></i>
        Información de Contacto
    </h5>
    
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-muted mb-3">Laboratorio</h6>
            
            <?php if($laboratorio['telefono']): ?>
            <div class="row mb-2">
                <div class="col-3 text-muted">
                    <i class="fas fa-phone"></i> Teléfono:
                </div>
                <div class="col-9"><?= e($laboratorio['telefono']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($laboratorio['email']): ?>
            <div class="row mb-2">
                <div class="col-3 text-muted">
                    <i class="fas fa-envelope"></i> Email:
                </div>
                <div class="col-9">
                    <a href="mailto:<?= e($laboratorio['email']) ?>"><?= e($laboratorio['email']) ?></a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if(!$laboratorio['telefono'] && !$laboratorio['email']): ?>
            <p class="text-muted">Sin información de contacto</p>
            <?php endif; ?>
        </div>
        
        <div class="col-md-6">
            <h6 class="text-muted mb-3">Contacto Principal</h6>
            
            <?php if($laboratorio['contacto_nombre']): ?>
            <div class="row mb-2">
                <div class="col-3 text-muted">
                    <i class="fas fa-user"></i> Nombre:
                </div>
                <div class="col-9"><strong><?= e($laboratorio['contacto_nombre']) ?></strong></div>
            </div>
            <?php endif; ?>
            
            <?php if($laboratorio['contacto_telefono']): ?>
            <div class="row mb-2">
                <div class="col-3 text-muted">
                    <i class="fas fa-phone"></i> Teléfono:
                </div>
                <div class="col-9"><?= e($laboratorio['contacto_telefono']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if($laboratorio['contacto_email']): ?>
            <div class="row mb-2">
                <div class="col-3 text-muted">
                    <i class="fas fa-envelope"></i> Email:
                </div>
                <div class="col-9">
                    <a href="mailto:<?= e($laboratorio['contacto_email']) ?>"><?= e($laboratorio['contacto_email']) ?></a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if(!$laboratorio['contacto_nombre'] && !$laboratorio['contacto_telefono'] && !$laboratorio['contacto_email']): ?>
            <p class="text-muted">Sin información de contacto</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Observaciones -->
<?php if($laboratorio['observaciones']): ?>
<div class="stat-card mb-4">
    <h5 class="mb-3">
        <i class="fas fa-clipboard me-2 text-warning"></i>
        Observaciones
    </h5>
    <p class="mb-0"><?= nl2br(e($laboratorio['observaciones'])) ?></p>
</div>
<?php endif; ?>

<!-- Estudios Asignados -->
<div class="stat-card">
    <h5 class="mb-3">
        <i class="fas fa-flask me-2 text-primary"></i>
        Estudios Asignados (<?= count($estudios) ?>)
    </h5>
    
    <?php if(count($estudios) > 0): ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre del Estudio</th>
                    <th>Área</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($estudios as $estudio): ?>
                <tr>
                    <td><span class="badge bg-secondary"><?= e($estudio['codigo_interno']) ?></span></td>
                    <td><?= e($estudio['nombre']) ?></td>
                    <td><span class="badge bg-info"><?= e($estudio['area']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="alert alert-info mb-0">
        <i class="fas fa-info-circle me-2"></i>
        No hay estudios asignados a este laboratorio de referencia.
    </div>
    <?php endif; ?>
</div>

<style>
    .stat-card .row {
        font-size: 14px;
    }
    
    .table {
        font-size: 14px;
    }
</style>
