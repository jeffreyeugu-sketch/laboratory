<?php
/**
 * Laboratorio Clínico - Sistema de Gestión
 * Punto de entrada principal
 */

// Iniciar sesión
session_start();

// Definir la ruta base
define('ROOT_PATH', dirname(__DIR__));
define('BASE_PATH', ROOT_PATH);

// Cargar configuración PRIMERO (antes de todo)
$config = require ROOT_PATH . '/config/app.php';

// Cargar constantes
require_once ROOT_PATH . '/config/constants.php';

// Configurar timezone
date_default_timezone_set($config['timezone']);

// Cargar helpers
require_once ROOT_PATH . '/helpers/functions.php';

// Cargar clases core
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/Validator.php';

// ============================================================================
// AUTOLOADER PARA MODELOS
// ============================================================================
spl_autoload_register(function ($className) {
    // Lista de directorios donde buscar clases
    $directories = [
        ROOT_PATH . '/models/',
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/core/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Manejo de errores
if ($config['app_env'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/storage/logs/php-errors.log');

// Obtener la ruta solicitada
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($scriptName, '', $requestUri);
$path = '/' . trim(parse_url($path, PHP_URL_PATH), '/');

// Separar ruta de query string
$pathParts = explode('?', $path);
$path = $pathParts[0];

// ============================================================================
// DEFINICIÓN DE RUTAS ESTÁTICAS
// ============================================================================
$routes = [
    // Autenticación
    '/' => ['controller' => 'DashboardController', 'method' => 'index'],
    '/login' => ['controller' => 'AuthController', 'method' => 'login'],
    '/logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    '/recuperar-password' => ['controller' => 'AuthController', 'method' => 'recuperarPassword'],
    '/cambiar-password' => ['controller' => 'AuthController', 'method' => 'cambiarPassword'],
    
    // Dashboard
    '/dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
    
    // Pacientes - Rutas estáticas (sin parámetros)
    '/pacientes' => ['controller' => 'PacienteController', 'method' => 'index'],
    '/pacientes/listar' => ['controller' => 'PacienteController', 'method' => 'listar'],
    '/pacientes/crear' => ['controller' => 'PacienteController', 'method' => 'crear'],
    '/pacientes/guardar' => ['controller' => 'PacienteController', 'method' => 'guardar'],
    '/pacientes/buscar' => ['controller' => 'PacienteController', 'method' => 'buscar'],
    
    // Órdenes - Rutas estáticas
    '/ordenes' => ['controller' => 'OrdenController', 'method' => 'index'],
    '/ordenes/crear' => ['controller' => 'OrdenController', 'method' => 'crear'],
    '/ordenes/guardar' => ['controller' => 'OrdenController', 'method' => 'guardar'],
    '/ordenes/buscar-estudios' => ['controller' => 'OrdenController', 'method' => 'buscarEstudios'],
    '/ordenes/obtener-precio-estudio' => ['controller' => 'OrdenController', 'method' => 'obtenerPrecioEstudio'],
    '/ordenes/imprimir-etiquetas' => ['controller' => 'OrdenController', 'method' => 'imprimirEtiquetas'],
    '/ordenes/imprimir-orden' => ['controller' => 'OrdenController', 'method' => 'imprimirOrden'],
    '/ordenes/imprimir-recibo' => ['controller' => 'OrdenController', 'method' => 'imprimirRecibo'],
    
    // Resultados
    '/resultados' => ['controller' => 'ResultadoController', 'method' => 'index'],
    '/resultados/lista-trabajo' => ['controller' => 'ResultadoController', 'method' => 'listaTrabajo'],
    '/resultados/microbiologia' => ['controller' => 'ResultadoController', 'method' => 'microbiologia'],
    '/resultados/capturar' => ['controller' => 'ResultadoController', 'method' => 'capturar'],
    '/resultados/guardar' => ['controller' => 'ResultadoController', 'method' => 'guardar'],
    '/resultados/guardar-cultivo' => ['controller' => 'ResultadoController', 'method' => 'guardarCultivo'],
    '/resultados/antibiograma' => ['controller' => 'ResultadoController', 'method' => 'antibiograma'],
    '/resultados/guardar-antibiograma' => ['controller' => 'ResultadoController', 'method' => 'guardarAntibiograma'],
    '/resultados/validar' => ['controller' => 'ResultadoController', 'method' => 'validar'],
    '/resultados/liberar' => ['controller' => 'ResultadoController', 'method' => 'liberar'],
    '/resultados/imprimir' => ['controller' => 'ResultadoController', 'method' => 'imprimir'],
    
    // Pagos
    '/pagos/registrar' => ['controller' => 'PagoController', 'method' => 'registrar'],
    '/pagos/guardar' => ['controller' => 'PagoController', 'method' => 'guardar'],
    '/pagos/historial' => ['controller' => 'PagoController', 'method' => 'historial'],
    '/pagos/cancelar' => ['controller' => 'PagoController', 'method' => 'cancelar'],
    '/pagos/imprimir-recibo' => ['controller' => 'PagoController', 'method' => 'imprimirRecibo'],
    
    // Catálogos
    '/catalogos/estudios' => ['controller' => 'EstudioController', 'method' => 'index'],
    '/catalogos/estudios/crear' => ['controller' => 'EstudioController', 'method' => 'crear'],
    '/catalogos/estudios/guardar' => ['controller' => 'EstudioController', 'method' => 'guardar'],
    '/catalogos/estudios/listar' => ['controller' => 'EstudioController', 'method' => 'listar'],
    
    // CATÁLOGO: LABORATORIOS DE REFERENCIA
    '/catalogos/laboratorios-referencia' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'index'],
    '/catalogos/laboratorios-referencia/crear' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'crear'],
    '/catalogos/laboratorios-referencia/guardar' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'guardar'],
    '/catalogos/laboratorios-referencia/listar' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'listar'],
    
    // CATÁLOGO: METODOLOGÍAS
    '/catalogos/metodologias' => ['controller' => 'MetodologiaController', 'method' => 'index'],
    '/catalogos/metodologias/crear' => ['controller' => 'MetodologiaController', 'method' => 'crear'],
    '/catalogos/metodologias/guardar' => ['controller' => 'MetodologiaController', 'method' => 'guardar'],
    '/catalogos/metodologias/listar' => ['controller' => 'MetodologiaController', 'method' => 'listar'],
    
    // CATÁLOGO: TIPOS DE MUESTRA
    '/catalogos/tipos-muestra' => ['controller' => 'TipoMuestraController', 'method' => 'index'],
    '/catalogos/tipos-muestra/crear' => ['controller' => 'TipoMuestraController', 'method' => 'crear'],
    '/catalogos/tipos-muestra/guardar' => ['controller' => 'TipoMuestraController', 'method' => 'guardar'],
    '/catalogos/tipos-muestra/listar' => ['controller' => 'TipoMuestraController', 'method' => 'listar'],
    
    // CATÁLOGO: ETIQUETAS
    '/catalogos/etiquetas' => ['controller' => 'EtiquetaController', 'method' => 'index'],
    '/catalogos/etiquetas/crear' => ['controller' => 'EtiquetaController', 'method' => 'crear'],
    '/catalogos/etiquetas/guardar' => ['controller' => 'EtiquetaController', 'method' => 'guardar'],
    '/catalogos/etiquetas/listar' => ['controller' => 'EtiquetaController', 'method' => 'listar'],
    
    // CATÁLOGO: INDICACIONES
    '/catalogos/indicaciones' => ['controller' => 'IndicacionController', 'method' => 'index'],
    '/catalogos/indicaciones/crear' => ['controller' => 'IndicacionController', 'method' => 'crear'],
    '/catalogos/indicaciones/guardar' => ['controller' => 'IndicacionController', 'method' => 'guardar'],
    '/catalogos/indicaciones/listar' => ['controller' => 'IndicacionController', 'method' => 'listar'],
    
    // CATÁLOGO: DEPARTAMENTOS
    '/catalogos/departamentos' => ['controller' => 'DepartamentoController', 'method' => 'index'],
    '/catalogos/departamentos/crear' => ['controller' => 'DepartamentoController', 'method' => 'crear'],
    '/catalogos/departamentos/guardar' => ['controller' => 'DepartamentoController', 'method' => 'guardar'],
    '/catalogos/departamentos/listar' => ['controller' => 'DepartamentoController', 'method' => 'listar'],
    
    // Usuarios
    '/usuarios' => ['controller' => 'UsuarioController', 'method' => 'index'],
    '/usuarios/crear' => ['controller' => 'UsuarioController', 'method' => 'crear'],
    '/usuarios/guardar' => ['controller' => 'UsuarioController', 'method' => 'guardar'],
    
    // Roles
    '/roles' => ['controller' => 'RolController', 'method' => 'index'],
    
    // Reportes
    '/reportes/produccion' => ['controller' => 'ReporteController', 'method' => 'produccion'],
    '/reportes/ingresos' => ['controller' => 'ReporteController', 'method' => 'ingresos'],
    '/reportes/estudios' => ['controller' => 'ReporteController', 'method' => 'estudios'],
    '/reportes/pacientes' => ['controller' => 'ReporteController', 'method' => 'pacientes'],
];

// ============================================================================
// RUTAS DINÁMICAS (CON PARÁMETROS)
// ============================================================================
$dynamicRoutes = [
    // Pacientes con ID
    '/pacientes/ver/' => ['controller' => 'PacienteController', 'method' => 'ver'],
    '/pacientes/editar/' => ['controller' => 'PacienteController', 'method' => 'editar'],
    '/pacientes/actualizar/' => ['controller' => 'PacienteController', 'method' => 'actualizar'],
    '/pacientes/eliminar/' => ['controller' => 'PacienteController', 'method' => 'eliminar'],
    
    // Órdenes con ID
    '/ordenes/ver/' => ['controller' => 'OrdenController', 'method' => 'ver'],
    '/ordenes/editar/' => ['controller' => 'OrdenController', 'method' => 'editar'],
    '/ordenes/actualizar/' => ['controller' => 'OrdenController', 'method' => 'actualizar'],
    '/ordenes/cancelar/' => ['controller' => 'OrdenController', 'method' => 'cancelar'],
    
    // Estudios con ID
    '/catalogos/estudios/ver/' => ['controller' => 'EstudioController', 'method' => 'ver'],
    '/catalogos/estudios/editar/' => ['controller' => 'EstudioController', 'method' => 'editar'],
    '/catalogos/estudios/actualizar/' => ['controller' => 'EstudioController', 'method' => 'actualizar'],
    '/catalogos/estudios/eliminar/' => ['controller' => 'EstudioController', 'method' => 'eliminar'],
    '/catalogos/estudios/cambiar-estado/' => ['controller' => 'EstudioController', 'method' => 'cambiarEstado'],
    
    // Laboratorios de Referencia con ID
    '/catalogos/laboratorios-referencia/ver/' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'ver'],
    '/catalogos/laboratorios-referencia/editar/' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'editar'],
    '/catalogos/laboratorios-referencia/actualizar/' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'actualizar'],
    '/catalogos/laboratorios-referencia/eliminar/' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'eliminar'],
    '/catalogos/laboratorios-referencia/cambiar-estado/' => ['controller' => 'LaboratorioReferenciaController', 'method' => 'cambiarEstado'],
    
    // Metodologías con ID
    '/catalogos/metodologias/editar/' => ['controller' => 'MetodologiaController', 'method' => 'editar'],
    '/catalogos/metodologias/actualizar/' => ['controller' => 'MetodologiaController', 'method' => 'actualizar'],
    '/catalogos/metodologias/eliminar/' => ['controller' => 'MetodologiaController', 'method' => 'eliminar'],
    
    // Tipos de Muestra con ID
    '/catalogos/tipos-muestra/editar/' => ['controller' => 'TipoMuestraController', 'method' => 'editar'],
    '/catalogos/tipos-muestra/actualizar/' => ['controller' => 'TipoMuestraController', 'method' => 'actualizar'],
    '/catalogos/tipos-muestra/eliminar/' => ['controller' => 'TipoMuestraController', 'method' => 'eliminar'],
    
    // Etiquetas con ID
    '/catalogos/etiquetas/editar/' => ['controller' => 'EtiquetaController', 'method' => 'editar'],
    '/catalogos/etiquetas/actualizar/' => ['controller' => 'EtiquetaController', 'method' => 'actualizar'],
    '/catalogos/etiquetas/eliminar/' => ['controller' => 'EtiquetaController', 'method' => 'eliminar'],
    
    // Indicaciones con ID
    '/catalogos/indicaciones/editar/' => ['controller' => 'IndicacionController', 'method' => 'editar'],
    '/catalogos/indicaciones/actualizar/' => ['controller' => 'IndicacionController', 'method' => 'actualizar'],
    '/catalogos/indicaciones/eliminar/' => ['controller' => 'IndicacionController', 'method' => 'eliminar'],
    
    // Departamentos con ID
    '/catalogos/departamentos/editar/' => ['controller' => 'DepartamentoController', 'method' => 'editar'],
    '/catalogos/departamentos/actualizar/' => ['controller' => 'DepartamentoController', 'method' => 'actualizar'],
    '/catalogos/departamentos/eliminar/' => ['controller' => 'DepartamentoController', 'method' => 'eliminar'],
    
    // Usuarios con ID
    '/usuarios/ver/' => ['controller' => 'UsuarioController', 'method' => 'ver'],
    '/usuarios/editar/' => ['controller' => 'UsuarioController', 'method' => 'editar'],
    '/usuarios/actualizar/' => ['controller' => 'UsuarioController', 'method' => 'actualizar'],
    '/usuarios/eliminar/' => ['controller' => 'UsuarioController', 'method' => 'eliminar'],
    '/usuarios/cambiar-estado/' => ['controller' => 'UsuarioController', 'method' => 'cambiarEstado'],
    
    // Roles con ID
    '/roles/editar/' => ['controller' => 'RolController', 'method' => 'editar'],
    '/roles/actualizar-permisos/' => ['controller' => 'RolController', 'method' => 'actualizarPermisos'],
];

// ============================================================================
// FUNCIÓN PARA EJECUTAR UNA RUTA
// ============================================================================
function executeRoute($controllerName, $methodName, $params = []) {
    $controllerFile = ROOT_PATH . '/controllers/' . $controllerName . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            
            if (method_exists($controller, $methodName)) {
                // Llamar al método con parámetros si existen
                if (!empty($params)) {
                    call_user_func_array([$controller, $methodName], $params);
                } else {
                    $controller->$methodName();
                }
                return true;
            }
        }
    }
    return false;
}

// ============================================================================
// SISTEMA DE ROUTING - BUSCAR Y EJECUTAR RUTA
// ============================================================================
$routeFound = false;

// 1. Intentar rutas estáticas primero
if (isset($routes[$path])) {
    $route = $routes[$path];
    $routeFound = executeRoute($route['controller'], $route['method']);
}

// 2. Si no se encuentra, intentar rutas dinámicas
if (!$routeFound) {
    foreach ($dynamicRoutes as $pattern => $route) {
        // Verificar si la ruta actual comienza con el patrón dinámico
        if (strpos($path, $pattern) === 0) {
            // Extraer el ID o parámetro
            $param = str_replace($pattern, '', $path);
            $param = trim($param, '/');
            
            // Solo ejecutar si hay un parámetro válido
            if (!empty($param)) {
                $routeFound = executeRoute($route['controller'], $route['method'], [$param]);
                break;
            }
        }
    }
}

// 3. Ruta no encontrada - 404
if (!$routeFound) {
    http_response_code(404);
    
    if (Auth::check()) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>404 - Página no encontrada</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    padding: 50px;
                    background-color: #f5f5f5;
                }
                .error-container {
                    background: white;
                    border-radius: 8px;
                    padding: 40px;
                    max-width: 600px;
                    margin: 0 auto;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                h1 {
                    color: #e74c3c;
                    font-size: 72px;
                    margin: 0;
                }
                h2 {
                    color: #333;
                    font-size: 24px;
                }
                p {
                    color: #666;
                    font-size: 16px;
                }
                a {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 30px;
                    background-color: #3498db;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background-color 0.3s;
                }
                a:hover {
                    background-color: #2980b9;
                }
            </style>
        </head>
        <body>
            <div class='error-container'>
                <h1>404</h1>
                <h2>Página no encontrada</h2>
                <p>La ruta <strong>{$path}</strong> no existe en el sistema</p>
                <a href='" . base_url() . "'>Volver al inicio</a>
            </div>
        </body>
        </html>";
    } else {
        // Si no está autenticado, redirigir al login
        header('Location: ' . base_url() . '/login');
        exit;
    }
}
