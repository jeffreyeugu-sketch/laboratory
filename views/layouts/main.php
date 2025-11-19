<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema de Laboratorio Clínico' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #059669;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --info-color: #0891b2;
            --dark-color: #1f2937;
            --light-color: #f3f4f6;
            --sidebar-width: 260px;
            --topbar-height: 60px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header i {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .sidebar-header h3 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: white;
        }
        
        .sidebar-header p {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            margin: 5px 0 0 0;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--primary-color);
        }
        
        .sidebar-menu a.active {
            background: rgba(37, 99, 235, 0.2);
            color: white;
            border-left-color: var(--primary-color);
            font-weight: 500;
        }
        
        .sidebar-menu a i {
            width: 24px;
            margin-right: 12px;
            font-size: 16px;
        }
        
        .sidebar-menu hr {
            border-color: rgba(255,255,255,0.1);
            margin: 10px 20px;
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Top Navbar */
        .top-navbar {
            height: var(--topbar-height);
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .top-navbar h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            border-radius: 8px;
            background: var(--light-color);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .user-role {
            font-size: 12px;
            color: #64748b;
        }
        
        .logout-btn {
            padding: 8px 16px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
            color: white;
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-card p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .stat-card h3 {
            color: var(--dark-color);
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }
        
        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .user-details {
                display: none;
            }
        }
        
        /* Utilities */
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-danger { color: var(--danger-color) !important; }
        .text-warning { color: var(--warning-color) !important; }
        .text-info { color: var(--info-color) !important; }
        
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-success { background-color: var(--success-color) !important; }
        .bg-danger { background-color: var(--danger-color) !important; }
        .bg-warning { background-color: var(--warning-color) !important; }
        .bg-info { background-color: var(--info-color) !important; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-flask fa-3x"></i>
            <h3>Laboratorio Clínico</h3>
            <p>Sistema Integral</p>
        </div>
        
        <div class="sidebar-menu">
            <!-- Dashboard -->
            <a href="<?= url('dashboard') ?>" class="<?= currentUrl() == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Pacientes -->
            <a href="<?= url('pacientes') ?>" class="<?= strpos(currentUrl(), 'pacientes') !== false ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Pacientes</span>
            </a>
            
            <!-- Órdenes -->
            <a href="<?= url('ordenes') ?>" class="<?= strpos(currentUrl(), 'ordenes') !== false ? 'active' : '' ?>">
                <i class="fas fa-file-medical"></i>
                <span>Órdenes</span>
            </a>
            
            <!-- Resultados -->
            <a href="<?= url('resultados') ?>" class="<?= strpos(currentUrl(), 'resultados') !== false ? 'active' : '' ?>">
                <i class="fas fa-vial"></i>
                <span>Resultados</span>
            </a>
            
            <!-- Pagos -->
            <a href="<?= url('pagos') ?>" class="<?= strpos(currentUrl(), 'pagos') !== false ? 'active' : '' ?>">
                <i class="fas fa-dollar-sign"></i>
                <span>Pagos</span>
            </a>
            
            <hr>
            
            <!-- Catálogos (Menú Desplegable) -->
            <div class="menu-item-dropdown <?= strpos(currentUrl(), 'catalogos') !== false ? 'active' : '' ?>">
                <a href="#" class="menu-toggle" onclick="toggleSubmenu(event, this)">
                    <i class="fas fa-flask"></i>
                    <span>Catálogos</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="submenu <?= strpos(currentUrl(), 'catalogos') !== false ? 'show' : '' ?>">
                    <a href="<?= url('catalogos/estudios') ?>" class="<?= strpos(currentUrl(), 'catalogos/estudios') !== false ? 'active' : '' ?>">
                        <i class="fas fa-circle submenu-bullet"></i>
                        <span>Estudios</span>
                    </a>
                    <a href="<?= url('catalogos/areas') ?>" class="<?= strpos(currentUrl(), 'catalogos/areas') !== false ? 'active' : '' ?>">
                        <i class="fas fa-circle submenu-bullet"></i>
                        <span>Áreas</span>
                    </a>
                    <a href="<?= url('catalogos/tipos-muestra') ?>" class="<?= strpos(currentUrl(), 'catalogos/tipos-muestra') !== false ? 'active' : '' ?>">
                        <i class="fas fa-circle submenu-bullet"></i>
                        <span>Tipos de Muestra</span>
                    </a>
                    <a href="<?= url('catalogos/metodologias') ?>" class="<?= strpos(currentUrl(), 'catalogos/metodologias') !== false ? 'active' : '' ?>">
                        <i class="fas fa-circle submenu-bullet"></i>
                        <span>Metodologías</span>
                    </a>
                    <a href="<?= url('catalogos/departamentos') ?>" class="<?= strpos(currentUrl(), 'catalogos/departamentos') !== false ? 'active' : '' ?>">
                        <i class="fas fa-circle submenu-bullet"></i>
                        <span>Departamentos</span>
                    </a>
                    <a href="<?= url('catalogos/laboratorios-referencia') ?>" class="<?= strpos(currentUrl(), 'catalogos/laboratorios-referencia') !== false ? 'active' : '' ?>">
                        <i class="fas fa-circle submenu-bullet"></i>
                        <span>Labs. Referencia</span>
                    </a>
                    <a href="<?= url('catalogos/indicaciones') ?>" class="<?= strpos(currentUrl(), 'catalogos/indicaciones') !== false ? 'active' : '' ?>">
                        <i class="fas fa-circle submenu-bullet"></i>
                        <span>Indicaciones</span>
                    </a>
                    <a href="<?= url('catalogos/etiquetas') ?>" class="<?= strpos(currentUrl(), 'catalogos/etiquetas') !== false ? 'active' : '' ?>">
                        <i class="fas fa-circle submenu-bullet"></i>
                        <span>Etiquetas</span>
                    </a>
                </div>
            </div>
            
            <hr>
            
            <!-- Usuarios -->
            <a href="<?= url('usuarios') ?>" class="<?= strpos(currentUrl(), 'usuarios') !== false ? 'active' : '' ?>">
                <i class="fas fa-user-cog"></i>
                <span>Usuarios</span>
            </a>
            
            <!-- Reportes -->
            <a href="<?= url('reportes') ?>" class="<?= strpos(currentUrl(), 'reportes') !== false ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Reportes</span>
            </a>
        </div>
    </div>

    <style>
    /* Estilos para menú desplegable */
    .menu-item-dropdown {
        position: relative;
    }

    .menu-item-dropdown > a.menu-toggle {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        cursor: pointer;
    }

    .menu-item-dropdown > a.menu-toggle:hover {
        background: rgba(255,255,255,0.1);
        color: white;
        border-left-color: var(--primary-color);
    }

    .menu-item-dropdown.active > a.menu-toggle {
        background: rgba(37, 99, 235, 0.2);
        color: white;
        border-left-color: var(--primary-color);
    }

    .menu-toggle .dropdown-icon {
        margin-left: auto;
        transition: transform 0.3s ease;
        font-size: 12px;
    }

    .menu-item-dropdown.open .dropdown-icon {
        transform: rotate(180deg);
    }

    .submenu {
        max-height: 0;
        overflow: hidden;
        background: rgba(0,0,0,0.2);
        transition: max-height 0.3s ease;
    }

    .submenu.show {
        max-height: 500px;
    }

    .submenu a {
        display: flex;
        align-items: center;
        padding: 10px 20px 10px 50px;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .submenu a:hover {
        background: rgba(255,255,255,0.1);
        color: white;
        padding-left: 55px;
    }

    .submenu a.active {
        background: rgba(37, 99, 235, 0.3);
        color: white;
        font-weight: 500;
    }

    .submenu-bullet {
        font-size: 6px;
        margin-right: 12px;
    }
    </style>

    <script>
    function toggleSubmenu(event, element) {
        event.preventDefault();
        
        const parent = element.parentElement;
        const submenu = parent.querySelector('.submenu');
        
        // Toggle clase 'open' en el contenedor
        parent.classList.toggle('open');
        
        // Toggle clase 'show' en el submenu
        submenu.classList.toggle('show');
    }

    // Auto-abrir submenú si estamos en una página de catálogos
    document.addEventListener('DOMContentLoaded', function() {
        const activeDropdown = document.querySelector('.menu-item-dropdown.active');
        if (activeDropdown) {
            activeDropdown.classList.add('open');
            const submenu = activeDropdown.querySelector('.submenu');
            if (submenu) {
                submenu.classList.add('show');
            }
        }
    });
    </script>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <h4><?= $title ?? 'Dashboard' ?></h4>
            </div>
            
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($usuario['nombres'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?= e($usuario['nombres'] ?? 'Usuario') ?> <?= e($usuario['apellido_paterno'] ?? '') ?></span>
                        <span class="user-role"><?= e($usuario['rol'] ?? 'Usuario') ?></span>
                    </div>
                </div>
                
                <a href="<?= url('logout') ?>" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Salir</span>
                </a>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $_SESSION['flash_type'] == 'success' ? 'check-circle' : 'info-circle' ?> me-2"></i>
                    <?= e($_SESSION['flash_message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php 
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                ?>
            <?php endif; ?>
            
            <?= $content ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
