<?php
/**
 * SCRIPT DE DIAGNÓSTICO DE LOGIN
 * Este script verifica la configuración de la base de datos y el usuario admin
 */

// Configuración manual - EDITAR ESTOS VALORES
$DB_HOST = 'localhost';
$DB_PORT = '3306';          // ← CAMBIAR A 3305
$DB_NAME = 'laboratorio_clinico';
$DB_USER = 'admin';          // Tu usuario de MariaDB
$DB_PASS = 'admin'; // Tu contraseña de MariaDB

echo "<h1>Diagnóstico de Login - Laboratorio Clínico</h1>";
echo "<hr>";

// ============================================
// 1. VERIFICAR CONEXIÓN A BASE DE DATOS
// ============================================
echo "<h2>1. Verificando conexión a base de datos...</h2>";

try {
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Conexión exitosa a la base de datos</p>";
    echo "<ul>";
    echo "<li>Host: {$DB_HOST}</li>";
    echo "<li>Database: {$DB_NAME}</li>";
    echo "<li>Usuario: {$DB_USER}</li>";
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ ERROR DE CONEXIÓN:</p>";
    echo "<pre>{$e->getMessage()}</pre>";
    echo "<p><strong>Solución:</strong> Verifica las credenciales en este archivo (líneas 8-11)</p>";
    die();
}

// ============================================
// 2. VERIFICAR SI EXISTE LA TABLA USUARIOS
// ============================================
echo "<h2>2. Verificando tabla usuarios...</h2>";

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "<p style='color: green;'>✓ La tabla 'usuarios' existe</p>";
    } else {
        echo "<p style='color: red;'>✗ La tabla 'usuarios' NO existe</p>";
        echo "<p><strong>Solución:</strong> Importa el archivo database/schema.sql</p>";
        die();
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ ERROR: {$e->getMessage()}</p>";
    die();
}

// ============================================
// 3. VERIFICAR SI EXISTE EL USUARIO ADMIN
// ============================================
echo "<h2>3. Verificando usuario admin...</h2>";

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<p style='color: green;'>✓ El usuario 'admin' existe en la base de datos</p>";
        echo "<ul>";
        echo "<li>ID: {$admin['id']}</li>";
        echo "<li>Username: {$admin['username']}</li>";
        echo "<li>Nombre: {$admin['nombres']} {$admin['apellido_paterno']}</li>";
        echo "<li>Email: {$admin['email']}</li>";
        echo "<li>Activo: " . ($admin['activo'] ? 'Sí' : 'No') . "</li>";
        echo "<li>Bloqueado: " . ($admin['bloqueado'] ? 'Sí' : 'No') . "</li>";
        echo "<li>Intentos fallidos: {$admin['intentos_fallidos_login']}</li>";
        echo "</ul>";
        
        if (!$admin['activo']) {
            echo "<p style='color: orange;'>⚠ ADVERTENCIA: El usuario está INACTIVO</p>";
        }
        
        if ($admin['bloqueado']) {
            echo "<p style='color: red;'>⚠ ADVERTENCIA: El usuario está BLOQUEADO</p>";
            echo "<p><strong>Solución:</strong> Ejecuta el siguiente comando SQL:</p>";
            echo "<pre>UPDATE usuarios SET bloqueado = 0, intentos_fallidos_login = 0 WHERE username = 'admin';</pre>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ El usuario 'admin' NO existe en la base de datos</p>";
        echo "<p><strong>Solución:</strong> Ejecuta el siguiente comando SQL para crear el usuario:</p>";
        
        // Generar hash nuevo
        $newHash = password_hash('admin123', PASSWORD_DEFAULT);
        
        echo "<pre>";
        echo "INSERT INTO usuarios (username, password_hash, nombres, apellido_paterno, apellido_materno, email, sucursal_id, activo, requiere_cambio_password) VALUES\n";
        echo "('admin', '{$newHash}', 'Administrador', 'Sistema', '', 'admin@laboratorio.com', 1, 1, 1);\n\n";
        echo "-- También asigna el rol:\n";
        echo "INSERT INTO usuario_roles (usuario_id, rol_id, asignado_por) VALUES\n";
        echo "(1, 1, 1);";
        echo "</pre>";
        die();
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ ERROR: {$e->getMessage()}</p>";
    die();
}

// ============================================
// 4. VERIFICAR EL HASH DE LA CONTRASEÑA
// ============================================
echo "<h2>4. Verificando contraseña...</h2>";

$password = 'admin123';
$passwordHash = $admin['password_hash'];

echo "<p>Hash almacenado en BD:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px; word-break: break-all;'>{$passwordHash}</pre>";

// Verificar si el hash es válido
if (password_verify($password, $passwordHash)) {
    echo "<p style='color: green;'>✓ La contraseña 'admin123' es CORRECTA</p>";
    echo "<p>El hash almacenado coincide con la contraseña 'admin123'</p>";
} else {
    echo "<p style='color: red;'>✗ La contraseña 'admin123' NO coincide con el hash almacenado</p>";
    echo "<p><strong>Solución:</strong> El hash de la contraseña es incorrecto. Ejecuta el siguiente comando SQL:</p>";
    
    // Generar un nuevo hash correcto
    $newHash = password_hash('admin123', PASSWORD_DEFAULT);
    
    echo "<pre>UPDATE usuarios SET password_hash = '{$newHash}' WHERE username = 'admin';</pre>";
}

// ============================================
// 5. VERIFICAR ROL DEL USUARIO
// ============================================
echo "<h2>5. Verificando roles del usuario...</h2>";

try {
    $stmt = $pdo->prepare("SELECT r.* FROM roles r 
                          INNER JOIN usuario_roles ur ON r.id = ur.rol_id 
                          WHERE ur.usuario_id = ?");
    $stmt->execute([$admin['id']]);
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($roles) > 0) {
        echo "<p style='color: green;'>✓ El usuario tiene " . count($roles) . " rol(es) asignado(s)</p>";
        echo "<ul>";
        foreach ($roles as $rol) {
            echo "<li>{$rol['nombre']} (ID: {$rol['id']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠ El usuario NO tiene roles asignados</p>";
        echo "<p><strong>Solución:</strong> Ejecuta el siguiente comando SQL:</p>";
        echo "<pre>INSERT INTO usuario_roles (usuario_id, rol_id, asignado_por) VALUES ({$admin['id']}, 1, 1);</pre>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ ERROR: {$e->getMessage()}</p>";
}

// ============================================
// 6. PRUEBA DE LOGIN SIMULADA
// ============================================
echo "<h2>6. Prueba de login simulada...</h2>";

if (password_verify('admin123', $admin['password_hash']) && $admin['activo'] && !$admin['bloqueado']) {
    echo "<p style='color: green; font-weight: bold;'>✓ ¡EL LOGIN DEBERÍA FUNCIONAR!</p>";
    echo "<p>Si aún así no funciona, revisa:</p>";
    echo "<ul>";
    echo "<li>Que el archivo .env tenga las credenciales correctas de la base de datos</li>";
    echo "<li>Que los logs en storage/logs/ para ver errores</li>";
    echo "<li>Que la sesión de PHP esté funcionando (session_start)</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ El login NO funcionará por los siguientes motivos:</p>";
    echo "<ul>";
    if (!password_verify('admin123', $admin['password_hash'])) {
        echo "<li>La contraseña no coincide con el hash</li>";
    }
    if (!$admin['activo']) {
        echo "<li>El usuario está inactivo</li>";
    }
    if ($admin['bloqueado']) {
        echo "<li>El usuario está bloqueado</li>";
    }
    echo "</ul>";
}

// ============================================
// 7. INFORMACIÓN ADICIONAL
// ============================================
echo "<h2>7. Información adicional</h2>";
echo "<ul>";
echo "<li>Versión de PHP: " . phpversion() . "</li>";
echo "<li>Extensión PDO MySQL: " . (extension_loaded('pdo_mysql') ? 'Instalada ✓' : 'NO instalada ✗') . "</li>";
echo "<li>Extensión password: " . (function_exists('password_hash') ? 'Disponible ✓' : 'NO disponible ✗') . "</li>";
echo "<li>Sesiones PHP: " . (function_exists('session_start') ? 'Disponibles ✓' : 'NO disponibles ✗') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Resumen</h2>";
echo "<p>Si todos los checks están en verde ✓, el login debería funcionar.</p>";
echo "<p>Si hay algún error en rojo ✗, sigue las instrucciones de <strong>Solución</strong> que aparecen en cada sección.</p>";
echo "<br>";
echo "<p><a href='login' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>← Volver al Login</a></p>";
?>
