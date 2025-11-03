<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-light bg-white border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= BASE_URL ?>/dashboard" class="nav-link">
                <i class="fas fa-home"></i> Inicio
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ms-auto">
        <!-- Nueva Orden Quick Access -->
        <li class="nav-item">
            <a class="nav-link btn btn-primary btn-sm me-2" href="<?= BASE_URL ?>/ordenes/crear" title="Nueva Orden">
                <i class="fas fa-plus-circle"></i>
                <span class="d-none d-md-inline ms-1">Nueva Orden</span>
            </a>
        </li>
        
        <!-- Búsqueda rápida -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#" title="Búsqueda rápida">
                <i class="fas fa-search"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-3" style="min-width: 400px;">
                <h6 class="dropdown-header">Búsqueda Rápida</h6>
                <div class="dropdown-divider"></div>
                <form id="quickSearchForm">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="quickSearch" 
                               placeholder="Buscar paciente, orden, folio..."
                               autocomplete="off">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div id="quickSearchResults" class="mt-2"></div>
            </div>
        </li>

        <!-- Notificaciones -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="fas fa-bell"></i>
                <span class="badge bg-danger navbar-badge" id="notificationCount">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <span class="dropdown-header">3 Notificaciones</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-flask text-warning me-2"></i> 5 resultados pendientes de validación
                    <span class="float-end text-muted text-sm">2 min</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user-clock text-info me-2"></i> 3 muestras esperando procesamiento
                    <span class="float-end text-muted text-sm">10 min</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-exclamation-circle text-danger me-2"></i> 2 órdenes urgentes
                    <span class="float-end text-muted text-sm">15 min</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= BASE_URL ?>/notificaciones" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
            </div>
        </li>

        <!-- Sucursal actual -->
        <?php if(isset($_SESSION['user']['sucursal_nombre'])): ?>
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#" title="Cambiar sucursal">
                <i class="fas fa-building"></i>
                <span class="d-none d-lg-inline ms-1"><?= htmlspecialchars($_SESSION['user']['sucursal_nombre']) ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <h6 class="dropdown-header">Cambiar Sucursal</h6>
                <div class="dropdown-divider"></div>
                <!-- Las sucursales se cargarían dinámicamente -->
                <a href="#" class="dropdown-item" onclick="cambiarSucursal(1)">
                    <i class="fas fa-building me-2"></i> Sucursal Centro
                </a>
                <a href="#" class="dropdown-item" onclick="cambiarSucursal(2)">
                    <i class="fas fa-building me-2"></i> Sucursal Norte
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- User Account -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img src="<?= BASE_URL ?>/assets/img/user-default.png" 
                     class="user-image rounded-circle" 
                     alt="User Image"
                     style="width: 30px; height: 30px;">
                <span class="d-none d-md-inline ms-1">
                    <?= htmlspecialchars($_SESSION['user']['nombre_completo'] ?? 'Usuario') ?>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!-- User image -->
                <li class="user-header bg-primary text-white">
                    <img src="<?= BASE_URL ?>/assets/img/user-default.png" 
                         class="rounded-circle" 
                         alt="User Image"
                         style="width: 80px; height: 80px;">
                    <p class="mt-2 mb-1">
                        <?= htmlspecialchars($_SESSION['user']['nombre_completo'] ?? 'Usuario') ?>
                        <small><?= htmlspecialchars($_SESSION['user']['rol_nombre'] ?? '') ?></small>
                    </p>
                </li>
                
                <!-- Menu Body -->
                <li class="user-body">
                    <div class="row">
                        <div class="col-6 text-center border-end">
                            <a href="<?= BASE_URL ?>/perfil" class="text-decoration-none">
                                <i class="fas fa-user"></i> Mi Perfil
                            </a>
                        </div>
                        <div class="col-6 text-center">
                            <a href="<?= BASE_URL ?>/configuracion" class="text-decoration-none">
                                <i class="fas fa-cog"></i> Configuración
                            </a>
                        </div>
                    </div>
                </li>
                
                <!-- Menu Footer-->
                <li class="user-footer">
                    <a href="<?= BASE_URL ?>/auth/cambiar-password" class="btn btn-default btn-flat">
                        <i class="fas fa-key"></i> Cambiar Contraseña
                    </a>
                    <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-danger btn-flat float-end">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
