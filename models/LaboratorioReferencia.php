<?php
/**
 * Modelo: LaboratorioReferencia
 * Gestión de laboratorios de referencia (subrogados)
 */

class LaboratorioReferencia extends Model {
    protected $table = 'laboratorios_referencia';
    
    /**
     * Obtener todos los laboratorios
     */
    public function obtenerTodos() {
        $query = "SELECT * FROM {$this->table} ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener laboratorios activos
     */
    public function obtenerActivos() {
        $query = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener laboratorio por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verificar si existe código
     */
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
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
}
