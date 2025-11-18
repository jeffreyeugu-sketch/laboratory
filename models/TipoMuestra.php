<?php
/**
 * Modelo: TipoMuestra
 * Gestión de tipos de muestras (Sangre, Orina, etc.)
 */

class TipoMuestra extends Model {
    protected $table = 'tipos_muestra';
    
    /**
     * Obtener todos los tipos de muestra
     */
    public function obtenerTodos() {
        $query = "SELECT * FROM {$this->table} ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener tipos de muestra activos
     */
    public function obtenerActivas() {
        $query = "SELECT * FROM {$this->table} WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener tipo de muestra por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
