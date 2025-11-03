<?php
$pageTitle = 'Dashboard';
$breadcrumbs = [
    ['label' => 'Inicio', 'url' => BASE_URL . '/dashboard'],
    ['label' => 'Dashboard', 'url' => '']
];
?>

<!-- Estadísticas Principales -->
<div class="row">
    <!-- Órdenes de Hoy -->
    <div class="col-lg-3 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-primary text-white">
                <i class="fas fa-file-medical"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Órdenes de Hoy</span>
                <span class="info-box-number"><?= $stats['ordenes_hoy'] ?? 0 ?></span>
                <small class="text-muted">
                    <i class="fas fa-arrow-up text-success"></i>
                    12% vs ayer
                </small>
            </div>
        </div>
    </div>

    <!-- Resultados Pendientes -->
    <div class="col-lg-3 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-warning text-white">
                <i class="fas fa-flask"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Resultados Pendientes</span>
                <span class="info-box-number"><?= $stats['resultados_pendientes'] ?? 0 ?></span>
                <small class="text-muted">
                    Por capturar
                </small>
            </div>
        </div>
    </div>

    <!-- Ingresos del Día -->
    <div class="col-lg-3 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-success text-white">
                <i class="fas fa-dollar-sign"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Ingresos de Hoy</span>
                <span class="info-box-number"><?= formatCurrency($stats['ingresos_hoy'] ?? 0) ?></span>
                <small class="text-muted">
                    <?= $stats['pagos_hoy'] ?? 0 ?> pagos registrados
                </small>
            </div>
        </div>
    </div>

    <!-- Pacientes Nuevos -->
    <div class="col-lg-3 col-md-6">
        <div class="info-box">
            <span class="info-box-icon bg-info text-white">
                <i class="fas fa-users"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Pacientes Nuevos</span>
                <span class="info-box-number"><?= $stats['pacientes_nuevos'] ?? 0 ?></span>
                <small class="text-muted">
                    Esta semana
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas y Alertas -->
<div class="row">
    <!-- Gráfica de Órdenes -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>Órdenes de los Últimos 7 Días
                </h5>
            </div>
            <div class="card-body">
                <canvas id="ordenesChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Alertas y Notificaciones -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Alertas Importantes
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if(!empty($alertas)): ?>
                        <?php foreach($alertas as $alerta): ?>
                            <a href="<?= $alerta['url'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= e($alerta['titulo']) ?></h6>
                                    <small class="text-danger"><?= $alerta['prioridad'] ?></small>
                                </div>
                                <p class="mb-1 small"><?= e($alerta['descripcion']) ?></p>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i><?= formatDateTime($alerta['fecha']) ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="mb-0">No hay alertas pendientes</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tablas de Datos -->
<div class="row">
    <!-- Órdenes Recientes -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>Órdenes Recientes
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Paciente</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($ordenes_recientes)): ?>
                                <?php foreach($ordenes_recientes as $orden): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($orden['folio']) ?></strong>
                                        </td>
                                        <td>
                                            <div><?= e($orden['paciente_nombre']) ?></div>
                                            <small class="text-muted"><?= e($orden['expediente']) ?></small>
                                        </td>
                                        <td>
                                            <?= statusBadge($orden['estatus']) ?>
                                        </td>
                                        <td>
                                            <a href="<?= url('ordenes/ver?id=' . $orden['id']) ?>" 
                                               class="btn btn-sm btn-primary"
                                               title="Ver orden">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No hay órdenes recientes
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= url('ordenes') ?>" class="btn btn-sm btn-outline-primary">
                    Ver todas las órdenes <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Estudios Más Solicitados -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Estudios Más Solicitados
                </h5>
            </div>
            <div class="card-body">
                <canvas id="estudiosChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Accesos Rápidos
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="<?= url('ordenes/crear') ?>" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                            <span>Nueva Orden</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="<?= url('pacientes/crear') ?>" class="btn btn-outline-info w-100 py-3">
                            <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                            <span>Nuevo Paciente</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="<?= url('resultados/capturar') ?>" class="btn btn-outline-warning w-100 py-3">
                            <i class="fas fa-flask fa-2x d-block mb-2"></i>
                            <span>Capturar Resultados</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="<?= url('pagos/registrar') ?>" class="btn btn-outline-success w-100 py-3">
                            <i class="fas fa-cash-register fa-2x d-block mb-2"></i>
                            <span>Registrar Pago</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="<?= url('reportes/produccion') ?>" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                            <span>Reportes</span>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="<?= url('ordenes/buscar') ?>" class="btn btn-outline-dark w-100 py-3">
                            <i class="fas fa-search fa-2x d-block mb-2"></i>
                            <span>Buscar Orden</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript adicional para gráficas
$additionalJS = <<<'JS'
<script>
$(document).ready(function() {
    // Gráfica de Órdenes
    const ordenesCtx = document.getElementById('ordenesChart');
    if (ordenesCtx) {
        new Chart(ordenesCtx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Órdenes',
                    data: [12, 19, 15, 25, 22, 18, 15],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Gráfica de Estudios
    const estudiosCtx = document.getElementById('estudiosChart');
    if (estudiosCtx) {
        new Chart(estudiosCtx, {
            type: 'doughnut',
            data: {
                labels: ['Biometría', 'Glucosa', 'Química Sanguínea', 'Urocultivo', 'Otros'],
                datasets: [{
                    data: [30, 25, 20, 15, 10],
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#17a2b8',
                        '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
JS;
?>
