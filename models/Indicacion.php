<?php
/**
 * Modelo: Indicacion
 * Gestión de indicaciones pre-analíticas para estudios
 */

class Indicacion extends Model {
    protected $table = 'indicaciones_estudios';
    
    public function obtenerActivas() {
        $query = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY orden ASC, nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerTodas() {
        $query = "SELECT * FROM {$this->table} ORDER BY orden ASC, nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorTipo($tipo) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE tipo = :tipo AND activo = 1 
                  ORDER BY orden ASC, nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function crear($datos) {
        $query = "INSERT INTO {$this->table} 
                  (nombre, descripcion, tipo, texto_paciente, orden, activo)
                  VALUES 
                  (:nombre, :descripcion, :tipo, :texto_paciente, :orden, :activo)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':tipo', $datos['tipo']);
        $stmt->bindParam(':texto_paciente', $datos['texto_paciente']);
        $stmt->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function actualizar($id, $datos) {
        $query = "UPDATE {$this->table} SET
                  nombre = :nombre,
                  descripcion = :descripcion,
                  tipo = :tipo,
                  texto_paciente = :texto_paciente,
                  orden = :orden,
                  activo = :activo
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':tipo', $datos['tipo']);
        $stmt->bindParam(':texto_paciente', $datos['texto_paciente']);
        $stmt->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    public function eliminar($id) {
        // Primero eliminar las asignaciones
        $queryAsignaciones = "DELETE FROM estudio_indicaciones_asignadas WHERE indicacion_id = :id";
        $stmt = $this->db->prepare($queryAsignaciones);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Luego eliminar la indicación
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Obtener indicaciones de un estudio
     */
    public function obtenerPorEstudio($estudioId) {
        $query = "SELECT i.*, eia.obligatoria, eia.orden as orden_asignacion
                  FROM {$this->table} i
                  INNER JOIN estudio_indicaciones_asignadas eia ON i.id = eia.indicacion_id
                  WHERE eia.estudio_id = :estudio_id
                  ORDER BY eia.orden ASC, i.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estudio_id', $estudioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Asignar indicación a estudio
     */
    public function asignarAEstudio($indicacionId, $estudioId, $obligatoria = true, $orden = 0) {
        $query = "INSERT INTO estudio_indicaciones_asignadas 
                  (estudio_id, indicacion_id, obligatoria, orden) 
                  VALUES (:estudio_id, :indicacion_id, :obligatoria, :orden)
                  ON DUPLICATE KEY UPDATE obligatoria = :obligatoria, orden = :orden";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estudio_id', $estudioId, PDO::PARAM_INT);
        $stmt->bindParam(':indicacion_id', $indicacionId, PDO::PARAM_INT);
        $stmt->bindParam(':obligatoria', $obligatoria, PDO::PARAM_BOOL);
        $stmt->bindParam(':orden', $orden, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Remover indicación de estudio
     */
    public function removerDeEstudio($indicacionId, $estudioId) {
        $query = "DELETE FROM estudio_indicaciones_asignadas 
                  WHERE estudio_id = :estudio_id AND indicacion_id = :indicacion_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estudio_id', $estudioId, PDO::PARAM_INT);
        $stmt->bindParam(':indicacion_id', $indicacionId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Contar estudios con esta indicación
     */
    public function contarEstudios($id) {
        $query = "SELECT COUNT(*) as total 
                  FROM estudio_indicaciones_asignadas 
                  WHERE indicacion_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'];
    }
}
