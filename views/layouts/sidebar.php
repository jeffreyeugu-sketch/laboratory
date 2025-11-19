<?php
/**
 * Sidebar - Menú lateral del sistema
 * Incluye navegación a todos los módulos
 */

$currentUri = $_SERVER['REQUEST_URI'];
$currentPath = parse_url($currentUri, PHP_URL_PATH);

// Función helper para determinar si un menú está activo
function isActive($path, $currentPath) {
    return strpos($currentPath, $path) !== false ? 'active' : '';
}

function isMenuOpen($paths, $currentPath) {
    foreach ($paths as $path) {
        if (strpos($currentPath, $path) !== false) {
            return 'menu-open';
        }
    }
    return '';
}
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    
    <!-- Brand Logo -->
    <a href="<?= base_url('/dashboard') ?>" class="brand-link">
        <i class="fas fa-flask brand-image"></i>
        <span class="brand-text font-weight-light">Lab Clínico</span>
    </a>
    
    <!-- Sidebar -->
    <div class="sidebar">
        
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?= htmlspecialchars($_SESSION['user']['nombre'] ?? 'Usuario') ?>
                </a>
                <small class="text-muted">
                    <?= htmlspecialchars($_SESSION['user']['rol_nombre'] ?? 'Sin rol') ?>
                </small>
            </div>
        </div>
        
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= base_url('/dashboard') ?>" 
                       class="nav-link <?= isActive('/dashboard', $currentPath) ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <!-- Separador -->
                <li class="nav-header">OPERACIONES</li>
                
                <!-- Pacientes -->
                <li class="nav-item">
                    <a href="<?= base_url('/pacientes') ?>" 
                       class="nav-link <?= isActive('/pacientes', $currentPath) ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Pacientes</p>
                    </a>
                </li>
                
                <!-- Órdenes -->
                <li class="nav-item">
                    <a href="<?= base_url('/ordenes') ?>" 
                       class="nav-link <?= isActive('/ordenes', $currentPath) ?>">
                        <i class="nav-icon fas fa-file-medical"></i>
                        <p>
                            Órdenes
                            <span class="badge badge-info right">Pronto</span>
                        </p>
                    </a>
                </li>
                
                <!-- Resultados -->
                <li class="nav-item">
                    <a href="<?= base_url('/resultados') ?>" 
                       class="nav-link <?= isActive('/resultados', $currentPath) ?>">
                        <i class="nav-icon fas fa-microscope"></i>
                        <p>
                            Resultados
                            <span class="badge badge-info right">Pronto</span>
                        </p>
                    </a>
                </li>
                
                <!-- Separador -->
                <li class="nav-header">CATÁLOGOS</li>
                
                <!-- Catálogos (Menú desplegable) -->
                <li class="nav-item <?= isMenuOpen(['/catalogos'], $currentPath) ?>">
                    <a href="#" class="nav-link <?= isActive('/catalogos', $currentPath) ?>">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>
                            Catálogos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <!-- Estudios -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/estudios') ?>" 
                               class="nav-link <?= isActive('/catalogos/estudios', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Estudios</p>
                            </a>
                        </li>
                        
                        <!-- Perfiles/Paquetes -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/perfiles') ?>" 
                               class="nav-link <?= isActive('/catalogos/perfiles', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Perfiles/Paquetes
                                    <span class="badge badge-warning right">Pronto</span>
                                </p>
                            </a>
                        </li>
                        
                        <!-- Áreas -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/areas') ?>" 
                               class="nav-link <?= isActive('/catalogos/areas', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Áreas</p>
                            </a>
                        </li>
                        
                        <!-- Tipos de Muestra -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/tipos-muestra') ?>" 
                               class="nav-link <?= isActive('/catalogos/tipos-muestra', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Tipos de Muestra
                                    <span class="badge badge-warning right">Pronto</span>
                                </p>
                            </a>
                        </li>
                        
                        <!-- Metodologías -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/metodologias') ?>" 
                               class="nav-link <?= isActive('/catalogos/metodologias', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Metodologías
                                    <span class="badge badge-warning right">Pronto</span>
                                </p>
                            </a>
                        </li>
                        
                        <!-- Departamentos -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/departamentos') ?>" 
                               class="nav-link <?= isActive('/catalogos/departamentos', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Departamentos
                                    <span class="badge badge-warning right">Pronto</span>
                                </p>
                            </a>
                        </li>
                        
                        <!-- Labs de Referencia -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/laboratorios-referencia') ?>" 
                               class="nav-link <?= isActive('/catalogos/laboratorios-referencia', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Labs. Referencia
                                    <span class="badge badge-warning right">Pronto</span>
                                </p>
                            </a>
                        </li>
                        
                        <!-- Indicaciones -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/indicaciones') ?>" 
                               class="nav-link <?= isActive('/catalogos/indicaciones', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Indicaciones
                                    <span class="badge badge-warning right">Pronto</span>
                                </p>
                            </a>
                        </li>
                        
                        <!-- Etiquetas -->
                        <li class="nav-item">
                            <a href="<?= base_url('/catalogos/etiquetas') ?>" 
                               class="nav-link <?= isActive('/catalogos/etiquetas', $currentPath) ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Etiquetas
                                    <span class="badge badge-warning right">Pronto</span>
                                </p>
                            </a>
                        </li>
                        
                    </ul>
                </li>
                
                <!-- Separador -->
                <li class="nav-header">ADMINISTRACIÓN</li>
                
                <!-- Usuarios -->
                <li class="nav-item">
                    <a href="<?= base_url('/usuarios') ?>" 
                       class="nav-link <?= isActive('/usuarios', $currentPath) ?>">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>
                            Usuarios
                            <span class="badge badge-info right">Pronto</span>
                        </p>
                    </a>
                </li>
                
                <!-- Reportes -->
                <li class="nav-item">
                    <a href="<?= base_url('/reportes') ?>" 
                       class="nav-link <?= isActive('/reportes', $currentPath) ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Reportes
                            <span class="badge badge-info right">Pronto</span>
                        </p>
                    </a>
                </li>
                
                <!-- Configuración -->
                <li class="nav-item">
                    <a href="<?= base_url('/configuracion') ?>" 
                       class="nav-link <?= isActive('/configuracion', $currentPath) ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Configuración
                            <span class="badge badge-info right">Pronto</span>
                        </p>
                    </a>
                </li>
                
                <!-- Separador -->
                <li class="nav-header">SESIÓN</li>
                
                <!-- Cerrar Sesión -->
                <li class="nav-item">
                    <a href="<?= base_url('/logout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Cerrar Sesión</p>
                    </a>
                </li>
                
            </ul>
        </nav>
        
    </div>
    
</aside>

<style>
/* Estilos adicionales para el sidebar */
.brand-link {
    display: flex;
    align-items: center;
    padding: 0.8125rem 0.5rem;
}

.brand-link .fas.fa-flask {
    font-size: 2rem;
    margin-right: 0.5rem;
}

.user-panel .image {
    display: flex;
    align-items: center;
}

.nav-header {
    font-size: 0.9rem;
    padding: 0.5rem 0.5rem 0.5rem 1rem;
}

.nav-treeview > .nav-item > .nav-link {
    padding-left: 2.5rem;
}

.badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.4rem;
}
</style>
