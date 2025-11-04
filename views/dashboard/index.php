<div class="row mb-4">
    <div class="col-12">
        <h2>Bienvenido, <?= e($usuario['nombres']) ?>!</h2>
        <p class="text-muted">Resumen de actividades del laboratorio</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Pacientes Registrados -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p>Total Pacientes</p>
                    <h3><?= number_format($stats['totalPacientes']) ?></h3>
                </div>
                <div>
                    <i class="fas fa-users fa-3x text-primary" style="opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Órdenes de Hoy -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p>Órdenes Hoy</p>
                    <h3><?= number_format($stats['ordenesHoy']) ?></h3>
                </div>
                <div>
                    <i class="fas fa-file-medical fa-3x text-success" style="opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Órdenes Pendientes -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p>Órdenes Pendientes</p>
                    <h3><?= number_format($stats['ordenesPendientes']) ?></h3>
                </div>
                <div>
                    <i class="fas fa-clock fa-3x text-warning" style="opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resultados por Validar -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p>Por Validar</p>
                    <h3><?= number_format($stats['resultadosPendientes']) ?></h3>
                </div>
                <div>
                    <i class="fas fa-vial fa-3x text-danger" style="opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="stat-card">
            <h5 class="mb-3">Accesos Rápidos</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="<?= url('pacientes/crear') ?>" class="btn btn-outline-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>
                        Nuevo Paciente
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= url('ordenes/crear') ?>" class="btn btn-outline-success w-100">
                        <i class="fas fa-file-medical me-2"></i>
                        Nueva Orden
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= url('resultados/capturar') ?>" class="btn btn-outline-info w-100">
                        <i class="fas fa-vial me-2"></i>
                        Capturar Resultados
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= url('pagos/registrar') ?>" class="btn btn-outline-warning w-100">
                        <i class="fas fa-dollar-sign me-2"></i>
                        Registrar Pago
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información del Sistema -->
<div class="row">
    <div class="col-md-6">
        <div class="stat-card">
            <h5 class="mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Estado del Sistema</h5>
            <div class="mb-2">
                <i class="fas fa-check-circle text-success me-2"></i> Base de datos: Conectada
            </div>
            <div class="mb-2">
                <i class="fas fa-check-circle text-success me-2"></i> Versión: 1.0.0
            </div>
            <div class="mb-2">
                <i class="fas fa-user-circle text-info me-2"></i> Usuario: <?= e($usuario['username']) ?>
            </div>
            <div>
                <i class="fas fa-shield-alt text-success me-2"></i> Sesión: Activa
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="stat-card">
            <h5 class="mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Tareas Pendientes</h5>
            <ul class="mb-0">
                <li>Revisar órdenes pendientes de procesamiento</li>
                <li>Validar resultados capturados</li>
                <li>Verificar pagos del día</li>
                <li>Generar reportes diarios</li>
            </ul>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card text-center">
            <p class="text-muted mb-0">
                <i class="fas fa-flask me-2"></i>
                Sistema de Laboratorio Clínico v1.0 - 
                <i class="fas fa-calendar me-1"></i><?= date('d/m/Y H:i') ?>
            </p>
        </div>
    </div>
</div>
