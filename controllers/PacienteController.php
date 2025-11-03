<?php
/**
 * PacienteController
 * 
 * Controlador para gestión de pacientes
 */

require_once CORE_PATH . '/Controller.php';
require_once MODELS_PATH . '/Paciente.php';

class PacienteController extends Controller {
    
    private $pacienteModel;
    
    public function __construct() {
        $this->pacienteModel = new Paciente();
    }
    
    /**
     * Lista de pacientes
     */
    public function index() {
        $this->requireAuth();
        $this->requirePermission('pacientes.ver');
        
        $sucursalId = $_SESSION['sucursal_id'];
        
        // Obtener pacientes recientes
        $pacientes = $this->pacienteModel->obtenerRecientes($sucursalId, 50);
        
        $data = [
            'pacientes' => $pacientes,
            'titulo' => 'Pacientes'
        ];
        
        $this->view('pacientes/index', $data);
    }
    
    /**
     * Muestra formulario para crear paciente
     */
    public function crear() {
        $this->requireAuth();
        $this->requirePermission('pacientes.crear');
        
        if ($this->isPost()) {
            // Procesar creación
            return $this->guardar();
        }
        
        // Generar expediente automático
        $expediente = $this->pacienteModel->generarExpediente();
        
        $data = [
            'expediente' => $expediente,
            'titulo' => 'Nuevo Paciente'
        ];
        
        $this->view('pacientes/crear', $data);
    }
    
    /**
     * Guarda un nuevo paciente
     */
    public function guardar() {
        $this->requireAuth();
        $this->requirePermission('pacientes.crear');
        
        if (!$this->isPost()) {
            $this->redirect('/laboratorio-clinico/public/pacientes');
        }
        
        // Obtener datos del formulario
        $data = $this->inputOnly([
            'expediente',
            'nombres',
            'apellido_paterno',
            'apellido_materno',
            'fecha_nacimiento',
            'sexo',
            'telefono',
            'celular',
            'email',
            'direccion',
            'codigo_postal',
            'ciudad',
            'estado',
            'ocupacion',
            'notas'
        ]);
        
        // Validar
        $validacion = $this->validate($data, [
            'expediente' => 'required|unique:pacientes,expediente',
            'nombres' => 'required|min:2',
            'apellido_paterno' => 'required|min:2',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:M,F,O',
            'email' => 'email'
        ]);
        
        if ($validacion !== true) {
            if ($this->isAjax()) {
                $this->jsonError('Error de validación', 400);
            } else {
                $data['errores'] = $validacion;
                $data['titulo'] = 'Nuevo Paciente';
                $this->view('pacientes/crear', $data);
                return;
            }
        }
        
        // Agregar datos adicionales
        $data['sucursal_registro_id'] = $_SESSION['sucursal_id'];
        $data['activo'] = 1;
        
        try {
            $pacienteId = $this->pacienteModel->create($data);
            
            // Registrar en auditoría
            Auth::logAudit(
                Auth::id(),
                'crear_paciente',
                'pacientes',
                'paciente',
                $pacienteId,
                null,
                $data,
                'Paciente creado: ' . $data['nombres'] . ' ' . $data['apellido_paterno']
            );
            
            if ($this->isAjax()) {
                $this->jsonSuccess([
                    'paciente_id' => $pacienteId,
                    'expediente' => $data['expediente']
                ], 'Paciente creado exitosamente');
            } else {
                $this->redirectWith(
                    '/laboratorio-clinico/public/pacientes/ver?id=' . $pacienteId,
                    'Paciente creado exitosamente',
                    'success'
                );
            }
            
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            
            if ($this->isAjax()) {
                $this->jsonError('Error al crear el paciente');
            } else {
                $data['error'] = 'Error al crear el paciente';
                $data['titulo'] = 'Nuevo Paciente';
                $this->view('pacientes/crear', $data);
            }
        }
    }
    
    /**
     * Muestra formulario para editar paciente
     */
    public function editar() {
        $this->requireAuth();
        $this->requirePermission('pacientes.editar');
        
        $id = $this->input('id');
        
        if (!$id) {
            $this->redirectWith('/laboratorio-clinico/public/pacientes', 'Paciente no encontrado', 'error');
        }
        
        if ($this->isPost()) {
            return $this->actualizar();
        }
        
        $paciente = $this->pacienteModel->findById($id);
        
        if (!$paciente) {
            $this->redirectWith('/laboratorio-clinico/public/pacientes', 'Paciente no encontrado', 'error');
        }
        
        $data = [
            'paciente' => $paciente,
            'titulo' => 'Editar Paciente'
        ];
        
        $this->view('pacientes/editar', $data);
    }
    
    /**
     * Actualiza un paciente
     */
    public function actualizar() {
        $this->requireAuth();
        $this->requirePermission('pacientes.editar');
        
        $id = $this->input('id');
        
        if (!$id) {
            $this->jsonError('ID de paciente no proporcionado');
        }
        
        $data = $this->inputOnly([
            'nombres',
            'apellido_paterno',
            'apellido_materno',
            'fecha_nacimiento',
            'sexo',
            'telefono',
            'celular',
            'email',
            'direccion',
            'codigo_postal',
            'ciudad',
            'estado',
            'ocupacion',
            'notas'
        ]);
        
        // Validar
        $validacion = $this->validate($data, [
            'nombres' => 'required|min:2',
            'apellido_paterno' => 'required|min:2',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:M,F,O',
            'email' => 'email'
        ]);
        
        if ($validacion !== true) {
            if ($this->isAjax()) {
                $this->jsonError('Error de validación', 400);
            } else {
                $paciente = $this->pacienteModel->findById($id);
                $data = array_merge($paciente, $data);
                $data['errores'] = $validacion;
                $data['titulo'] = 'Editar Paciente';
                $this->view('pacientes/editar', $data);
                return;
            }
        }
        
        try {
            $this->pacienteModel->update($id, $data);
            
            if ($this->isAjax()) {
                $this->jsonSuccess(null, 'Paciente actualizado exitosamente');
            } else {
                $this->redirectWith(
                    '/laboratorio-clinico/public/pacientes/ver?id=' . $id,
                    'Paciente actualizado exitosamente',
                    'success'
                );
            }
            
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            
            if ($this->isAjax()) {
                $this->jsonError('Error al actualizar el paciente');
            } else {
                $this->redirectWith(
                    '/laboratorio-clinico/public/pacientes/editar?id=' . $id,
                    'Error al actualizar el paciente',
                    'error'
                );
            }
        }
    }
    
    /**
     * Muestra detalles de un paciente
     */
    public function ver() {
        $this->requireAuth();
        $this->requirePermission('pacientes.ver');
        
        $id = $this->input('id');
        
        if (!$id) {
            $this->redirectWith('/laboratorio-clinico/public/pacientes', 'Paciente no encontrado', 'error');
        }
        
        $paciente = $this->pacienteModel->obtenerConDetalles($id);
        
        if (!$paciente) {
            $this->redirectWith('/laboratorio-clinico/public/pacientes', 'Paciente no encontrado', 'error');
        }
        
        // Obtener historial de órdenes
        $historial = $this->pacienteModel->obtenerHistorialOrdenes($id, 10);
        
        $data = [
            'paciente' => $paciente,
            'historial' => $historial,
            'titulo' => 'Detalle del Paciente'
        ];
        
        $this->view('pacientes/ver', $data);
    }
    
    /**
     * Busca pacientes (AJAX)
     */
    public function buscar() {
        $this->requireAuth();
        $this->requirePermission('pacientes.buscar');
        
        $termino = $this->input('q', '');
        
        if (strlen($termino) < 2) {
            $this->jsonSuccess([]);
        }
        
        // Buscar por nombre
        $pacientes = $this->pacienteModel->buscarPorNombre($termino, 20);
        
        // Formatear para select2
        $resultados = array_map(function($p) {
            return [
                'id' => $p['id'],
                'text' => $p['nombre_completo'] . ' - ' . $p['expediente'],
                'expediente' => $p['expediente'],
                'nombre_completo' => $p['nombre_completo'],
                'edad' => $p['edad'],
                'sexo' => $p['sexo']
            ];
        }, $pacientes);
        
        $this->jsonSuccess($resultados);
    }
    
    /**
     * Elimina (desactiva) un paciente
     */
    public function eliminar() {
        $this->requireAuth();
        $this->requirePermission('pacientes.eliminar');
        
        $id = $this->input('id');
        
        if (!$id) {
            $this->jsonError('ID de paciente no proporcionado');
        }
        
        try {
            $this->pacienteModel->desactivar($id);
            $this->jsonSuccess(null, 'Paciente desactivado exitosamente');
            
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');
            $this->jsonError('Error al desactivar el paciente');
        }
    }
}
