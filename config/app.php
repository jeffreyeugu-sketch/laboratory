<?php
/**
 * Configuración General de la Aplicación
 * Laboratorio Clínico
 */

return [
    // Información de la aplicación
    'app_name' => 'Laboratorio Clínico',
    'app_version' => '1.0.0',
    'app_env' => getenv('APP_ENV') ?: 'production', // development, production
    'timezone' => 'America/Mexico_City', // ← CORREGIDO: era 'app_timezone'
    'locale' => 'es_MX',
    
    // URL base de la aplicación
    'base_url' => getenv('BASE_URL') ?: 'http://localhost/laboratorio-clinico/public',
    
    // Sesión
    'session_lifetime' => 7200, // 2 horas en segundos
    'session_name' => 'LAB_CLINICO_SESSION',
    
    // Seguridad
    'password_min_length' => 8,
    'password_require_uppercase' => true,
    'password_require_lowercase' => true,
    'password_require_numbers' => true,
    'password_require_special' => false,
    'password_expiration_days' => 90,
    'max_login_attempts' => 5,
    'lockout_duration' => 900, // 15 minutos en segundos
    
    // Uploads
    'upload_max_size' => 5242880, // 5MB en bytes
    'upload_allowed_types' => ['jpg', 'jpeg', 'png', 'pdf'],
    'upload_path' => __DIR__ . '/../public/uploads/',
    
    // PDFs
    'pdf_logo_path' => __DIR__ . '/../public/assets/img/logo.png',
    'pdf_temp_path' => __DIR__ . '/../storage/temp/',
    
    // Logs
    'log_path' => __DIR__ . '/../storage/logs/',
    'log_level' => getenv('LOG_LEVEL') ?: 'error', // debug, info, warning, error
    
    // Email (para futuro)
    'mail_from_address' => 'noreply@laboratorio.com',
    'mail_from_name' => 'Laboratorio Clínico',
    
    // Paginación
    'pagination_per_page' => 20,
    
    // Fecha y hora
    'date_format' => 'd/m/Y',
    'time_format' => 'H:i',
    'datetime_format' => 'd/m/Y H:i',
    
    // Formato de moneda
    'currency_symbol' => '$',
    'currency_decimals' => 2,
    'currency_decimal_separator' => '.',
    'currency_thousands_separator' => ',',
];
