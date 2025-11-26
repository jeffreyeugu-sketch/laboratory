<?php
/**
 * Modelo: LaboratorioReferencia
 * Gestiona los laboratorios de referencia externos
 * 
 * Tabla: laboratorios_referencia
 */

class LaboratorioReferencia extends Model
{
    protected $table = 'laboratorios_referencia';
    protected $fillable = [
        'codigo', 'nombre', 'razon_social', 'rfc',
        'direccion', 'ciudad', 'estado', 'codigo_postal',
        'telefono', 'email',
        'contacto_nombre', 'contacto_telefono', 'contacto_email',
        'dias_entrega_promedio', 'observaciones', 'activo'
    ];

    /**
     * Obtiene listado paginado para DataTables (server-side)
     */
    public function listar($start = 0, $length = 10, $search = '', $orderBy = 'id', $orderDir = 'DESC')
    {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (
                codigo LIKE :search OR
                nombre LIKE :search OR
                razon_social LIKE :search OR
                ciudad LIKE :search OR
                rfc LIKE :search
            )";
        }
        
        $allowedColumns = ['id', 'codigo', 'nombre', 'ciudad', 'dias_entrega_promedio', 'activo'];
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'id';
        }
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
        
        $query .= " ORDER BY {$orderBy} {$orderDir}";
        $query .= " LIMIT {$start}, {$length}";
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarFiltrados($search = '')
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (
                codigo LIKE :search OR
                nombre LIKE :search OR
                razon_social LIKE :search OR
                ciudad LIKE :search OR
                rfc LIKE :search
            )";
        }
        
        $stmt = $this->db->prepare($query);
        
        if (!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }

    public function contarTotal()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }

    public function existeCodigo($codigo, $idExcluir = null)
    {
        $query = "SELECT id FROM {$this->table} WHERE codigo = :codigo";
        
        if ($idExcluir) {
            $query .= " AND id != :idExcluir";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        
        if ($idExcluir) {
            $stmt->bindParam(':idExcluir', $idExcluir, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function obtenerActivos()
    {
        $query = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCodigo($codigo)
    {
        $query = "SELECT * FROM {$this->table} WHERE codigo = :codigo LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function buscar($termino, $limite = 10)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE activo = 1 
                  AND (codigo LIKE :termino OR nombre LIKE :termino)
                  ORDER BY nombre ASC
                  LIMIT {$limite}";
        
        $stmt = $this->db->prepare($query);
        $terminoParam = "%{$termino}%";
        $stmt->bindParam(':termino', $terminoParam, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function activar($id)
    {
        $query = "UPDATE {$this->table} SET activo = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function desactivar($id)
    {
        $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtiene estadísticas del laboratorio
     */
    public function obtenerEstadisticas($laboratorioId)
    {
        $stats = [
            'total_estudios' => 0,
            'estudios_activos' => 0
        ];
        
        // Contar estudios subrogados a este laboratorio
        $query = "SELECT COUNT(*) as total FROM estudios WHERE laboratorio_id = :labId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':labId', $laboratorioId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_estudios'] = (int) $row['total'];
        
        $query = "SELECT COUNT(*) as total FROM estudios WHERE laboratorio_id = :labId AND activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':labId', $laboratorioId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['estudios_activos'] = (int) $row['total'];
        
        return $stats;
    }
}