<?php
/**
 * Modelo Paciente
 * 
 * Maneja todas las operaciones relacionadas con pacientes
 */

require_once CORE_PATH . '/Model.php';

class Paciente extends Model {
    
    protected $table = 'pacientes';
    protected $primaryKey = 'id';
    
    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'expediente',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'sexo',
        'telefono',
        'celular',
        'email',
        'direccion',
        'codigo_postal',
        'ciudad',
        'estado',
        'ocupacion',
        'sucursal_registro_id',
        'activo',
        'notas'
    ];
    
    /**
     * Busca un paciente por expediente
     * 
     * @param string $expediente
     * @return array|null
     */
    public function buscarPorExpediente($expediente) {
        return $this->findOne(['expediente' => $expediente]);
    }
    
    /**
     * Busca pacientes por nombre (búsqueda parcial)
     * 
     * @param string $nombre
     * @param int $limit
     * @return array
     */
    public function buscarPorNombre($nombre, $limit = 20) {
        $sql = "SELECT *, 
                       CONCAT(nombres, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, '')) as nombre_completo,
                       TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) as edad
                FROM {$this->table}
                WHERE (nombres LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ?)
                AND activo = 1
                ORDER BY apellido_paterno, apellido_materno, nombres
                LIMIT ?";
        
        $searchTerm = "%{$nombre}%";
        return $this->queryAll($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
    }
    
    /**
     * Obtiene un paciente con toda su información
     * 
     * @param int $id
     * @return array|null
     */
    public function obtenerConDetalles($id) {
        $sql = "SELECT p.*,
                       CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as nombre_completo,
                       TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad,
                       TIMESTAMPDIFF(MONTH, p.fecha_nacimiento, CURDATE()) as edad_meses,
                       s.nombre as sucursal_nombre
                FROM {$this->table} p
                LEFT JOIN sucursales s ON p.sucursal_registro_id = s.id
                WHERE p.id = ?";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Obtiene el historial de órdenes del paciente
     * 
     * @param int $pacienteId
     * @param int $limit
     * @return array
     */
    public function obtenerHistorialOrdenes($pacienteId, $limit = 10) {
        $sql = "SELECT o.*,
                       s.nombre as sucursal_nombre,
                       COUNT(DISTINCT oe.id) as total_estudios
                FROM ordenes o
                JOIN sucursales s ON o.sucursal_id = s.id
                LEFT JOIN orden_estudios oe ON o.id = oe.orden_id
                WHERE o.paciente_id = ?
                GROUP BY o.id
                ORDER BY o.fecha_registro DESC
                LIMIT ?";
        
        return $this->queryAll($sql, [$pacienteId, $limit]);
    }
    
    /**
     * Genera un nuevo número de expediente
     * 
     * @return string
     */
    public function generarExpediente() {
        $sql = "SELECT MAX(CAST(expediente AS UNSIGNED)) as ultimo FROM {$this->table}";
        $result = $this->queryOne($sql);
        
        $siguiente = ($result['ultimo'] ?? 10000000) + 1;
        
        return str_pad($siguiente, 8, '0', STR_PAD_LEFT);
    }
    
    /**
     * Verifica si un expediente ya existe
     * 
     * @param string $expediente
     * @param int|null $excludeId
     * @return bool
     */
    public function expedienteExiste($expediente, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE expediente = ?";
        $params = [$expediente];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->queryOne($sql, $params);
        return $result['count'] > 0;
    }
}
