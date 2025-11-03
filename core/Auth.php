<?php
/**
 * Clase Auth
 * Maneja autenticación y autorización de usuarios
 */

require_once __DIR__ . '/Database.php';

class Auth {
    
    /**
     * Intentar login con username y password
     * 
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function login($username, $password) {
        $db = Database::getInstance()->getConnection();
        
        // Buscar usuario
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ? AND activo = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        // Verificar si está bloqueado
        if ($user['bloqueado']) {
            return false;
        }
        
        // Verificar contraseña
        if (!password_verify($password, $user['password_hash'])) {
            // Incrementar intentos fallidos
            self::incrementLoginAttempts($user['id']);
            return false;
        }
        
        // Login exitoso - Resetear intentos fallidos
        $stmt = $db->prepare("UPDATE usuarios SET 
                             intentos_fallidos_login = 0, 
                             fecha_ultimo_acceso = NOW() 
                             WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Guardar en sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_nombres'] = $user['nombres'] . ' ' . $user['apellido_paterno'];
        $_SESSION['sucursal_id'] = $user['sucursal_id'];
        $_SESSION['area_id'] = $user['area_id'];
        
        // Cargar permisos
        self::loadPermissions($user['id']);
        
        // Registrar en auditoría
        self::logAudit($user['id'], 'login', 'usuarios', 'usuario', $user['id']);
        
        return true;
    }
    
    /**
     * Cerrar sesión
     */
    public static function logout() {
        if (self::check()) {
            self::logAudit($_SESSION['user_id'], 'logout', 'usuarios', 'usuario', $_SESSION['user_id']);
        }
        
        session_destroy();
        return true;
    }
    
    /**
     * Verificar si el usuario está autenticado
     * 
     * @return bool
     */
    public static function check() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Obtener el usuario actual
     * 
     * @return array|null
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT u.*, s.nombre as sucursal_nombre, a.nombre as area_nombre
                             FROM usuarios u
                             LEFT JOIN sucursales s ON u.sucursal_id = s.id
                             LEFT JOIN areas a ON u.area_id = a.id
                             WHERE u.id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener ID del usuario actual
     * 
     * @return int|null
     */
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Cargar permisos del usuario en sesión
     * 
     * @param int $userId
     */
    private static function loadPermissions($userId) {
        $db = Database::getInstance()->getConnection();
        
        // Obtener permisos de roles asignados
        $sql = "SELECT DISTINCT p.clave
                FROM permisos p
                INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
                INNER JOIN usuario_roles ur ON rp.rol_id = ur.rol_id
                WHERE ur.usuario_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        
        $permisos = [];
        while ($row = $stmt->fetch()) {
            $permisos[] = $row['clave'];
        }
        
        // Obtener permisos extras concedidos
        $sql = "SELECT p.clave
                FROM permisos p
                INNER JOIN usuario_permisos_extra upe ON p.id = upe.permiso_id
                WHERE upe.usuario_id = ? AND upe.tipo = 'conceder'";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        
        while ($row = $stmt->fetch()) {
            $permisos[] = $row['clave'];
        }
        
        // Quitar permisos revocados
        $sql = "SELECT p.clave
                FROM permisos p
                INNER JOIN usuario_permisos_extra upe ON p.id = upe.permiso_id
                WHERE upe.usuario_id = ? AND upe.tipo = 'revocar'";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        
        $permisosRevocados = [];
        while ($row = $stmt->fetch()) {
            $permisosRevocados[] = $row['clave'];
        }
        
        // Filtrar permisos revocados
        $permisos = array_diff($permisos, $permisosRevocados);
        
        // Eliminar duplicados
        $permisos = array_unique($permisos);
        
        $_SESSION['permisos'] = $permisos;
    }
    
    /**
     * Verificar si el usuario tiene un permiso
     * 
     * @param string $permiso
     * @return bool
     */
    public static function can($permiso) {
        if (!self::check()) {
            return false;
        }
        
        // Verificar si es superusuario (tiene todos los permisos)
        if (self::isSuperuser()) {
            return true;
        }
        
        return in_array($permiso, $_SESSION['permisos'] ?? []);
    }
    
    /**
     * Verificar si el usuario es superusuario
     * 
     * @return bool
     */
    public static function isSuperuser() {
        if (!self::check()) {
            return false;
        }
        
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT COUNT(*) as total
                FROM usuario_roles ur
                INNER JOIN roles r ON ur.rol_id = r.id
                WHERE ur.usuario_id = ? AND r.id = 1"; // 1 = Superusuario
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();
        
        return $result['total'] > 0;
    }
    
    /**
     * Incrementar intentos fallidos de login
     * 
     * @param int $userId
     */
    private static function incrementLoginAttempts($userId) {
        $db = Database::getInstance()->getConnection();
        $config = require __DIR__ . '/../config/app.php';
        $maxAttempts = $config['max_login_attempts'];
        
        $stmt = $db->prepare("UPDATE usuarios SET 
                             intentos_fallidos_login = intentos_fallidos_login + 1,
                             bloqueado = CASE 
                                 WHEN intentos_fallidos_login + 1 >= ? THEN 1 
                                 ELSE bloqueado 
                             END
                             WHERE id = ?");
        $stmt->execute([$maxAttempts, $userId]);
    }
    
    /**
     * Registrar acción en auditoría
     * 
     * @param int $userId
     * @param string $accion
     * @param string $modulo
     * @param string $entidadTipo
     * @param int $entidadId
     * @param array $datosAnteriores
     * @param array $datosNuevos
     */
    public static function logAudit($userId, $accion, $modulo, $entidadTipo = null, $entidadId = null, $datosAnteriores = null, $datosNuevos = null) {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("INSERT INTO auditoria 
                                 (usuario_id, accion, modulo, entidad_tipo, entidad_id, 
                                  datos_anteriores, datos_nuevos, ip_address, user_agent, sucursal_id)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $userId,
                $accion,
                $modulo,
                $entidadTipo,
                $entidadId,
                $datosAnteriores ? json_encode($datosAnteriores) : null,
                $datosNuevos ? json_encode($datosNuevos) : null,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $_SESSION['sucursal_id'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Error al registrar auditoría: " . $e->getMessage());
        }
    }
    
    /**
     * Cambiar contraseña del usuario
     * 
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public static function changePassword($userId, $newPassword) {
        $db = Database::getInstance()->getConnection();
        
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("UPDATE usuarios SET 
                             password_hash = ?,
                             requiere_cambio_password = 0,
                             fecha_expiracion_password = DATE_ADD(NOW(), INTERVAL 90 DAY)
                             WHERE id = ?");
        
        return $stmt->execute([$passwordHash, $userId]);
    }
}
