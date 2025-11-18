<?php
/**
 * Modelo: Metodologia
 * Gestión de metodologías de laboratorio
 */

class Metodologia extends Model {
    protected $table = 'metodologias';
    
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
    
    public function crear($datos) {
        $query = "INSERT INTO {$this->table} 
                  (codigo, nombre, abreviatura, descripcion, orden, activo)
                  VALUES 
                  (:codigo, :nombre, :abreviatura, :descripcion, :orden, :activo)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':abreviatura', $datos['abreviatura']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function actualizar($id, $datos) {
        $query = "UPDATE {$this->table} SET
                  codigo = :codigo,
                  nombre = :nombre,
                  abreviatura = :abreviatura,
                  descripcion = :descripcion,
                  orden = :orden,
                  activo = :activo
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':abreviatura', $datos['abreviatura']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    public function eliminar($id) {
        // Verificar si hay estudios usando esta metodología
        $queryVerificar = "SELECT COUNT(*) as total FROM estudios WHERE metodologia_id = :id";
        $stmt = $this->db->prepare($queryVerificar);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['total'] > 0) {
            // Solo desactivar
            $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        
        // Eliminar físicamente si no hay estudios
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function existeCodigo($codigo, $excluirId = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE codigo = :codigo";
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
    
    public function contarEstudios($id) {
        $query = "SELECT COUNT(*) as total FROM estudios WHERE metodologia_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
}
