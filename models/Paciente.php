<?php
/**
 * Modelo Paciente
 * 
 * Maneja todas las operaciones relacionadas con pacientes
 */

require_once CORE_PATH . '/Model.php';

class Paciente extends Model {
    
    protected $table = 'pacientes';
    protected $primaryKey = 'id';
    
    /**
     * Generar número de expediente único
     * 
     * @return string
     */
    public function generarExpediente() {
        $db = Database::getInstance()->getConnection();
        
        try {
            // Obtener el último expediente
            $stmt = $db->query("SELECT expediente FROM pacientes ORDER BY id DESC LIMIT 1");
            $ultimo = $stmt->fetch();
            
            if ($ultimo) {
                $numero = intval($ultimo['expediente']) + 1;
            } else {
                $numero = 1;
            }
            
            return str_pad($numero, 8, '0', STR_PAD_LEFT);
            
        } catch (PDOException $e) {
            logError('Error al generar expediente: ' . $e->getMessage());
            return str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Verificar si existe un paciente con datos similares (duplicado)
     * 
     * @param array $datos
     * @return array Array de pacientes similares
     */
    public function verificarDuplicados($datos) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                SELECT 
                    id,
                    expediente,
                    CONCAT(nombres, ' ', apellido_paterno, ' ', IFNULL(apellido_materno, '')) as nombre_completo,
                    fecha_nacimiento,
                    TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) as edad
                FROM pacientes
                WHERE activo = 1
                AND (
                    (nombres = :nombres 
                     AND apellido_paterno = :apellido_paterno 
                     AND (apellido_materno = :apellido_materno OR apellido_materno IS NULL))
                    OR
                    (fecha_nacimiento = :fecha_nacimiento 
                     AND apellido_paterno = :apellido_paterno2)
                    OR
                    (curp = :curp AND curp IS NOT NULL)
                )
                LIMIT 5
            ");
            
            $stmt->execute([
                ':nombres' => $datos['nombres'],
                ':apellido_paterno' => $datos['apellido_paterno'],
                ':apellido_materno' => $datos['apellido_materno'] ?? null,
                ':fecha_nacimiento' => $datos['fecha_nacimiento'],
                ':apellido_paterno2' => $datos['apellido_paterno'],
                ':curp' => $datos['curp'] ?? null
            ]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            logError('Error al verificar duplicados: ' . $e->getMessage());
            return [];
        }
    }
    /*
     * Buscar pacientes duplicados (método alternativo)
    */
    public function buscarDuplicados($nombres, $apellidoPaterno, $apellidoMaterno = null, $fechaNacimiento = null) {
    $datos = [
        'nombres' => $nombres,
        'apellido_paterno' => $apellidoPaterno,
        'apellido_materno' => $apellidoMaterno,
        'fecha_nacimiento' => $fechaNacimiento
    ];
    
    return $this->verificarDuplicados($datos);
    }
    
    /**
     * Crear un nuevo paciente
     * 
     * @param array $datos
     * @return int ID del paciente creado
     */
    public function crear($datos) {
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("
                INSERT INTO pacientes (
                    expediente, nombres, apellido_paterno, apellido_materno,
                    fecha_nacimiento, sexo, curp, telefono, celular, email,
                    calle, numero_exterior, numero_interior, colonia,
                    codigo_postal, ciudad, estado, ocupacion, estado_civil,
                    nombre_contacto_emergencia, telefono_contacto_emergencia, 
                    parentesco_contacto_emergencia, observaciones,
                    sucursal_registro_id
                ) VALUES (
                    :expediente, :nombres, :apellido_paterno, :apellido_materno,
                    :fecha_nacimiento, :sexo, :curp, :telefono, :celular, :email,
                    :calle, :numero_exterior, :numero_interior, :colonia,
                    :codigo_postal, :ciudad, :estado, :ocupacion, :estado_civil,
                    :nombre_contacto_emergencia, :telefono_contacto_emergencia,
                    :parentesco_contacto_emergencia, :observaciones,
                    :sucursal_registro_id
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
                ':celular' => $datos['celular'] ?? null,
                ':email' => $datos['email'] ?? null,
                ':calle' => $datos['calle'] ?? null,
                ':numero_exterior' => $datos['numero_exterior'] ?? null,
                ':numero_interior' => $datos['numero_interior'] ?? null,
                ':colonia' => $datos['colonia'] ?? null,
                ':codigo_postal' => $datos['codigo_postal'] ?? null,
                ':ciudad' => $datos['ciudad'] ?? null,
                ':estado' => $datos['estado'] ?? null,
                ':ocupacion' => $datos['ocupacion'] ?? null,
                ':estado_civil' => $datos['estado_civil'] ?? null,
                ':nombre_contacto_emergencia' => $datos['nombre_contacto_emergencia'] ?? null,
                ':telefono_contacto_emergencia' => $datos['telefono_contacto_emergencia'] ?? null,
                ':parentesco_contacto_emergencia' => $datos['parentesco_contacto_emergencia'] ?? null,
                ':observaciones' => $datos['observaciones'] ?? null,
                ':sucursal_registro_id' => $datos['sucursal_registro_id'] ?? 1
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
                    celular = :celular,
                    email = :email,
                    calle = :calle,
                    numero_exterior = :numero_exterior,
                    numero_interior = :numero_interior,
                    colonia = :colonia,
                    codigo_postal = :codigo_postal,
                    ciudad = :ciudad,
                    estado = :estado,
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
                ':celular' => $datos['celular'] ?? null,
                ':email' => $datos['email'] ?? null,
                ':calle' => $datos['calle'] ?? null,
                ':numero_exterior' => $datos['numero_exterior'] ?? null,
                ':numero_interior' => $datos['numero_interior'] ?? null,
                ':colonia' => $datos['colonia'] ?? null,
                ':codigo_postal' => $datos['codigo_postal'] ?? null,
                ':ciudad' => $datos['ciudad'] ?? null,
                ':estado' => $datos['estado'] ?? null,
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
                    p.celular,
                    p.curp,
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
                    OR p.celular LIKE :termino
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
     * Obtener historial de órdenes de un paciente
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
                    o.total,
                    o.estatus,
                    o.estatus_pago,
                    COUNT(oe.id) as num_estudios
                FROM ordenes o
                LEFT JOIN orden_estudios oe ON o.id = oe.orden_id
                WHERE o.paciente_id = ?
                GROUP BY o.id
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
     * Listar pacientes con paginación y filtros
     * 
     * @param int $inicio
     * @param int $limite
     * @param string $busqueda
     * @return array
     */
    public function listar($inicio = 0, $limite = 50, $busqueda = '') {
        $db = Database::getInstance()->getConnection();
        
        try {
            $sql = "
                SELECT 
                    p.id,
                    p.expediente,
                    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as nombre_completo,
                    p.fecha_nacimiento,
                    p.sexo,
                    p.telefono,
                    p.celular,
                    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad,
                    p.fecha_registro
                FROM pacientes p
                WHERE p.activo = 1
            ";
            
            $params = [];
            
            if (!empty($busqueda)) {
                $sql .= " AND (
                    p.expediente LIKE :busqueda
                    OR p.nombres LIKE :busqueda
                    OR p.apellido_paterno LIKE :busqueda
                    OR p.apellido_materno LIKE :busqueda
                    OR CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', p.apellido_materno) LIKE :busqueda
                    OR p.curp LIKE :busqueda
                )";
                $params[':busqueda'] = "%{$busqueda}%";
            }
            
            $sql .= " ORDER BY p.fecha_registro DESC LIMIT :inicio, :limite";
            
            $stmt = $db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            logError('Error al listar pacientes: ' . $e->getMessage());
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
