// Clan Leader Dashboard - JavaScript Interactivo

document.addEventListener('DOMContentLoaded', function() {
    initClanLeaderDashboard();
});

function initClanLeaderDashboard() {
    // Inicializar animaciones
    setTimeout(() => {
        animateNumbers();
        animateProgressBars();
    }, 300);

    // Configurar eventos
    setupEventListeners();
}

// Animaciones de números
function animateNumbers() {
    const statNumbers = document.querySelectorAll('.stat-value');
    
    statNumbers.forEach(element => {
        const finalNumber = parseInt(element.textContent.replace(/,/g, ''));
        if (isNaN(finalNumber)) return;
        
        const duration = 1500;
        const start = 0;
        const startTime = performance.now();
        
        function updateNumber(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            const currentNumber = Math.floor(easeOutCubic * finalNumber);
            
            element.textContent = new Intl.NumberFormat().format(currentNumber);
            
            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            } else {
                element.textContent = new Intl.NumberFormat().format(finalNumber);
            }
        }
        
        requestAnimationFrame(updateNumber);
    });
}

// Animar progreso de barras
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill-small');
    
    progressBars.forEach((bar, index) => {
        const targetWidth = bar.style.width;
        const currentWidth = parseFloat(targetWidth) || 0;
        
        // Solo animar si hay progreso (width > 0)
        if (currentWidth > 0) {
            // Establecer width inicial solo si no está ya establecido
            if (!bar.dataset.animated) {
                bar.style.width = '0%';
                bar.dataset.animated = 'true';
                
                // Animar con delay escalonado para efecto más suave
                setTimeout(() => {
                    bar.style.transition = 'width 0.8s ease-out';
                    bar.style.width = targetWidth;
                }, 100 + (index * 50));
            }
        }
    });
}

// Configurar listeners de eventos
function setupEventListeners() {
    // Listener para tecla F5 personalizada
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
            e.preventDefault();
            refreshDashboard();
        }
    });

    // Listeners para botones de acción
    setupActionButtons();

    // Listener para tooltips
    setupTooltips();
}

function setupActionButtons() {
    // Botones de acción
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Efecto de ripple
            createRippleEffect(this);
        });
    });
}

function createRippleEffect(element) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

function setupTooltips() {
    const elementsWithTooltips = document.querySelectorAll('[title]');
    
    elementsWithTooltips.forEach(element => {
        let tooltip;
        
        element.addEventListener('mouseenter', function() {
            const title = this.getAttribute('title');
            if (!title) return;
            
            // Crear tooltip
            tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = title;
            document.body.appendChild(tooltip);
            
            // Posicionar tooltip
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
            
            // Remover title para evitar tooltip nativo
            this.setAttribute('data-original-title', title);
            this.removeAttribute('title');
            
            // Animar entrada
            setTimeout(() => {
                tooltip.style.opacity = '1';
            }, 10);
        });
        
        element.addEventListener('mouseleave', function() {
            if (tooltip) {
                tooltip.style.opacity = '0';
                setTimeout(() => {
                    if (tooltip.parentNode) {
                        tooltip.parentNode.removeChild(tooltip);
                    }
                }, 200);
            }
            
            // Restaurar title original
            const originalTitle = this.getAttribute('data-original-title');
            if (originalTitle) {
                this.setAttribute('title', originalTitle);
                this.removeAttribute('data-original-title');
            }
        });
    });
}

// Función para refrescar dashboard
function refreshDashboard() {
    showToast('Actualizando dashboard...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Funciones de utilidad
function formatNumber(number) {
    if (number >= 1000000) {
        return (number / 1000000).toFixed(1) + 'M';
    } else if (number >= 1000) {
        return (number / 1000).toFixed(1) + 'K';
    }
    return number.toLocaleString();
}

function getProgressColor(percentage) {
    if (percentage >= 80) return 'var(--success)';
    if (percentage >= 60) return 'var(--warning)';
    if (percentage >= 40) return 'var(--info)';
    return 'var(--error)';
}

// Función para mostrar toast
function showToast(message, type = 'info') {
    // Crear toast si no existe
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    
    // Configurar contenido
    toast.innerHTML = `
        <div class="toast-content ${type}">
            <i class="fas fa-${getToastIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Mostrar toast
    toast.classList.add('show');
    
    // Ocultar después de 3 segundos
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function getToastIcon(type) {
    switch (type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        default: return 'info-circle';
    }
}

// Funciones específicas para gestión de miembros
function openAddMemberModal() {
    const modal = document.getElementById('addMemberModal');
    if (modal) {
        modal.style.display = 'flex';
        loadAvailableUsers();
    }
}

function closeAddMemberModal() {
    const modal = document.getElementById('addMemberModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function loadAvailableUsers() {
    fetch('?route=clan_leader/get-available-users', {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('userId');
            if (select) {
                select.innerHTML = '<option value="">Seleccionar usuario...</option>';
                
                data.users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.user_id;
                    option.textContent = `${user.full_name} (@${user.username})`;
                    select.appendChild(option);
                });
            }
        } else {
            showToast('Error al cargar usuarios disponibles', 'error');
        }
    })
    .catch(error => {
        console.error('Error al cargar usuarios:', error);
        showToast('Error de conexión al cargar usuarios', 'error');
    });
}

function removeMember(userId, userName) {
    showConfirmationModal({
        title: 'Confirmar Remoción',
        message: `¿Estás seguro de que quieres remover a ${userName} del clan?`,
        type: 'warning',
        confirmText: 'Remover',
        cancelText: 'Cancelar',
        onConfirm: () => {
            const formData = new FormData();
            formData.append('userId', userId);
            
            fetch('?route=clan_leader/remove-member', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error de conexión', 'error');
            });
        }
    });
}

// Manejar envío del formulario de agregar miembro
document.addEventListener('DOMContentLoaded', function() {
    const addMemberForm = document.getElementById('addMemberForm');
    if (addMemberForm) {
        addMemberForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('?route=clan_leader/add-member', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeAddMemberModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error de conexión', 'error');
            });
        });
    }
    
    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('addMemberModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddMemberModal();
            }
        });
    }
});

// Funciones para el calendario de tareas
let tasksData = [];
let currentDate = new Date();

// Generar calendario
function generateCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Actualizar título del mes
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const monthElement = document.getElementById('currentMonth');
    if (monthElement) {
        monthElement.textContent = `${monthNames[month]} ${year}`;
    }
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const calendarDays = document.getElementById('calendarDays');
    if (!calendarDays) {
        return;
    }
    
    calendarDays.innerHTML = '';
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    for (let i = 0; i < 42; i++) {
        const currentDay = new Date(startDate);
        currentDay.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        // Verificar si es otro mes
        if (currentDay.getMonth() !== month) {
            dayElement.classList.add('other-month');
        }
        
        // Verificar si es hoy
        if (currentDay.getTime() === today.getTime()) {
            dayElement.classList.add('today');
        }
        
        // Verificar si tiene tareas
        const dayTasks = getTasksForDate(currentDay);
        if (dayTasks.length > 0) {
            dayElement.classList.add('has-tasks');
        }
        
        const dayNumber = document.createElement('div');
        dayNumber.className = 'day-number';
        dayNumber.textContent = currentDay.getDate();
        
        const dayTasksInfo = document.createElement('div');
        dayTasksInfo.className = 'day-tasks';
        dayTasksInfo.textContent = `${dayTasks.length} tarea${dayTasks.length !== 1 ? 's' : ''}`;
        
        dayElement.appendChild(dayNumber);
        dayElement.appendChild(dayTasksInfo);
        
        // Agregar indicadores de estado
        if (dayTasks.length > 0) {
            const statusCounts = {};
            dayTasks.forEach(task => {
                const status = task.task.status;
                statusCounts[status] = (statusCounts[status] || 0) + 1;
            });
            
            const indicators = document.createElement('div');
            indicators.style.marginTop = '0.25rem';
            
            Object.keys(statusCounts).forEach(status => {
                const indicator = document.createElement('span');
                indicator.className = `task-indicator ${status}`;
                indicator.title = `${statusCounts[status]} tarea${statusCounts[status] !== 1 ? 's' : ''} ${status}`;
                indicators.appendChild(indicator);
            });
            
            dayElement.appendChild(indicators);
        }
        
        // Agregar evento click
        dayElement.addEventListener('click', () => {
            showTasksForDate(currentDay, dayTasks);
        });
        
        calendarDays.appendChild(dayElement);
    }
}

// Obtener tareas para una fecha específica
function getTasksForDate(date) {
    const dateStr = date.toISOString().split('T')[0];
    
    const filteredTasks = tasksData.filter(taskData => {
        const taskDate = taskData.task.due_date;
        return taskDate === dateStr;
    });
    
    return filteredTasks;
}

// Mostrar tareas para una fecha específica
function showTasksForDate(date, tasks) {
    const modal = document.getElementById('taskModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalTaskList = document.getElementById('modalTaskList');
    
    if (!modal || !modalTitle || !modalTaskList) return;
    
    const dateStr = date.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    modalTitle.textContent = `Tareas del ${dateStr}`;
    modalTaskList.innerHTML = '';
    
    if (tasks.length === 0) {
        modalTaskList.innerHTML = '<p>No hay tareas programadas para este día.</p>';
    } else {
        tasks.forEach(taskData => {
            const task = taskData.task;
            const project = taskData.project;
            const assignedUser = taskData.assigned_user;
            
            const taskElement = document.createElement('div');
            taskElement.className = 'task-item';
            
            const isOverdue = new Date(task.due_date) < new Date() && task.status !== 'completed';
            const statusClass = isOverdue ? 'overdue' : task.status;
            
            taskElement.innerHTML = `
                <div class="task-header">
                    <h4 class="task-title">${task.task_name}</h4>
                    <span class="task-status ${statusClass}">
                        ${isOverdue ? 'Vencida' : 
                          task.status === 'pending' ? 'Pendiente' :
                          task.status === 'in_progress' ? 'En Progreso' :
                          task.status === 'completed' ? 'Completada' : 'Cancelada'}
                    </span>
                </div>
                <div class="task-details">
                    <div class="task-project">
                        <i class="fas fa-folder"></i> ${project.project_name}
                    </div>
                    ${assignedUser ? `
                        <div class="task-assigned">
                            <i class="fas fa-user"></i> ${assignedUser.full_name}
                        </div>
                    ` : ''}
                    ${task.description ? `
                        <div style="margin-top: 0.5rem;">
                            <i class="fas fa-align-left"></i> ${task.description}
                        </div>
                    ` : ''}
                </div>
            `;
            
            modalTaskList.appendChild(taskElement);
        });
    }
    
    modal.classList.add('show');
}

// Cerrar modal
function closeTaskModal() {
    const modal = document.getElementById('taskModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

// Navegación del calendario
function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    generateCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    generateCalendar();
}

// Inicializar calendario cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', (e) => {
        const modal = document.getElementById('taskModal');
        if (modal && e.target === modal) {
            closeTaskModal();
        }
    });
    
    // Inicializar calendario si estamos en la página de disponibilidad
    if (document.getElementById('calendarDays')) {
        generateCalendar();
        // Restaurar estado del colapso
        setTimeout(() => {
            restoreCalendarState();
        }, 100);
    }
});

// Función para establecer los datos de tareas (llamada desde PHP)
function setTasksData(data) {
    tasksData = data;
    if (document.getElementById('calendarDays')) {
        generateCalendar();
    }
}

// Función para colapsar/expandir el calendario
function toggleCalendar() {
    const container = document.getElementById('calendarContainer');
    const toggle = document.getElementById('calendarToggle');
    const icon = document.getElementById('calendarIcon');
    const text = document.getElementById('calendarToggleText');
    
    if (!container || !toggle || !icon || !text) return;
    
    const isCollapsed = container.classList.contains('collapsed');
    
    if (isCollapsed) {
        // Expandir
        container.classList.remove('collapsed');
        toggle.classList.remove('collapsed');
        icon.className = 'fas fa-chevron-up';
        text.textContent = 'Ocultar';
        localStorage.setItem('calendarCollapsed', 'false');
    } else {
        // Colapsar
        container.classList.add('collapsed');
        toggle.classList.add('collapsed');
        icon.className = 'fas fa-chevron-down';
        text.textContent = 'Mostrar';
        localStorage.setItem('calendarCollapsed', 'true');
    }
}

// Función para restaurar el estado del colapso
function restoreCalendarState() {
    const container = document.getElementById('calendarContainer');
    const toggle = document.getElementById('calendarToggle');
    const icon = document.getElementById('calendarIcon');
    const text = document.getElementById('calendarToggleText');
    
    if (!container || !toggle || !icon || !text) return;
    
    const isCollapsed = localStorage.getItem('calendarCollapsed') === 'true';
    
    if (isCollapsed) {
        container.classList.add('collapsed');
        toggle.classList.add('collapsed');
        icon.className = 'fas fa-chevron-down';
        text.textContent = 'Mostrar';
    }
}

// Estilos CSS para toast
const toastStyles = `
<style>
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast-content {
    background: white;
    border-radius: 8px;
    padding: 16px 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 300px;
    border-left: 4px solid;
}

.toast-content.success {
    border-left-color: #48bb78;
}

.toast-content.error {
    border-left-color: #e53e3e;
}

.toast-content.warning {
    border-left-color: #ed8936;
}

.toast-content.info {
    border-left-color: #4299e1;
}

.toast-content i {
    font-size: 18px;
}

.toast-content.success i {
    color: #48bb78;
}

.toast-content.error i {
    color: #e53e3e;
}

.toast-content.warning i {
    color: #ed8936;
}

.toast-content.info i {
    color: #4299e1;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: ripple 0.6s linear;
    pointer-events: none;
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.custom-tooltip {
    position: absolute;
    background: #2d3748;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
    white-space: nowrap;
}

.custom-tooltip::before {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #2d3748;
}
</style>
`;

// Agregar estilos al head
document.head.insertAdjacentHTML('beforeend', toastStyles);

// Funciones para el modal de estadísticas de usuario
function showUserStats(userId) {
    // Mostrar loading en el modal
    const modal = document.getElementById('userStatsModal');
    const content = document.getElementById('userStatsContent');
    
    modal.style.display = 'block';
    content.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Cargando estadísticas del usuario...</p>
        </div>
    `;
    
    // Hacer petición AJAX para obtener estadísticas
    fetch(`?route=clan_leader/get-user-stats&user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUserStats(data);
            } else {
                showErrorInModal(data.message || 'Error al cargar estadísticas');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorInModal('Error de conexión al cargar estadísticas');
        });
}

function displayUserStats(data) {
    const content = document.getElementById('userStatsContent');
    const user = data.user;
    const stats = data.task_stats;
    
    // Actualizar título del modal
    document.getElementById('userStatsTitle').textContent = `Estadísticas de ${user.full_name}`;
    
    // Generar HTML del contenido
    const html = `
        <div class="user-stats-container">
            <!-- Header del usuario -->
            <div class="user-header">
                <div class="user-avatar-large" style="background-color: ${user.avatar_color}">
                    ${user.initial}
                </div>
                <div class="user-info">
                    <h3>${user.full_name}</h3>
                    <p>${user.email}</p>
                    <p><strong>Estado:</strong> ${user.is_active ? 'Activo' : 'Inactivo'}</p>
                    ${user.last_login ? `<p><strong>Último acceso:</strong> ${formatDate(user.last_login)}</p>` : ''}
                </div>
            </div>
            
            <!-- Estadísticas generales -->
            <div class="user-stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-title">Tareas Completadas</div>
                    </div>
                    <div class="stat-value">${stats.completed_tasks}</div>
                    <div class="stat-percentage">${stats.completion_percentage}% del total</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-title">Tareas Pendientes</div>
                    </div>
                    <div class="stat-value">${stats.pending_tasks}</div>
                    <div class="stat-percentage">${stats.total_tasks > 0 ? Math.round((stats.pending_tasks / stats.total_tasks) * 100) : 0}% del total</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon in-progress">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div class="stat-title">En Progreso</div>
                    </div>
                    <div class="stat-value">${stats.in_progress_tasks}</div>
                    <div class="stat-percentage">${stats.total_tasks > 0 ? Math.round((stats.in_progress_tasks / stats.total_tasks) * 100) : 0}% del total</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon overdue">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-title">Tareas Vencidas</div>
                    </div>
                    <div class="stat-value">${stats.overdue_tasks}</div>
                    <div class="stat-percentage">${stats.total_tasks > 0 ? Math.round((stats.overdue_tasks / stats.total_tasks) * 100) : 0}% del total</div>
                </div>
            </div>
            
            <!-- Tareas por proyecto -->
            ${data.tasks_by_project.length > 0 ? `
                <div class="tasks-by-project">
                    <div class="project-tasks-header">
                        <h4><i class="fas fa-project-diagram"></i> Tareas por Proyecto</h4>
                    </div>
                    <div class="project-tasks-list">
                        ${data.tasks_by_project.map(project => `
                            <div class="project-task-item">
                                <div class="task-item-header">
                                    <div class="task-item-name">${project.project_name}</div>
                                    <div class="task-item-status">${project.tasks.length} tareas</div>
                                </div>
                                <div class="task-item-meta">
                                    ${project.tasks.map(task => `
                                        <span>
                                            <i class="fas fa-tasks"></i>
                                            ${task.task_name}
                                            <span class="task-item-status ${task.status}">${getStatusText(task.status)}</span>
                                            ${task.due_date ? `<i class="fas fa-calendar"></i> ${formatDate(task.due_date)}` : ''}
                                        </span>
                                    `).join('')}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : `
                <div class="empty-state">
                    <i class="fas fa-tasks"></i>
                    <p>No hay tareas asignadas a este usuario</p>
                </div>
            `}
            
            <!-- Actividad reciente -->
            ${data.recent_activity.length > 0 ? `
                <div class="activity-section">
                    <div class="activity-header">
                        <h4><i class="fas fa-history"></i> Actividad Reciente</h4>
                    </div>
                    <div class="activity-list">
                        ${data.recent_activity.map(activity => `
                            <div class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-text">
                                        <div class="activity-action">${getActivityText(activity)}</div>
                                        <div class="activity-task">${activity.task_name}</div>
                                    </div>
                                    <div class="activity-time">${formatDate(activity.created_at)}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : `
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <p>No hay actividad reciente</p>
                </div>
            `}
        </div>
    `;
    
    content.innerHTML = html;
}

function showErrorInModal(message) {
    const content = document.getElementById('userStatsContent');
    content.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle"></i>
            <p>${message}</p>
        </div>
    `;
}

function closeUserStatsModal() {
    const modal = document.getElementById('userStatsModal');
    modal.style.display = 'none';
}

function getStatusText(status) {
    const statusMap = {
        'completed': 'Completada',
        'pending': 'Pendiente',
        'in_progress': 'En Progreso',
        'overdue': 'Vencida'
    };
    return statusMap[status] || status;
}

function getActivityText(activity) {
    const actionMap = {
        'created': 'Creó la tarea',
        'updated': 'Actualizó la tarea',
        'status_changed': 'Cambió el estado',
        'assigned': 'Fue asignado',
        'unassigned': 'Fue desasignado',
        'commented': 'Agregó un comentario',
        'completed': 'Completó la tarea',
        'reopened': 'Reabrió la tarea',
        'deleted': 'Eliminó la tarea'
    };
    
    let text = actionMap[activity.action_type] || activity.action_type;
    
    if (activity.field_name && activity.old_value !== activity.new_value) {
        text += ` (${activity.field_name}: ${activity.old_value || 'N/A'} → ${activity.new_value || 'N/A'})`;
    }
    
    return text;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) {
        return 'Hoy';
    } else if (diffDays === 2) {
        return 'Ayer';
    } else if (diffDays <= 7) {
        return `Hace ${diffDays - 1} días`;
    } else {
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Cerrar modal al hacer clic fuera de él
window.addEventListener('click', function(event) {
    const modal = document.getElementById('userStatsModal');
    if (event.target === modal) {
        closeUserStatsModal();
    }
}); 

// Función para mostrar modales de confirmación personalizados
function showConfirmationModal(options) {
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
}

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

// Función para agregar comentarios a tareas
function addComment() {
    const commentText = document.getElementById('newComment').value.trim();
    if (!commentText) {
        showNotification('Por favor escribe un comentario', 'error');
        return;
    }
    
    // Obtener el ID de la tarea del botón de eliminar que está en el HTML
    const deleteButton = document.querySelector('button[onclick*="deleteTask"]');
    let taskId = null;
    
    if (deleteButton) {
        const onclickAttr = deleteButton.getAttribute('onclick');
        const match = onclickAttr.match(/deleteTask\((\d+)\)/);
        if (match) {
            taskId = match[1];
        }
    }
    
    if (!taskId) {
        showNotification('Error: No se pudo obtener el ID de la tarea', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('comment_text', commentText);
    
    // Verificar si hay archivo adjunto
    const fileInput = document.getElementById('fileAttachment');
    if (fileInput && fileInput.files[0]) {
        formData.append('attachment', fileInput.files[0]);
    }
    
    fetch('?route=clan_leader/add-task-comment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('newComment').value = '';
            // Limpiar archivo adjunto si existe
            if (fileInput) {
                fileInput.value = '';
                const attachmentPreview = document.getElementById('attachmentPreview');
                if (attachmentPreview) {
                    attachmentPreview.style.display = 'none';
                }
            }
            showNotification('Comentario agregado exitosamente', 'success');
            // Recargar la página para mostrar el nuevo comentario
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Error al agregar comentario: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al agregar comentario', 'error');
    });
}



// Función para manejar archivos adjuntos
function handleFileAttachment(input) {
    const file = input.files[0];
    if (file) {
        const attachmentPreview = document.getElementById('attachmentPreview');
        const attachmentName = document.getElementById('attachmentName');
        
        if (attachmentPreview && attachmentName) {
            attachmentName.textContent = file.name;
            attachmentPreview.style.display = 'block';
        }
    }
}

// Función para remover archivo adjunto
function removeAttachment() {
    const fileInput = document.getElementById('fileAttachment');
    const attachmentPreview = document.getElementById('attachmentPreview');
    
    if (fileInput) {
        fileInput.value = '';
    }
    
    if (attachmentPreview) {
        attachmentPreview.style.display = 'none';
    }
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    if (type === 'success') {
        notification.style.background = '#10b981';
    } else if (type === 'error') {
        notification.style.background = '#ef4444';
    } else {
        notification.style.background = '#3b82f6';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Estilos CSS para modales de confirmación
const confirmationModalStyles = document.createElement('style');
confirmationModalStyles.textContent = `
    .confirmation-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .confirmation-modal-overlay.show {
        opacity: 1;
    }

    .confirmation-modal {
        background: white;
        border-radius: 12px;
        padding: 0;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    .confirmation-modal-overlay.show .confirmation-modal {
        transform: scale(1);
    }

    .confirmation-modal-header {
        padding: 20px 24px 0;
    }

    .confirmation-modal-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
    }

    .confirmation-modal-body {
        padding: 20px 24px 24px;
        text-align: center;
    }

    .confirmation-modal-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .confirmation-modal-icon.warning {
        color: #f59e0b;
    }

    .confirmation-modal-icon.info {
        color: #3b82f6;
    }

    .confirmation-modal-icon.success {
        color: #10b981;
    }

    .confirmation-modal-icon.danger {
        color: #ef4444;
    }

    .confirmation-modal-message {
        margin: 0 0 1.5rem 0;
        color: #6b7280;
        line-height: 1.5;
        white-space: pre-line;
    }

    .confirmation-modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .confirmation-modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 100px;
    }

    .confirmation-modal-btn.cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .confirmation-modal-btn.cancel:hover {
        background: #e5e7eb;
    }

    .confirmation-modal-btn.confirm {
        background: #ef4444;
        color: white;
    }

    .confirmation-modal-btn.confirm:hover {
        background: #dc2626;
    }

    .confirmation-modal-btn.confirm.warning {
        background: #f59e0b;
    }

    .confirmation-modal-btn.confirm.warning:hover {
        background: #d97706;
    }

    .confirmation-modal-btn.confirm.success {
        background: #10b981;
    }

    .confirmation-modal-btn.confirm.success:hover {
        background: #059669;
    }

    .confirmation-modal-btn.confirm.info {
        background: #3b82f6;
    }

    .confirmation-modal-btn.confirm.info:hover {
        background: #2563eb;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;

// Agregar estilos al head
document.head.appendChild(confirmationModalStyles); 