<?php
/**
 * TEST SIMPLE - Verificar funcionamiento básico
 * Coloca este archivo en: C:\xampp\htdocs\laboratorio-clinico\public\test.php
 * Accede en: http://localhost/laboratorio-clinico/public/test.php
 */

echo "<!DOCTYPE html>";
echo "<html><head><title>Test</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo ".ok{color:green;} .error{color:red;}</style></head><body>";

echo "<h1>🔍 Test de Funcionamiento</h1>";

// Test 1: PHP funciona
echo "<div class='box'>";
echo "<h2>1. PHP Funciona</h2>";
echo "<p class='ok'>✓ Si ves esto, PHP está funcionando</p>";
echo "<p>Versión de PHP: " . PHP_VERSION . "</p>";
echo "</div>";

// Test 2: Ruta base
echo "<div class='box'>";
echo "<h2>2. Rutas del Sistema</h2>";
$rootPath = dirname(__DIR__);
echo "<p><strong>ROOT_PATH detectado:</strong> " . $rootPath . "</p>";
echo "<p><strong>Archivo actual:</strong> " . __FILE__ . "</p>";
echo "</div>";

// Test 3: Archivo config/app.php
echo "<div class='box'>";
echo "<h2>3. Archivo config/app.php</h2>";
$configFile = $rootPath . '/config/app.php';
if (file_exists($configFile)) {
    echo "<p class='ok'>✓ El archivo existe</p>";
    
    try {
        $config = require $configFile;
        echo "<p class='ok'>✓ Se puede cargar sin errores</p>";
        
        if (isset($config['base_url'])) {
            echo "<p class='ok'>✓ Tiene base_url definido</p>";
            echo "<p><strong>BASE_URL:</strong> " . htmlspecialchars($config['base_url']) . "</p>";
            
            if ($config['base_url'] === 'http://localhost/laboratorio-clinico/public') {
                echo "<p class='ok'>✓ BASE_URL está correcto</p>";
            } else {
                echo "<p class='error'>✗ BASE_URL no es el correcto</p>";
                echo "<p>Debería ser: http://localhost/laboratorio-clinico/public</p>";
            }
        } else {
            echo "<p class='error'>✗ No tiene base_url definido</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error al cargar: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p class='error'>✗ El archivo NO existe</p>";
    echo "<p>Buscado en: $configFile</p>";
}
echo "</div>";

// Test 4: Controladores
echo "<div class='box'>";
echo "<h2>4. Controladores</h2>";
$controllersPath = $rootPath . '/controllers';
$authController = $controllersPath . '/AuthController.php';
$dashboardController = $controllersPath . '/DashboardController.php';

if (file_exists($authController)) {
    echo "<p class='ok'>✓ AuthController.php existe</p>";
} else {
    echo "<p class='error'>✗ AuthController.php NO existe</p>";
}

if (file_exists($dashboardController)) {
    echo "<p class='ok'>✓ DashboardController.php existe</p>";
} else {
    echo "<p class='error'>✗ DashboardController.php NO existe</p>";
}
echo "</div>";

// Test 5: Vistas
echo "<div class='box'>";
echo "<h2>5. Vistas</h2>";
$loginView = $rootPath . '/views/auth/login.php';
$mainLayout = $rootPath . '/views/layouts/main.php';

if (file_exists($loginView)) {
    echo "<p class='ok'>✓ views/auth/login.php existe</p>";
} else {
    echo "<p class='error'>✗ views/auth/login.php NO existe</p>";
}

if (file_exists($mainLayout)) {
    echo "<p class='ok'>✓ views/layouts/main.php existe</p>";
} else {
    echo "<p class='error'>✗ views/layouts/main.php NO existe</p>";
}
echo "</div>";

// Test 6: Helpers
echo "<div class='box'>";
echo "<h2>6. Archivo helpers/functions.php</h2>";
$helpersFile = $rootPath . '/helpers/functions.php';
if (file_exists($helpersFile)) {
    echo "<p class='ok'>✓ helpers/functions.php existe</p>";
    
    require_once $helpersFile;
    
    if (function_exists('url')) {
        echo "<p class='ok'>✓ Función url() existe</p>";
        
        // Probar la función url()
        $testUrl = url('login');
        echo "<p><strong>url('login') retorna:</strong> " . htmlspecialchars($testUrl) . "</p>";
        
        if ($testUrl === 'http://localhost/laboratorio-clinico/public/login') {
            echo "<p class='ok'>✓ La función url() funciona correctamente</p>";
        } else {
            echo "<p class='error'>✗ La función url() NO retorna la URL correcta</p>";
            echo "<p>Debería retornar: http://localhost/laboratorio-clinico/public/login</p>";
        }
    } else {
        echo "<p class='error'>✗ Función url() NO existe</p>";
    }
    
    if (function_exists('redirect')) {
        echo "<p class='ok'>✓ Función redirect() existe</p>";
    } else {
        echo "<p class='error'>✗ Función redirect() NO existe</p>";
    }
} else {
    echo "<p class='error'>✗ helpers/functions.php NO existe</p>";
}
echo "</div>";

// Test 7: Core classes
echo "<div class='box'>";
echo "<h2>7. Clases Core</h2>";
$authClass = $rootPath . '/core/Auth.php';
$controllerClass = $rootPath . '/core/Controller.php';

if (file_exists($authClass)) {
    echo "<p class='ok'>✓ core/Auth.php existe</p>";
} else {
    echo "<p class='error'>✗ core/Auth.php NO existe</p>";
}

if (file_exists($controllerClass)) {
    echo "<p class='ok'>✓ core/Controller.php existe</p>";
} else {
    echo "<p class='error'>✗ core/Controller.php NO existe</p>";
}
echo "</div>";

// Test 8: Archivo public/index.php
echo "<div class='box'>";
echo "<h2>8. Archivo public/index.php</h2>";
$indexFile = dirname(__FILE__) . '/index.php';
if (file_exists($indexFile)) {
    echo "<p class='ok'>✓ public/index.php existe</p>";
    echo "<p><strong>Tamaño:</strong> " . filesize($indexFile) . " bytes</p>";
    echo "<p><strong>Última modificación:</strong> " . date('Y-m-d H:i:s', filemtime($indexFile)) . "</p>";
} else {
    echo "<p class='error'>✗ public/index.php NO existe</p>";
}
echo "</div>";

// Test 9: Acceso directo al index
echo "<div class='box'>";
echo "<h2>9. Test de Acceso</h2>";
echo "<p>Intenta acceder a estas URLs:</p>";
echo "<ul>";
echo "<li><a href='http://localhost/laboratorio-clinico/public/index.php' target='_blank'>http://localhost/laboratorio-clinico/public/index.php</a> (acceso directo)</li>";
echo "<li><a href='http://localhost/laboratorio-clinico/public/' target='_blank'>http://localhost/laboratorio-clinico/public/</a> (con mod_rewrite)</li>";
echo "<li><a href='http://localhost/laboratorio-clinico/public/login' target='_blank'>http://localhost/laboratorio-clinico/public/login</a> (ruta login)</li>";
echo "</ul>";
echo "</div>";

// Resumen
echo "<div class='box' style='background:#e7f3ff;border-left:4px solid #0066cc;'>";
echo "<h2>📋 Próximo Paso</h2>";
echo "<p><strong>Después de ver los resultados de este test:</strong></p>";
echo "<ol>";
echo "<li>Toma un pantallazo de TODA esta página</li>";
echo "<li>Intenta hacer clic en los enlaces del Test 9</li>";
echo "<li>Anota qué pasa con cada enlace</li>";
echo "<li>Comparte toda esa información</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>
