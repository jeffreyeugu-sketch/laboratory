<?php
/**
 * Funciones Helper del Sistema
 * Laboratorio Clínico
 */

/**
 * Obtener la URL base de la aplicación
 * 
 * @return string
 */
function base_url() {
    static $baseUrl = null;
    
    if ($baseUrl === null) {
        // Calcular ROOT_PATH dinámicamente
        $rootPath = dirname(__DIR__);
        
        // Cargar config directamente
        $configFile = $rootPath . '/config/app.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            $baseUrl = $config['base_url'];
        } else {
            // Fallback si no existe el archivo
            $baseUrl = 'http://localhost/laboratorio-clinico/public';
        }
        
        // Remover slash final si existe
        $baseUrl = rtrim($baseUrl, '/');
    }
    
    return $baseUrl;
}

/**
 * Generar URL completa
 * 
 * @param string $path Ruta (con o sin / al inicio)
 * @return string
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return base_url() . '/' . $path;
}

/**
 * Redirigir a una URL
 * 
 * @param string $path Ruta a redirigir
 */
function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

/**
 * Generar URL de asset (CSS, JS, imágenes)
 * 
 * @param string $path Ruta del asset
 * @return string
 */
function asset($path) {
    $path = ltrim($path, '/');
    return base_url() . '/assets/' . $path;
}

/**
 * Formatear fecha a formato legible
 * 
 * @param string $fecha Fecha en formato MySQL
 * @param string $formato Formato de salida
 * @return string
 */
function formatearFecha($fecha, $formato = 'd/m/Y') {
    if (empty($fecha) || $fecha == '0000-00-00' || $fecha == '0000-00-00 00:00:00') {
        return '';
    }
    
    $timestamp = strtotime($fecha);
    return date($formato, $timestamp);
}

/**
 * Formatear fecha y hora
 * 
 * @param string $fecha Fecha en formato MySQL
 * @return string
 */
function formatearFechaHora($fecha) {
    return formatearFecha($fecha, 'd/m/Y H:i');
}

/**
 * Formatear cantidad a formato de moneda
 * 
 * @param float $cantidad
 * @param string $simbolo
 * @return string
 */
function formatearMoneda($cantidad, $simbolo = '$') {
    return $simbolo . number_format($cantidad, 2, '.', ',');
}

/**
 * Calcular edad a partir de fecha de nacimiento
 * 
 * @param string $fechaNacimiento Fecha en formato MySQL
 * @return int Edad en años
 */
function calcularEdad($fechaNacimiento) {
    if (empty($fechaNacimiento) || $fechaNacimiento == '0000-00-00') {
        return 0;
    }
    
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($nacimiento);
    
    return $edad->y;
}

/**
 * Generar un folio único
 * 
 * @param string $prefijo Prefijo del folio
 * @param int $numero Número secuencial
 * @param int $longitud Longitud del número
 * @return string
 */
function generarFolio($prefijo, $numero, $longitud = 6) {
    $numeroFormateado = str_pad($numero, $longitud, '0', STR_PAD_LEFT);
    return $prefijo . $numeroFormateado;
}

/**
 * Sanitizar string para prevenir XSS
 * 
 * @param string $string
 * @return string
 */
function sanitizar($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Escapar output HTML
 * 
 * @param string $string
 * @return string
 */
function e($string) {
    return sanitizar($string);
}

/**
 * Obtener valor de sesión
 * 
 * @param string $key Clave de la sesión
 * @param mixed $default Valor por defecto
 * @return mixed
 */
function session($key = null, $default = null) {
    if ($key === null) {
        return $_SESSION;
    }
    
    return $_SESSION[$key] ?? $default;
}

/**
 * Establecer valor en sesión
 * 
 * @param string $key
 * @param mixed $value
 */
function setSession($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Verificar si existe flash message
 * 
 * @param string $key
 * @return bool
 */
function hasFlash($key) {
    return isset($_SESSION['flash'][$key]);
}

/**
 * Obtener y eliminar flash message
 * 
 * @param string $key
 * @return mixed
 */
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    return null;
}

/**
 * Establecer flash message
 * 
 * @param string $key
 * @param mixed $value
 */
function setFlash($key, $value) {
    $_SESSION['flash'][$key] = $value;
}

/**
 * Logging simple
 * 
 * @param string $message Mensaje a registrar
 * @param string $level Nivel (info, error, debug)
 */
function logMessage($message, $level = 'info') {
    $rootPath = dirname(__DIR__);
    $logFile = $rootPath . '/storage/logs/app-' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;
    
    @file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Dump and die (para debugging)
 * 
 * @param mixed $data
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Validar email
 * 
 * @param string $email
 * @return bool
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar CURP mexicano
 * 
 * @param string $curp
 * @return bool
 */
function validarCURP($curp) {
    $pattern = '/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/';
    return preg_match($pattern, $curp) === 1;
}

/**
 * Validar RFC mexicano
 * 
 * @param string $rfc
 * @return bool
 */
function validarRFC($rfc) {
    $pattern = '/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/';
    return preg_match($pattern, $rfc) === 1;
}

/**
 * Obtener el usuario actual
 * 
 * @return array|null
 */
function currentUser() {
    return session('usuario');
}

/**
 * Verificar si el usuario está autenticado
 * 
 * @return bool
 */
function isAuthenticated() {
    return Auth::check();
}

/**
 * Verificar si el usuario tiene un permiso
 * 
 * @param string $permiso
 * @return bool
 */
function can($permiso) {
    return Auth::can($permiso);
}

/**
 * Obtener el nombre completo del usuario actual
 * 
 * @return string
 */
function currentUserName() {
    $usuario = currentUser();
    if (!$usuario) return 'Invitado';
    
    return trim($usuario['nombres'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']);
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
 * Verificar token CSRF
 * 
 * @param string $token
 * @return bool
 */
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Campo hidden de CSRF para formularios
 * 
 * @return string
 */
function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
