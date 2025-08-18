<?php
ob_start();
?>

<div class="profile-container">
    <!-- Header minimalista -->
    <header class="profile-header">
        <div class="header-content">
            <div class="back-link">
                <a href="?route=clan_member" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Dashboard</span>
                </a>
            </div>
            <div class="header-title">
                <h1>Mi Perfil</h1>
                <p>Gestiona tu información personal y configuración</p>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="profile-main">
        <div class="profile-grid">
            <!-- Tarjeta de datos de cuenta -->
            <div class="profile-card">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>Datos de la cuenta</h3>
                </div>
                <form id="profileForm" class="profile-form">
                    <input type="hidden" name="route" value="clan_member/update-profile" />
                    
                    <div class="form-group">
                        <label for="full_name">Nombre completo</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo Utils::escape($user['full_name'] ?? ''); ?>" required />
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Nombre de usuario</label>
                        <input type="text" id="username" name="username" value="<?php echo Utils::escape($user['username'] ?? ''); ?>" disabled readonly />
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="<?php echo Utils::escape($user['email'] ?? ''); ?>" required />
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tarjeta de cambio de contraseña -->
            <div class="profile-card">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3>Cambiar contraseña</h3>
                </div>
                <form id="passwordForm" class="profile-form">
                    <input type="hidden" name="route" value="clan_member/update-password-plain" />
                    
                    <div class="form-group">
                        <label for="new_password">Nueva contraseña</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="new_password" name="new_password" required />
                            <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                <i class="fas fa-eye" id="eye-new"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar contraseña</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" required />
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye" id="eye-confirm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-secondary">
                            <i class="fas fa-key"></i>
                            Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tarjeta de foto de perfil -->
            <div class="profile-card">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3>Foto de perfil</h3>
                </div>
                
                <div class="avatar-section">
                    <div class="avatar-preview">
                        <?php if (!empty($user['avatar_path'])): ?>
                            <img id="avatarPreview" src="<?php echo Utils::asset($user['avatar_path']); ?>" alt="Avatar" />
                        <?php else: ?>
                            <div id="avatarPreview" class="avatar-placeholder">
                                <?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <form id="avatarForm" enctype="multipart/form-data" class="avatar-form">
                        <div class="file-input-wrapper">
                            <input type="file" id="avatar" name="avatar" accept="image/*" required />
                            <label for="avatar" class="file-label">
                                <i class="fas fa-upload"></i>
                                Seleccionar imagen
                            </label>
                        </div>
                        
                        <button type="submit" class="btn-secondary">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Subir foto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Guardar datos del perfil
document.getElementById('profileForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Estado de carga
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    submitBtn.disabled = true;
    
    const fd = new FormData(form);
    fetch('?route=clan_member/update-profile', { 
        method: 'POST', 
        body: fd, 
        credentials: 'same-origin' 
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            showNotification('Perfil actualizado correctamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(d.message || 'Error al actualizar el perfil', 'error');
        }
    })
    .catch(error => {
        showNotification('Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Cambiar contraseña
document.getElementById('passwordForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validar que las contraseñas coincidan
    const newPass = form.querySelector('#new_password').value;
    const confirmPass = form.querySelector('#confirm_password').value;
    
    if (newPass !== confirmPass) {
        showNotification('Las contraseñas no coinciden', 'error');
        return;
    }
    
    // Estado de carga
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    submitBtn.disabled = true;
    
    const fd = new FormData(form);
    fetch('?route=clan_member/update-password-plain', { 
        method: 'POST', 
        body: fd, 
        credentials: 'same-origin' 
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            showNotification('Contraseña actualizada correctamente', 'success');
            form.reset();
        } else {
            showNotification(d.message || 'Error al actualizar la contraseña', 'error');
        }
    })
    .catch(error => {
        showNotification('Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Subir avatar
document.getElementById('avatarForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Estado de carga
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
    submitBtn.disabled = true;
    
    const fd = new FormData(form);
    fetch('?route=clan_member/upload-avatar', { 
        method: 'POST', 
        body: fd, 
        credentials: 'same-origin' 
    })
    .then(r => r.json())
    .then(d => {
        if(d.success && d.avatar_url) {
            const preview = document.getElementById('avatarPreview');
            if (preview && preview.tagName === 'IMG') {
                preview.src = d.avatar_url;
            } else if (preview) {
                const img = new Image();
                img.src = d.avatar_url;
                img.style = "width:100%;height:100%;object-fit:cover;border-radius:50%";
                preview.replaceWith(img);
                img.id = 'avatarPreview';
            }
            showNotification('Foto de perfil actualizada correctamente', 'success');
            form.reset();
        } else {
            showNotification(d.message || 'Error al subir la foto', 'error');
        }
    })
    .catch(error => {
        showNotification('Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Mostrar notificación
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Ocultar después de 4 segundos
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Preview de imagen antes de subir
document.getElementById('avatar')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview && preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else if (preview) {
                const img = new Image();
                img.src = e.target.result;
                img.style = "width:100%;height:100%;object-fit:cover;border-radius:50%";
                preview.replaceWith(img);
                img.id = 'avatarPreview';
            }
        };
        reader.readAsDataURL(file);
    }
});

// Función para mostrar/ocultar contraseñas
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(`eye-${inputId.replace('new_', '').replace('confirm_', '')}`);

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<style>
/* Variables CSS del sistema */
:root {
    --primary-color: #1e3a8a;
    --primary-light: #3b82f6;
    --primary-dark: #1e40af;
    --text-primary: #1e3a8a;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    --bg-primary: #ffffff;
    --bg-secondary: #f8fafc;
    --bg-accent: #e5e7eb;
    --border-color: #e5e7eb;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --spacing-xs: 0.5rem;
    --spacing-sm: 0.75rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    --spacing-2xl: 3rem;
}

/* Reset y base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: var(--bg-secondary);
    color: var(--text-primary);
    line-height: 1.6;
}

/* Contenedor principal */
.profile-container {
    min-height: 100vh;
    background: var(--bg-secondary);
}

/* Header */
.profile-header {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    padding: var(--spacing-xl) 0;
    margin-bottom: var(--spacing-2xl);
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

.back-link {
    margin-bottom: var(--spacing-md);
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.back-btn:hover {
    color: var(--primary-color);
}

.header-title h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
}

.header-title p {
    font-size: 1.1rem;
    color: var(--text-secondary);
    font-weight: 400;
}

/* Contenido principal */
.profile-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: var(--spacing-xl);
}

/* Tarjetas */
.profile-card {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.profile-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-xl);
    padding-bottom: var(--spacing-md);
    border-bottom: 1px solid var(--border-color);
}

.header-icon {
    width: 48px;
    height: 48px;
    background: var(--primary-color);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.card-header h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

/* Formularios */
.profile-form {
    display: grid;
    gap: var(--spacing-lg);
}

.form-group {
    display: grid;
    gap: var(--spacing-sm);
}

.form-group label {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.form-group input {
    padding: var(--spacing-md);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: all 0.2s ease;
    background: var(--bg-primary);
    height: 48px; /* Altura consistente para todos los inputs */
    min-height: 48px; /* Asegura altura mínima */
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.form-group input:hover {
    border-color: var(--primary-light);
}

/* Wrapper para inputs de contraseña */
.password-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input-wrapper input {
    flex: 1;
    padding-right: 50px; /* Espacio para el botón del ojo */
}

.password-toggle {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 8px;
    border-radius: var(--radius-sm);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
}

.password-toggle:hover {
    background: var(--bg-accent);
    color: var(--text-primary);
}

.password-toggle:focus {
    outline: none;
    background: var(--bg-accent);
    color: var(--text-primary);
    box-shadow: 0 0 0 2px var(--primary-color);
}

/* Estilos para campos deshabilitados */
.form-group input:disabled,
.form-group input[readonly] {
    background-color: var(--bg-secondary);
    color: var(--text-muted);
    cursor: not-allowed;
    opacity: 0.7;
    border-color: var(--border-color);
}

.form-group input:disabled:hover,
.form-group input[readonly]:hover {
    border-color: var(--border-color);
    transform: none;
}

/* Botones */
.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: var(--spacing-md);
}

.btn-primary, .btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md) var(--spacing-lg);
    border: none;
    border-radius: var(--radius-md);
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    height: 48px; /* Altura consistente con los inputs */
    min-height: 48px; /* Asegura altura mínima */
    white-space: nowrap; /* Evita que el texto se rompa */
    max-width: 100%; /* Evita que se salga del contenedor */
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--bg-accent);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--border-color);
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

/* Sección de avatar */
.avatar-section {
    display: grid;
    gap: var(--spacing-lg);
}

.avatar-preview {
    display: flex;
    justify-content: center;
}

.avatar-preview img,
.avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--border-color);
    transition: all 0.3s ease;
}

.avatar-placeholder {
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 700;
}

.avatar-form {
    display: grid;
    gap: var(--spacing-md);
}

.file-input-wrapper {
    position: relative;
}

.file-input-wrapper input[type="file"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md);
    background: var(--bg-accent);
    border: 2px dashed var(--border-color);
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    height: 48px; /* Altura consistente con los inputs */
    min-height: 48px; /* Asegura altura mínima */
}

.file-label:hover {
    border-color: var(--primary-color);
    background: rgba(30, 58, 138, 0.05);
}

/* Ajustes específicos para el formulario de contraseña */
#passwordForm .form-group input {
    height: 48px;
    min-height: 48px;
}

/* Ajustes para botones en formularios específicos */
#passwordForm .form-actions .btn-secondary,
#avatarForm .btn-secondary {
    height: 48px;
    min-height: 48px;
    max-width: 100%;
    box-sizing: border-box;
}

/* Notificaciones */
.notification {
    position: fixed;
    top: var(--spacing-lg);
    right: var(--spacing-lg);
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: var(--spacing-md) var(--spacing-lg);
    box-shadow: var(--shadow-lg);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.notification.show {
    transform: translateX(0);
}

.notification-success {
    border-left: 4px solid #10b981;
}

.notification-error {
    border-left: 4px solid #ef4444;
}

.notification-info {
    border-left: 4px solid var(--primary-color);
}

/* Responsive */
@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
    }
    
    .profile-card {
        padding: var(--spacing-lg);
    }
    
    .header-title h1 {
        font-size: 2rem;
    }
    
    .form-actions {
        justify-content: stretch;
    }
    
    .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .header-content,
    .profile-main {
        padding: 0 var(--spacing-md);
    }
    
    .profile-header {
        padding: var(--spacing-lg) 0;
    }
    
    .card-header {
        flex-direction: column;
        text-align: center;
        gap: var(--spacing-sm);
    }
}
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>


