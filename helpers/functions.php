<?php
/**
 * Funciones Helper
 * Funciones útiles para usar en toda la aplicación
 */

/**
 * Escapar HTML para prevenir XSS
 * 
 * @param string $text
 * @return string
 */
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear fecha
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '';
    }
    
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Formatear moneda
 * 
 * @param float $amount
 * @param string $currency
 * @return string
 */
function formatCurrency($amount, $currency = '$') {
    return $currency . number_format($amount, 2, '.', ',');
}

/**
 * Calcular edad desde fecha de nacimiento
 * 
 * @param string $birthdate
 * @return int
 */
function calculateAge($birthdate) {
    if (empty($birthdate) || $birthdate === '0000-00-00') {
        return 0;
    }
    
    $birth = new DateTime($birthdate);
    $today = new DateTime();
    $age = $today->diff($birth);
    
    return $age->y;
}

/**
 * Obtener edad en formato legible (años, meses, días)
 * 
 * @param string $birthdate
 * @return string
 */
function getAgeString($birthdate) {
    if (empty($birthdate) || $birthdate === '0000-00-00') {
        return '';
    }
    
    $birth = new DateTime($birthdate);
    $today = new DateTime();
    $diff = $today->diff($birth);
    
    if ($diff->y > 0) {
        return $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
    } elseif ($diff->m > 0) {
        return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
    } else {
        return $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
    }
}

/**
 * Generar opciones para un select de HTML
 * 
 * @param array $options Array asociativo [value => label]
 * @param mixed $selected Valor seleccionado
 * @return string
 */
function selectOptions($options, $selected = null) {
    $html = '';
    foreach ($options as $value => $label) {
        $isSelected = ($value == $selected) ? 'selected' : '';
        $html .= "<option value=\"{$value}\" {$isSelected}>" . e($label) . "</option>\n";
    }
    return $html;
}

/**
 * Obtener mensaje flash y eliminarlo de la sesión
 * 
 * @return array|null
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redireccionar a una URL
 * 
 * @param string $url
 */
function redirect($url) {
    header("Location: {$url}");
    exit;
}

/**
 * Obtener la URL base de la aplicación
 * 
 * @return string
 */
function baseUrl() {
    $config = require __DIR__ . '/../config/app.php';
    return $config['base_url'];
}

/**
 * Generar URL completa
 * 
 * @param string $path
 * @return string
 */
function url($path = '') {
    return baseUrl() . $path;
}

/**
 * Generar URL de asset
 * 
 * @param string $path
 * @return string
 */
function asset($path) {
    return baseUrl() . '/assets/' . ltrim($path, '/');
}

/**
 * Debuggear variable y detener ejecución
 * 
 * @param mixed $var
 */
function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

/**
 * Validar formato de email
 * 
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitizar string
 * 
 * @param string $string
 * @return string
 */
function sanitize($string) {
    return filter_var($string, FILTER_SANITIZE_STRING);
}

/**
 * Generar token aleatorio
 * 
 * @param int $length
 * @return string
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Verificar si es una petición AJAX
 * 
 * @return bool
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Obtener el valor de un array de forma segura
 * 
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function arrayGet($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Convertir array a JSON
 * 
 * @param mixed $data
 * @return string
 */
function toJson($data) {
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

/**
 * Generar folio de orden
 * 
 * @param int $sucursalId
 * @return string
 */
function generarFolioOrden($sucursalId) {
    $fecha = date('Ymd');
    $sucursal = str_pad($sucursalId, 2, '0', STR_PAD_LEFT);
    
    $db = Database::getInstance()->getConnection();
    
    // Obtener el último consecutivo del día
    $stmt = $db->prepare("SELECT ultimo_consecutivo FROM folios_control 
                         WHERE sucursal_id = ? AND fecha = CURDATE()");
    $stmt->execute([$sucursalId]);
    $result = $stmt->fetch();
    
    if ($result) {
        // Incrementar consecutivo
        $consecutivo = $result['ultimo_consecutivo'] + 1;
        $stmt = $db->prepare("UPDATE folios_control SET ultimo_consecutivo = ? 
                             WHERE sucursal_id = ? AND fecha = CURDATE()");
        $stmt->execute([$consecutivo, $sucursalId]);
    } else {
        // Primer folio del día
        $consecutivo = 1;
        $stmt = $db->prepare("INSERT INTO folios_control (sucursal_id, fecha, ultimo_consecutivo) 
                             VALUES (?, CURDATE(), ?)");
        $stmt->execute([$sucursalId, $consecutivo]);
    }
    
    $consecutivoStr = str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
    return $fecha . $sucursal . $consecutivoStr;
}

/**
 * Formatear folio para visualización
 * 
 * @param string $folio
 * @return string
 */
function formatFolio($folio) {
    // Formato: YYYYMMDDSSNNNN -> YYYY-MM-DD-SS-NNNN
    if (strlen($folio) === 14) {
        return substr($folio, 0, 4) . '-' . 
               substr($folio, 4, 2) . '-' . 
               substr($folio, 6, 2) . '-' . 
               substr($folio, 8, 2) . '-' . 
               substr($folio, 10, 4);
    }
    return $folio;
}

/**
 * Obtener nombre completo del paciente
 * 
 * @param array $paciente
 * @return string
 */
function getNombreCompleto($paciente) {
    $nombre = $paciente['nombres'] . ' ' . $paciente['apellido_paterno'];
    if (!empty($paciente['apellido_materno'])) {
        $nombre .= ' ' . $paciente['apellido_materno'];
    }
    return $nombre;
}

/**
 * Obtener badge de estatus de orden
 * 
 * @param string $estatus
 * @return string
 */
function getEstatusBadge($estatus) {
    $badges = [
        'registrada' => '<span class="badge bg-secondary">Registrada</span>',
        'en_proceso' => '<span class="badge bg-info">En Proceso</span>',
        'parcial' => '<span class="badge bg-warning">Parcial</span>',
        'validada' => '<span class="badge bg-primary">Validada</span>',
        'liberada' => '<span class="badge bg-success">Liberada</span>',
        'entregada' => '<span class="badge bg-dark">Entregada</span>',
        'cancelada' => '<span class="badge bg-danger">Cancelada</span>',
    ];
    
    return $badges[$estatus] ?? '<span class="badge bg-secondary">' . e($estatus) . '</span>';
}

/**
 * Obtener badge de estatus de pago
 * 
 * @param string $estatusPago
 * @return string
 */
function getEstatusPagoBadge($estatusPago) {
    $badges = [
        'pendiente' => '<span class="badge bg-danger">Pendiente</span>',
        'parcial' => '<span class="badge bg-warning">Parcial</span>',
        'pagado' => '<span class="badge bg-success">Pagado</span>',
        'credito' => '<span class="badge bg-info">Crédito</span>',
    ];
    
    return $badges[$estatusPago] ?? '<span class="badge bg-secondary">' . e($estatusPago) . '</span>';
}

/**
 * Log de error
 * 
 * @param string $message
 * @param array $context
 */
function logError($message, $context = []) {
    $config = require __DIR__ . '/../config/app.php';
    $logFile = $config['log_path'] . 'error-' . date('Y-m-d') . '.log';
    
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
    $logMessage = "[{$timestamp}] ERROR: {$message}{$contextStr}\n";
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Log de info
 * 
 * @param string $message
 * @param array $context
 */
function logInfo($message, $context = []) {
    $config = require __DIR__ . '/../config/app.php';
    
    if ($config['log_level'] === 'debug' || $config['log_level'] === 'info') {
        $logFile = $config['log_path'] . 'info-' . date('Y-m-d') . '.log';
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] INFO: {$message}{$contextStr}\n";
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
