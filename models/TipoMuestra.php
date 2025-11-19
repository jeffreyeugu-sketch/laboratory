<?php
/**
 * TipoMuestra.php
 * Modelo para gestión de tipos de muestra
 */

require_once __DIR__ . '/../core/Model.php';

class TipoMuestra extends Model
{
    protected $table = 'tipos_muestra';
    protected $fillable = ['codigo', 'nombre', 'descripcion', 'color_tubo', 'volumen_minimo', 'activo'];
    
    /**
     * Listar con filtros y paginación
     */
    public function listar($start = 0, $length = 25, $search = '', $orderBy = 'id', $orderDir = 'DESC')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (codigo LIKE ? OR nombre LIKE ? OR descripcion LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $validColumns = ['id', 'codigo', 'nombre', 'color_tubo', 'activo'];
        if (!in_array($orderBy, $validColumns)) {
            $orderBy = 'id';
        }
        
        $orderDir = strtoupper($orderDir);
        if (!in_array($orderDir, ['ASC', 'DESC'])) {
            $orderDir = 'DESC';
        }
        
        $sql .= " ORDER BY {$orderBy} {$orderDir} LIMIT ? OFFSET ?";
        $params[] = (int)$length;
        $params[] = (int)$start;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Contar filtrados
     */
    public function contarFiltrados($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        
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
     * Contar total
     */
    public function contarTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Verificar código único
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
     * Obtener activos
     */
    public function obtenerActivas()
    {
        $sql = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verificar si tiene estudios relacionados
     */
    public function tieneEstudiosRelacionados($id)
    {
        $sql = "SELECT COUNT(*) as total FROM estudios WHERE tipo_muestra_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['total'] ?? 0) > 0;
    }
    
    /**
     * Activar
     */
    public function activar($id)
    {
        return $this->actualizar($id, ['activo' => 1]);
    }
    
    /**
     * Desactivar
     */
    public function desactivar($id)
    {
        return $this->actualizar($id, ['activo' => 0]);
    }
}