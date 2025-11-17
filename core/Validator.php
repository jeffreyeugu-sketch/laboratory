<?php
/**
 * Clase Validator
 * Manejo de validaciones de datos
 */

class Validator {
    
    private $errors = [];
    private $data = [];
    
    /**
     * Constructor
     * 
     * @param array $data Datos a validar
     */
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * Validar campo requerido
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
            $this->errors[$field] = $message ?? "El campo {$field} es requerido";
        }
        return $this;
    }
    
    /**
     * Validar email
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?? "El campo {$field} debe ser un email válido";
            }
        }
        return $this;
    }
    
    /**
     * Validar longitud mínima
     * 
     * @param string $field
     * @param int $min
     * @param string $message
     * @return self
     */
    public function minLength($field, $min, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = $message ?? "El campo {$field} debe tener al menos {$min} caracteres";
        }
        return $this;
    }
    
    /**
     * Validar longitud máxima
     * 
     * @param string $field
     * @param int $max
     * @param string $message
     * @return self
     */
    public function maxLength($field, $max, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = $message ?? "El campo {$field} no puede tener más de {$max} caracteres";
        }
        return $this;
    }
    
    /**
     * Validar valor numérico
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = $message ?? "El campo {$field} debe ser numérico";
        }
        return $this;
    }
    
    /**
     * Validar fecha
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function date($field, $message = null) {
        if (isset($this->data[$field]) && !strtotime($this->data[$field])) {
            $this->errors[$field] = $message ?? "El campo {$field} debe ser una fecha válida";
        }
        return $this;
    }
    
    /**
     * Validar que sea igual a otro campo
     * 
     * @param string $field
     * @param string $otherField
     * @param string $message
     * @return self
     */
    public function match($field, $otherField, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$otherField])) {
            if ($this->data[$field] !== $this->data[$otherField]) {
                $this->errors[$field] = $message ?? "El campo {$field} no coincide con {$otherField}";
            }
        }
        return $this;
    }
    
    /**
     * Validar valor único en base de datos
     * 
     * @param string $field
     * @param string $table
     * @param string $column
     * @param int|null $excludeId ID a excluir (para updates)
     * @param string $message
     * @return self
     */
    public function unique($field, $table, $column = null, $excludeId = null, $message = null) {
        if (!isset($this->data[$field]) || empty($this->data[$field])) {
            return $this;
        }
        
        $column = $column ?? $field;
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $params = [$this->data[$field]];
        
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            $this->errors[$field] = $message ?? "El valor del campo {$field} ya existe";
        }
        
        return $this;
    }
    
    /**
     * Validar que exista en base de datos
     * 
     * @param string $field
     * @param string $table
     * @param string $column
     * @param string $message
     * @return self
     */
    public function exists($field, $table, $column = null, $message = null) {
        if (!isset($this->data[$field]) || empty($this->data[$field])) {
            return $this;
        }
        
        $column = $column ?? $field;
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$this->data[$field]]);
        $count = $stmt->fetchColumn();
        
        if ($count === 0) {
            $this->errors[$field] = $message ?? "El valor del campo {$field} no existe";
        }
        
        return $this;
    }
    
    /**
     * Validar expresión regular
     * 
     * @param string $field
     * @param string $pattern
     * @param string $message
     * @return self
     */
    public function regex($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !preg_match($pattern, $this->data[$field])) {
            $this->errors[$field] = $message ?? "El campo {$field} no tiene el formato correcto";
        }
        return $this;
    }
    
    /**
     * Validar que sea una de las opciones válidas
     * 
     * @param string $field
     * @param array $options
     * @param string $message
     * @return self
     */
    public function in($field, $options, $message = null) {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $options)) {
            $this->errors[$field] = $message ?? "El campo {$field} debe ser uno de los valores permitidos";
        }
        return $this;
    }
    
    /**
     * Verificar si hay errores
     * 
     * @return bool
     */
    public function fails() {
        return !empty($this->errors);
    }
    
    /**
     * Verificar si pasa la validación
     * 
     * @return bool
     */
    public function passes() {
        return empty($this->errors);
    }
    
    /**
     * Obtener errores
     * 
     * @return array
     */
    public function errors() {
        return $this->errors;
    }
    
    /**
     * Obtener primer error
     * 
     * @param string|null $field
     * @return string|null
     */
    public function firstError($field = null) {
        if ($field !== null) {
            return $this->errors[$field] ?? null;
        }
        
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Agregar error personalizado
     * 
     * @param string $field
     * @param string $message
     * @return self
     */
    public function addError($field, $message) {
        $this->errors[$field] = $message;
        return $this;
    }
    
    /**
     * Validación estática rápida
     * 
     * @param array $data
     * @param array $rules
     * @return array Errores si hay, array vacío si no
     */
    public static function validate($data, $rules) {
        $validator = new self($data);
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $rulesArray = explode('|', $fieldRules);
            
            foreach ($rulesArray as $rule) {
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $ruleValue) = explode(':', $rule, 2);
                } else {
                    $ruleName = $rule;
                    $ruleValue = null;
                }
                
                switch ($ruleName) {
                    case 'required':
                        $validator->required($field);
                        break;
                    case 'email':
                        $validator->email($field);
                        break;
                    case 'min':
                        $validator->minLength($field, $ruleValue);
                        break;
                    case 'max':
                        $validator->maxLength($field, $ruleValue);
                        break;
                    case 'numeric':
                        $validator->numeric($field);
                        break;
                    case 'date':
                        $validator->date($field);
                        break;
                }
            }
        }
        
        return $validator->errors();
    }
}
