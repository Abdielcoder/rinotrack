<?php
// Capturar el contenido de la vista
ob_start();
?>

<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <i class="fas fa-star logo"></i>
            <h1><?php echo APP_NAME; ?></h1>
            <p>Inicia sesión en tu cuenta</p>
        </div>
        
        <form id="loginForm" class="login-form">
            <div class="form-group">
                <div class="input-container">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="username" name="username" placeholder="Usuario o Email" required>
                </div>
                <span class="error-message" id="usernameError"></span>
            </div>
            
            <div class="form-group">
                <div class="input-container">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" placeholder="Contraseña" required>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
                <span class="error-message" id="passwordError"></span>
            </div>
            
            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" id="rememberMe" name="rememberMe">
                    <span class="checkmark"></span>
                    Recordarme
                </label>
                <small class="remember-info">
                    <i class="fas fa-info-circle"></i>
                    Mantendrá tu sesión activa por 30 días
                </small>
            </div>
            
            <button type="submit" class="login-btn" id="loginBtn">
                <span class="btn-text">Iniciar Sesión</span>
                <span class="btn-loader" id="btnLoader">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
            
            <div class="login-message" id="loginMessage"></div>
        </form>
        

    </div>
</div>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// Configurar variables para el layout
$title = APP_NAME . ' - Iniciar Sesión';

// Incluir el layout principal
include __DIR__ . '/layout.php';
?>