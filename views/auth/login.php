<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar Sesión - Laboratorio Clínico</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --danger-color: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
        }
        
        .login-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            text-align: center;
            padding: 40px 30px 30px;
        }
        
        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .login-logo i {
            font-size: 40px;
            color: #667eea;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem 0.75rem;
            height: calc(3.5rem + 2px);
        }
        
        .form-floating .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
        }
        
        .form-floating label {
            padding: 1rem 0.75rem;
        }
        
        .form-check {
            margin-bottom: 20px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: #fff;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: #e9ecef;
        }
        
        .divider span {
            background: #fff;
            padding: 0 15px;
            position: relative;
            color: #6c757d;
            font-size: 14px;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }
        
        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 13px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-container {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-flask"></i>
                </div>
                <h1>Laboratorio Clínico</h1>
                <p>Sistema de Gestión Integral</p>
            </div>
            
            <div class="login-body">
                <?php if(isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?>" role="alert">
                        <i class="fas fa-<?= $_SESSION['flash_type'] === 'success' ? 'check-circle' : ($_SESSION['flash_type'] === 'danger' ? 'exclamation-circle' : 'info-circle') ?> me-2"></i>
                        <?= htmlspecialchars($_SESSION['flash_message']) ?>
                    </div>
                    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                <?php endif; ?>
                
                <form id="loginForm" action="<?= BASE_URL ?>/auth/login" method="POST" class="needs-validation" novalidate>
                    <div class="form-floating">
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Usuario"
                               required
                               autocomplete="username"
                               autofocus>
                        <label for="username">
                            <i class="fas fa-user me-2"></i>Usuario
                        </label>
                        <div class="invalid-feedback">
                            Por favor ingrese su usuario
                        </div>
                    </div>
                    
                    <div class="form-floating position-relative">
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Contraseña"
                               required
                               autocomplete="current-password">
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Contraseña
                        </label>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                        <div class="invalid-feedback">
                            Por favor ingrese su contraseña
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="remember" 
                               name="remember">
                        <label class="form-check-label" for="remember">
                            Recordar mi sesión
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-login" id="btnLogin">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </button>
                    
                    <div class="forgot-password">
                        <a href="<?= BASE_URL ?>/auth/recuperar-password">
                            <i class="fas fa-key me-1"></i>¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="login-footer">
                <p class="mb-0">
                    <i class="fas fa-shield-alt me-1"></i>
                    Acceso seguro y protegido
                </p>
                <p class="mb-0 mt-2">
                    &copy; <?= date('Y') ?> Laboratorio Clínico. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap 5 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Validación del formulario
        (function() {
            'use strict';
            const form = document.querySelector('.needs-validation');
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
        
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Prevenir doble submit
        $('#loginForm').on('submit', function() {
            const $btn = $('#btnLogin');
            $btn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin me-2"></i>Iniciando sesión...');
        });
        
        // Auto focus en campo de usuario
        $(document).ready(function() {
            $('#username').focus();
        });
        
        // Capturar Enter en los campos
        $('#username, #password').on('keypress', function(e) {
            if (e.which === 13) {
                $('#loginForm').submit();
            }
        });
    </script>
</body>
</html>
