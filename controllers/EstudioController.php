<?php
/**
 * Controlador: EstudioController
 * Gestión del catálogo de estudios
 */

class EstudioController extends Controller {
    private $estudioModel;
    private $areaModel;
    private $tipoMuestraModel;
    private $metodologiaModel;
    private $departamentoModel;
    private $laboratorioReferenciaModel;
    
    public function __construct() {
        parent::__construct();
        $this->estudioModel = new Estudio();
        $this->areaModel = new Area();
        $this->tipoMuestraModel = new TipoMuestra();
        $this->metodologiaModel = new Metodologia();
        $this->departamentoModel = new Departamento();
        $this->laboratorioReferenciaModel = new LaboratorioReferencia();
    }
    
    /**
     * Listar estudios
     */
    public function index() {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        $estudios = $this->estudioModel->obtenerTodos();
        
        $data = [
            'title' => 'Catálogo de Estudios',
            'estudios' => $estudios
        ];
        
        $this->view('catalogos/estudios/index', $data);
    }
    
    /**
     * Formulario para crear estudio
     */
    public function crear() {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        $data = [
            'title' => 'Nuevo Estudio',
            'areas' => $this->areaModel->obtenerActivas(),
            'tipos_muestra' => $this->tipoMuestraModel->obtenerActivas(),
            'metodologias' => $this->metodologiaModel->obtenerActivas(),
            'departamentos' => $this->departamentoModel->obtenerActivos(),
            'laboratorios' => $this->laboratorioReferenciaModel->obtenerActivos()
        ];
        
        $this->view('catalogos/estudios/crear', $data);
    }
    
    /**
     * Guardar nuevo estudio
     */
    public function guardar() {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('catalogos/estudios');
        }
        
        // Validaciones
        $errores = [];
        
        if (empty($_POST['codigo_interno'])) {
            $errores[] = 'El código interno es requerido';
        } elseif ($this->estudioModel->existeCodigoInterno($_POST['codigo_interno'])) {
            $errores[] = 'El código interno ya existe';
        }
        
        if (empty($_POST['nombre'])) {
            $errores[] = 'El nombre del estudio es requerido';
        }
        
        if (empty($_POST['area_id'])) {
            $errores[] = 'El área es requerida';
        }
        
        if (empty($_POST['tipo_muestra_id'])) {
            $errores[] = 'El tipo de muestra es requerido';
        }
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            redirect('catalogos/estudios/crear');
        }
        
        // Preparar datos
        $datos = [
            'codigo_interno' => trim($_POST['codigo_interno']),
            'codigo_loinc' => trim($_POST['codigo_loinc'] ?? ''),
            'nombre' => trim($_POST['nombre']),
            'nombre_corto' => trim($_POST['nombre_corto'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'area_id' => intval($_POST['area_id']),
            'tipo_muestra_id' => intval($_POST['tipo_muestra_id']),
            'volumen_requerido' => trim($_POST['volumen_requerido'] ?? ''),
            'metodologia_id' => !empty($_POST['metodologia_id']) ? intval($_POST['metodologia_id']) : null,
            'dias_proceso' => intval($_POST['dias_proceso'] ?? 1),
            'indicaciones_paciente' => trim($_POST['indicaciones_paciente'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0,
            'es_subrogado' => isset($_POST['es_subrogado']) ? 1 : 0,
            'laboratorio_referencia_id' => !empty($_POST['laboratorio_referencia_id']) ? intval($_POST['laboratorio_referencia_id']) : null,
            'departamento_id' => !empty($_POST['departamento_id']) ? intval($_POST['departamento_id']) : null
        ];
        
        // Guardar
        if ($this->estudioModel->crear($datos)) {
            $_SESSION['flash_message'] = 'Estudio creado exitosamente';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al crear el estudio';
            $_SESSION['flash_type'] = 'error';
        }
        
        redirect('catalogos/estudios');
    }
    
    /**
     * Formulario para editar estudio
     */
    public function editar($id) {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        $estudio = $this->estudioModel->obtenerPorId($id);
        
        if (!$estudio) {
            $_SESSION['flash_message'] = 'Estudio no encontrado';
            $_SESSION['flash_type'] = 'error';
            redirect('catalogos/estudios');
        }
        
        $data = [
            'title' => 'Editar Estudio',
            'estudio' => $estudio,
            'areas' => $this->areaModel->obtenerActivas(),
            'tipos_muestra' => $this->tipoMuestraModel->obtenerActivas(),
            'metodologias' => $this->metodologiaModel->obtenerActivas(),
            'departamentos' => $this->departamentoModel->obtenerActivos(),
            'laboratorios' => $this->laboratorioReferenciaModel->obtenerActivos()
        ];
        
        $this->view('catalogos/estudios/editar', $data);
    }
    
    /**
     * Actualizar estudio
     */
    public function actualizar($id) {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('catalogos/estudios');
        }
        
        // Validaciones
        $errores = [];
        
        if (empty($_POST['codigo_interno'])) {
            $errores[] = 'El código interno es requerido';
        } elseif ($this->estudioModel->existeCodigoInterno($_POST['codigo_interno'], $id)) {
            $errores[] = 'El código interno ya existe';
        }
        
        if (empty($_POST['nombre'])) {
            $errores[] = 'El nombre del estudio es requerido';
        }
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $_POST;
            redirect("catalogos/estudios/editar/{$id}");
        }
        
        // Preparar datos
        $datos = [
            'codigo_interno' => trim($_POST['codigo_interno']),
            'codigo_loinc' => trim($_POST['codigo_loinc'] ?? ''),
            'nombre' => trim($_POST['nombre']),
            'nombre_corto' => trim($_POST['nombre_corto'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'area_id' => intval($_POST['area_id']),
            'tipo_muestra_id' => intval($_POST['tipo_muestra_id']),
            'volumen_requerido' => trim($_POST['volumen_requerido'] ?? ''),
            'metodologia_id' => !empty($_POST['metodologia_id']) ? intval($_POST['metodologia_id']) : null,
            'dias_proceso' => intval($_POST['dias_proceso'] ?? 1),
            'indicaciones_paciente' => trim($_POST['indicaciones_paciente'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0,
            'es_subrogado' => isset($_POST['es_subrogado']) ? 1 : 0,
            'laboratorio_referencia_id' => !empty($_POST['laboratorio_referencia_id']) ? intval($_POST['laboratorio_referencia_id']) : null,
            'departamento_id' => !empty($_POST['departamento_id']) ? intval($_POST['departamento_id']) : null
        ];
        
        // Actualizar
        if ($this->estudioModel->actualizar($id, $datos)) {
            $_SESSION['flash_message'] = 'Estudio actualizado exitosamente';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al actualizar el estudio';
            $_SESSION['flash_type'] = 'error';
        }
        
        redirect('catalogos/estudios');
    }
    
    /**
     * Eliminar estudio
     */
    public function eliminar($id) {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('catalogos/estudios');
        }
        
        if ($this->estudioModel->eliminar($id)) {
            $_SESSION['flash_message'] = 'Estudio desactivado exitosamente';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al desactivar el estudio';
            $_SESSION['flash_type'] = 'error';
        }
        
        redirect('catalogos/estudios');
    }
    
    /**
     * Ver detalles de estudio
     */
    public function ver($id) {
        if (!Auth::isAuthenticated()) {
            redirect('login');
        }
        
        $estudio = $this->estudioModel->obtenerPorId($id);
        
        if (!$estudio) {
            $_SESSION['flash_message'] = 'Estudio no encontrado';
            $_SESSION['flash_type'] = 'error';
            redirect('catalogos/estudios');
        }
        
        $data = [
            'title' => 'Detalle del Estudio',
            'estudio' => $estudio
        ];
        
        $this->view('catalogos/estudios/ver', $data);
    }
    
    /**
     * API: Listar para DataTables
     */
    public function listar() {
        if (!Auth::isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
        
        $estudios = $this->estudioModel->obtenerTodos();
        
        header('Content-Type: application/json');
        echo json_encode(['data' => $estudios]);
    }
}
