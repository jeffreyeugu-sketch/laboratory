<?php
/**
 * Area.php
 * Modelo para gestión de áreas del laboratorio
 * 
 * Áreas: Química Clínica, Hematología, Microbiología, etc.
 */

require_once __DIR__ . '/../core/Model.php';

class Area extends Model
{
    protected $table = 'areas';
    protected $fillable = ['codigo', 'nombre', 'descripcion', 'activo'];
    
    /**
     * Listar áreas con filtros y paginación
     */
    public function listar($start = 0, $length = 25, $search = '', $orderBy = 'id', $orderDir = 'DESC')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        // Filtro de búsqueda
        if (!empty($search)) {
            $sql .= " AND (codigo LIKE ? OR nombre LIKE ? OR descripcion LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Ordenamiento
        $validColumns = ['id', 'codigo', 'nombre', 'descripcion', 'activo'];
        if (!in_array($orderBy, $validColumns)) {
            $orderBy = 'id';
        }
        
        $orderDir = strtoupper($orderDir);
        if (!in_array($orderDir, ['ASC', 'DESC'])) {
            $orderDir = 'DESC';
        }
        
        $sql .= " ORDER BY {$orderBy} {$orderDir}";
        
        // Paginación
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = (int)$length;
        $params[] = (int)$start;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Contar total de registros filtrados
     */
    public function contarFiltrados($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        
        // Filtro de búsqueda
        if (!empty($search)) {
            $sql .= " AND (codigo LIKE ? OR nombre LIKE ? OR descripcion LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    /**
     * Contar total de registros
     */
    public function contarTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Verificar si existe un código
     */
    public function existeCodigo($codigo, $idExcluir = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE codigo = ?";
        $params = [$codigo];
        
        if ($idExcluir !== null) {
            $sql .= " AND id != ?";
            $params[] = $idExcluir;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['total'] ?? 0) > 0;
    }
    
    /**
     * Verificar si el área tiene estudios relacionados
     */
    public function tieneEstudiosRelacionados($areaId)
    {
        $sql = "SELECT COUNT(*) as total FROM estudios WHERE area_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$areaId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['total'] ?? 0) > 0;
    }
    
    /**
     * Obtener áreas activas
     */
    public function obtenerActivas()
    {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener área por código
     */
    public function obtenerPorCodigo($codigo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE codigo = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$codigo]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estadísticas del área
     */
    public function obtenerEstadisticas($areaId)
    {
        $sql = "SELECT 
                    COUNT(DISTINCT e.id) as total_estudios,
                    COUNT(DISTINCT oe.orden_id) as total_ordenes,
                    SUM(CASE WHEN e.activo = 1 THEN 1 ELSE 0 END) as estudios_activos,
                    SUM(CASE WHEN e.activo = 0 THEN 1 ELSE 0 END) as estudios_inactivos
                FROM estudios e
                LEFT JOIN orden_estudios oe ON e.id = oe.estudio_id
                WHERE e.area_id = ?
                GROUP BY e.area_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$areaId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estudios de un área
     */
    public function obtenerEstudios($areaId, $activos = null)
    {
        $sql = "SELECT e.* 
                FROM estudios e 
                WHERE e.area_id = ?";
        $params = [$areaId];
        
        if ($activos !== null) {
            $sql .= " AND e.activo = ?";
            $params[] = $activos;
        }
        
        $sql .= " ORDER BY e.nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Buscar áreas (para autocompletado)
     */
    public function buscar($termino, $limite = 10)
    {
        $sql = "SELECT id, codigo, nombre 
                FROM {$this->table} 
                WHERE activo = 1 
                AND (codigo LIKE ? OR nombre LIKE ?)
                ORDER BY nombre ASC
                LIMIT ?";
        
        $searchTerm = '%' . $termino . '%';
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $limite]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Activar área
     */
    public function activar($id)
    {
        return $this->actualizar($id, ['activo' => 1]);
    }
    
    /**
     * Desactivar área
     */
    public function desactivar($id)
    {
        return $this->actualizar($id, ['activo' => 0]);
    }
}