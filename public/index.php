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

// Definir rutas de la aplicación
$routes = [
    // Autenticación
    '/' => ['controller' => 'DashboardController', 'method' => 'index'],
    '/login' => ['controller' => 'AuthController', 'method' => 'login'],
    '/logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    '/recuperar-password' => ['controller' => 'AuthController', 'method' => 'recuperarPassword'],
    '/cambiar-password' => ['controller' => 'AuthController', 'method' => 'cambiarPassword'],
    
    // Dashboard
    '/dashboard' => ['controller' => 'DashboardController', 'method' => 'index'],
    
    // Pacientes
    '/pacientes' => ['controller' => 'PacienteController', 'method' => 'index'],
    '/pacientes/crear' => ['controller' => 'PacienteController', 'method' => 'crear'],
    '/pacientes/guardar' => ['controller' => 'PacienteController', 'method' => 'guardar'],
    '/pacientes/editar' => ['controller' => 'PacienteController', 'method' => 'editar'],
    '/pacientes/actualizar' => ['controller' => 'PacienteController', 'method' => 'actualizar'],
    '/pacientes/ver' => ['controller' => 'PacienteController', 'method' => 'ver'],
    '/pacientes/buscar' => ['controller' => 'PacienteController', 'method' => 'buscar'],
    '/pacientes/eliminar' => ['controller' => 'PacienteController', 'method' => 'eliminar'],
    
    // Órdenes
    '/ordenes' => ['controller' => 'OrdenController', 'method' => 'index'],
    '/ordenes/crear' => ['controller' => 'OrdenController', 'method' => 'crear'],
    '/ordenes/guardar' => ['controller' => 'OrdenController', 'method' => 'guardar'],
    '/ordenes/ver' => ['controller' => 'OrdenController', 'method' => 'ver'],
    '/ordenes/editar' => ['controller' => 'OrdenController', 'method' => 'editar'],
    '/ordenes/actualizar' => ['controller' => 'OrdenController', 'method' => 'actualizar'],
    '/ordenes/cancelar' => ['controller' => 'OrdenController', 'method' => 'cancelar'],
    '/ordenes/imprimir-etiquetas' => ['controller' => 'OrdenController', 'method' => 'imprimirEtiquetas'],
    '/ordenes/imprimir-orden' => ['controller' => 'OrdenController', 'method' => 'imprimirOrden'],
    '/ordenes/imprimir-recibo' => ['controller' => 'OrdenController', 'method' => 'imprimirRecibo'],
    '/ordenes/buscar-estudios' => ['controller' => 'OrdenController', 'method' => 'buscarEstudios'],
    '/ordenes/obtener-precio-estudio' => ['controller' => 'OrdenController', 'method' => 'obtenerPrecioEstudio'],
    
    // Resultados
    '/resultados' => ['controller' => 'ResultadoController', 'method' => 'index'],
    '/resultados/lista-trabajo' => ['controller' => 'ResultadoController', 'method' => 'listaTrabajo'],
    '/resultados/capturar' => ['controller' => 'ResultadoController', 'method' => 'capturar'],
    '/resultados/guardar' => ['controller' => 'ResultadoController', 'method' => 'guardar'],
    '/resultados/microbiologia' => ['controller' => 'ResultadoController', 'method' => 'microbiologia'],
    '/resultados/guardar-cultivo' => ['controller' => 'ResultadoController', 'method' => 'guardarCultivo'],
    '/resultados/antibiograma' => ['controller' => 'ResultadoController', 'method' => 'antibiograma'],
    '/resultados/guardar-antibiograma' => ['controller' => 'ResultadoController', 'method' => 'guardarAntibiograma'],
    '/resultados/validar' => ['controller' => 'ResultadoController', 'method' => 'validar'],
    '/resultados/liberar' => ['controller' => 'ResultadoController', 'method' => 'liberar'],
    '/resultados/imprimir' => ['controller' => 'ResultadoController', 'method' => 'imprimir'],
    
    // Pagos
    '/pagos/registrar' => ['controller' => 'PagoController', 'method' => 'registrar'],
    '/pagos/guardar' => ['controller' => 'PagoController', 'method' => 'guardar'],
    '/pagos/ver' => ['controller' => 'PagoController', 'method' => 'ver'],
    '/pagos/historial' => ['controller' => 'PagoController', 'method' => 'historial'],
    '/pagos/cancelar' => ['controller' => 'PagoController', 'method' => 'cancelar'],
    '/pagos/imprimir-recibo' => ['controller' => 'PagoController', 'method' => 'imprimirRecibo'],
    
    // Catálogos
    '/catalogos/estudios' => ['controller' => 'CatalogoController', 'method' => 'estudios'],
    '/catalogos/estudios/crear' => ['controller' => 'CatalogoController', 'method' => 'crearEstudio'],
    '/catalogos/estudios/guardar' => ['controller' => 'CatalogoController', 'method' => 'guardarEstudio'],
    '/catalogos/estudios/editar' => ['controller' => 'CatalogoController', 'method' => 'editarEstudio'],
    '/catalogos/precios' => ['controller' => 'CatalogoController', 'method' => 'precios'],
    '/catalogos/sucursales' => ['controller' => 'CatalogoController', 'method' => 'sucursales'],
    '/catalogos/areas' => ['controller' => 'CatalogoController', 'method' => 'areas'],
    '/catalogos/companias' => ['controller' => 'CatalogoController', 'method' => 'companias'],
    
    // Usuarios y Roles
    '/usuarios' => ['controller' => 'UsuarioController', 'method' => 'index'],
    '/usuarios/crear' => ['controller' => 'UsuarioController', 'method' => 'crear'],
    '/usuarios/guardar' => ['controller' => 'UsuarioController', 'method' => 'guardar'],
    '/usuarios/editar' => ['controller' => 'UsuarioController', 'method' => 'editar'],
    '/usuarios/actualizar' => ['controller' => 'UsuarioController', 'method' => 'actualizar'],
    '/usuarios/eliminar' => ['controller' => 'UsuarioController', 'method' => 'eliminar'],
    '/roles' => ['controller' => 'RolController', 'method' => 'index'],
    '/roles/editar' => ['controller' => 'RolController', 'method' => 'editar'],
    '/roles/actualizar-permisos' => ['controller' => 'RolController', 'method' => 'actualizarPermisos'],
    
    // Reportes
    '/reportes' => ['controller' => 'ReporteController', 'method' => 'index'],
    '/reportes/produccion' => ['controller' => 'ReporteController', 'method' => 'produccion'],
    '/reportes/ingresos' => ['controller' => 'ReporteController', 'method' => 'ingresos'],
    '/reportes/estudios' => ['controller' => 'ReporteController', 'method' => 'estudios'],
];

// Buscar y ejecutar la ruta
if (isset($routes[$path])) {
    $route = $routes[$path];
    
    $controllerName = $route['controller'];
    $methodName = $route['method'];
    
    $controllerFile = ROOT_PATH . '/controllers/' . $controllerName . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            
            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                http_response_code(404);
                echo "Método {$methodName} no encontrado";
            }
        } else {
            http_response_code(404);
            echo "Controlador {$controllerName} no encontrado";
        }
    } else {
        http_response_code(404);
        die("Archivo del controlador no encontrado: {$controllerName}<br>Buscado en: {$controllerFile}");
    }
    
} else {
    // Ruta no encontrada - 404
    http_response_code(404);
    
    if (Auth::check()) {
        // Usuario logueado - mostrar página 404
        echo "<!DOCTYPE html>
        <html><head><title>404</title></head><body style='text-align:center;padding:50px;'>
        <h1>404 - Página no encontrada</h1>
        <p>La ruta <strong>{$path}</strong> no existe</p>
        <a href='" . $config['base_url'] . "'>Volver al inicio</a>
        </body></html>";
    } else {
        // Usuario no logueado - redirigir a login
        header('Location: ' . $config['base_url'] . '/login');
        exit;
    }
}
