<?php
/**
 * Modelo: TipoMuestra
 * Gestión de tipos de muestra del laboratorio
 */

class TipoMuestra extends Model {
    protected $table = 'tipos_muestra';
    
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
                  (codigo, nombre, descripcion, requiere_ayuno, requiere_refrigeracion,
                   tiempo_estabilidad_horas, temperatura_almacenamiento, 
                   instrucciones_recoleccion, orden, activo)
                  VALUES 
                  (:codigo, :nombre, :descripcion, :requiere_ayuno, :requiere_refrigeracion,
                   :tiempo_estabilidad_horas, :temperatura_almacenamiento,
                   :instrucciones_recoleccion, :orden, :activo)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':requiere_ayuno', $datos['requiere_ayuno'], PDO::PARAM_BOOL);
        $stmt->bindParam(':requiere_refrigeracion', $datos['requiere_refrigeracion'], PDO::PARAM_BOOL);
        $stmt->bindParam(':tiempo_estabilidad_horas', $datos['tiempo_estabilidad_horas'], PDO::PARAM_INT);
        $stmt->bindParam(':temperatura_almacenamiento', $datos['temperatura_almacenamiento']);
        $stmt->bindParam(':instrucciones_recoleccion', $datos['instrucciones_recoleccion']);
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
                  descripcion = :descripcion,
                  requiere_ayuno = :requiere_ayuno,
                  requiere_refrigeracion = :requiere_refrigeracion,
                  tiempo_estabilidad_horas = :tiempo_estabilidad_horas,
                  temperatura_almacenamiento = :temperatura_almacenamiento,
                  instrucciones_recoleccion = :instrucciones_recoleccion,
                  orden = :orden,
                  activo = :activo
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':requiere_ayuno', $datos['requiere_ayuno'], PDO::PARAM_BOOL);
        $stmt->bindParam(':requiere_refrigeracion', $datos['requiere_refrigeracion'], PDO::PARAM_BOOL);
        $stmt->bindParam(':tiempo_estabilidad_horas', $datos['tiempo_estabilidad_horas'], PDO::PARAM_INT);
        $stmt->bindParam(':temperatura_almacenamiento', $datos['temperatura_almacenamiento']);
        $stmt->bindParam(':instrucciones_recoleccion', $datos['instrucciones_recoleccion']);
        $stmt->bindParam(':orden', $datos['orden'], PDO::PARAM_INT);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    public function eliminar($id) {
        // Verificar si hay estudios usando este tipo de muestra
        $queryVerificar = "SELECT COUNT(*) as total FROM estudios WHERE tipo_muestra_id = :id";
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
        $query = "SELECT COUNT(*) as total FROM estudios WHERE tipo_muestra_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
}
