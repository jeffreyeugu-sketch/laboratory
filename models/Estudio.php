<?php
/**
 * Modelo: Estudio
 * Gestión de estudios del laboratorio
 */

class Estudio extends Model {
    protected $table = 'estudios';
    
    /**
     * Obtener todos los estudios con información relacionada
     */
    public function obtenerTodos() {
        $query = "SELECT e.*, 
                         a.nombre as area_nombre,
                         tm.nombre as tipo_muestra_nombre,
                         m.nombre as metodologia_nombre,
                         d.nombre as departamento_nombre,
                         lr.nombre as laboratorio_referencia_nombre
                  FROM {$this->table} e
                  INNER JOIN areas a ON e.area_id = a.id
                  INNER JOIN tipos_muestra tm ON e.tipo_muestra_id = tm.id
                  LEFT JOIN metodologias m ON e.metodologia_id = m.id
                  LEFT JOIN departamentos d ON e.departamento_id = d.id
                  LEFT JOIN laboratorios_referencia lr ON e.laboratorio_referencia_id = lr.id
                  ORDER BY e.activo DESC, e.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estudios activos
     */
    public function obtenerActivos() {
        $query = "SELECT e.*, 
                         a.nombre as area_nombre,
                         tm.nombre as tipo_muestra_nombre
                  FROM {$this->table} e
                  INNER JOIN areas a ON e.area_id = a.id
                  INNER JOIN tipos_muestra tm ON e.tipo_muestra_id = tm.id
                  WHERE e.activo = 1
                  ORDER BY e.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estudio por ID con toda la información
     */
    public function obtenerPorId($id) {
        $query = "SELECT e.*, 
                         a.nombre as area_nombre,
                         tm.nombre as tipo_muestra_nombre,
                         m.nombre as metodologia_nombre,
                         d.nombre as departamento_nombre,
                         lr.nombre as laboratorio_referencia_nombre
                  FROM {$this->table} e
                  INNER JOIN areas a ON e.area_id = a.id
                  INNER JOIN tipos_muestra tm ON e.tipo_muestra_id = tm.id
                  LEFT JOIN metodologias m ON e.metodologia_id = m.id
                  LEFT JOIN departamentos d ON e.departamento_id = d.id
                  LEFT JOIN laboratorios_referencia lr ON e.laboratorio_referencia_id = lr.id
                  WHERE e.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear nuevo estudio
     */
    public function crear($datos) {
        $query = "INSERT INTO {$this->table} 
                  (codigo_interno, codigo_loinc, nombre, nombre_corto, descripcion,
                   area_id, tipo_muestra_id, volumen_requerido, metodologia_id,
                   dias_proceso, indicaciones_paciente, activo,
                   es_subrogado, laboratorio_referencia_id, departamento_id)
                  VALUES 
                  (:codigo_interno, :codigo_loinc, :nombre, :nombre_corto, :descripcion,
                   :area_id, :tipo_muestra_id, :volumen_requerido, :metodologia_id,
                   :dias_proceso, :indicaciones_paciente, :activo,
                   :es_subrogado, :laboratorio_referencia_id, :departamento_id)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':codigo_interno', $datos['codigo_interno']);
        $stmt->bindParam(':codigo_loinc', $datos['codigo_loinc']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':nombre_corto', $datos['nombre_corto']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':area_id', $datos['area_id'], PDO::PARAM_INT);
        $stmt->bindParam(':tipo_muestra_id', $datos['tipo_muestra_id'], PDO::PARAM_INT);
        $stmt->bindParam(':volumen_requerido', $datos['volumen_requerido']);
        $stmt->bindParam(':metodologia_id', $datos['metodologia_id'], PDO::PARAM_INT);
        $stmt->bindParam(':dias_proceso', $datos['dias_proceso'], PDO::PARAM_INT);
        $stmt->bindParam(':indicaciones_paciente', $datos['indicaciones_paciente']);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        $stmt->bindParam(':es_subrogado', $datos['es_subrogado'], PDO::PARAM_BOOL);
        $stmt->bindParam(':laboratorio_referencia_id', $datos['laboratorio_referencia_id'], PDO::PARAM_INT);
        $stmt->bindParam(':departamento_id', $datos['departamento_id'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Actualizar estudio
     */
    public function actualizar($id, $datos) {
        $query = "UPDATE {$this->table} SET
                  codigo_interno = :codigo_interno,
                  codigo_loinc = :codigo_loinc,
                  nombre = :nombre,
                  nombre_corto = :nombre_corto,
                  descripcion = :descripcion,
                  area_id = :area_id,
                  tipo_muestra_id = :tipo_muestra_id,
                  volumen_requerido = :volumen_requerido,
                  metodologia_id = :metodologia_id,
                  dias_proceso = :dias_proceso,
                  indicaciones_paciente = :indicaciones_paciente,
                  activo = :activo,
                  es_subrogado = :es_subrogado,
                  laboratorio_referencia_id = :laboratorio_referencia_id,
                  departamento_id = :departamento_id
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':codigo_interno', $datos['codigo_interno']);
        $stmt->bindParam(':codigo_loinc', $datos['codigo_loinc']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':nombre_corto', $datos['nombre_corto']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':area_id', $datos['area_id'], PDO::PARAM_INT);
        $stmt->bindParam(':tipo_muestra_id', $datos['tipo_muestra_id'], PDO::PARAM_INT);
        $stmt->bindParam(':volumen_requerido', $datos['volumen_requerido']);
        $stmt->bindParam(':metodologia_id', $datos['metodologia_id'], PDO::PARAM_INT);
        $stmt->bindParam(':dias_proceso', $datos['dias_proceso'], PDO::PARAM_INT);
        $stmt->bindParam(':indicaciones_paciente', $datos['indicaciones_paciente']);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        $stmt->bindParam(':es_subrogado', $datos['es_subrogado'], PDO::PARAM_BOOL);
        $stmt->bindParam(':laboratorio_referencia_id', $datos['laboratorio_referencia_id'], PDO::PARAM_INT);
        $stmt->bindParam(':departamento_id', $datos['departamento_id'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar estudio (solo desactivar)
     */
    public function eliminar($id) {
        // Solo desactivar, no eliminar físicamente
        $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Verificar si existe código interno
     */
    public function existeCodigoInterno($codigo, $excluirId = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE codigo_interno = :codigo";
        if ($excluirId) {
            $query .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        if ($excluirId) {
            $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    }
    
    /**
     * Obtener estudios por área
     */
    public function obtenerPorArea($areaId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE area_id = :area_id AND activo = 1
                  ORDER BY nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':area_id', $areaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estudios subrogados
     */
    public function obtenerSubrogados() {
        $query = "SELECT e.*, lr.nombre as laboratorio_nombre
                  FROM {$this->table} e
                  INNER JOIN laboratorios_referencia lr ON e.laboratorio_referencia_id = lr.id
                  WHERE e.es_subrogado = 1 AND e.activo = 1
                  ORDER BY lr.nombre ASC, e.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Buscar estudios
     */
    public function buscar($termino) {
        $query = "SELECT e.*, a.nombre as area_nombre
                  FROM {$this->table} e
                  INNER JOIN areas a ON e.area_id = a.id
                  WHERE (e.nombre LIKE :termino 
                  OR e.nombre_corto LIKE :termino
                  OR e.codigo_interno LIKE :termino
                  OR e.codigo_loinc LIKE :termino)
                  ORDER BY e.activo DESC, e.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $searchTerm = "%{$termino}%";
        $stmt->bindParam(':termino', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
