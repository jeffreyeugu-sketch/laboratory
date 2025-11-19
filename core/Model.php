<?php
/**
 * Clase Model Base
 * Todos los modelos heredan de esta clase
 */

require_once __DIR__ . '/Database.php';

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // ========================================
    // MÉTODOS EN ESPAÑOL (PRINCIPALES)
    // ========================================
    
    /**
     * Obtener todos los registros
     * 
     * @return array
     */
    public function obtenerTodos() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener un registro por ID
     * 
     * @param int $id
     * @return array|false
     */
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener registros con condiciones
     * 
     * @param array $conditions ['campo' => 'valor']
     * @param string $operator 'AND' o 'OR'
     * @return array
     */
    public function obtenerDonde($conditions, $operator = 'AND') {
        $where = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $where[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $whereClause = implode(" {$operator} ", $where);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear un nuevo registro
     * 
     * @param array $data
     * @return int ID del registro creado
     */
    public function crear($data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Actualizar un registro
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function actualizar($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Eliminar un registro
     * 
     * @param int $id
     * @return bool
     */
    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Contar registros
     * 
     * @param array $conditions
     * @return int
     */
    public function contar($conditions = []) {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $params = [];
        } else {
            $where = [];
            $params = [];
            
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = ?";
                $params[] = $value;
            }
            
            $whereClause = implode(" AND ", $where);
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereClause}";
        }
        
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
    
    /**
     * Verificar si existe un registro
     * 
     * @param int $id
     * @return bool
     */
    public function existe($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
    
    /**
     * Ejecutar una consulta SQL personalizada
     * 
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    // ========================================
    // MÉTODOS EN INGLÉS (ALIAS PARA COMPATIBILIDAD)
    // ========================================
    
    /**
     * @deprecated Usar obtenerTodos() en su lugar
     */
    public function findAll() {
        return $this->obtenerTodos();
    }
    
    /**
     * @deprecated Usar obtenerPorId() en su lugar
     */
    public function findById($id) {
        return $this->obtenerPorId($id);
    }
    
    /**
     * @deprecated Usar obtenerDonde() en su lugar
     */
    public function findWhere($conditions, $operator = 'AND') {
        return $this->obtenerDonde($conditions, $operator);
    }
    
    /**
     * @deprecated Usar crear() en su lugar
     */
    public function create($data) {
        return $this->crear($data);
    }
    
    /**
     * @deprecated Usar actualizar() en su lugar
     */
    public function update($id, $data) {
        return $this->actualizar($id, $data);
    }
    
    /**
     * @deprecated Usar eliminar() en su lugar
     */
    public function delete($id) {
        return $this->eliminar($id);
    }
    
    /**
     * @deprecated Usar contar() en su lugar
     */
    public function count($conditions = []) {
        return $this->contar($conditions);
    }
    
    /**
     * @deprecated Usar existe() en su lugar
     */
    public function exists($id) {
        return $this->existe($id);
    }
}