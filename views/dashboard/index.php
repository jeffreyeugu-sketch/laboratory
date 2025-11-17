<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-1">¡Bienvenido, <?= e($usuario['nombres']) ?>!</h2>
        <p class="text-muted">Resumen de actividades del laboratorio - <?= date('l, d \d\e F \d\e Y') ?></p>
    </div>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row g-4 mb-4">
    <!-- Total Pacientes -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Total Pacientes</p>
                    <h3 class="text-primary"><?= number_format($stats['totalPacientes']) ?></h3>
                    <small class="text-muted">
                        <i class="fas fa-users me-1"></i>Registrados en el sistema
                    </small>
                </div>
                <div>
                    <i class="fas fa-users fa-3x text-primary" style="opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Órdenes de Hoy -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Órdenes de Hoy</p>
                    <h3 class="text-success"><?= number_format($stats['ordenesHoy']) ?></h3>
                    <small class="text-muted">
                        <i class="fas fa-calendar-day me-1"></i><?= date('d/m/Y') ?>
                    </small>
                </div>
                <div>
                    <i class="fas fa-file-medical fa-3x text-success" style="opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Órdenes Pendientes -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Órdenes Pendientes</p>
                    <h3 class="text-warning"><?= number_format($stats['ordenesPendientes']) ?></h3>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>En proceso
                    </small>
                </div>
                <div>
                    <i class="fas fa-clock fa-3x text-warning" style="opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resultados por Validar -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Por Validar</p>
                    <h3 class="text-danger"><?= number_format($stats['resultadosPendientes']) ?></h3>
                    <small class="text-muted">
                        <i class="fas fa-vial me-1"></i>Resultados capturados
                    </small>
                </div>
                <div>
                    <i class="fas fa-vial fa-3x text-danger" style="opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card">
            <h5 class="mb-3">
                <i class="fas fa-bolt text-warning me-2"></i>
                Accesos Rápidos
            </h5>
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <a href="<?= url('pacientes/crear') ?>" class="btn btn-outline-primary w-100 py-3">
                        <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                        <strong>Nuevo Paciente</strong>
                        <small class="d-block text-muted">Registrar paciente</small>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="<?= url('ordenes/crear') ?>" class="btn btn-outline-success w-100 py-3">
                        <i class="fas fa-file-medical fa-2x mb-2 d-block"></i>
                        <strong>Nueva Orden</strong>
                        <small class="d-block text-muted">Crear orden de servicio</small>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="<?= url('resultados/capturar') ?>" class="btn btn-outline-info w-100 py-3">
                        <i class="fas fa-vial fa-2x mb-2 d-block"></i>
                        <strong>Capturar Resultados</strong>
                        <small class="d-block text-muted">Ingresar resultados</small>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a href="<?= url('pagos/registrar') ?>" class="btn btn-outline-warning w-100 py-3">
                        <i class="fas fa-dollar-sign fa-2x mb-2 d-block"></i>
                        <strong>Registrar Pago</strong>
                        <small class="d-block text-muted">Procesar pago</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección Inferior: Información y Tareas -->
<div class="row g-4">
    <!-- Estado del Sistema -->
    <div class="col-lg-6">
        <div class="stat-card h-100">
            <h5 class="mb-3">
                <i class="fas fa-server text-primary me-2"></i>
                Estado del Sistema
            </h5>
            <div class="mb-3 pb-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-database text-success me-2"></i>Base de Datos</span>
                    <span class="badge bg-success">Conectada</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-code-branch text-info me-2"></i>Versión del Sistema</span>
                    <span class="badge bg-info">v1.0.0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-user-shield text-primary me-2"></i>Usuario Activo</span>
                    <span class="badge bg-primary"><?= e($usuario['username']) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-clock text-secondary me-2"></i>Última Actualización</span>
                    <span class="badge bg-secondary"><?= date('H:i:s') ?></span>
                </div>
            </div>
            <div class="text-center">
                <small class="text-muted">
                    <i class="fas fa-shield-alt text-success me-1"></i>
                    Sistema seguro y funcionando correctamente
                </small>
            </div>
        </div>
    </div>
    
    <!-- Tareas Pendientes y Recordatorios -->
    <div class="col-lg-6">
        <div class="stat-card h-100">
            <h5 class="mb-3">
                <i class="fas fa-tasks text-warning me-2"></i>
                Tareas del Día
            </h5>
            <ul class="list-unstyled mb-0">
                <?php if ($stats['ordenesPendientes'] > 0): ?>
                <li class="mb-3 pb-3 border-bottom">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-circle text-warning me-3 mt-1" style="font-size: 8px;"></i>
                        <div class="flex-grow-1">
                            <strong>Revisar órdenes pendientes</strong>
                            <p class="mb-0 text-muted small">
                                Hay <?= $stats['ordenesPendientes'] ?> orden(es) pendiente(s) de procesamiento
                            </p>
                        </div>
                    </div>
                </li>
                <?php endif; ?>
                
                <?php if ($stats['resultadosPendientes'] > 0): ?>
                <li class="mb-3 pb-3 border-bottom">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-circle text-danger me-3 mt-1" style="font-size: 8px;"></i>
                        <div class="flex-grow-1">
                            <strong>Validar resultados capturados</strong>
                            <p class="mb-0 text-muted small">
                                <?= $stats['resultadosPendientes'] ?> resultado(s) esperando validación
                            </p>
                        </div>
                    </div>
                </li>
                <?php endif; ?>
                
                <li class="mb-3 pb-3 border-bottom">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-circle text-info me-3 mt-1" style="font-size: 8px;"></i>
                        <div class="flex-grow-1">
                            <strong>Verificar pagos del día</strong>
                            <p class="mb-0 text-muted small">
                                Revisar ingresos y conciliación diaria
                            </p>
                        </div>
                    </div>
                </li>
                
                <li class="mb-0">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-circle text-success me-3 mt-1" style="font-size: 8px;"></i>
                        <div class="flex-grow-1">
                            <strong>Generar reportes diarios</strong>
                            <p class="mb-0 text-muted small">
                                Crear reporte de producción e ingresos
                            </p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Actividad Reciente (opcional - para futuras mejoras) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-history text-secondary me-2"></i>
                    Actividad Reciente
                </h5>
                <a href="<?= url('reportes/actividad') ?>" class="btn btn-sm btn-outline-secondary">
                    Ver todo
                </a>
            </div>
            <p class="text-muted text-center py-4">
                <i class="fas fa-info-circle me-2"></i>
                El registro de actividades estará disponible próximamente
            </p>
        </div>
    </div>
</div>

<!-- Footer Info -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card text-center bg-light">
            <p class="text-muted mb-0">
                <i class="fas fa-flask me-2"></i>
                <strong>Sistema de Laboratorio Clínico v1.0</strong>
                <span class="mx-2">|</span>
                <i class="fas fa-calendar me-1"></i>
                <?= strftime('%A, %d de %B de %Y', strtotime('now')) ?>
                <span class="mx-2">|</span>
                <i class="fas fa-clock me-1"></i>
                <?= date('H:i:s') ?>
            </p>
        </div>
    </div>
</div>
