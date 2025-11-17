<?php
/**
 * Modelo Usuario
 * 
 * Maneja todas las operaciones relacionadas con usuarios del sistema
 */

require_once CORE_PATH . '/Model.php';

class Usuario extends Model {
    
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'username',
        'password_hash',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'telefono',
        'cedula_profesional',
        'firma_digital',
        'sucursal_id',
        'area_id',
        'activo'
    ];
    
    protected $hidden = ['password_hash'];
    
    /**
     * Crea un nuevo usuario
     * 
     * @param array $data
     * @return int ID del usuario creado
     */
    public function crearUsuario($data) {
        // Hashear la contraseña
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        $data['requiere_cambio_password'] = 1;
        $data['activo'] = 1;
        
        return $this->create($data);
    }
    
    /**
     * Asigna un rol a un usuario
     * 
     * @param int $usuarioId
     * @param int $rolId
     * @param int $asignadoPor
     * @return bool
     */
    public function asignarRol($usuarioId, $rolId, $asignadoPor) {
        $sql = "INSERT INTO usuario_roles (usuario_id, rol_id, asignado_por)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE asignado_por = ?";
        
        $this->query($sql, [$usuarioId, $rolId, $asignadoPor, $asignadoPor]);
        return true;
    }
    
    /**
     * Obtiene los roles de un usuario
     * 
     * @param int $usuarioId
     * @return array
     */
    public function obtenerRoles($usuarioId) {
        $sql = "SELECT r.*
                FROM roles r
                JOIN usuario_roles ur ON r.id = ur.rol_id
                WHERE ur.usuario_id = ?";
        
        return $this->queryAll($sql, [$usuarioId]);
    }
    
    /**
     * Obtiene los permisos de un usuario
     * 
     * @param int $usuarioId
     * @return array
     */
    public function obtenerPermisos($usuarioId) {
        $sql = "SELECT DISTINCT p.*
                FROM permisos p
                JOIN rol_permisos rp ON p.id = rp.permiso_id
                JOIN usuario_roles ur ON rp.rol_id = ur.rol_id
                WHERE ur.usuario_id = ?";
        
        return $this->queryAll($sql, [$usuarioId]);
    }
    
    /**
     * Obtiene un usuario con todos sus detalles
     * 
     * @param int $id
     * @return array|null
     */
    public function obtenerConDetalles($id) {
        $sql = "SELECT u.*,
                       s.nombre as sucursal_nombre,
                       a.nombre as area_nombre
                FROM {$this->table} u
                LEFT JOIN sucursales s ON u.sucursal_id = s.id
                LEFT JOIN areas a ON u.area_id = a.id
                WHERE u.id = ?";
        
        $usuario = $this->queryOne($sql, [$id]);
        
        if ($usuario) {
            // Obtener roles
            $usuario['roles'] = $this->obtenerRoles($id);
            
            // Obtener permisos
            $usuario['permisos'] = $this->obtenerPermisos($id);
        }
        
        return $usuario;
    }
    
    /**
     * Verifica si un username ya existe
     * 
     * @param string $username
     * @param int|null $excludeId
     * @return bool
     */
    public function usernameExiste($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Verifica si un email ya existe
     * 
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExiste($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Obtiene usuarios por sucursal
     * 
     * @param int $sucursalId
     * @return array
     */
    public function obtenerPorSucursal($sucursalId) {
        $sql = "SELECT u.*,
                       s.nombre as sucursal_nombre,
                       a.nombre as area_nombre,
                       GROUP_CONCAT(r.nombre SEPARATOR ', ') as roles_nombres
                FROM {$this->table} u
                JOIN sucursales s ON u.sucursal_id = s.id
                LEFT JOIN areas a ON u.area_id = a.id
                LEFT JOIN usuario_roles ur ON u.id = ur.usuario_id
                LEFT JOIN roles r ON ur.rol_id = r.id
                WHERE u.sucursal_id = ?
                GROUP BY u.id
                ORDER BY u.apellido_paterno, u.nombres";
        
        return $this->queryAll($sql, [$sucursalId]);
    }
    
    /**
     * Cambia la contraseña de un usuario
     * 
     * @param int $usuarioId
     * @param string $nuevaPassword
     * @return bool
     */
    public function cambiarPassword($usuarioId, $nuevaPassword) {
        $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE {$this->table}
                SET password_hash = ?,
                    requiere_cambio_password = 0,
                    fecha_expiracion_password = DATE_ADD(NOW(), INTERVAL 90 DAY)
                WHERE id = ?";
        
        $this->query($sql, [$passwordHash, $usuarioId]);
        return true;
    }
}
