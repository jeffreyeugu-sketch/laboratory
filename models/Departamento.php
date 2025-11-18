<?php
/**
 * Modelo: Departamento
 * Gestión de departamentos (sub-áreas dentro de áreas)
 */

class Departamento extends Model {
    protected $table = 'departamentos';
    
    public function obtenerActivos() {
        $query = "SELECT d.*, a.nombre as area_nombre, a.codigo as area_codigo
                  FROM {$this->table} d
                  INNER JOIN areas a ON d.area_id = a.id
                  WHERE d.activo = 1
                  ORDER BY a.nombre ASC, d.orden ASC, d.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerTodos() {
        $query = "SELECT d.*, a.nombre as area_nombre, a.codigo as area_codigo
                  FROM {$this->table} d
                  INNER JOIN areas a ON d.area_id = a.id
                  ORDER BY a.nombre ASC, d.orden ASC, d.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT d.*, a.nombre as area_nombre 
                  FROM {$this->table} d
                  INNER JOIN areas a ON d.area_id = a.id
                  WHERE d.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorArea($areaId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE area_id = :area_id AND activo = 1
                  ORDER BY orden ASC, nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':area_id', $areaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function crear($datos) {
        $query = "INSERT INTO {$this->table} 
                  (area_id, codigo, nombre, descripcion, orden, activo)
                  VALUES 
                  (:area_id, :codigo, :nombre, :descripcion, :orden, :activo)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':area_id', $datos['area_id'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
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
                  area_id = :area_id,
                  codigo = :codigo,
                  nombre = :nombre,
                  descripcion = :descripcion,
                  orden = :orden,
                  activo = :activo
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':area_id', $datos['area_id'], PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    public function eliminar($id) {
        // Verificar si hay estudios usando este departamento
        $queryVerificar = "SELECT COUNT(*) as total FROM estudios WHERE departamento_id = :id";
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
        $query = "SELECT COUNT(*) as total FROM estudios WHERE departamento_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
}
