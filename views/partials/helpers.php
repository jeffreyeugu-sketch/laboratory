<?php
/**
 * FUNCIONES HELPER PARA VISTAS
 * =============================
 * Funciones útiles para usar en las vistas
 */

/**
 * Verificar si una ruta está activa
 * 
 * @param string $route Ruta a verificar
 * @param string $exact Comparación exacta o parcial
 * @return string Clase 'active' si la ruta coincide
 */
function isActive($route, $exact = 'partial') {
    $currentRoute = $_SERVER['REQUEST_URI'] ?? '';
    $currentRoute = parse_url($currentRoute, PHP_URL_PATH);
    $currentRoute = trim($currentRoute, '/');
    
    $route = trim($route, '/');
    
    if ($exact === 'exact') {
        return $currentRoute === $route ? 'active' : '';
    }
    
    // Comparación parcial
    return strpos($currentRoute, $route) !== false ? 'active' : '';
}

/**
 * Verificar si un menú hijo tiene rutas activas
 * 
 * @param array $routes Array de rutas a verificar
 * @return bool
 */
function hasActiveChild($routes) {
    $currentRoute = $_SERVER['REQUEST_URI'] ?? '';
    $currentRoute = parse_url($currentRoute, PHP_URL_PATH);
    $currentRoute = trim($currentRoute, '/');
    
    foreach ($routes as $route) {
        $route = trim($route, '/');
        if (strpos($currentRoute, $route) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Verificar si el usuario tiene un permiso
 * 
 * @param string $permission Permiso a verificar
 * @return bool
 */
function hasPermission($permission) {
    if (!isset($_SESSION['user']['permisos'])) {
        return false;
    }
    
    // Super admin tiene todos los permisos
    if (in_array('*', $_SESSION['user']['permisos'])) {
        return true;
    }
    
    return in_array($permission, $_SESSION['user']['permisos']);
}

/**
 * Verificar si el usuario tiene un rol específico
 * 
 * @param string|array $roles Rol o array de roles a verificar
 * @return bool
 */
function hasRole($roles) {
    if (!isset($_SESSION['user']['rol_slug'])) {
        return false;
    }
    
    if (is_array($roles)) {
        return in_array($_SESSION['user']['rol_slug'], $roles);
    }
    
    return $_SESSION['user']['rol_slug'] === $roles;
}

/**
 * Obtener el nombre del usuario actual
 * 
 * @return string
 */
function currentUserName() {
    return $_SESSION['user']['nombre_completo'] ?? 'Usuario';
}

/**
 * Obtener el ID del usuario actual
 * 
 * @return int|null
 */
function currentUserId() {
    return $_SESSION['user']['id'] ?? null;
}

/**
 * Obtener el rol del usuario actual
 * 
 * @return string
 */
function currentUserRole() {
    return $_SESSION['user']['rol_nombre'] ?? 'Sin rol';
}

/**
 * Obtener la sucursal actual
 * 
 * @return int|null
 */
function currentSucursalId() {
    return $_SESSION['user']['sucursal_id'] ?? null;
}

/**
 * Formatear fecha para mostrar
 * 
 * @param string $date Fecha en formato Y-m-d o Y-m-d H:i:s
 * @param string $format Formato de salida
 * @return string
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return 'N/A';
    }
    
    return date($format, $timestamp);
}

/**
 * Formatear fecha y hora
 * 
 * @param string $datetime Fecha y hora
 * @return string
 */
function formatDateTime($datetime) {
    return formatDate($datetime, 'd/m/Y H:i');
}

/**
 * Formatear moneda
 * 
 * @param float $amount Cantidad
 * @param string $currency Símbolo de moneda
 * @return string
 */
function formatCurrency($amount, $currency = '$') {
    if (!is_numeric($amount)) {
        return $currency . '0.00';
    }
    
    return $currency . number_format($amount, 2, '.', ',');
}

/**
 * Formatear porcentaje
 * 
 * @param float $value Valor
 * @param int $decimals Decimales
 * @return string
 */
function formatPercent($value, $decimals = 0) {
    if (!is_numeric($value)) {
        return '0%';
    }
    
    return number_format($value, $decimals) . '%';
}

/**
 * Truncar texto
 * 
 * @param string $text Texto a truncar
 * @param int $length Longitud máxima
 * @param string $suffix Sufijo a agregar
 * @return string
 */
function truncate($text, $length = 50, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Obtener badge de estado
 * 
 * @param string $status Estado
 * @return string HTML del badge
 */
function statusBadge($status) {
    $badges = [
        'pendiente' => '<span class="status-badge status-pendiente">Pendiente</span>',
        'proceso' => '<span class="status-badge status-proceso">En Proceso</span>',
        'parcial' => '<span class="status-badge status-parcial">Pago Parcial</span>',
        'validado' => '<span class="status-badge status-validado">Validado</span>',
        'liberado' => '<span class="status-badge status-liberado">Liberado</span>',
        'cancelado' => '<span class="status-badge status-cancelado">Cancelado</span>',
        'entregado' => '<span class="status-badge status-entregado">Entregado</span>',
    ];
    
    return $badges[$status] ?? '<span class="status-badge status-pendiente">' . ucfirst($status) . '</span>';
}

/**
 * Obtener icono de estatus
 * 
 * @param string $status Estado
 * @return string HTML del icono
 */
function statusIcon($status) {
    $icons = [
        'pendiente' => '<i class="fas fa-clock text-warning"></i>',
        'proceso' => '<i class="fas fa-spinner text-info"></i>',
        'validado' => '<i class="fas fa-check text-secondary"></i>',
        'liberado' => '<i class="fas fa-check-double text-success"></i>',
        'cancelado' => '<i class="fas fa-times text-danger"></i>',
        'entregado' => '<i class="fas fa-box text-primary"></i>',
    ];
    
    return $icons[$status] ?? '<i class="fas fa-question-circle"></i>';
}

/**
 * Calcular edad a partir de fecha de nacimiento
 * 
 * @param string $fechaNacimiento Fecha de nacimiento (Y-m-d)
 * @return int Edad en años
 */
function calcularEdad($fechaNacimiento) {
    if (empty($fechaNacimiento) || $fechaNacimiento === '0000-00-00') {
        return 0;
    }
    
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento);
    
    return $edad->y;
}

/**
 * Obtener el sexo formateado
 * 
 * @param string $sexo M o F
 * @return string
 */
function formatSexo($sexo) {
    return $sexo === 'M' ? 'Masculino' : ($sexo === 'F' ? 'Femenino' : 'N/A');
}

/**
 * Generar opciones de select
 * 
 * @param array $options Array de opciones [value => label]
 * @param mixed $selected Valor seleccionado
 * @return string HTML de opciones
 */
function selectOptions($options, $selected = null) {
    $html = '';
    foreach ($options as $value => $label) {
        $isSelected = $selected == $value ? 'selected' : '';
        $html .= sprintf(
            '<option value="%s" %s>%s</option>',
            htmlspecialchars($value),
            $isSelected,
            htmlspecialchars($label)
        );
    }
    return $html;
}

/**
 * Generar URL completa
 * 
 * @param string $path Ruta relativa
 * @return string URL completa
 */
function url($path = '') {
    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}

/**
 * Generar URL de asset
 * 
 * @param string $path Ruta del asset
 * @return string URL completa del asset
 */
function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Escapar HTML
 * 
 * @param string $text Texto a escapar
 * @return string Texto escapado
 */
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener el valor antiguo de un input (útil para validación)
 * 
 * @param string $key Nombre del input
 * @param mixed $default Valor por defecto
 * @return mixed
 */
function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $_POST[$key] ?? $default;
}

/**
 * Verificar si hay errores de validación
 * 
 * @param string|null $key Clave específica o null para verificar cualquier error
 * @return bool
 */
function hasError($key = null) {
    if ($key === null) {
        return !empty($_SESSION['errors']);
    }
    return isset($_SESSION['errors'][$key]);
}

/**
 * Obtener mensaje de error de validación
 * 
 * @param string $key Clave del error
 * @return string
 */
function error($key) {
    return $_SESSION['errors'][$key] ?? '';
}

/**
 * Generar token CSRF
 * 
 * @return string
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generar campo hidden de CSRF
 * 
 * @return string HTML del input
 */
function csrf_field() {
    return sprintf(
        '<input type="hidden" name="csrf_token" value="%s">',
        csrf_token()
    );
}

/**
 * Crear paginación
 * 
 * @param int $currentPage Página actual
 * @param int $totalPages Total de páginas
 * @param string $baseUrl URL base
 * @return string HTML de paginación
 */
function pagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Paginación"><ul class="pagination">';
    
    // Anterior
    $disabled = $currentPage <= 1 ? 'disabled' : '';
    $prevPage = $currentPage - 1;
    $html .= sprintf(
        '<li class="page-item %s"><a class="page-link" href="%s?page=%d">Anterior</a></li>',
        $disabled,
        $baseUrl,
        $prevPage
    );
    
    // Páginas
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $currentPage ? 'active' : '';
        $html .= sprintf(
            '<li class="page-item %s"><a class="page-link" href="%s?page=%d">%d</a></li>',
            $active,
            $baseUrl,
            $i,
            $i
        );
    }
    
    // Siguiente
    $disabled = $currentPage >= $totalPages ? 'disabled' : '';
    $nextPage = $currentPage + 1;
    $html .= sprintf(
        '<li class="page-item %s"><a class="page-link" href="%s?page=%d">Siguiente</a></li>',
        $disabled,
        $baseUrl,
        $nextPage
    );
    
    $html .= '</ul></nav>';
    
    return $html;
}
