<?php
/**
 * Modelo: Area
 * Gestión de áreas del laboratorio (Química Clínica, Hematología, etc.)
 */

class Area extends Model {
    protected $table = 'areas';
    
    /**
     * Obtener todas las áreas
     */
    public function obtenerTodas() {
        $query = "SELECT * FROM {$this->table} ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener áreas activas
     */
    public function obtenerActivas() {
        $query = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener área por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
