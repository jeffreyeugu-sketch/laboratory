<?php
/**
 * Modelo: Metodologia
 * Gestiona las metodologías de análisis del laboratorio
 * 
 * Tabla: metodologias
 * Campos: id, codigo, nombre, descripcion, abreviatura, orden, activo, fecha_creacion
 */

class Metodologia extends Model
{
    protected $table = 'metodologias';
    protected $fillable = ['codigo', 'nombre', 'descripcion', 'abreviatura', 'orden', 'activo'];

    /**
     * Obtiene listado paginado para DataTables (server-side)
     */
    public function listar($start = 0, $length = 10, $search = '', $orderBy = 'id', $orderDir = 'ASC')
    {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (
                codigo LIKE :search OR
                nombre LIKE :search OR
                abreviatura LIKE :search OR
                descripcion LIKE :search
            )";
        }
        
        // Ordenamiento
        $allowedColumns = ['id', 'codigo', 'nombre', 'abreviatura', 'orden', 'activo'];
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'orden';
        }
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
        
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

    /**
     * Cuenta registros filtrados (para DataTables)
     */
    public function contarFiltrados($search = '')
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (
                codigo LIKE :search OR
                nombre LIKE :search OR
                abreviatura LIKE :search OR
                descripcion LIKE :search
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

    /**
     * Cuenta total de registros (para DataTables)
     */
    public function contarTotal()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $row['total'];
    }

    /**
     * Verifica si existe un código (solo si no es NULL)
     */
    public function existeCodigo($codigo, $idExcluir = null)
    {
        if (empty($codigo)) {
            return false; // Código vacío es permitido
        }
        
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

    /**
     * Verifica si la metodología tiene estudios relacionados
     */
    public function tieneEstudiosRelacionados($metodologiaId)
    {
        $query = "SELECT COUNT(*) as total FROM estudios WHERE metodologia_id = :metodologiaId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':metodologiaId', $metodologiaId, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'] > 0;
    }

    /**
     * Obtiene solo las metodologías activas (para selects)
     */
    public function obtenerActivas()
    {
        $query = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY orden ASC, nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una metodología por su código
     */
    public function obtenerPorCodigo($codigo)
    {
        if (empty($codigo)) {
            return null;
        }
        
        $query = "SELECT * FROM {$this->table} WHERE codigo = :codigo LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtiene estadísticas de una metodología
     */
    public function obtenerEstadisticas($metodologiaId)
    {
        $stats = [
            'total_estudios' => 0,
            'estudios_activos' => 0,
            'estudios_inactivos' => 0
        ];
        
        // Total de estudios
        $query = "SELECT COUNT(*) as total FROM estudios WHERE metodologia_id = :metodologiaId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':metodologiaId', $metodologiaId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_estudios'] = (int) $row['total'];
        
        // Estudios activos
        $query = "SELECT COUNT(*) as total FROM estudios WHERE metodologia_id = :metodologiaId AND activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':metodologiaId', $metodologiaId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['estudios_activos'] = (int) $row['total'];
        
        // Estudios inactivos
        $stats['estudios_inactivos'] = $stats['total_estudios'] - $stats['estudios_activos'];
        
        return $stats;
    }

    /**
     * Obtiene los estudios asociados a una metodología
     */
    public function obtenerEstudios($metodologiaId, $activos = null)
    {
        $query = "SELECT * FROM estudios WHERE metodologia_id = :metodologiaId";
        
        if ($activos !== null) {
            $query .= " AND activo = :activo";
        }
        
        $query .= " ORDER BY nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':metodologiaId', $metodologiaId, PDO::PARAM_INT);
        
        if ($activos !== null) {
            $activoValue = $activos ? 1 : 0;
            $stmt->bindParam(':activo', $activoValue, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca metodologías por término (para autocomplete)
     */
    public function buscar($termino, $limite = 10)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE activo = 1 
                  AND (codigo LIKE :termino OR nombre LIKE :termino OR abreviatura LIKE :termino)
                  ORDER BY orden ASC, nombre ASC
                  LIMIT {$limite}";
        
        $stmt = $this->db->prepare($query);
        $terminoParam = "%{$termino}%";
        $stmt->bindParam(':termino', $terminoParam, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Activa una metodología
     */
    public function activar($id)
    {
        $query = "UPDATE {$this->table} SET activo = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Desactiva una metodología
     */
    public function desactivar($id)
    {
        $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Obtiene el próximo número de orden disponible
     */
    public function obtenerSiguienteOrden()
    {
        $query = "SELECT MAX(orden) as max_orden FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row['max_orden'] ?? 0) + 1;
    }
}
