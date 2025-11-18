<?php
/**
 * Controlador: LaboratorioReferenciaController
 * Gestión de laboratorios de referencia
 */

class LaboratorioReferenciaController extends Controller {
    private $laboratorioModel;
    
    public function __construct() {
        parent::__construct();
        $this->laboratorioModel = new LaboratorioReferencia();
    }
    
    /**
     * Listar todos los laboratorios de referencia
     */
    public function index() {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        $laboratorios = $this->laboratorioModel->obtenerTodos();
        
        $data = [
            'title' => 'Laboratorios de Referencia',
            'laboratorios' => $laboratorios
        ];
        
        $this->view('catalogos/laboratorios-referencia/index', $data);
    }
    
    /**
     * Formulario para crear un nuevo laboratorio
     */
    public function crear() {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        $data = [
            'title' => 'Nuevo Laboratorio de Referencia'
        ];
        
        $this->view('catalogos/laboratorios-referencia/crear', $data);
    }
    
    /**
     * Guardar un nuevo laboratorio
     */
    public function guardar() {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('catalogos/laboratorios-referencia');
        }
        
        // Validaciones
        $errores = [];
        
        if (empty($_POST['codigo'])) {
            $errores[] = 'El código es requerido';
        } elseif ($this->laboratorioModel->existeCodigo($_POST['codigo'])) {
            $errores[] = 'El código ya existe';
        }
        
        if (empty($_POST['nombre'])) {
            $errores[] = 'El nombre es requerido';
        }
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            redirect('catalogos/laboratorios-referencia/crear');
        }
        
        // Preparar datos
        $datos = [
            'codigo' => trim($_POST['codigo']),
            'nombre' => trim($_POST['nombre']),
            'razon_social' => trim($_POST['razon_social'] ?? ''),
            'rfc' => trim($_POST['rfc'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'ciudad' => trim($_POST['ciudad'] ?? ''),
            'estado' => trim($_POST['estado'] ?? ''),
            'codigo_postal' => trim($_POST['codigo_postal'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'contacto_nombre' => trim($_POST['contacto_nombre'] ?? ''),
            'contacto_telefono' => trim($_POST['contacto_telefono'] ?? ''),
            'contacto_email' => trim($_POST['contacto_email'] ?? ''),
            'dias_entrega_promedio' => intval($_POST['dias_entrega_promedio'] ?? 3),
            'observaciones' => trim($_POST['observaciones'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        // Guardar
        if ($this->laboratorioModel->crear($datos)) {
            $_SESSION['flash_message'] = 'Laboratorio de referencia creado exitosamente';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al crear el laboratorio de referencia';
            $_SESSION['flash_type'] = 'error';
        }
        
        redirect('catalogos/laboratorios-referencia');
    }
    
    /**
     * Formulario para editar un laboratorio
     */
    public function editar($id) {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        $laboratorio = $this->laboratorioModel->obtenerPorId($id);
        
        if (!$laboratorio) {
            $_SESSION['flash_message'] = 'Laboratorio no encontrado';
            $_SESSION['flash_type'] = 'error';
            redirect('catalogos/laboratorios-referencia');
        }
        
        $data = [
            'title' => 'Editar Laboratorio de Referencia',
            'laboratorio' => $laboratorio
        ];
        
        $this->view('catalogos/laboratorios-referencia/editar', $data);
    }
    
    /**
     * Actualizar un laboratorio
     */
    public function actualizar($id) {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('catalogos/laboratorios-referencia');
        }
        
        // Validaciones
        $errores = [];
        
        if (empty($_POST['codigo'])) {
            $errores[] = 'El código es requerido';
        } elseif ($this->laboratorioModel->existeCodigo($_POST['codigo'], $id)) {
            $errores[] = 'El código ya existe';
        }
        
        if (empty($_POST['nombre'])) {
            $errores[] = 'El nombre es requerido';
        }
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            redirect("catalogos/laboratorios-referencia/editar/{$id}");
        }
        
        // Preparar datos
        $datos = [
            'codigo' => trim($_POST['codigo']),
            'nombre' => trim($_POST['nombre']),
            'razon_social' => trim($_POST['razon_social'] ?? ''),
            'rfc' => trim($_POST['rfc'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'ciudad' => trim($_POST['ciudad'] ?? ''),
            'estado' => trim($_POST['estado'] ?? ''),
            'codigo_postal' => trim($_POST['codigo_postal'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'contacto_nombre' => trim($_POST['contacto_nombre'] ?? ''),
            'contacto_telefono' => trim($_POST['contacto_telefono'] ?? ''),
            'contacto_email' => trim($_POST['contacto_email'] ?? ''),
            'dias_entrega_promedio' => intval($_POST['dias_entrega_promedio'] ?? 3),
            'observaciones' => trim($_POST['observaciones'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        // Actualizar
        if ($this->laboratorioModel->actualizar($id, $datos)) {
            $_SESSION['flash_message'] = 'Laboratorio de referencia actualizado exitosamente';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al actualizar el laboratorio de referencia';
            $_SESSION['flash_type'] = 'error';
        }
        
        redirect('catalogos/laboratorios-referencia');
    }
    
    /**
     * Eliminar un laboratorio
     */
    public function eliminar($id) {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('catalogos/laboratorios-referencia');
        }
        
        if ($this->laboratorioModel->eliminar($id)) {
            $_SESSION['flash_message'] = 'Laboratorio eliminado/desactivado exitosamente';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al eliminar el laboratorio';
            $_SESSION['flash_type'] = 'error';
        }
        
        redirect('catalogos/laboratorios-referencia');
    }
    
    /**
     * Ver detalles de un laboratorio
     */
    public function ver($id) {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        $laboratorio = $this->laboratorioModel->obtenerPorId($id);
        
        if (!$laboratorio) {
            $_SESSION['flash_message'] = 'Laboratorio no encontrado';
            $_SESSION['flash_type'] = 'error';
            redirect('catalogos/laboratorios-referencia');
        }
        
        $estudios = $this->laboratorioModel->obtenerEstudiosAsignados($id);
        $estadisticas = $this->laboratorioModel->obtenerEstadisticas($id);
        
        $data = [
            'title' => 'Detalle de Laboratorio de Referencia',
            'laboratorio' => $laboratorio,
            'estudios' => $estudios,
            'estadisticas' => $estadisticas
        ];
        
        $this->view('catalogos/laboratorios-referencia/ver', $data);
    }
    
    /**
     * API: Listar laboratorios para DataTables
     */
    public function listar() {
        if (!Auth::isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        
        $laboratorios = $this->laboratorioModel->obtenerTodos();
        
        header('Content-Type: application/json');
        echo json_encode(['data' => $laboratorios]);
    }
    
    /**
     * API: Cambiar estado activo/inactivo
     */
    public function cambiarEstado($id) {
        if (!Auth::isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 0;
        
        if ($this->laboratorioModel->cambiarEstado($id, $activo)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Estado actualizado correctamente'
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => 'Error al actualizar el estado'
            ]);
        }
    }
}
