<?php
/**
 * Script de Diagnóstico - Verificar BASE_URL
 * Coloca este archivo en: C:\xampp\htdocs\laboratorio-clinico\diagnostico-base-url.php
 * Accede en: http://localhost/laboratorio-clinico/diagnostico-base-url.php
 */

// Iniciar output
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico BASE_URL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .ok { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .status {
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .status.ok { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
        .status.warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de BASE_URL</h1>
    
    <?php
    $baseDir = __DIR__;
    
    // Verificar archivo .env
    echo '<div class="box">';
    echo '<h2>1. Archivo .env</h2>';
    
    $envFile = $baseDir . '/.env';
    $envExists = file_exists($envFile);
    
    if ($envExists) {
        echo '<div class="status ok">✓ El archivo .env existe</div>';
        
        // Leer contenido
        $envContent = file_get_contents($envFile);
        
        // Buscar BASE_URL
        if (preg_match('/BASE_URL\s*=\s*(.+)/', $envContent, $matches)) {
            $baseUrlValue = trim($matches[1]);
            echo '<p><strong>BASE_URL encontrado en .env:</strong></p>';
            echo '<pre>' . htmlspecialchars($baseUrlValue) . '</pre>';
            
            if ($baseUrlValue === 'http://localhost/laboratorio-clinico/public') {
                echo '<div class="status ok">✓ BASE_URL está configurado correctamente</div>';
            } else {
                echo '<div class="status error">✗ BASE_URL NO está configurado correctamente</div>';
                echo '<p>Debería ser: <code>http://localhost/laboratorio-clinico/public</code></p>';
            }
        } else {
            echo '<div class="status error">✗ BASE_URL no encontrado en el archivo .env</div>';
            echo '<p>Agrega esta línea al archivo .env:</p>';
            echo '<pre>BASE_URL=http://localhost/laboratorio-clinico/public</pre>';
        }
        
        echo '<p><strong>Contenido completo del .env:</strong></p>';
        echo '<pre>' . htmlspecialchars($envContent) . '</pre>';
        
    } else {
        echo '<div class="status error">✗ El archivo .env NO existe</div>';
        echo '<p>Debes crear el archivo: <code>' . $envFile . '</code></p>';
        echo '<p>Copia el archivo .env.example y renómbralo a .env, luego agrega:</p>';
        echo '<pre>BASE_URL=http://localhost/laboratorio-clinico/public</pre>';
    }
    echo '</div>';
    
    // Verificar carga de variables
    echo '<div class="box">';
    echo '<h2>2. Variables de Entorno Cargadas</h2>';
    
    $baseUrlEnv = getenv('BASE_URL');
    if ($baseUrlEnv) {
        echo '<div class="status ok">✓ BASE_URL se cargó en getenv()</div>';
        echo '<pre>getenv("BASE_URL") = ' . htmlspecialchars($baseUrlEnv) . '</pre>';
    } else {
        echo '<div class="status error">✗ BASE_URL NO se cargó en getenv()</div>';
        echo '<p>Esto significa que el archivo .env no se está leyendo correctamente en public/index.php</p>';
    }
    echo '</div>';
    
    // Verificar config/app.php
    echo '<div class="box">';
    echo '<h2>3. Archivo config/app.php</h2>';
    
    $appConfigFile = $baseDir . '/config/app.php';
    if (file_exists($appConfigFile)) {
        echo '<div class="status ok">✓ El archivo config/app.php existe</div>';
        
        $config = require $appConfigFile;
        
        echo '<p><strong>BASE_URL en config:</strong></p>';
        echo '<pre>' . htmlspecialchars($config['base_url']) . '</pre>';
        
        if ($config['base_url'] === 'http://localhost/laboratorio-clinico/public') {
            echo '<div class="status ok">✓ BASE_URL correcto en config</div>';
        } elseif ($config['base_url'] === 'http://localhost') {
            echo '<div class="status error">✗ BASE_URL usa valor por defecto (el .env no se leyó)</div>';
            echo '<p>El archivo .env no se está cargando correctamente</p>';
        } else {
            echo '<div class="status warning">⚠ BASE_URL tiene un valor inesperado</div>';
        }
    } else {
        echo '<div class="status error">✗ El archivo config/app.php NO existe</div>';
    }
    echo '</div>';
    
    // Verificar helpers/functions.php
    echo '<div class="box">';
    echo '<h2>4. Archivo helpers/functions.php</h2>';
    
    $functionsFile = $baseDir . '/helpers/functions.php';
    if (file_exists($functionsFile)) {
        echo '<div class="status ok">✓ El archivo helpers/functions.php existe</div>';
        
        // Verificar si tiene la función base_url correcta
        $functionsContent = file_get_contents($functionsFile);
        
        if (strpos($functionsContent, 'function base_url()') !== false || strpos($functionsContent, 'function url(') !== false) {
            echo '<div class="status ok">✓ Contiene funciones de URL</div>';
            
            // Verificar si es la versión nueva (con static $baseUrl)
            if (strpos($functionsContent, 'static $baseUrl') !== false) {
                echo '<div class="status ok">✓ Es la versión actualizada con caché</div>';
            } else {
                echo '<div class="status warning">⚠ Parece ser la versión antigua (sin caché)</div>';
                echo '<p>Deberías reemplazarlo con la versión actualizada del paquete</p>';
            }
        } else {
            echo '<div class="status error">✗ No contiene las funciones de URL necesarias</div>';
        }
        
        echo '<p><strong>Primeras líneas del archivo:</strong></p>';
        $lines = array_slice(explode("\n", $functionsContent), 0, 30);
        echo '<pre>' . htmlspecialchars(implode("\n", $lines)) . '</pre>';
        
    } else {
        echo '<div class="status error">✗ El archivo helpers/functions.php NO existe</div>';
    }
    echo '</div>';
    
    // Verificar controladores
    echo '<div class="box">';
    echo '<h2>5. Controladores</h2>';
    
    $authController = $baseDir . '/controllers/AuthController.php';
    $dashboardController = $baseDir . '/controllers/DashboardController.php';
    
    if (file_exists($authController)) {
        echo '<div class="status ok">✓ AuthController.php existe</div>';
    } else {
        echo '<div class="status error">✗ AuthController.php NO existe</div>';
    }
    
    if (file_exists($dashboardController)) {
        echo '<div class="status ok">✓ DashboardController.php existe</div>';
    } else {
        echo '<div class="status error">✗ DashboardController.php NO existe</div>';
    }
    echo '</div>';
    
    // Verificar vistas
    echo '<div class="box">';
    echo '<h2>6. Vistas</h2>';
    
    $loginView = $baseDir . '/views/auth/login.php';
    $mainLayout = $baseDir . '/views/layouts/main.php';
    $dashboardView = $baseDir . '/views/dashboard/index.php';
    
    if (file_exists($loginView)) {
        echo '<div class="status ok">✓ views/auth/login.php existe</div>';
    } else {
        echo '<div class="status error">✗ views/auth/login.php NO existe</div>';
    }
    
    if (file_exists($mainLayout)) {
        echo '<div class="status ok">✓ views/layouts/main.php existe</div>';
    } else {
        echo '<div class="status error">✗ views/layouts/main.php NO existe</div>';
    }
    
    if (file_exists($dashboardView)) {
        echo '<div class="status ok">✓ views/dashboard/index.php existe</div>';
    } else {
        echo '<div class="status error">✗ views/dashboard/index.php NO existe</div>';
    }
    echo '</div>';
    
    // Resumen y solución
    echo '<div class="box">';
    echo '<h2>📋 Resumen y Solución</h2>';
    
    if (!$envExists) {
        echo '<div class="status error">';
        echo '<h3>PROBLEMA PRINCIPAL: Falta el archivo .env</h3>';
        echo '<p><strong>Solución:</strong></p>';
        echo '<ol>';
        echo '<li>Crea un archivo llamado <code>.env</code> en: <code>' . $baseDir . '</code></li>';
        echo '<li>Agrega este contenido:</li>';
        echo '</ol>';
        echo '<pre>';
        echo 'DB_HOST=localhost' . "\n";
        echo 'DB_PORT=3306' . "\n";
        echo 'DB_NAME=laboratorio_clinico' . "\n";
        echo 'DB_USER=root' . "\n";
        echo 'DB_PASS=' . "\n\n";
        echo 'APP_ENV=development' . "\n";
        echo 'BASE_URL=http://localhost/laboratorio-clinico/public' . "\n";
        echo 'APP_TIMEZONE=America/Mexico_City' . "\n";
        echo '</pre>';
        echo '<li>Guarda el archivo y recarga esta página</li>';
        echo '</div>';
    } elseif (!$baseUrlEnv || $baseUrlEnv !== 'http://localhost/laboratorio-clinico/public') {
        echo '<div class="status warning">';
        echo '<h3>PROBLEMA: BASE_URL no está configurado correctamente</h3>';
        echo '<p><strong>Solución:</strong></p>';
        echo '<ol>';
        echo '<li>Abre el archivo <code>.env</code></li>';
        echo '<li>Busca la línea que dice <code>BASE_URL=...</code></li>';
        echo '<li>Cámbiala por: <code>BASE_URL=http://localhost/laboratorio-clinico/public</code></li>';
        echo '<li>Si no existe, agrégala</li>';
        echo '<li>Guarda y recarga esta página</li>';
        echo '</ol>';
        echo '</div>';
    } else {
        echo '<div class="status ok">';
        echo '<h3>✓ Configuración Correcta</h3>';
        echo '<p>El BASE_URL está configurado correctamente. Ahora:</p>';
        echo '<ol>';
        echo '<li>Cierra COMPLETAMENTE tu navegador (todas las ventanas)</li>';
        echo '<li>Abre el navegador nuevamente</li>';
        echo '<li>Ve a: <a href="http://localhost/laboratorio-clinico/public/">http://localhost/laboratorio-clinico/public/</a></li>';
        echo '<li>Deberías ver la página de login</li>';
        echo '</ol>';
        echo '</div>';
    }
    
    echo '</div>';
    ?>
    
    <div class="box" style="text-align: center; background: #f8f9fa;">
        <p>Fecha: <?= date('Y-m-d H:i:s') ?></p>
        <p><strong>Siguiente paso:</strong> Una vez que todo esté OK, elimina este archivo por seguridad</p>
    </div>
</body>
</html>
