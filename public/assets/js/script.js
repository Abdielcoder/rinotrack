// Función global para mostrar notificaciones toast
window.showToast = function(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--bg-primary);
        border: 1px solid var(--bg-accent);
        border-radius: var(--radius-md);
        padding: var(--spacing-md) var(--spacing-lg);
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
        color: var(--text-primary);
    `;
    
    if (type === 'success') {
        toast.style.borderLeftColor = 'var(--success)';
        toast.style.borderLeftWidth = '4px';
    } else if (type === 'error') {
        toast.style.borderLeftColor = 'var(--error)';
        toast.style.borderLeftWidth = '4px';
    } else if (type === 'warning') {
        toast.style.borderLeftColor = 'var(--warning)';
        toast.style.borderLeftWidth = '4px';
    } else if (type === 'info') {
        toast.style.borderLeftColor = 'var(--info)';
        toast.style.borderLeftWidth = '4px';
    }
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    // Elementos del DOM
    const loginForm = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const loginBtn = document.getElementById('loginBtn');
    const loginMessage = document.getElementById('loginMessage');
    const usernameError = document.getElementById('usernameError');
    const passwordError = document.getElementById('passwordError');
    
    // Solo ejecutar código de login si estamos en la página de login
    if (!loginForm) {
        // No estamos en la página de login, salir del script
        return;
    }
    
    // Verificar si el usuario ya tiene un token de "recordarme" activo
    function checkRememberMeStatus() {
        // Verificar si hay una cookie de remember_token
        const hasRememberToken = document.cookie.split(';').some(item => item.trim().startsWith('remember_token='));
        
        if (hasRememberToken) {
            // Si hay token, marcar el checkbox y mostrar mensaje
            if (rememberMeCheckbox) {
                rememberMeCheckbox.checked = true;
                showLoginMessage('Sesión recordada activa', 'info');
                
                // Ocultar mensaje después de 3 segundos
                setTimeout(() => {
                    hideLoginMessage();
                }, 3000);
            }
        } else {
            // Restaurar preferencia previa del usuario
            const savedPreference = localStorage.getItem('rememberMePreference');
            if (savedPreference === 'true' && rememberMeCheckbox) {
                rememberMeCheckbox.checked = true;
            }
        }
    }
    
    // Ejecutar verificación al cargar la página
    checkRememberMeStatus();
    
    // Toggle para mostrar/ocultar contraseña
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Cambiar el ícono
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
        });
    }
    
    // Validación en tiempo real
    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            validateUsername();
        });
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            validatePassword();
        });
    }
    
    // Función para validar username
    function validateUsername() {
        const username = usernameInput.value.trim();
        
        if (username.length === 0) {
            showError(usernameError, 'El usuario es requerido');
            return false;
        }
        
        if (username.length < 3) {
            showError(usernameError, 'El usuario debe tener al menos 3 caracteres');
            return false;
        }
        
        // Validar email si contiene @
        if (username.includes('@')) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(username)) {
                showError(usernameError, 'Formato de email inválido');
                return false;
            }
        }
        
        hideError(usernameError);
        return true;
    }
    
    // Función para validar contraseña
    function validatePassword() {
        const password = passwordInput.value;
        
        if (password.length === 0) {
            showError(passwordError, 'La contraseña es requerida');
            return false;
        }
        
        if (password.length < 6) {
            showError(passwordError, 'La contraseña debe tener al menos 6 caracteres');
            return false;
        }
        
        hideError(passwordError);
        return true;
    }
    
    // Mostrar error
    function showError(element, message) {
        element.textContent = message;
        element.classList.add('show');
    }
    
    // Ocultar error
    function hideError(element) {
        element.classList.remove('show');
    }
    
    // Mostrar mensaje de login
    function showLoginMessage(message, type) {
        loginMessage.textContent = message;
        loginMessage.className = `login-message show ${type}`;
    }
    
    // Ocultar mensaje de login
    function hideLoginMessage() {
        loginMessage.classList.remove('show');
    }
    
    // Función para mostrar/ocultar loader del botón
    function toggleButtonLoader(show) {
        if (show) {
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
        } else {
            loginBtn.classList.remove('loading');
            loginBtn.disabled = false;
        }
    }
    
    // Manejar envío del formulario
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Ocultar mensajes previos
        hideLoginMessage();
        
        // Validar formulario
        const isUsernameValid = validateUsername();
        const isPasswordValid = validatePassword();
        
        if (!isUsernameValid || !isPasswordValid) {
            showLoginMessage('Por favor corrige los errores antes de continuar', 'error');
            return;
        }
        
        // Mostrar loader
        toggleButtonLoader(true);
        
        // Preparar datos para enviar
        const formData = new FormData();
        formData.append('username', usernameInput.value.trim());
        formData.append('password', passwordInput.value);
        
        // Obtener el estado del checkbox "Recordarme"
        const rememberMeCheckbox = document.getElementById('rememberMe');
        const rememberMe = rememberMeCheckbox ? rememberMeCheckbox.checked : false;
        formData.append('rememberMe', rememberMe);
        
        // Guardar preferencia de "Recordarme" en localStorage para futuras visitas
        if (rememberMe) {
            localStorage.setItem('rememberMePreference', 'true');
        } else {
            localStorage.removeItem('rememberMePreference');
        }
        
        // Enviar datos con AJAX (compatible con y sin mod_rewrite)
        fetch('?route=process-login', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            toggleButtonLoader(false);
            
            if (data.success) {
                showLoginMessage(data.message, 'success');
                
                // Redireccionar después de 1.5 segundos
                setTimeout(() => {
                    // Construir URL correcta con parámetro route
                    const redirectRoute = data.redirect || 'clan_member';
                    window.location.href = '?route=' + redirectRoute;
                }, 1500);
                
            } else {
                showLoginMessage(data.message, 'error');
                
                // Si hay errores específicos de campos
                if (data.errors) {
                    if (data.errors.username) {
                        showError(usernameError, data.errors.username);
                    }
                    if (data.errors.password) {
                        showError(passwordError, data.errors.password);
                    }
                }
            }
        })
        .catch(error => {
            toggleButtonLoader(false);
            console.error('Error:', error);
            showLoginMessage('Error de conexión. Por favor intenta de nuevo.', 'error');
        });
        });
    }
    
    // Animaciones adicionales
    const inputs = document.querySelectorAll('input');
    if (inputs.length > 0) {
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    }
    
    // Efecto de ripple en el botón
    if (loginBtn) {
        loginBtn.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
        });
    }
    
    // CSS para el efecto ripple (solo si existe el botón de login)
    if (loginBtn) {
        const style = document.createElement('style');
        style.textContent = `
            .login-btn {
                position: relative;
                overflow: hidden;
            }
            
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // === SISTEMA DE TEMAS ===
    const dashboard = document.querySelector('.modern-dashboard');
    const themeButtons = document.querySelectorAll('.theme-btn');
    
    if (dashboard && themeButtons.length > 0) {
        // Cargar tema guardado
        const savedTheme = localStorage.getItem('rinotrack-theme') || 'default';
        setTheme(savedTheme);
        
        // Event listeners para los botones de tema
        themeButtons.forEach(button => {
            if (button.dataset.theme === savedTheme) {
                button.classList.add('active');
            }
            
            button.addEventListener('click', function() {
                const theme = this.dataset.theme;
                setTheme(theme);
                
                // Actualizar botón activo
                themeButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Guardar tema en localStorage
                localStorage.setItem('rinotrack-theme', theme);
                
                // Efecto visual al cambiar tema
                dashboard.style.transform = 'scale(0.99)';
                setTimeout(() => {
                    dashboard.style.transform = 'scale(1)';
                }, 150);
            });
        });
        
        function setTheme(theme) {
            dashboard.setAttribute('data-theme', theme);
            document.body.setAttribute('data-theme', theme);
        }
    }
    
    // === ANIMACIONES DE ENTRADA ===
    const animatedElements = document.querySelectorAll('.animate-fade-in');
    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeIn 0.6s ease-out forwards';
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(el => {
            observer.observe(el);
        });
    }
    
    // === EFECTOS HOVER MEJORADOS ===
    const cards = document.querySelectorAll('.stat-card, .content-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // === TOOLTIPS DINÁMICOS ===
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.textContent = this.getAttribute('title');
            tooltip.className = 'dynamic-tooltip';
            tooltip.style.cssText = `
                position: fixed;
                background: var(--bg-dark);
                color: var(--text-white);
                padding: 8px 12px;
                border-radius: 8px;
                font-size: 0.8rem;
                z-index: 10000;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s ease;
                box-shadow: var(--shadow-lg);
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = tooltip.getBoundingClientRect();
            tooltip.style.left = (e.clientX - rect.width / 2) + 'px';
            tooltip.style.top = (e.clientY - rect.height - 10) + 'px';
            
            setTimeout(() => tooltip.style.opacity = '1', 10);
            
            this.addEventListener('mouseleave', function() {
                tooltip.remove();
            });
        });
    });
    

}); 

/* ===== SISTEMA DE MODALES DE CONFIRMACIÓN ===== */

// Función global para mostrar modales de confirmación personalizados
window.showConfirmationModal = function(options) {
    const {
        title = 'Confirmar Acción',
        message = '¿Estás seguro de que quieres realizar esta acción?',
        icon = 'warning',
        confirmText = 'Confirmar',
        cancelText = 'Cancelar',
        onConfirm = null,
        onCancel = null,
        type = 'warning' // warning, info, success
    } = options;

    // Crear el HTML del modal
    const modalHTML = `
        <div class="confirmation-modal-overlay" id="confirmationModalOverlay">
            <div class="confirmation-modal" id="confirmationModal">
                <div class="confirmation-modal-header">
                    <h3 class="confirmation-modal-title">${title}</h3>
                </div>
                <div class="confirmation-modal-body">
                    <i class="fas fa-${getIconForType(type)} confirmation-modal-icon ${type}"></i>
                    <p class="confirmation-modal-message">${message}</p>
                    <div class="confirmation-modal-actions">
                        <button class="confirmation-modal-btn cancel" id="confirmationCancelBtn">
                            ${cancelText}
                        </button>
                        <button class="confirmation-modal-btn confirm" id="confirmationConfirmBtn">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Agregar el modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const overlay = document.getElementById('confirmationModalOverlay');
    const modal = document.getElementById('confirmationModal');
    const confirmBtn = document.getElementById('confirmationConfirmBtn');
    const cancelBtn = document.getElementById('confirmationCancelBtn');

    // Mostrar el modal con animación
    setTimeout(() => {
        overlay.classList.add('show');
    }, 10);

    // Función para cerrar el modal
    const closeModal = (result) => {
        overlay.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(overlay);
            if (result && onConfirm) {
                onConfirm();
            } else if (!result && onCancel) {
                onCancel();
            }
        }, 300);
    };

    // Event listeners
    confirmBtn.addEventListener('click', () => closeModal(true));
    cancelBtn.addEventListener('click', () => closeModal(false));
    
    // Cerrar al hacer clic en el overlay
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeModal(false);
        }
    });

    // Cerrar con Escape
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            closeModal(false);
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);

    // Enfocar el botón de cancelar por defecto
    cancelBtn.focus();
};

// Función para obtener el icono según el tipo
function getIconForType(type) {
    const icons = {
        warning: 'exclamation-triangle',
        info: 'info-circle',
        success: 'check-circle',
        danger: 'times-circle',
        question: 'question-circle'
    };
    return icons[type] || 'exclamation-triangle';
}

// Función de conveniencia para confirmaciones de eliminación
window.confirmDelete = function(message, onConfirm, onCancel = null) {
    showConfirmationModal({
        title: 'Confirmar Eliminación',
        message: message,
        icon: 'warning',
        confirmText: 'Eliminar',
        cancelText: 'Cancelar',
        type: 'warning',
        onConfirm: onConfirm,
        onCancel: onCancel
    });
};

// Función de conveniencia para confirmaciones de información
window.confirmInfo = function(message, onConfirm, onCancel = null) {
    showConfirmationModal({
        title: 'Información',
        message: message,
        icon: 'info',
        confirmText: 'Aceptar',
        cancelText: 'Cancelar',
        type: 'info',
        onConfirm: onConfirm,
        onCancel: onCancel
    });
};

// Función de conveniencia para confirmaciones de éxito
window.confirmSuccess = function(message, onConfirm, onCancel = null) {
    showConfirmationModal({
        title: 'Éxito',
        message: message,
        icon: 'check',
        confirmText: 'Continuar',
        cancelText: 'Cancelar',
        type: 'success',
        onConfirm: onConfirm,
        onCancel: onCancel
    });
}; 