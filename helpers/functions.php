<?php
/**
 * Funciones Helper del Sistema
 * Laboratorio Clínico
 */

/* ====================================
   FUNCIONES DE URL Y NAVEGACIÓN
   ==================================== */

/**
 * Obtener la URL base de la aplicación
 * 
 * @return string
 */
function base_url() {
    static $baseUrl = null;
    
    if ($baseUrl === null) {
        $rootPath = dirname(__DIR__);
        $configFile = $rootPath . '/config/app.php';
        
        if (file_exists($configFile)) {
            $config = require $configFile;
            $baseUrl = rtrim($config['base_url'], '/');
        } else {
            $baseUrl = 'http://localhost/laboratorio-clinico/public';
        }
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
 * Obtener la URL actual (sin el dominio)
 * 
 * @return string
 */
function currentUrl() {
    $url = $_SERVER['REQUEST_URI'] ?? '';
    $url = parse_url($url, PHP_URL_PATH);
    $url = trim($url, '/');
    
    // Remover el directorio base si existe
    $baseUrl = parse_url(base_url(), PHP_URL_PATH);
    if ($baseUrl && strpos($url, trim($baseUrl, '/')) === 0) {
        $url = substr($url, strlen(trim($baseUrl, '/')));
        $url = ltrim($url, '/');
    }
    
    return $url;
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

/* ====================================
   FUNCIONES DE AUTENTICACIÓN
   ==================================== */

/**
 * Obtener usuario actual
 * 
 * @return array|null
 */
function currentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Obtener ID del usuario actual
 * 
 * @return int|null
 */
function currentUserId() {
    return $_SESSION['user']['id'] ?? null;
}

/**
 * Obtener nombre del usuario actual
 * 
 * @return string
 */
function currentUserName() {
    $user = currentUser();
    if (!$user) return 'Usuario';
    
    return trim($user['nombres'] . ' ' . ($user['apellido_paterno'] ?? ''));
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

/* ====================================
   FUNCIONES DE FORMATEO
   ==================================== */

/**
 * Escapar HTML para prevenir XSS
 * 
 * @param string $string Cadena a escapar
 * @return string
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
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
    return $simbolo . ' ' . number_format($cantidad, 2, '.', ',');
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
 * Formatear número de teléfono
 * 
 * @param string $telefono
 * @return string
 */
function formatearTelefono($telefono) {
    // Remover caracteres no numéricos
    $telefono = preg_replace('/[^0-9]/', '', $telefono);
    
    if (strlen($telefono) == 10) {
        return '(' . substr($telefono, 0, 3) . ') ' . 
               substr($telefono, 3, 3) . '-' . 
               substr($telefono, 6);
    }
    
    return $telefono;
}

/* ====================================
   FUNCIONES DE VALIDACIÓN
   ==================================== */

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
 * Validar CURP (México)
 * 
 * @param string $curp
 * @return bool
 */
function validarCURP($curp) {
    $pattern = '/^[A-Z]{1}[AEIOU]{1}[A-Z]{2}[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[HM]{1}(AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{1}[0-9]{1}$/';
    return preg_match($pattern, $curp) === 1;
}

/**
 * Sanitizar string (remover caracteres especiales)
 * 
 * @param string $string
 * @return string
 */
function sanitize($string) {
    return filter_var($string, FILTER_SANITIZE_STRING);
}

/* ====================================
   FUNCIONES DE GENERACIÓN
   ==================================== */

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
 * Generar número de expediente (8 dígitos)
 * 
 * @param int $numero Número secuencial
 * @return string
 */
function generarExpediente($numero) {
    return str_pad($numero, 8, '0', STR_PAD_LEFT);
}

/**
 * Generar folio de orden
 * Formato: YYYYMMDDSSNNNN
 * 
 * @param int $sucursalId ID de sucursal
 * @param int $consecutivo Número consecutivo del día
 * @return string
 */
function generarFolioOrden($sucursalId, $consecutivo) {
    $fecha = date('Ymd');
    $sucursal = str_pad($sucursalId, 2, '0', STR_PAD_LEFT);
    $numero = str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
    
    return $fecha . $sucursal . $numero;
}

/**
 * Generar código de barras (Code 128)
 * 
 * @param string $codigo Código a generar
 * @return string URL de la imagen
 */
function generarCodigoBarras($codigo) {
    // Usar servicio externo para generar código de barras
    return "https://barcode.tec-it.com/barcode.ashx?data={$codigo}&code=Code128&translate-esc=on";
}

/* ====================================
   FUNCIONES DE MENSAJES FLASH
   ==================================== */

/**
 * Establecer mensaje flash
 * 
 * @param string $mensaje
 * @param string $tipo success|error|warning|info
 */
function setFlash($mensaje, $tipo = 'info') {
    $_SESSION['flash_message'] = $mensaje;
    $_SESSION['flash_type'] = $tipo;
}

/**
 * Obtener y limpiar mensaje flash
 * 
 * @return array|null
 */
function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type'] ?? 'info'
        ];
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return $flash;
    }
    
    return null;
}

/* ====================================
   FUNCIONES DE LOGGING
   ==================================== */

/**
 * Registrar mensaje en log
 * 
 * @param string $mensaje Mensaje a registrar
 * @param string $nivel Nivel: INFO, WARNING, ERROR
 * @param string $archivo Archivo de log (sin extensión)
 */
function logMessage($mensaje, $nivel = 'INFO', $archivo = 'app') {
    $rootPath = dirname(__DIR__);
    $logDir = $rootPath . '/storage/logs';
    
    // Crear directorio si no existe
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $fecha = date('Y-m-d H:i:s');
    $logFile = $logDir . '/' . $archivo . '-' . date('Y-m-d') . '.log';
    
    $linea = "[{$fecha}] [{$nivel}] {$mensaje}" . PHP_EOL;
    
    file_put_contents($logFile, $linea, FILE_APPEND);
}

/**
 * Registrar error
 * 
 * @param string $mensaje
 */
function logError($mensaje) {
    logMessage($mensaje, 'ERROR', 'errors');
}

/**
 * Registrar auditoría
 * 
 * @param string $accion
 * @param string $entidad
 * @param int $entidadId
 * @param array $detalles
 */
function logAuditoria($accion, $entidad, $entidadId, $detalles = []) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            INSERT INTO auditoria 
            (tipo_entidad, entidad_id, accion, usuario_id, detalles, ip_address, user_agent)
            VALUES (:entidad, :entidad_id, :accion, :usuario_id, :detalles, :ip, :user_agent)
        ");
        
        $stmt->execute([
            ':entidad' => $entidad,
            ':entidad_id' => $entidadId,
            ':accion' => $accion,
            ':usuario_id' => currentUserId(),
            ':detalles' => json_encode($detalles),
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        logError('Error al registrar auditoría: ' . $e->getMessage());
    }
}

/* ====================================
   FUNCIONES DE UTILIDAD
   ==================================== */

/**
 * Obtener días de la semana en español
 * 
 * @return array
 */
function diasSemana() {
    return [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        0 => 'Domingo'
    ];
}

/**
 * Obtener meses en español
 * 
 * @return array
 */
function meses() {
    return [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
}

/**
 * Convertir array a opciones de select
 * 
 * @param array $items Array de items
 * @param string $valorKey Key del valor
 * @param string $textoKey Key del texto
 * @param mixed $selected Valor seleccionado
 * @return string HTML de opciones
 */
function arrayToOptions($items, $valorKey = 'id', $textoKey = 'nombre', $selected = null) {
    $html = '';
    foreach ($items as $item) {
        $valor = $item[$valorKey];
        $texto = $item[$textoKey];
        $selectedAttr = ($valor == $selected) ? 'selected' : '';
        $html .= "<option value=\"{$valor}\" {$selectedAttr}>" . e($texto) . "</option>";
    }
    return $html;
}

/**
 * Debugear variable (solo en desarrollo)
 * 
 * @param mixed $var Variable a debugear
 * @param bool $die Terminar ejecución después
 */
function dd($var, $die = true) {
    echo '<pre style="background: #f4f4f4; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">';
    var_dump($var);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * Imprimir variable de forma legible
 * 
 * @param mixed $var
 */
function dump($var) {
    dd($var, false);
}

/* ====================================
   FUNCIONES ADICIONALES DE FORMATEO
   ==================================== */

/**
 * Formatear fecha a formato legible
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
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
 */
function formatDateTime($datetime) {
    return formatDate($datetime, 'd/m/Y H:i');
}

/**
 * Formatear cantidad a formato de moneda
 */
function formatMoney($amount, $currency = '$') {
    if (!is_numeric($amount)) {
        return $currency . ' 0.00';
    }
    
    return $currency . ' ' . number_format($amount, 2, '.', ',');
}
