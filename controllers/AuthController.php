<?php
/**
 * Controlador de Autenticación
 * Maneja login, logout y recuperación de contraseñas
 */

class AuthController extends Controller {
    
    /**
     * Mostrar formulario de login
     */
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            redirect('dashboard');
        }
        
        // Si es POST, procesar el login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarLogin();
            return;
        }
        
        // Mostrar vista de login
        $data = [
            'title' => 'Iniciar Sesión',
            'error' => getFlash('error'),
            'success' => getFlash('success')
        ];
        
        // Renderizar login sin layout
        require ROOT_PATH . '/views/auth/login.php';
    }
    
    /**
     * Procesar el login
     */
    private function procesarLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validar campos vacíos
        if (empty($username) || empty($password)) {
            setFlash('error', 'Por favor complete todos los campos');
            redirect('login');
        }
        
        // Intentar autenticar
        if (Auth::attempt($username, $password, $remember)) {
            // Login exitoso
            logMessage("Login exitoso: {$username}");
            redirect('dashboard');
        } else {
            // Login fallido
            logMessage("Login fallido: {$username}", 'warning');
            setFlash('error', 'Usuario o contraseña incorrectos');
            redirect('login');
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        $username = session('usuario')['username'] ?? 'unknown';
        Auth::logout();
        logMessage("Logout: {$username}");
        
        setFlash('success', 'Sesión cerrada correctamente');
        redirect('login');
    }
    
    /**
     * Mostrar formulario de recuperación de contraseña
     */
    public function recuperarPassword() {
        if (Auth::check()) {
            redirect('dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarRecuperacion();
            return;
        }
        
        $data = [
            'title' => 'Recuperar Contraseña',
            'error' => getFlash('error'),
            'success' => getFlash('success')
        ];
        
        require ROOT_PATH . '/views/auth/recuperar-password.php';
    }
    
    /**
     * Procesar recuperación de contraseña
     */
    private function procesarRecuperacion() {
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            setFlash('error', 'Por favor ingrese su correo electrónico');
            redirect('recuperar-password');
        }
        
        if (!validarEmail($email)) {
            setFlash('error', 'Por favor ingrese un correo electrónico válido');
            redirect('recuperar-password');
        }
        
        // TODO: Implementar envío de correo de recuperación
        // Por ahora solo mostramos mensaje de éxito
        
        setFlash('success', 'Se ha enviado un correo con instrucciones para recuperar su contraseña');
        redirect('login');
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarPassword() {
        // Verificar que esté autenticado
        if (!Auth::check()) {
            redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarCambioPassword();
            return;
        }
        
        $data = [
            'title' => 'Cambiar Contraseña',
            'usuario' => currentUser(),
            'error' => getFlash('error'),
            'success' => getFlash('success')
        ];
        
        $this->view('auth/cambiar-password', $data);
    }
    
    /**
     * Procesar cambio de contraseña
     */
    private function procesarCambioPassword() {
        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNuevo = $_POST['password_nuevo'] ?? '';
        $passwordConfirmar = $_POST['password_confirmar'] ?? '';
        
        // Validaciones
        if (empty($passwordActual) || empty($passwordNuevo) || empty($passwordConfirmar)) {
            setFlash('error', 'Por favor complete todos los campos');
            redirect('cambiar-password');
        }
        
        if ($passwordNuevo !== $passwordConfirmar) {
            setFlash('error', 'Las contraseñas no coinciden');
            redirect('cambiar-password');
        }
        
        if (strlen($passwordNuevo) < 6) {
            setFlash('error', 'La contraseña debe tener al menos 6 caracteres');
            redirect('cambiar-password');
        }
        
        // Verificar contraseña actual
        $usuario = currentUser();
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT password_hash FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario['id']]);
        $usuarioDb = $stmt->fetch();
        
        if (!password_verify($passwordActual, $usuarioDb['password_hash'])) {
            setFlash('error', 'La contraseña actual es incorrecta');
            redirect('cambiar-password');
        }
        
        // Actualizar contraseña
        $nuevoHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        
        if ($stmt->execute([$nuevoHash, $usuario['id']])) {
            logMessage("Cambio de contraseña exitoso: {$usuario['username']}");
            setFlash('success', 'Contraseña actualizada correctamente');
            redirect('dashboard');
        } else {
            setFlash('error', 'Error al actualizar la contraseña');
            redirect('cambiar-password');
        }
    }
}
