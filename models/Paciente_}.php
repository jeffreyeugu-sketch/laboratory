<?php
/**
 * Modelo Paciente
 * Maneja todas las operaciones relacionadas con pacientes
 */

class Paciente extends Model {
    
    protected $table = 'pacientes';
    
    /**
     * Crear un nuevo paciente
     * 
     * @param array $datos
     * @return int ID del paciente creado
     */
    public function crear($datos) {
        $db = Database::getInstance()->getConnection();
        
        // Generar expediente si no viene
        if (empty($datos['expediente'])) {
            $datos['expediente'] = $this->generarExpediente();
        }
        
        try {
            $stmt = $db->prepare("
                INSERT INTO pacientes (
                    expediente,
                    nombres,
                    apellido_paterno,
                    apellido_materno,
                    fecha_nacimiento,
                    sexo,
                    curp,
                    telefono,
                    email,
                    calle,
                    numero_exterior,
                    numero_interior,
                    colonia,
                    ciudad,
                    estado,
                    codigo_postal,
                    ocupacion,
                    estado_civil,
                    nombre_contacto_emergencia,
                    telefono_contacto_emergencia,
                    parentesco_contacto_emergencia,
                    observaciones,
                    usuario_registro_id,
                    activo,
                    fecha_registro
                ) VALUES (
                    :expediente,
                    :nombres,
                    :apellido_paterno,
                    :apellido_materno,
                    :fecha_nacimiento,
                    :sexo,
                    :curp,
                    :telefono,
                    :email,
                    :calle,
                    :numero_exterior,
                    :numero_interior,
                    :colonia,
                    :ciudad,
                    :estado,
                    :codigo_postal,
                    :ocupacion,
                    :estado_civil,
                    :nombre_contacto_emergencia,
                    :telefono_contacto_emergencia,
                    :parentesco_contacto_emergencia,
                    :observaciones,
                    :usuario_registro_id,
                    1,
                    NOW()
                )
            ");
            
            $stmt->execute([
                ':expediente' => $datos['expediente'],
                ':nombres' => $datos['nombres'],
                ':apellido_paterno' => $datos['apellido_paterno'],
                ':apellido_materno' => $datos['apellido_materno'] ?? null,
                ':fecha_nacimiento' => $datos['fecha_nacimiento'],
                ':sexo' => $datos['sexo'],
                ':curp' => $datos['curp'] ?? null,
                ':telefono' => $datos['telefono'] ?? null,
                ':email' => $datos['email'] ?? null,
                ':calle' => $datos['calle'] ?? null,
                ':numero_exterior' => $datos['numero_exterior'] ?? null,
                ':numero_interior' => $datos['numero_interior'] ?? null,
                ':colonia' => $datos['colonia'] ?? null,
                ':ciudad' => $datos['ciudad'] ?? null,
                ':estado' => $datos['estado'] ?? null,
                ':codigo_postal' => $datos['codigo_postal'] ?? null,
                ':ocupacion' => $datos['ocupacion'] ?? null,
                ':estado_civil' => $datos['estado_civil'] ?? null,
                ':nombre_contacto_emergencia' => $datos['nombre_contacto_emergencia'] ?? null,
                ':telefono_contacto_emergencia' => $datos['telefono_contacto_emergencia'] ?? null,
                ':parentesco_contacto_emergencia' => $datos['parentesco_contacto_emergencia'] ?? null,
                ':observaciones' => $datos['observaciones'] ?? null,
                ':usuario_registro_id' => $datos['usuario_registro_id']
            ]);
            
            return $db->lastInsertId();
            
        } catch (PDOException $e) {
            logError('Error al crear paciente: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener paciente por ID
     * 
     * @param int $id
     * @return array|null
     */
    public function obtenerPorId($id) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                SELECT 
                    p.*,
                    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as nombre_completo,
                    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad
                FROM pacientes p
                WHERE p.id = ? AND p.activo = 1
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            logError('Error al obtener paciente: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Actualizar paciente
     * 
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar($id, $datos) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                UPDATE pacientes SET
                    nombres = :nombres,
                    apellido_paterno = :apellido_paterno,
                    apellido_materno = :apellido_materno,
                    fecha_nacimiento = :fecha_nacimiento,
                    sexo = :sexo,
                    curp = :curp,
                    telefono = :telefono,
                    email = :email,
                    calle = :calle,
                    numero_exterior = :numero_exterior,
                    numero_interior = :numero_interior,
                    colonia = :colonia,
                    ciudad = :ciudad,
                    estado = :estado,
                    codigo_postal = :codigo_postal,
                    ocupacion = :ocupacion,
                    estado_civil = :estado_civil,
                    nombre_contacto_emergencia = :nombre_contacto_emergencia,
                    telefono_contacto_emergencia = :telefono_contacto_emergencia,
                    parentesco_contacto_emergencia = :parentesco_contacto_emergencia,
                    observaciones = :observaciones
                WHERE id = :id
            ");
            
            return $stmt->execute([
                ':id' => $id,
                ':nombres' => $datos['nombres'],
                ':apellido_paterno' => $datos['apellido_paterno'],
                ':apellido_materno' => $datos['apellido_materno'] ?? null,
                ':fecha_nacimiento' => $datos['fecha_nacimiento'],
                ':sexo' => $datos['sexo'],
                ':curp' => $datos['curp'] ?? null,
                ':telefono' => $datos['telefono'] ?? null,
                ':email' => $datos['email'] ?? null,
                ':calle' => $datos['calle'] ?? null,
                ':numero_exterior' => $datos['numero_exterior'] ?? null,
                ':numero_interior' => $datos['numero_interior'] ?? null,
                ':colonia' => $datos['colonia'] ?? null,
                ':ciudad' => $datos['ciudad'] ?? null,
                ':estado' => $datos['estado'] ?? null,
                ':codigo_postal' => $datos['codigo_postal'] ?? null,
                ':ocupacion' => $datos['ocupacion'] ?? null,
                ':estado_civil' => $datos['estado_civil'] ?? null,
                ':nombre_contacto_emergencia' => $datos['nombre_contacto_emergencia'] ?? null,
                ':telefono_contacto_emergencia' => $datos['telefono_contacto_emergencia'] ?? null,
                ':parentesco_contacto_emergencia' => $datos['parentesco_contacto_emergencia'] ?? null,
                ':observaciones' => $datos['observaciones'] ?? null
            ]);
            
        } catch (PDOException $e) {
            logError('Error al actualizar paciente: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Eliminar paciente (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function eliminar($id) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("UPDATE pacientes SET activo = 0 WHERE id = ?");
            return $stmt->execute([$id]);
            
        } catch (PDOException $e) {
            logError('Error al eliminar paciente: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Buscar pacientes
     * 
     * @param string $termino
     * @return array
     */
    public function buscar($termino) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                SELECT 
                    p.id,
                    p.expediente,
                    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as nombre_completo,
                    p.fecha_nacimiento,
                    p.sexo,
                    p.telefono,
                    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad
                FROM pacientes p
                WHERE p.activo = 1
                AND (
                    p.expediente LIKE :termino
                    OR p.nombres LIKE :termino
                    OR p.apellido_paterno LIKE :termino
                    OR p.apellido_materno LIKE :termino
                    OR CONCAT(p.nombres, ' ', p.apellido_paterno) LIKE :termino
                    OR CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) LIKE :termino
                    OR p.telefono LIKE :termino
                    OR p.curp LIKE :termino
                )
                ORDER BY p.apellido_paterno, p.apellido_materno, p.nombres
                LIMIT 20
            ");
            
            $terminoBusqueda = "%{$termino}%";
            $stmt->execute([':termino' => $terminoBusqueda]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            logError('Error al buscar pacientes: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Buscar pacientes duplicados
     * 
     * @param string $nombres
     * @param string $apellidoPaterno
     * @param string|null $apellidoMaterno
     * @param string $fechaNacimiento
     * @return array
     */
    public function buscarDuplicados($nombres, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                SELECT 
                    p.id,
                    p.expediente,
                    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as nombre_completo,
                    p.fecha_nacimiento,
                    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad
                FROM pacientes p
                WHERE p.activo = 1
                AND p.nombres LIKE :nombres
                AND p.apellido_paterno LIKE :apellido_paterno
                AND (
                    p.fecha_nacimiento = :fecha_nacimiento
                    OR (
                        :apellido_materno IS NOT NULL 
                        AND p.apellido_materno LIKE :apellido_materno
                    )
                )
                LIMIT 5
            ");
            
            $stmt->execute([
                ':nombres' => "%{$nombres}%",
                ':apellido_paterno' => "%{$apellidoPaterno}%",
                ':apellido_materno' => $apellidoMaterno ? "%{$apellidoMaterno}%" : null,
                ':fecha_nacimiento' => $fechaNacimiento
            ]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            logError('Error al buscar duplicados: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener historial de órdenes del paciente
     * 
     * @param int $pacienteId
     * @return array
     */
    public function obtenerHistorialOrdenes($pacienteId) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                SELECT 
                    o.id,
                    o.folio,
                    o.fecha_registro,
                    o.estatus,
                    o.total,
                    o.saldo
                FROM ordenes o
                WHERE o.paciente_id = ?
                ORDER BY o.fecha_registro DESC
                LIMIT 50
            ");
            
            $stmt->execute([$pacienteId]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            logError('Error al obtener historial de órdenes: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generar número de expediente único
     * 
     * @return string
     */
    public function generarExpediente() {
        $db = Database::getInstance()->getConnection();
        
        try {
            // Obtener el último expediente
            $stmt = $db->query("
                SELECT MAX(CAST(expediente AS UNSIGNED)) as ultimo_expediente 
                FROM pacientes
                WHERE expediente REGEXP '^[0-9]+$'
            ");
            
            $resultado = $stmt->fetch();
            $ultimoExpediente = $resultado['ultimo_expediente'] ?? 0;
            
            // Incrementar y formatear con ceros a la izquierda (8 dígitos)
            $nuevoExpediente = str_pad($ultimoExpediente + 1, 8, '0', STR_PAD_LEFT);
            
            return $nuevoExpediente;
            
        } catch (PDOException $e) {
            logError('Error al generar expediente: ' . $e->getMessage());
            // En caso de error, generar un número basado en timestamp
            return str_pad(time() % 99999999, 8, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Obtener todos los pacientes activos
     * 
     * @return array
     */
    public function obtenerTodos() {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->query("
                SELECT 
                    p.*,
                    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as nombre_completo,
                    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad
                FROM pacientes p
                WHERE p.activo = 1
                ORDER BY p.fecha_registro DESC
            ");
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            logError('Error al obtener pacientes: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Contar total de pacientes activos
     * 
     * @return int
     */
    public function contarActivos() {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->query("SELECT COUNT(*) as total FROM pacientes WHERE activo = 1");
            $resultado = $stmt->fetch();
            return (int)$resultado['total'];
            
        } catch (PDOException $e) {
            logError('Error al contar pacientes: ' . $e->getMessage());
            return 0;
        }
    }
}
