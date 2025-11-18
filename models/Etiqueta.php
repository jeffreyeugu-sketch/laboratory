<?php
/**
 * Modelo: Etiqueta
 * Gestión de etiquetas para clasificación de estudios
 */

class Etiqueta extends Model {
    protected $table = 'etiquetas_estudios';
    
    /**
     * Obtener todas las etiquetas activas
     */
    public function obtenerActivas() {
        $query = "SELECT * FROM {$this->table} 
                  WHERE activo = 1 
                  ORDER BY nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todas las etiquetas
     */
    public function obtenerTodas() {
        $query = "SELECT * FROM {$this->table} 
                  ORDER BY activo DESC, nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener etiqueta por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear nueva etiqueta
     */
    public function crear($datos) {
        $query = "INSERT INTO {$this->table} 
                  (nombre, descripcion, color, icono, activo)
                  VALUES 
                  (:nombre, :descripcion, :color, :icono, :activo)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':color', $datos['color']);
        $stmt->bindParam(':icono', $datos['icono']);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Actualizar etiqueta
     */
    public function actualizar($id, $datos) {
        $query = "UPDATE {$this->table} SET
                  nombre = :nombre,
                  descripcion = :descripcion,
                  color = :color,
                  icono = :icono,
                  activo = :activo
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':color', $datos['color']);
        $stmt->bindParam(':icono', $datos['icono']);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar etiqueta
     */
    public function eliminar($id) {
        // Primero eliminar las asignaciones
        $queryAsignaciones = "DELETE FROM estudio_etiquetas WHERE etiqueta_id = :id";
        $stmt = $this->db->prepare($queryAsignaciones);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Luego eliminar la etiqueta
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Verificar si existe el nombre (para validación)
     */
    public function existeNombre($nombre, $excluirId = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE nombre = :nombre";
        
        if ($excluirId) {
            $query .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        
        if ($excluirId) {
            $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] > 0;
    }
    
    /**
     * Obtener etiquetas de un estudio
     */
    public function obtenerPorEstudio($estudioId) {
        $query = "SELECT e.* 
                  FROM {$this->table} e
                  INNER JOIN estudio_etiquetas ee ON e.id = ee.etiqueta_id
                  WHERE ee.estudio_id = :estudio_id
                  ORDER BY e.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estudio_id', $estudioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Asignar etiqueta a estudio
     */
    public function asignarAEstudio($etiquetaId, $estudioId) {
        $query = "INSERT INTO estudio_etiquetas (estudio_id, etiqueta_id) 
                  VALUES (:estudio_id, :etiqueta_id)
                  ON DUPLICATE KEY UPDATE fecha_asignacion = NOW()";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estudio_id', $estudioId, PDO::PARAM_INT);
        $stmt->bindParam(':etiqueta_id', $etiquetaId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Remover etiqueta de estudio
     */
    public function removerDeEstudio($etiquetaId, $estudioId) {
        $query = "DELETE FROM estudio_etiquetas 
                  WHERE estudio_id = :estudio_id AND etiqueta_id = :etiqueta_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estudio_id', $estudioId, PDO::PARAM_INT);
        $stmt->bindParam(':etiqueta_id', $etiquetaId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Contar estudios con esta etiqueta
     */
    public function contarEstudios($id) {
        $query = "SELECT COUNT(*) as total 
                  FROM estudio_etiquetas 
                  WHERE etiqueta_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'];
    }
}
