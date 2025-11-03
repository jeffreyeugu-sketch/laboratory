<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-1">
    <!-- Brand Logo -->
    <a href="<?= BASE_URL ?>/dashboard" class="brand-link text-center">
        <img src="<?= BASE_URL ?>/assets/img/logo.png" 
             alt="Logo" 
             class="brand-image"
             style="max-height: 40px;">
        <span class="brand-text font-weight-bold d-block">Laboratorio Clínico</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= isActive('dashboard') ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Recepción -->
                <li class="nav-header">RECEPCIÓN</li>
                
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pacientes" class="nav-link <?= isActive('pacientes') ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Pacientes</p>
                    </a>
                </li>

                <li class="nav-item <?= hasActiveChild(['ordenes']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isActive('ordenes') ?>">
                        <i class="nav-icon fas fa-file-medical"></i>
                        <p>
                            Órdenes
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/ordenes/crear" class="nav-link <?= isActive('ordenes/crear') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nueva Orden</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/ordenes" class="nav-link <?= isActive('ordenes', 'index') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Órdenes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/ordenes/buscar" class="nav-link <?= isActive('ordenes/buscar') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Buscar Orden</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Laboratorio -->
                <li class="nav-header">LABORATORIO</li>

                <li class="nav-item <?= hasActiveChild(['resultados']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isActive('resultados') ?>">
                        <i class="nav-icon fas fa-flask"></i>
                        <p>
                            Resultados
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/resultados/lista" class="nav-link <?= isActive('resultados/lista') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lista de Trabajo</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/resultados/capturar" class="nav-link <?= isActive('resultados/capturar') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Capturar Resultados</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/resultados/validar" class="nav-link <?= isActive('resultados/validar') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Validación Técnica</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/resultados/liberar" class="nav-link <?= isActive('resultados/liberar') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Liberación Médica</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/resultados/microbiologia" class="nav-link <?= isActive('resultados/microbiologia') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Microbiología</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Caja -->
                <li class="nav-header">CAJA</li>

                <li class="nav-item <?= hasActiveChild(['pagos']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isActive('pagos') ?>">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>
                            Pagos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/pagos/registrar" class="nav-link <?= isActive('pagos/registrar') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Registrar Pago</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/pagos/corte" class="nav-link <?= isActive('pagos/corte') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Corte de Caja</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/pagos/historial" class="nav-link <?= isActive('pagos/historial') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Historial de Pagos</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reportes -->
                <li class="nav-header">REPORTES</li>

                <li class="nav-item <?= hasActiveChild(['reportes']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isActive('reportes') ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Reportes
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/reportes/produccion" class="nav-link <?= isActive('reportes/produccion') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Producción</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/reportes/ingresos" class="nav-link <?= isActive('reportes/ingresos') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ingresos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/reportes/estudios" class="nav-link <?= isActive('reportes/estudios') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Por Estudios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/reportes/pacientes" class="nav-link <?= isActive('reportes/pacientes') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Por Pacientes</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Administración -->
                <?php if(hasPermission('admin.access')): ?>
                <li class="nav-header">ADMINISTRACIÓN</li>

                <li class="nav-item <?= hasActiveChild(['catalogos']) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= isActive('catalogos') ?>">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            Catálogos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/catalogos/estudios" class="nav-link <?= isActive('catalogos/estudios') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Estudios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/catalogos/precios" class="nav-link <?= isActive('catalogos/precios') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Listas de Precios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/catalogos/areas" class="nav-link <?= isActive('catalogos/areas') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Áreas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/catalogos/companias" class="nav-link <?= isActive('catalogos/companias') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Compañías</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/catalogos/sucursales" class="nav-link <?= isActive('catalogos/sucursales') ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sucursales</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/usuarios" class="nav-link <?= isActive('usuarios') ?>">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Usuarios</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/roles" class="nav-link <?= isActive('roles') ?>">
                        <i class="nav-icon fas fa-user-tag"></i>
                        <p>Roles y Permisos</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/auditoria" class="nav-link <?= isActive('auditoria') ?>">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Auditoría</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/configuracion" class="nav-link <?= isActive('configuracion') ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Configuración</p>
                    </a>
                </li>
                <?php endif; ?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
