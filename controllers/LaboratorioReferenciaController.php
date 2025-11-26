<?php
/**
 * Controlador: LaboratorioReferenciaController
 * 
 * Gestiona el CRUD del catálogo de Laboratorios de Referencia.
 */

class LaboratorioReferenciaController extends Controller
{
    private $laboratorioModel;

    public function __construct()
    {
        parent::__construct();
        $this->laboratorioModel = new LaboratorioReferencia();
    }

    private function verificarAuth()
    {
        if (!Auth::check()) {
            redirect('/login');
            exit;
        }
    }

    public function index()
    {
        $this->verificarAuth();
        
        $data = [
            'title' => 'Laboratorios de Referencia',
            'seccion' => 'Catálogos',
            'subseccion' => 'Laboratorios de Referencia'
        ];
        
        $this->view('catalogos/laboratorios-referencia/index', $data);
    }

    public function crear()
    {
        $this->verificarAuth();
        
        $data = [
            'title' => 'Nuevo Laboratorio de Referencia',
            'seccion' => 'Catálogos',
            'subseccion' => 'Laboratorios de Referencia',
            'errores' => $_SESSION['errores'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ];
        
        unset($_SESSION['errores'], $_SESSION['old']);
        
        $this->view('catalogos/laboratorios-referencia/crear', $data);
    }

    public function guardar()
    {
        $this->verificarAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/catalogos/laboratorios-referencia');
            exit;
        }
        
        $datos = [
            'codigo' => strtoupper(trim($_POST['codigo'] ?? '')),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'razon_social' => trim($_POST['razon_social'] ?? ''),
            'rfc' => strtoupper(trim($_POST['rfc'] ?? '')),
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
        
        $errores = $this->validarDatos($datos);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $datos;
            redirect('/catalogos/laboratorios-referencia/crear');
            exit;
        }
        
        if ($this->laboratorioModel->crear($datos)) {
            $_SESSION['flash_message'] = 'Laboratorio de referencia creado exitosamente';
            $_SESSION['flash_type'] = 'success';
            redirect('/catalogos/laboratorios-referencia');
        } else {
            $_SESSION['flash_message'] = 'Error al crear el laboratorio';
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['old'] = $datos;
            redirect('/catalogos/laboratorios-referencia/crear');
        }
        
        exit;
    }

    public function editar($id)
    {
        $this->verificarAuth();
        
        $laboratorio = $this->laboratorioModel->obtenerPorId($id);
        
        if (!$laboratorio) {
            $_SESSION['flash_message'] = 'Laboratorio no encontrado';
            $_SESSION['flash_type'] = 'danger';
            redirect('/catalogos/laboratorios-referencia');
            exit;
        }
        
        $data = [
            'title' => 'Editar Laboratorio de Referencia',
            'seccion' => 'Catálogos',
            'subseccion' => 'Laboratorios de Referencia',
            'laboratorio' => $laboratorio,
            'errores' => $_SESSION['errores'] ?? [],
            'old' => $_SESSION['old'] ?? []
        ];
        
        unset($_SESSION['errores'], $_SESSION['old']);
        
        $this->view('catalogos/laboratorios-referencia/editar', $data);
    }

    public function actualizar($id)
    {
        $this->verificarAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/catalogos/laboratorios-referencia');
            exit;
        }
        
        $laboratorio = $this->laboratorioModel->obtenerPorId($id);
        
        if (!$laboratorio) {
            $_SESSION['flash_message'] = 'Laboratorio no encontrado';
            $_SESSION['flash_type'] = 'danger';
            redirect('/catalogos/laboratorios-referencia');
            exit;
        }
        
        $datos = [
            'codigo' => strtoupper(trim($_POST['codigo'] ?? '')),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'razon_social' => trim($_POST['razon_social'] ?? ''),
            'rfc' => strtoupper(trim($_POST['rfc'] ?? '')),
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
        
        $errores = $this->validarDatos($datos, $id);
        
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['old'] = $datos;
            redirect('/catalogos/laboratorios-referencia/editar/' . $id);
            exit;
        }
        
        if ($this->laboratorioModel->actualizar($id, $datos)) {
            $_SESSION['flash_message'] = 'Laboratorio actualizado exitosamente';
            $_SESSION['flash_type'] = 'success';
            redirect('/catalogos/laboratorios-referencia');
        } else {
            $_SESSION['flash_message'] = 'Error al actualizar el laboratorio';
            $_SESSION['flash_type'] = 'danger';
            $_SESSION['old'] = $datos;
            redirect('/catalogos/laboratorios-referencia/editar/' . $id);
        }
        
        exit;
    }

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
        
        $laboratorio = $this->laboratorioModel->obtenerPorId($id);
        
        if (!$laboratorio) {
            echo json_encode([
                'success' => false,
                'message' => 'Laboratorio no encontrado'
            ]);
            exit;
        }
        
        if ($this->laboratorioModel->eliminar($id)) {
            echo json_encode([
                'success' => true,
                'message' => 'Laboratorio eliminado exitosamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el laboratorio'
            ]);
        }
        
        exit;
    }

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
        
        $columns = ['id', 'codigo', 'nombre', 'ciudad', 'dias_entrega_promedio', 'activo'];
        $orderBy = $columns[$orderColumnIndex] ?? 'id';
        
        $laboratorios = $this->laboratorioModel->listar($start, $length, $search, $orderBy, $orderDir);
        $totalRegistros = $this->laboratorioModel->contarTotal();
        $totalFiltrados = $this->laboratorioModel->contarFiltrados($search);
        
        $data = [];
        foreach ($laboratorios as $lab) {
            $data[] = [
                'id' => $lab['id'],
                'codigo' => '<code>' . htmlspecialchars($lab['codigo']) . '</code>',
                'nombre' => htmlspecialchars($lab['nombre']),
                'ciudad' => htmlspecialchars($lab['ciudad'] ?? 'N/A'),
                'telefono' => htmlspecialchars($lab['telefono'] ?? 'N/A'),
                'dias_entrega' => ($lab['dias_entrega_promedio'] ?? 3) . ' días',
                'activo' => $lab['activo'] ? 
                    '<span class="badge bg-success">Activo</span>' : 
                    '<span class="badge bg-secondary">Inactivo</span>',
                'acciones' => $this->generarBotonesAccion($lab)
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
     * Vista de detalle de un laboratorio (solo lectura)
     */
    public function ver($id)
    {
        $this->verificarAuth();
        
        $laboratorio = $this->laboratorioModel->obtenerPorId($id);
        
        if (!$laboratorio) {
            $_SESSION['flash_message'] = 'Laboratorio no encontrado';
            $_SESSION['flash_type'] = 'danger';
            redirect('/catalogos/laboratorios-referencia');
            exit;
        }
        
        // Obtener estadísticas del laboratorio
        $estadisticas = $this->laboratorioModel->obtenerEstadisticas($id);
        
        $data = [
            'title' => 'Ver Laboratorio de Referencia',
            'seccion' => 'Catálogos',
            'subseccion' => 'Laboratorios de Referencia',
            'laboratorio' => $laboratorio,
            'estadisticas' => $estadisticas
        ];
        
        $this->view('catalogos/laboratorios-referencia/ver', $data);
    }

    /**
     * Cambia el estado (activo/inactivo) de un laboratorio (AJAX - POST)
     */
    public function cambiarEstado($id)
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
        
        $laboratorio = $this->laboratorioModel->obtenerPorId($id);
        
        if (!$laboratorio) {
            echo json_encode([
                'success' => false,
                'message' => 'Laboratorio no encontrado'
            ]);
            exit;
        }
        
        // Cambiar el estado
        $nuevoEstado = $laboratorio['activo'] ? 0 : 1;
        
        $query = "UPDATE laboratorios_referencia SET activo = :activo WHERE id = :id";
        $stmt = $this->laboratorioModel->db->prepare($query);
        $stmt->bindParam(':activo', $nuevoEstado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $mensaje = $nuevoEstado ? 'Laboratorio activado exitosamente' : 'Laboratorio desactivado exitosamente';
            echo json_encode([
                'success' => true,
                'message' => $mensaje,
                'nuevo_estado' => $nuevoEstado
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al cambiar el estado del laboratorio'
            ]);
        }
        
        exit;
    }

    private function validarDatos($datos, $id = null)
    {
        $errores = [];
        
        // Código (obligatorio)
        if (empty($datos['codigo'])) {
            $errores['codigo'] = 'El código es obligatorio';
        } elseif (strlen($datos['codigo']) > 20) {
            $errores['codigo'] = 'El código no puede tener más de 20 caracteres';
        } elseif ($this->laboratorioModel->existeCodigo($datos['codigo'], $id)) {
            $errores['codigo'] = 'El código ya existe';
        }
        
        // Nombre (obligatorio)
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio';
        } elseif (strlen($datos['nombre']) > 150) {
            $errores['nombre'] = 'El nombre no puede tener más de 150 caracteres';
        }
        
        // Razón social (opcional)
        if (!empty($datos['razon_social']) && strlen($datos['razon_social']) > 200) {
            $errores['razon_social'] = 'La razón social no puede tener más de 200 caracteres';
        }
        
        // RFC (opcional, pero validar formato si se proporciona)
        if (!empty($datos['rfc'])) {
            if (strlen($datos['rfc']) > 15) {
                $errores['rfc'] = 'El RFC no puede tener más de 15 caracteres';
            }
        }
        
        // Email (opcional, pero validar formato si se proporciona)
        if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El email no es válido';
        }
        
        // Email de contacto (opcional, pero validar formato)
        if (!empty($datos['contacto_email']) && !filter_var($datos['contacto_email'], FILTER_VALIDATE_EMAIL)) {
            $errores['contacto_email'] = 'El email de contacto no es válido';
        }
        
        // Días de entrega (debe ser positivo)
        if (isset($datos['dias_entrega_promedio']) && $datos['dias_entrega_promedio'] < 0) {
            $errores['dias_entrega_promedio'] = 'Los días de entrega deben ser un número positivo';
        }
        
        return $errores;
    }

    private function generarBotonesAccion($laboratorio)
    {
        $id = $laboratorio['id'];
        $nombre = htmlspecialchars($laboratorio['nombre']);
        
        return '
            <div class="btn-group btn-group-sm" role="group">
                <a href="' . url('/catalogos/laboratorios-referencia/editar/' . $id) . '" 
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