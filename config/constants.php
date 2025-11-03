<?php
/**
 * Constantes del Sistema
 * Laboratorio Clínico
 */

// ================================================
// RUTAS DEL SISTEMA
// ================================================

// Ruta raíz del proyecto (ya definida en index.php, pero la redefinimos por si acaso)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', ROOT_PATH);
}

// Rutas de directorios principales
define('CORE_PATH', ROOT_PATH . '/core');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('MODELS_PATH', ROOT_PATH . '/models');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('HELPERS_PATH', ROOT_PATH . '/helpers');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('DATABASE_PATH', ROOT_PATH . '/database');
define('PDF_PATH', ROOT_PATH . '/pdf');

// Rutas dentro de public
define('ASSETS_PATH', PUBLIC_PATH . '/assets');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Rutas dentro de storage
define('LOGS_PATH', STORAGE_PATH . '/logs');
define('TEMP_PATH', STORAGE_PATH . '/temp');

// Cargar configuración para obtener BASE_URL
if (file_exists(CONFIG_PATH . '/app.php')) {
    $appConfig = require CONFIG_PATH . '/app.php';
    if (!defined('BASE_URL')) {
        define('BASE_URL', $appConfig['base_url']);
    }
}

// ================================================
// ESTATUS DE ÓRDENES
// ================================================

define('ORDEN_ESTATUS_REGISTRADA', 'registrada');
define('ORDEN_ESTATUS_EN_PROCESO', 'en_proceso');
define('ORDEN_ESTATUS_PARCIAL', 'parcial');
define('ORDEN_ESTATUS_VALIDADA', 'validada');
define('ORDEN_ESTATUS_LIBERADA', 'liberada');
define('ORDEN_ESTATUS_ENTREGADA', 'entregada');
define('ORDEN_ESTATUS_CANCELADA', 'cancelada');

// ================================================
// ESTATUS DE PAGO
// ================================================

define('PAGO_ESTATUS_PENDIENTE', 'pendiente');
define('PAGO_ESTATUS_PARCIAL', 'parcial');
define('PAGO_ESTATUS_PAGADO', 'pagado');
define('PAGO_ESTATUS_CREDITO', 'credito');

// ================================================
// PRIORIDADES
// ================================================

define('PRIORIDAD_NORMAL', 'normal');
define('PRIORIDAD_URGENTE', 'urgente');
define('PRIORIDAD_STAT', 'stat');

// ================================================
// ESTATUS DE ESTUDIOS
// ================================================

define('ESTUDIO_ESTATUS_PENDIENTE', 'pendiente');
define('ESTUDIO_ESTATUS_CAPTURADO', 'capturado');
define('ESTUDIO_ESTATUS_VALIDADO', 'validado');
define('ESTUDIO_ESTATUS_LIBERADO', 'liberado');

// ================================================
// TIPOS DE VALIDACIÓN
// ================================================

define('VALIDACION_TECNICA', 'tecnica');
define('VALIDACION_MEDICA', 'medica');

// ================================================
// MÉTODOS DE CAPTURA
// ================================================

define('CAPTURA_MANUAL', 'manual');
define('CAPTURA_INTERFAZ', 'interfaz');
define('CAPTURA_CALCULADO', 'calculado');

// ================================================
// TIPOS DE PROCEDENCIA
// ================================================

define('PROCEDENCIA_PARTICULAR', 'particular');
define('PROCEDENCIA_EMPRESA', 'empresa');
define('PROCEDENCIA_MEDICO', 'medico');

// ================================================
// SEXO
// ================================================

define('SEXO_MASCULINO', 'M');
define('SEXO_FEMENINO', 'F');
define('SEXO_OTRO', 'O');

// ================================================
// TIPOS DE MICROORGANISMO
// ================================================

define('MICROORGANISMO_BACTERIA', 'bacteria');
define('MICROORGANISMO_HONGO', 'hongo');
define('MICROORGANISMO_PARASITO', 'parasito');
define('MICROORGANISMO_VIRUS', 'virus');

// ================================================
// SENSIBILIDAD ANTIBIÓTICA
// ================================================

define('SENSIBILIDAD_SENSIBLE', 'S');
define('SENSIBILIDAD_INTERMEDIO', 'I');
define('SENSIBILIDAD_RESISTENTE', 'R');

// ================================================
// DESARROLLO MICROBIANO
// ================================================

define('DESARROLLO_NO', 'no');
define('DESARROLLO_SI', 'si');
define('DESARROLLO_ESCASO', 'escaso');
define('DESARROLLO_MODERADO', 'moderado');
define('DESARROLLO_ABUNDANTE', 'abundante');

// ================================================
// ROLES DEL SISTEMA
// ================================================

define('ROL_SUPERUSUARIO', 1);
define('ROL_ADMINISTRADOR', 2);
define('ROL_QUIMICO_SUPERVISOR', 3);
define('ROL_QUIMICO_ESTANDAR', 4);
define('ROL_RECEPCIONISTA', 5);

// ================================================
// PERMISOS
// ================================================

define('PERMISO_VER', 'view');
define('PERMISO_CREAR', 'create');
define('PERMISO_EDITAR', 'edit');
define('PERMISO_ELIMINAR', 'delete');
define('PERMISO_ADMIN', 'admin');

// ================================================
// FORMATOS
// ================================================

define('DATE_FORMAT', 'd/m/Y');
define('TIME_FORMAT', 'H:i');
define('DATETIME_FORMAT', 'd/m/Y H:i');
define('DATETIME_SQL_FORMAT', 'Y-m-d H:i:s');
define('DATE_SQL_FORMAT', 'Y-m-d');

// ================================================
// OTROS
// ================================================

define('ITEMS_PER_PAGE', 20);
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('SESSION_TIMEOUT', 7200); // 2 horas
