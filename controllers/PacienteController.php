<?php
/**
 * Controlador de Pacientes
 * Gestión completa de pacientes del laboratorio
 */

// Cargar el modelo Paciente
require_once __DIR__ . '/../models/Paciente.php';

class PacienteController extends Controller {
    
    private $pacienteModel;
    
    public function __construct() {
        // Verificar autenticación
        if (!Auth::check()) {
            redirect('login');
        }
        
        $this->pacienteModel = new Paciente();
    }
    
    /**
     * Lista de pacientes
     */
    public function index() {
        $data = [
            'title' => 'Pacientes - Listado',
            'usuario' => currentUser()
        ];
        
        $this->view('pacientes/index', $data);
    }
    
    /**
     * Obtener lista de pacientes (AJAX)
     */
    public function listar() {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Obtener todos los pacientes activos
            $stmt = $db->query("
                SELECT 
                    p.id,
                    p.expediente,
                    p.nombres,
                    p.apellido_paterno,
                    p.apellido_materno,
                    p.fecha_nacimiento,
                    p.sexo,
                    p.telefono,
                    p.email,
                    p.fecha_registro,
                    CONCAT(p.nombres, ' ', p.apellido_paterno, ' ', IFNULL(p.apellido_materno, '')) as nombre_completo,
                    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad
                FROM pacientes p
                WHERE p.activo = 1
                ORDER BY p.fecha_registro DESC
            ");
            
            $pacientes = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $pacientes
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener pacientes: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Formulario para crear nuevo paciente
     */
    public function crear() {
        $data = [
            'title' => 'Nuevo Paciente',
            'usuario' => currentUser()
        ];
        
        $this->view('pacientes/crear', $data);
    }
    
    /**
     * Guardar nuevo paciente
     */
    public function guardar() {
        // Verificar que sea petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('pacientes');
        }
        
        // Validar datos
        $errores = $this->validarDatos($_POST);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            redirect('pacientes/crear');
        }
        
        try {
            // Verificar si ya existe un paciente similar
            $duplicados = $this->pacienteModel->buscarDuplicados(
                $_POST['nombres'],
                $_POST['apellido_paterno'],
                $_POST['apellido_materno'] ?? null,
                $_POST['fecha_nacimiento']
            );
            
            if (!empty($duplicados)) {
                $_SESSION['warning'] = 'Se encontraron pacientes similares. Por favor verifica antes de continuar.';
                $_SESSION['duplicados'] = $duplicados;
                $_SESSION['old'] = $_POST;
                redirect('pacientes/crear');
            }
            
            // Generar número de expediente
            $expediente = $this->pacienteModel->generarExpediente();

            // Preparar datos
            $datos = [
                'expediente' => $expediente,  // ← AGREGAR ESTA LÍNEA
                'nombres' => trim($_POST['nombres']),
                'apellido_paterno' => trim($_POST['apellido_paterno']),
                'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'sexo' => $_POST['sexo'],
                'curp' => strtoupper(trim($_POST['curp'] ?? '')),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'calle' => trim($_POST['calle'] ?? ''),
                'numero_exterior' => trim($_POST['numero_exterior'] ?? ''),
                'numero_interior' => trim($_POST['numero_interior'] ?? ''),
                'colonia' => trim($_POST['colonia'] ?? ''),
                'ciudad' => trim($_POST['ciudad'] ?? ''),
                'estado' => trim($_POST['estado'] ?? ''),
                'codigo_postal' => trim($_POST['codigo_postal'] ?? ''),
                'ocupacion' => trim($_POST['ocupacion'] ?? ''),
                'estado_civil' => $_POST['estado_civil'] ?? null,
                'nombre_contacto_emergencia' => trim($_POST['nombre_contacto_emergencia'] ?? ''),
                'telefono_contacto_emergencia' => trim($_POST['telefono_contacto_emergencia'] ?? ''),
                'parentesco_contacto_emergencia' => trim($_POST['parentesco_contacto_emergencia'] ?? ''),
                'observaciones' => trim($_POST['observaciones'] ?? ''),
                'usuario_registro_id' => currentUserId()
            ];
            
            // Crear paciente
            $pacienteId = $this->pacienteModel->crear($datos);
            
            if ($pacienteId) {
                // Registrar en auditoría
                logAuditoria('crear', 'paciente', $pacienteId, ['datos' => $datos]);
                
                setFlash('Paciente registrado exitosamente', 'success');
                redirect('pacientes/ver/' . $pacienteId);
            } else {
                throw new Exception('No se pudo crear el paciente');
            }
            
        } catch (Exception $e) {
            logError('Error al crear paciente: ' . $e->getMessage());
            setFlash('Error al registrar paciente: ' . $e->getMessage(), 'error');
            $_SESSION['old'] = $_POST;
            redirect('pacientes/crear');
        }
    }
    
    /**
     * Formulario para editar paciente
     */
    public function editar($id) {
        $paciente = $this->pacienteModel->obtenerPorId($id);
        
        if (!$paciente) {
            setFlash('Paciente no encontrado', 'error');
            redirect('pacientes');
        }
        
        $data = [
            'title' => 'Editar Paciente',
            'usuario' => currentUser(),
            'paciente' => $paciente
        ];
        
        $this->view('pacientes/editar', $data);
    }
    
    /**
     * Actualizar paciente
     */
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('pacientes');
        }
        
        $paciente = $this->pacienteModel->obtenerPorId($id);
        if (!$paciente) {
            setFlash('Paciente no encontrado', 'error');
            redirect('pacientes');
        }
        
        // Validar datos
        $errores = $this->validarDatos($_POST, true);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            redirect('pacientes/editar/' . $id);
        }
        
        try {
            $datos = [
                'nombres' => trim($_POST['nombres']),
                'apellido_paterno' => trim($_POST['apellido_paterno']),
                'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'sexo' => $_POST['sexo'],
                'curp' => strtoupper(trim($_POST['curp'] ?? '')),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'calle' => trim($_POST['calle'] ?? ''),
                'numero_exterior' => trim($_POST['numero_exterior'] ?? ''),
                'numero_interior' => trim($_POST['numero_interior'] ?? ''),
                'colonia' => trim($_POST['colonia'] ?? ''),
                'ciudad' => trim($_POST['ciudad'] ?? ''),
                'estado' => trim($_POST['estado'] ?? ''),
                'codigo_postal' => trim($_POST['codigo_postal'] ?? ''),
                'ocupacion' => trim($_POST['ocupacion'] ?? ''),
                'estado_civil' => $_POST['estado_civil'] ?? null,
                'nombre_contacto_emergencia' => trim($_POST['nombre_contacto_emergencia'] ?? ''),
                'telefono_contacto_emergencia' => trim($_POST['telefono_contacto_emergencia'] ?? ''),
                'parentesco_contacto_emergencia' => trim($_POST['parentesco_contacto_emergencia'] ?? ''),
                'observaciones' => trim($_POST['observaciones'] ?? '')
            ];
            
            $resultado = $this->pacienteModel->actualizar($id, $datos);
            
            if ($resultado) {
                logAuditoria('actualizar', 'paciente', $id, ['datos' => $datos]);
                setFlash('Paciente actualizado exitosamente', 'success');
                redirect('pacientes/ver/' . $id);
            } else {
                throw new Exception('No se pudo actualizar el paciente');
            }
            
        } catch (Exception $e) {
            logError('Error al actualizar paciente: ' . $e->getMessage());
            setFlash('Error al actualizar paciente: ' . $e->getMessage(), 'error');
            $_SESSION['old'] = $_POST;
            redirect('pacientes/editar/' . $id);
        }
    }
    
    /**
     * Ver detalle del paciente
     */
    public function ver($id) {
        $paciente = $this->pacienteModel->obtenerPorId($id);
        
        if (!$paciente) {
            setFlash('Paciente no encontrado', 'error');
            redirect('pacientes');
        }
        
        // Obtener historial de órdenes
        $historialOrdenes = $this->pacienteModel->obtenerHistorialOrdenes($id);
        
        $data = [
            'title' => 'Detalle del Paciente',
            'usuario' => currentUser(),
            'paciente' => $paciente,
            'historialOrdenes' => $historialOrdenes
        ];
        
        $this->view('pacientes/ver', $data);
    }
    
    /**
     * Buscar pacientes (AJAX)
     */
    public function buscar() {
        header('Content-Type: application/json');
        
        $termino = $_GET['q'] ?? '';
        
        if (strlen($termino) < 2) {
            echo json_encode([
                'success' => true,
                'data' => []
            ]);
            exit;
        }
        
        try {
            $resultados = $this->pacienteModel->buscar($termino);
            
            echo json_encode([
                'success' => true,
                'data' => $resultados
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en búsqueda: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Eliminar paciente (soft delete)
     */
    public function eliminar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('pacientes');
        }
        
        try {
            $resultado = $this->pacienteModel->eliminar($id);
            
            if ($resultado) {
                logAuditoria('eliminar', 'paciente', $id);
                setFlash('Paciente eliminado exitosamente', 'success');
            } else {
                setFlash('No se pudo eliminar el paciente', 'error');
            }
            
        } catch (Exception $e) {
            logError('Error al eliminar paciente: ' . $e->getMessage());
            setFlash('Error al eliminar paciente: ' . $e->getMessage(), 'error');
        }
        
        redirect('pacientes');
    }
    
    /**
     * Validar datos del paciente
     */
    private function validarDatos($datos, $esEdicion = false) {
        $errores = [];
        
        // Nombres (requerido)
        if (empty(trim($datos['nombres'] ?? ''))) {
            $errores['nombres'] = 'El nombre es obligatorio';
        }
        
        // Apellido paterno (requerido)
        if (empty(trim($datos['apellido_paterno'] ?? ''))) {
            $errores['apellido_paterno'] = 'El apellido paterno es obligatorio';
        }
        
        // Fecha de nacimiento (requerido y válido)
        if (empty($datos['fecha_nacimiento'])) {
            $errores['fecha_nacimiento'] = 'La fecha de nacimiento es obligatoria';
        } else {
            $fecha = strtotime($datos['fecha_nacimiento']);
            if (!$fecha || $fecha > time()) {
                $errores['fecha_nacimiento'] = 'La fecha de nacimiento no es válida';
            }
        }
        
        // Sexo (requerido)
        if (empty($datos['sexo']) || !in_array($datos['sexo'], ['M', 'F'])) {
            $errores['sexo'] = 'El sexo es obligatorio';
        }
        
        // CURP (opcional pero si se proporciona debe ser válido)
        if (!empty($datos['curp']) && !validarCURP($datos['curp'])) {
            $errores['curp'] = 'El CURP no es válido';
        }
        
        // Email (opcional pero si se proporciona debe ser válido)
        if (!empty($datos['email']) && !validarEmail($datos['email'])) {
            $errores['email'] = 'El email no es válido';
        }
        
        // Teléfono (opcional pero debe tener formato válido)
        if (!empty($datos['telefono'])) {
            $telefono = preg_replace('/[^0-9]/', '', $datos['telefono']);
            if (strlen($telefono) < 10) {
                $errores['telefono'] = 'El teléfono debe tener al menos 10 dígitos';
            }
        }
        
        return $errores;
    }
    
    /**
     * Obtener paciente por ID (AJAX)
     */
    public function obtener($id) {
        header('Content-Type: application/json');
        
        try {
            $paciente = $this->pacienteModel->obtenerPorId($id);
            
            if ($paciente) {
                echo json_encode([
                    'success' => true,
                    'data' => $paciente
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}
