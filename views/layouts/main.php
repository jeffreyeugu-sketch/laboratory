<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $pageTitle ?? 'Sistema de Laboratorio Clínico' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/favicon.png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    
    <?php if(isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body>
    <div class="wrapper">
        <?php include __DIR__ . '/header.php'; ?>
        
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $pageTitle ?? 'Dashboard' ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <?php if(isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                                    <?php foreach($breadcrumbs as $index => $crumb): ?>
                                        <?php if($index === array_key_last($breadcrumbs)): ?>
                                            <li class="breadcrumb-item active"><?= htmlspecialchars($crumb['label']) ?></li>
                                        <?php else: ?>
                                            <li class="breadcrumb-item">
                                                <a href="<?= htmlspecialchars($crumb['url']) ?>">
                                                    <?= htmlspecialchars($crumb['label']) ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Inicio</a></li>
                                    <li class="breadcrumb-item active"><?= $pageTitle ?? 'Dashboard' ?></li>
                                <?php endif; ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <?php if(isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?= $_SESSION['flash_type'] === 'success' ? 'check-circle' : ($_SESSION['flash_type'] === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                            <?= htmlspecialchars($_SESSION['flash_message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                    <?php endif; ?>
                    
                    <?= $content ?>
                </div>
            </div>
        </div>
        
        <?php include __DIR__ . '/footer.php'; ?>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap 5 Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    
    <!-- Moment.js -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/es.js"></script>
    
    <!-- App JavaScript -->
    <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
    
    <?php if(isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>
    
    <script>
        // Configuración global
        const BASE_URL = '<?= BASE_URL ?>';
        const CURRENT_USER = <?= json_encode($_SESSION['user'] ?? null) ?>;
        
        // Inicializar tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Configurar moment.js en español
        moment.locale('es');
    </script>
</body>
</html>
