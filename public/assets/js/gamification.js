/**
 * Sistema de Gamificación Mitológica - JavaScript
 */

class GamificationSystem {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeAnimations();
        this.setupNotifications();
    }

    setupEventListeners() {
        // Event listeners para badges
        document.addEventListener('click', (e) => {
            if (e.target.matches('.badge-card')) {
                this.showBadgeDetails(e.target.dataset.badgeId);
            }
        });

        // Event listeners para filtros
        const filterInputs = document.querySelectorAll('.filter-select, .filter-input');
        filterInputs.forEach(input => {
            input.addEventListener('change', () => this.filterItems());
            input.addEventListener('keyup', () => this.filterItems());
        });

        // Event listeners para modales
        document.addEventListener('click', (e) => {
            if (e.target.matches('.close') || e.target.matches('.modal')) {
                this.closeModal(e.target.closest('.modal'));
            }
        });

        // Event listeners para formularios
        document.addEventListener('submit', (e) => {
            if (e.target.matches('#badgeForm')) {
                e.preventDefault();
                this.saveBadge(e.target);
            }
            if (e.target.matches('#awardForm')) {
                e.preventDefault();
                this.awardBadge(e.target);
            }
        });
    }

    initializeAnimations() {
        // Animación de entrada para badges
        const badges = document.querySelectorAll('.badge-card');
        badges.forEach((badge, index) => {
            badge.style.opacity = '0';
            badge.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                badge.style.transition = 'all 0.5s ease';
                badge.style.opacity = '1';
                badge.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Animación para leaderboard
        const leaderboardItems = document.querySelectorAll('.leaderboard-item');
        leaderboardItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, index * 50);
        });
    }

    setupNotifications() {
        // Crear contenedor de notificaciones
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                pointer-events: none;
            `;
            document.body.appendChild(container);
        }
    }

    // Funciones de filtrado
    filterItems() {
        const categoryFilter = document.getElementById('categoryFilter')?.value || '';
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        const searchFilter = document.getElementById('searchFilter')?.value.toLowerCase() || '';

        const items = document.querySelectorAll('.badge-card, .leaderboard-item, .event-card');
        
        items.forEach(item => {
            const category = item.dataset.category || '';
            const status = item.dataset.status || '';
            const name = item.dataset.name || '';
            
            let show = true;
            
            if (categoryFilter && category !== categoryFilter) show = false;
            if (statusFilter && status !== statusFilter) show = false;
            if (searchFilter && !name.includes(searchFilter)) show = false;
            
            if (show) {
                item.style.display = 'block';
                item.style.animation = 'fadeIn 0.3s ease';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Funciones de modal
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
    }

    closeModal(modal) {
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    }

    // Funciones de badges
    showBadgeDetails(badgeId) {
        // Implementar vista detallada del badge
        console.log('Mostrando detalles del badge:', badgeId);
    }

    saveBadge(form) {
        const formData = new FormData(form);
        const badgeId = formData.get('badge_id');
        const url = badgeId ? '?route=gamification/updateBadge' : '?route=gamification/createBadge';

        this.showLoading(form);

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading(form);
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.closeModal(document.getElementById('badgeModal'));
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showNotification(data.message, 'error');
                if (data.errors) {
                    this.displayErrors(data.errors);
                }
            }
        })
        .catch(error => {
            this.hideLoading(form);
            console.error('Error:', error);
            this.showNotification('Error al guardar el badge', 'error');
        });
    }

    awardBadge(form) {
        const formData = new FormData(form);

        this.showLoading(form);

        fetch('?route=gamification/awardBadge', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading(form);
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.closeModal(document.getElementById('awardModal'));
            } else {
                this.showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            this.hideLoading(form);
            console.error('Error:', error);
            this.showNotification('Error al otorgar el badge', 'error');
        });
    }

    deleteBadge(badgeId) {
        if (confirm('¿Estás seguro de que quieres eliminar este badge? Esta acción no se puede deshacer.')) {
            const formData = new FormData();
            formData.append('badge_id', badgeId);

            fetch('?route=gamification/deleteBadge', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('Error al eliminar el badge', 'error');
            });
        }
    }

    // Funciones de utilidad
    showLoading(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-gamification"></span> Guardando...';
        }
    }

    hideLoading(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Guardar';
        }
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `achievement-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">
                    ${this.getNotificationIcon(type)}
                </div>
                <div class="notification-text">${message}</div>
            </div>
        `;

        container.appendChild(notification);

        // Mostrar notificación
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Ocultar notificación
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        return icons[type] || icons.info;
    }

    displayErrors(errors) {
        // Limpiar errores anteriores
        document.querySelectorAll('.error-message').forEach(el => el.remove());

        // Mostrar nuevos errores
        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.style.cssText = 'color: #EF4444; font-size: 12px; margin-top: 4px;';
                errorDiv.textContent = errors[field];
                input.parentNode.appendChild(errorDiv);
            }
        });
    }

    // Funciones de animación
    animatePointsEarned(points, element) {
        const pointsElement = element || document.querySelector('.points-display');
        if (!pointsElement) return;

        const originalText = pointsElement.textContent;
        const originalPoints = parseInt(originalText.replace(/\D/g, '')) || 0;
        const newPoints = originalPoints + points;

        // Animación de conteo
        let currentPoints = originalPoints;
        const increment = points / 20;
        const interval = setInterval(() => {
            currentPoints += increment;
            if (currentPoints >= newPoints) {
                currentPoints = newPoints;
                clearInterval(interval);
            }
            pointsElement.textContent = Math.floor(currentPoints).toLocaleString();
        }, 50);

        // Efecto visual
        pointsElement.classList.add('points-earned');
        setTimeout(() => {
            pointsElement.classList.remove('points-earned');
        }, 500);
    }

    // Funciones de progreso
    updateProgressBar(progressElement, current, target) {
        const percentage = Math.min((current / target) * 100, 100);
        const fillElement = progressElement.querySelector('.progress-fill');
        
        if (fillElement) {
            fillElement.style.width = `${percentage}%`;
        }

        // Actualizar texto de progreso
        const progressText = progressElement.querySelector('.progress-text');
        if (progressText) {
            progressText.textContent = `${current}/${target}`;
        }
    }

    // Funciones de leaderboard
    updateLeaderboardPosition(element, newPosition) {
        const positionElement = element.querySelector('.rank-position');
        if (positionElement) {
            positionElement.style.animation = 'bounce 0.5s ease';
            setTimeout(() => {
                positionElement.textContent = newPosition;
                positionElement.style.animation = '';
            }, 250);
        }
    }
}

// Funciones globales para compatibilidad
window.GamificationSystem = GamificationSystem;

// Funciones específicas para modales
window.openCreateBadgeModal = function() {
    document.getElementById('modalTitle').textContent = 'Crear Nuevo Badge';
    document.getElementById('badgeForm').reset();
    document.getElementById('badgeId').value = '';
    document.getElementById('isActiveGroup').style.display = 'none';
    document.getElementById('badgeModal').style.display = 'block';
};

window.editBadge = function(badgeId) {
    document.getElementById('modalTitle').textContent = 'Editar Badge';
    document.getElementById('badgeId').value = badgeId;
    document.getElementById('isActiveGroup').style.display = 'block';
    document.getElementById('badgeModal').style.display = 'block';
    
    // Cargar datos del badge
    loadBadgeData(badgeId);
};

window.closeBadgeModal = function() {
    document.getElementById('badgeModal').style.display = 'none';
};

window.awardBadge = function(badgeId) {
    document.getElementById('awardBadgeId').value = badgeId;
    document.getElementById('awardModal').style.display = 'block';
    loadUsersList();
};

window.closeAwardModal = function() {
    document.getElementById('awardModal').style.display = 'none';
};

window.deleteBadge = function(badgeId) {
    if (window.gamificationSystem) {
        window.gamificationSystem.deleteBadge(badgeId);
    }
};

// Funciones auxiliares
function loadBadgeData(badgeId) {
    // Implementar carga de datos del badge via AJAX
    console.log('Cargando datos del badge:', badgeId);
}

function loadUsersList() {
    // Implementar carga de lista de usuarios via AJAX
    console.log('Cargando lista de usuarios');
}

// Inicializar sistema cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.gamificationSystem = new GamificationSystem();
});

// Estilos CSS adicionales para animaciones
const additionalStyles = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    
    .achievement-notification {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .notification-icon {
        font-size: 20px;
    }
    
    .notification-text {
        font-weight: 500;
    }
    
    .error-message {
        color: #EF4444;
        font-size: 12px;
        margin-top: 4px;
    }
    
    .loading-gamification {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #3B82F6;
        animation: spin 1s ease-in-out infinite;
    }
`;

// Agregar estilos al documento
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet); 