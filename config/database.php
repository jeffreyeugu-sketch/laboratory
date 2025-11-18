<?php
/**
 * Configuración de Base de Datos
 * Laboratorio Clínico
 */

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: 3305,
    'database' => getenv('DB_NAME') ?: 'laboratorio_clinico',
    'username' => getenv('DB_USER') ?: 'admin',
    'password' => getenv('DB_PASS') ?: 'admin',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
