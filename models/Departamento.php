<?php
/**
 * Modelo: Departamento
 * Gestiona los departamentos del laboratorio
 * 
 * Tabla: departamentos
 * Campos: id, area_id, codigo, nombre, descripcion, orden, activo, fecha_creacion
 * Relación: Pertenece a un Área (areas.id)
 */

class Departamento extends Model
{
    protected $table = 'departamentos';
    protected $fillable = ['area_id', 'codigo', 'nombre', 'descripcion', 'orden', 'activo'];

    /**
     * Obtiene listado paginado para DataTables (server-side) con JOIN a areas
     */
    public function listar($start = 0, $length = 10, $search = '', $orderBy = 'id', $orderDir = 'DESC')
    {
        $query = "SELECT d.*, a.nombre as area_nombre 
                  FROM {$this->table} d
                  LEFT JOIN areas a ON d.area_id = a.id
                  WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (
                d.codigo LIKE :search OR
                d.nombre LIKE :search OR
                d.descripcion LIKE :search OR
                a.nombre LIKE :search
            )";
        }
        
        // Validar columnas permitidas
        $allowedColumns = ['id', 'codigo', 'nombre', 'orden', 'activo', 'area_nombre'];
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'id';
        }
        $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
        
        // Mapear area_nombre a la columna real
        if ($orderBy === 'area_nombre') {
            $orderBy = 'a.nombre';
        } else {
            $orderBy = 'd.' . $orderBy;
        }
        
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
        $query = "SELECT COUNT(*) as total 
                  FROM {$this->table} d
                  LEFT JOIN areas a ON d.area_id = a.id
                  WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (
                d.codigo LIKE :search OR
                d.nombre LIKE :search OR
                d.descripcion LIKE :search OR
                a.nombre LIKE :search
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
     * Verifica si existe un código
     */
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

    /**
     * Obtiene un departamento por ID con información del área
     */
    public function obtenerPorId($id)
    {
        $query = "SELECT d.*, a.nombre as area_nombre 
                  FROM {$this->table} d
                  LEFT JOIN areas a ON d.area_id = a.id
                  WHERE d.id = :id 
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtiene solo los departamentos activos (para selects)
     */
    public function obtenerActivos()
    {
        $query = "SELECT d.*, a.nombre as area_nombre 
                  FROM {$this->table} d
                  LEFT JOIN areas a ON d.area_id = a.id
                  WHERE d.activo = 1 
                  ORDER BY d.orden ASC, d.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene departamentos por área
     */
    public function obtenerPorArea($areaId, $soloActivos = true)
    {
        $query = "SELECT * FROM {$this->table} WHERE area_id = :areaId";
        
        if ($soloActivos) {
            $query .= " AND activo = 1";
        }
        
        $query .= " ORDER BY orden ASC, nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':areaId', $areaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un departamento por su código
     */
    public function obtenerPorCodigo($codigo)
    {
        $query = "SELECT d.*, a.nombre as area_nombre 
                  FROM {$this->table} d
                  LEFT JOIN areas a ON d.area_id = a.id
                  WHERE d.codigo = :codigo 
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Busca departamentos por término (para autocomplete)
     */
    public function buscar($termino, $limite = 10)
    {
        $query = "SELECT d.*, a.nombre as area_nombre 
                  FROM {$this->table} d
                  LEFT JOIN areas a ON d.area_id = a.id
                  WHERE d.activo = 1 
                  AND (d.codigo LIKE :termino OR d.nombre LIKE :termino)
                  ORDER BY d.orden ASC, d.nombre ASC
                  LIMIT {$limite}";
        
        $stmt = $this->db->prepare($query);
        $terminoParam = "%{$termino}%";
        $stmt->bindParam(':termino', $terminoParam, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Activa un departamento
     */
    public function activar($id)
    {
        $query = "UPDATE {$this->table} SET activo = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Desactiva un departamento
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

    /**
     * Cuenta departamentos por área
     */
    public function contarPorArea($areaId)
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE area_id = :areaId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':areaId', $areaId, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }
}