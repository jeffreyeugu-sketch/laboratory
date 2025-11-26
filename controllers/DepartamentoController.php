<?php
/**
 * Controlador: DepartamentoController
 * 
 * Gestiona el CRUD del catálogo de Departamentos.
 * 
 * Rutas:
 * - GET  /catalogos/departamentos          -> index()
 * - GET  /catalogos/departamentos/crear    -> crear()
 * - POST /catalogos/departamentos/guardar  -> guardar()
 * - GET  /catalogos/departamentos/editar/:id  -> editar($id)
 * - POST /catalogos/departamentos/actualizar/:id -> actualizar($id)
 * - POST /catalogos/departamentos/eliminar/:id -> eliminar($id)
 * - POST /catalogos/departamentos/listar   -> listar() [AJAX DataTables]
 */

class DepartamentoController extends Controller
{
    private $departamentoModel;
    private $areaModel;

    public function __construct()
    {
        parent::__construct();
        $this->departamentoModel = new Departamento();
        $this->areaModel = new Area();
    }

    /**
     * Verifica autenticación
     */
    private function verificarAuth()
    {
        if (!Auth::check()) {
            redirect('/login');
            exit;
        }
    }

    /**
     * Vista principal con listado de departamentos (DataTables)
     */
    public function index()
    {
        $this->verificarAuth();
        
        $data = [
            'title' => 'Departamentos',
            'seccion' => 'Catálogos',
            'subseccion' => 'Departamentos'
        ];
        
        $this->view('catalogos/departamentos/index', $data);
    }

    /**
     * Formulario para crear nuevo departamento
     */
    public function crear()
    {
        $this->verificarAuth();
        
        // Obtener áreas activas para el select
        $areas = $this->areaModel->obtenerActivas();
        
        $data = [
            'title' => 'Nuevo Departamento',
            'seccion' => 'Catálogos',
            'subseccion' => 'Departamentos',
            'areas' => $areas,
            'errores' => $_SESSION['errores'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ];
        
        unset($_SESSION['errores'], $_SESSION['old']);
        
        $this->view('catalogos/departamentos/crear', $data);
    }

    /**
     * Procesa la creación de un nuevo departamento (POST)
     */
    public function guardar()
    {
        $this->verificarAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/catalogos/departamentos');
            exit;
        }
        
        $datos = [
            'area_id' => intval($_POST['area_id'] ?? 0),
            'codigo' => strtoupper(trim($_POST['codigo'] ?? '')),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'orden' => intval($_POST['orden'] ?? $this->departamentoModel->obtenerSiguienteOrden()),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        $errores = $this->validarDatos($datos);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $datos;
            redirect('/catalogos/departamentos/crear');
            exit;
        }
        
        if ($this->departamentoModel->crear($datos)) {
            $_SESSION['flash_message'] = 'Departamento creado exitosamente';
            $_SESSION['flash_type'] = 'success';
            redirect('/catalogos/departamentos');
        } else {
            $_SESSION['flash_message'] = 'Error al crear el departamento';
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['old'] = $datos;
            redirect('/catalogos/departamentos/crear');
        }
        
        exit;
    }

    /**
     * Formulario para editar departamento existente
     */
    public function editar($id)
    {
        $this->verificarAuth();
        
        $departamento = $this->departamentoModel->obtenerPorId($id);
        
        if (!$departamento) {
            $_SESSION['flash_message'] = 'Departamento no encontrado';
            $_SESSION['flash_type'] = 'danger';
            redirect('/catalogos/departamentos');
            exit;
        }
        
        $areas = $this->areaModel->obtenerActivas();
        
        $data = [
            'title' => 'Editar Departamento',
            'seccion' => 'Catálogos',
            'subseccion' => 'Departamentos',
            'departamento' => $departamento,
            'areas' => $areas,
            'errores' => $_SESSION['errores'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ];
        
        unset($_SESSION['errores'], $_SESSION['old']);
        
        $this->view('catalogos/departamentos/editar', $data);
    }

    /**
     * Procesa la actualización de un departamento (POST)
     */
    public function actualizar($id)
    {
        $this->verificarAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/catalogos/departamentos');
            exit;
        }
        
        $departamento = $this->departamentoModel->obtenerPorId($id);
        
        if (!$departamento) {
            $_SESSION['flash_message'] = 'Departamento no encontrado';
            $_SESSION['flash_type'] = 'danger';
            redirect('/catalogos/departamentos');
            exit;
        }
        
        $datos = [
            'area_id' => intval($_POST['area_id'] ?? 0),
            'codigo' => strtoupper(trim($_POST['codigo'] ?? '')),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'orden' => intval($_POST['orden'] ?? $departamento['orden'] ?? 0),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];
        
        $errores = $this->validarDatos($datos, $id);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $datos;
            redirect('/catalogos/departamentos/editar/' . $id);
            exit;
        }
        
        if ($this->departamentoModel->actualizar($id, $datos)) {
            $_SESSION['flash_message'] = 'Departamento actualizado exitosamente';
            $_SESSION['flash_type'] = 'success';
            redirect('/catalogos/departamentos');
        } else {
            $_SESSION['flash_message'] = 'Error al actualizar el departamento';
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['old'] = $datos;
            redirect('/catalogos/departamentos/editar/' . $id);
        }
        
        exit;
    }

    /**
     * Elimina o desactiva un departamento (AJAX - POST)
     */
    public function eliminar($id)
    {
        $this->verificarAuth();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            exit;
        }
        
        $departamento = $this->departamentoModel->obtenerPorId($id);
        
        if (!$departamento) {
            echo json_encode([
                'success' => false,
                'message' => 'Departamento no encontrado'
            ]);
            exit;
        }
        
        // Por ahora eliminar directamente (puedes agregar verificación de relaciones si es necesario)
        if ($this->departamentoModel->eliminar($id)) {
            echo json_encode([
                'success' => true,
                'message' => 'Departamento eliminado exitosamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el departamento'
            ]);
        }
        
        exit;
    }

    /**
     * Listado AJAX para DataTables (server-side processing)
     */
    public function listar()
    {
        $this->verificarAuth();
        
        header('Content-Type: application/json');
        
        $draw = intval($_POST['draw'] ?? 1);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search = $_POST['search']['value'] ?? '';
        $orderColumnIndex = intval($_POST['order'][0]['column'] ?? 0);
        $orderDir = $_POST['order'][0]['dir'] ?? 'DESC';
        
        // Mapeo de columnas
        $columns = ['id', 'area_nombre', 'codigo', 'nombre', 'orden', 'activo'];
        $orderBy = $columns[$orderColumnIndex] ?? 'id';
        
        $departamentos = $this->departamentoModel->listar($start, $length, $search, $orderBy, $orderDir);
        $totalRegistros = $this->departamentoModel->contarTotal();
        $totalFiltrados = $this->departamentoModel->contarFiltrados($search);
        
        $data = [];
        foreach ($departamentos as $depto) {
            $data[] = [
                'id' => $depto['id'],
                'area' => htmlspecialchars($depto['area_nombre'] ?? 'Sin área'),
                'codigo' => '<code>' . htmlspecialchars($depto['codigo']) . '</code>',
                'nombre' => htmlspecialchars($depto['nombre']),
                'orden' => $depto['orden'] ?? 0,
                'activo' => $depto['activo'] ? 
                    '<span class="badge bg-success">Activo</span>' : 
                    '<span class="badge bg-secondary">Inactivo</span>',
                'acciones' => $this->generarBotonesAccion($depto)
            ];
        }
        
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $totalRegistros,
            'recordsFiltered' => $totalFiltrados,
            'data' => $data
        ]);
        
        exit;
    }

    /**
     * Valida los datos del formulario
     */
    private function validarDatos($datos, $id = null)
    {
        $errores = [];
        
        // Validar área (obligatorio)
        if (empty($datos['area_id']) || $datos['area_id'] <= 0) {
            $errores['area_id'] = 'El área es obligatoria';
        }
        
        // Validar código (obligatorio)
        if (empty($datos['codigo'])) {
            $errores['codigo'] = 'El código es obligatorio';
        } elseif (strlen($datos['codigo']) > 20) {
            $errores['codigo'] = 'El código no puede tener más de 20 caracteres';
        } elseif ($this->departamentoModel->existeCodigo($datos['codigo'], $id)) {
            $errores['codigo'] = 'El código ya existe';
        }
        
        // Validar nombre (obligatorio)
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'El nombre no puede tener más de 100 caracteres';
        }
        
        // Validar descripción (opcional)
        if (!empty($datos['descripcion']) && strlen($datos['descripcion']) > 500) {
            $errores['descripcion'] = 'La descripción no puede tener más de 500 caracteres';
        }
        
        // Validar orden
        if (isset($datos['orden']) && $datos['orden'] < 0) {
            $errores['orden'] = 'El orden debe ser un número positivo';
        }
        
        return $errores;
    }

    /**
     * Genera los botones de acción para cada registro
     */
    private function generarBotonesAccion($departamento)
    {
        $id = $departamento['id'];
        $nombre = htmlspecialchars($departamento['nombre']);
        
        return '
            <div class="btn-group btn-group-sm" role="group">
                <a href="' . url('/catalogos/departamentos/editar/' . $id) . '" 
                   class="btn btn-outline-primary" 
                   title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" 
                        class="btn btn-outline-danger btn-eliminar" 
                        data-id="' . $id . '" 
                        data-nombre="' . $nombre . '"
                        title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }
}