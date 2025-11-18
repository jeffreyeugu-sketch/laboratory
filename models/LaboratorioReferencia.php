<?php
/**
 * Modelo: LaboratorioReferencia
 * Gestión de laboratorios de referencia externos para subrogación
 */

class LaboratorioReferencia extends Model {
    protected $table = 'laboratorios_referencia';
    
    /**
     * Obtener todos los laboratorios de referencia activos
     */
    public function obtenerActivos() {
        $query = "SELECT * FROM {$this->table} 
                  WHERE activo = 1 
                  ORDER BY nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener todos los laboratorios (activos e inactivos)
     */
    public function obtenerTodos() {
        $query = "SELECT * FROM {$this->table} 
                  ORDER BY activo DESC, nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener un laboratorio por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener un laboratorio por código
     */
    public function obtenerPorCodigo($codigo) {
        $query = "SELECT * FROM {$this->table} WHERE codigo = :codigo";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear un nuevo laboratorio de referencia
     */
    public function crear($datos) {
        $query = "INSERT INTO {$this->table} 
                  (codigo, nombre, razon_social, rfc, direccion, ciudad, estado, codigo_postal,
                   telefono, email, contacto_nombre, contacto_telefono, contacto_email,
                   dias_entrega_promedio, observaciones, activo)
                  VALUES 
                  (:codigo, :nombre, :razon_social, :rfc, :direccion, :ciudad, :estado, :codigo_postal,
                   :telefono, :email, :contacto_nombre, :contacto_telefono, :contacto_email,
                   :dias_entrega_promedio, :observaciones, :activo)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':razon_social', $datos['razon_social']);
        $stmt->bindParam(':rfc', $datos['rfc']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':ciudad', $datos['ciudad']);
        $stmt->bindParam(':estado', $datos['estado']);
        $stmt->bindParam(':codigo_postal', $datos['codigo_postal']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':contacto_nombre', $datos['contacto_nombre']);
        $stmt->bindParam(':contacto_telefono', $datos['contacto_telefono']);
        $stmt->bindParam(':contacto_email', $datos['contacto_email']);
        $stmt->bindParam(':dias_entrega_promedio', $datos['dias_entrega_promedio'], PDO::PARAM_INT);
        $stmt->bindParam(':observaciones', $datos['observaciones']);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Actualizar un laboratorio de referencia
     */
    public function actualizar($id, $datos) {
        $query = "UPDATE {$this->table} SET
                  codigo = :codigo,
                  nombre = :nombre,
                  razon_social = :razon_social,
                  rfc = :rfc,
                  direccion = :direccion,
                  ciudad = :ciudad,
                  estado = :estado,
                  codigo_postal = :codigo_postal,
                  telefono = :telefono,
                  email = :email,
                  contacto_nombre = :contacto_nombre,
                  contacto_telefono = :contacto_telefono,
                  contacto_email = :contacto_email,
                  dias_entrega_promedio = :dias_entrega_promedio,
                  observaciones = :observaciones,
                  activo = :activo
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':razon_social', $datos['razon_social']);
        $stmt->bindParam(':rfc', $datos['rfc']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':ciudad', $datos['ciudad']);
        $stmt->bindParam(':estado', $datos['estado']);
        $stmt->bindParam(':codigo_postal', $datos['codigo_postal']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':contacto_nombre', $datos['contacto_nombre']);
        $stmt->bindParam(':contacto_telefono', $datos['contacto_telefono']);
        $stmt->bindParam(':contacto_email', $datos['contacto_email']);
        $stmt->bindParam(':dias_entrega_promedio', $datos['dias_entrega_promedio'], PDO::PARAM_INT);
        $stmt->bindParam(':observaciones', $datos['observaciones']);
        $stmt->bindParam(':activo', $datos['activo'], PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar (desactivar) un laboratorio de referencia
     */
    public function eliminar($id) {
        // Verificar si hay estudios asignados
        $queryVerificar = "SELECT COUNT(*) as total FROM estudios 
                          WHERE laboratorio_referencia_id = :id";
        $stmt = $this->db->prepare($queryVerificar);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['total'] > 0) {
            // No eliminar, solo desactivar
            $query = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        
        // Si no hay estudios asignados, permitir eliminación física
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Verificar si un código ya existe (para validación)
     */
    public function existeCodigo($codigo, $excluirId = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE codigo = :codigo";
        
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
     * Obtener estudios asignados a un laboratorio
     */
    public function obtenerEstudiosAsignados($id) {
        $query = "SELECT e.id, e.codigo_interno, e.nombre, a.nombre as area
                  FROM estudios e
                  INNER JOIN areas a ON e.area_id = a.id
                  WHERE e.laboratorio_referencia_id = :id
                  AND e.activo = 1
                  ORDER BY e.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener estadísticas de un laboratorio
     */
    public function obtenerEstadisticas($id) {
        $stats = [];
        
        // Total de estudios asignados
        $query = "SELECT COUNT(*) as total FROM estudios 
                  WHERE laboratorio_referencia_id = :id AND activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stats['total_estudios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total de órdenes enviadas (últimos 30 días)
        $query = "SELECT COUNT(DISTINCT oe.orden_id) as total
                  FROM orden_estudios oe
                  INNER JOIN estudios e ON oe.estudio_id = e.id
                  INNER JOIN ordenes o ON oe.orden_id = o.id
                  WHERE e.laboratorio_referencia_id = :id
                  AND o.fecha_registro >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stats['ordenes_mes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return $stats;
    }
    
    /**
     * Buscar laboratorios por término
     */
    public function buscar($termino) {
        $query = "SELECT * FROM {$this->table}
                  WHERE (nombre LIKE :termino 
                  OR razon_social LIKE :termino
                  OR codigo LIKE :termino
                  OR ciudad LIKE :termino)
                  ORDER BY activo DESC, nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $searchTerm = "%{$termino}%";
        $stmt->bindParam(':termino', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cambiar estado activo/inactivo
     */
    public function cambiarEstado($id, $activo) {
        $query = "UPDATE {$this->table} SET activo = :activo WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
}
