<?php
// Verificar que las variables necesarias existan
if (!isset($clan)) {
    die('Error: Variable $clan no está definida');
}

if (!isset($summary)) {
    die('Error: Variable $summary no está definida');
}

if (!isset($view)) {
    $view = 'calendar';
}

if (!isset($all_tasks)) {
    $all_tasks = [];
}

// Guardar el contenido en una variable
ob_start();
?>

<div class="collaborator-availability-dashboard minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <div class="clan-icon-large">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="title-content">
                    <h1>Disponibilidad de Colaboradores</h1>
                    <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name']); ?> - <?php echo htmlspecialchars($clan['clan_departamento']); ?></span>
                </div>
            </div>
            
            <div class="actions-minimal">
                <div class="view-toggle">
                    <a href="?route=clan_leader/collaborator-availability&view=calendar" class="btn-minimal <?= ($view === 'calendar') ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Calendario</span>
                    </a>
                    <a href="?route=clan_leader/collaborator-availability&view=gantt" class="btn-minimal <?= ($view === 'gantt') ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Diagrama Gantt</span>
                    </a>
                </div>
                <div class="navigation-actions">
                    <!-- Botón de cerrar sesión removido - ahora está en el menú principal -->
                </div>
            </div>
        </div>
    </header>

    <!-- Reglas de Disponibilidad -->
    <div class="content-minimal">
        <section class="availability-rules">
            <div class="rules-card">
                <div class="rules-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Lógica de Disponibilidad</h3>
                </div>
                <div class="rules-content">
                    <p>Disponibilidad basada en tareas de proyectos <strong>ACTIVOS</strong> en los próximos 15 días:</p>
                    <ul>
                        <li><span class="rule-badge disponible">0 tareas</span> - Disponible ahora</li>
                        <li><span class="rule-badge poco-ocupado">1-3 tareas</span> - Poco ocupado</li>
                        <li><span class="rule-badge ocupado">4-9 tareas</span> - Ocupado</li>
                        <li><span class="rule-badge sobrecargado">10+ tareas</span> - Sobrecargado</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Vista de Calendario -->
        <section class="calendar-view">
            <div class="calendar-header-section">
                <h3>Calendario de Tareas</h3>
                <div class="calendar-info">
                    <span class="calendar-hint">
                        <i class="fas fa-info-circle"></i>
                        Haz clic en una fecha para ver las tareas del día
                    </span>
                    <button class="btn-collapse" onclick="toggleCalendar()" id="calendarToggle">
                        <i class="fas fa-chevron-up" id="calendarIcon"></i>
                        <span id="calendarToggleText">Ocultar</span>
                    </button>
                </div>
            </div>
            
            <div class="calendar-container" id="calendarContainer">
                <div class="calendar-header">
                    <button class="btn-calendar-nav" onclick="previousMonth()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h4 id="currentMonth"><?= date('F Y') ?></h4>
                    <button class="btn-calendar-nav" onclick="nextMonth()">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div class="calendar-grid">
                    <div class="calendar-weekdays">
                        <div class="weekday">Dom</div>
                        <div class="weekday">Lun</div>
                        <div class="weekday">Mar</div>
                        <div class="weekday">Mié</div>
                        <div class="weekday">Jue</div>
                        <div class="weekday">Vie</div>
                        <div class="weekday">Sáb</div>
                    </div>
                    
                    <div class="calendar-days" id="calendarDays">
                        <!-- Los días se generarán con JavaScript -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Resumen de Disponibilidad -->
        <section class="availability-summary">
            <h3>Resumen de Disponibilidad</h3>
            <div class="summary-grid">
                <div class="summary-card disponible">
                    <div class="summary-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="summary-content">
                        <span class="summary-number"><?php echo $summary['disponibles']; ?></span>
                        <span class="summary-label">Disponibles ahora</span>
                    </div>
                </div>
                
                <div class="summary-card poco-ocupado">
                    <div class="summary-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="summary-content">
                        <span class="summary-number"><?php echo $summary['poco_ocupados']; ?></span>
                        <span class="summary-label">Poco ocupados</span>
                    </div>
                </div>
                
                <div class="summary-card ocupado">
                    <div class="summary-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="summary-content">
                        <span class="summary-number"><?php echo $summary['ocupados']; ?></span>
                        <span class="summary-label">Ocupados</span>
                    </div>
                </div>
                
                <div class="summary-card sobrecargado">
                    <div class="summary-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="summary-content">
                        <span class="summary-number"><?php echo $summary['sobrecargados']; ?></span>
                        <span class="summary-label">Sobrecargados</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Lista de Colaboradores -->
        <section class="collaborators-list">
            <h3>Colaboradores del Clan</h3>
            <div class="collaborators-grid">
                <?php if (empty($availability_data)): ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No hay colaboradores en el clan</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($availability_data as $collaborator): ?>
                        <div class="collaborator-card <?php echo $collaborator['availability']; ?>">
                            <div class="collaborator-header">
                                <div class="collaborator-avatar">
                                    <?php if (isset($collaborator['member']['profile_picture']) && $collaborator['member']['profile_picture']): ?>
                                        <img src="<?php echo htmlspecialchars($collaborator['member']['profile_picture']); ?>" alt="<?php echo htmlspecialchars($collaborator['member']['full_name']); ?>">
                                    <?php else: ?>
                                        <?php 
                                        $colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
                                        $memberColor = $colors[$collaborator['member']['user_id'] % count($colors)];
                                        ?>
                                        <div class="avatar-initial" style="background-color: <?php echo $memberColor; ?>">
                                            <?php echo strtoupper(substr($collaborator['member']['full_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="collaborator-info">
                                    <div class="collaborator-name"><?php echo htmlspecialchars($collaborator['member']['full_name']); ?></div>
                                    <div class="availability-badge <?php echo $collaborator['availability']; ?>">
                                        <i class="fas fa-clock"></i>
                                        <?php 
                                        switch($collaborator['availability']) {
                                            case 'disponible': echo 'Disponible ahora'; break;
                                            case 'poco_ocupado': echo 'Poco ocupado'; break;
                                            case 'ocupado': echo 'Ocupado'; break;
                                            case 'sobrecargado': echo 'Sobrecargado'; break;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="collaborator-metrics">
                                <div class="metric-item">
                                    <i class="fas fa-tasks"></i>
                                    <span class="metric-label">Tareas activas:</span>
                                    <span class="metric-value"><?php echo $collaborator['task_count']; ?></span>
                                </div>
                            </div>
                            
                            <?php if (!empty($collaborator['tasks'])): ?>
                                <div class="upcoming-tasks">
                                    <h4>Próximas tareas:</h4>
                                    <div class="tasks-list">
                                        <?php foreach (array_slice($collaborator['tasks'], 0, 3) as $task): ?>
                                            <div class="task-item">
                                                <i class="fas fa-circle"></i>
                                                <span class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></span>
                                                <?php if ($task['due_date']): ?>
                                                    <span class="task-date"><?php echo date('d/m/Y', strtotime($task['due_date'])); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- Modal para mostrar tareas del día -->
<div class="task-modal" id="taskModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tareas del día</h3>
            <div class="modal-header-actions">
                <button class="btn-create-task" onclick="openCreateTaskFromModal()" title="Crear nueva tarea">
                    <i class="fas fa-plus"></i>
                    <span>Nueva Tarea</span>
                </button>
                <button class="modal-close" onclick="closeTaskModal()">&times;</button>
            </div>
        </div>
        <div class="task-list" id="modalTaskList">
            <!-- Las tareas se cargarán dinámicamente -->
        </div>
        <div class="modal-footer">
            <button class="btn-create-task-full" onclick="openCreateTaskFromModal()">
                <i class="fas fa-plus"></i>
                Crear Nueva Tarea para este Día
            </button>
        </div>
    </div>
</div>

<!-- Modal para crear nueva tarea -->
<div class="task-modal" id="createTaskModal">
    <div class="modal-content create-task-modal">
        <div class="modal-header">
            <h3 id="createModalTitle">Crear Nueva Tarea</h3>
            <button class="modal-close" onclick="closeCreateTaskModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="createTaskForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="create_task_title">Título de la tarea *</label>
                        <input type="text" id="create_task_title" name="task_title" placeholder="Título de la tarea *" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="create_task_due_date">Fecha límite *</label>
                        <div class="date-input-wrapper">
                            <input type="date" id="create_task_due_date" name="task_due_date" required>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="create_task_project">Proyecto/Concepto</label>
                        <div class="select-wrapper">
                            <select id="create_task_project" name="task_project">
                                <option value="">Seleccionar proyecto...</option>
                                <?php 
                                // Obtener proyectos para el clan actual
                                if (isset($clan) && isset($clan['clan_id'])) {
                                    require_once __DIR__ . '/../../models/Project.php';
                                    $projectModel = new Project();
                                    $projects = $projectModel->getByClan($clan['clan_id']);
                                    
                                    // Filtrar proyectos personales
                                    $filteredProjects = array_filter($projects, function($project) {
                                        if (($project['is_personal'] ?? 0) == 1) {
                                            return ($project['created_by_user_id'] ?? 0) == ($_SESSION['user_id'] ?? 0);
                                        }
                                        return true;
                                    });
                                    $filteredProjects = array_values($filteredProjects);
                                    
                                    foreach ($filteredProjects as $project): 
                                ?>
                                    <option value="<?php echo $project['project_id']; ?>">
                                        <?php echo htmlspecialchars($project['project_name']); ?>
                                    </option>
                                <?php 
                                    endforeach;
                                }
                                ?>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="create_priority">Prioridad</label>
                        <div class="select-wrapper">
                            <select id="create_priority" name="priority">
                                <option value="low">Baja</option>
                                <option value="medium" selected>Media</option>
                                <option value="high">Alta</option>
                                <option value="critical">Urgente</option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="create_assigned_to_user_id">Asignar a</label>
                        <div class="select-wrapper">
                            <select id="create_assigned_to_user_id" name="assigned_to_user_id">
                                <option value="">Sin asignar</option>
                                <?php 
                                // Obtener miembros del clan
                                if (isset($availability_data) && !empty($availability_data)) {
                                    foreach ($availability_data as $collaborator): 
                                        $member = $collaborator['member'];
                                ?>
                                    <option value="<?php echo $member['user_id']; ?>">
                                        <?php echo htmlspecialchars($member['full_name']); ?>
                                    </option>
                                <?php 
                                    endforeach;
                                }
                                ?>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="create_task_description">Descripción</label>
                        <textarea id="create_task_description" name="task_description" rows="3" placeholder="Descripción de la tarea..."></textarea>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-minimal secondary" onclick="closeCreateTaskModal()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn-minimal primary">
                        <i class="fas fa-plus"></i>
                        Crear Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para pasar datos de tareas al JavaScript -->
<script>
// Datos de tareas para el calendario
window.calendarTasksData = <?= json_encode($all_tasks) ?>;

// Esperar a que el DOM esté listo y las funciones estén disponibles
document.addEventListener('DOMContentLoaded', function() {
    if (typeof setTasksData === 'function') {
        setTasksData(window.calendarTasksData);
    } else {
        console.error('La función setTasksData no está disponible');
        // Fallback: establecer directamente la variable global
        window.tasksData = window.calendarTasksData;
        if (typeof generateCalendar === 'function') {
            generateCalendar();
        }
    }
    
    // Configurar el formulario de creación de tareas
    setupCreateTaskModal();
});

// Función para configurar el modal de creación de tareas
function setupCreateTaskModal() {
    const form = document.getElementById('createTaskForm');
    if (form) {
        form.addEventListener('submit', handleCreateTask);
    }
}

// Función para abrir el modal de creación de tareas
function openCreateTaskModal(selectedDate = null) {
    const modal = document.getElementById('createTaskModal');
    const dateInput = document.getElementById('create_task_due_date');
    
    if (modal) {
        modal.style.display = 'block';
        
        // Si se proporciona una fecha, establecerla en el campo
        if (selectedDate) {
            dateInput.value = selectedDate;
        } else {
            // Establecer la fecha de hoy como predeterminada
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
        }
        
        // Enfocar el primer campo
        document.getElementById('create_task_title').focus();
    }
}

// Función para cerrar el modal de creación de tareas
function closeCreateTaskModal() {
    const modal = document.getElementById('createTaskModal');
    if (modal) {
        modal.style.display = 'none';
        
        // Limpiar el formulario
        const form = document.getElementById('createTaskForm');
        if (form) {
            form.reset();
        }
    }
}

// Variable global para almacenar la fecha seleccionada
let selectedCalendarDate = null;

// Función para abrir el modal de creación desde el modal de tareas
function openCreateTaskFromModal() {
    // Cerrar el modal de tareas actual
    closeTaskModal();
    
    // Usar la fecha que estaba seleccionada en el calendario
    const dateToUse = window.selectedCalendarDate || new Date().toISOString().split('T')[0];
    
    // Abrir el modal de creación
    setTimeout(() => {
        openCreateTaskModal(dateToUse);
    }, 300); // Pequeño delay para que se vea la transición
}

// Función para manejar el envío del formulario de creación de tareas
function handleCreateTask(event) {
    event.preventDefault();
    
    // Obtener los datos del formulario
    const formData = new FormData(event.target);
    
    // Validaciones básicas
    const taskTitle = formData.get('task_title');
    const taskDueDate = formData.get('task_due_date');
    const taskProject = formData.get('task_project');
    
    if (!taskTitle.trim()) {
        showToast('El título de la tarea es requerido', 'error');
        return;
    }
    
    if (!taskDueDate) {
        showToast('La fecha límite es requerida', 'error');
        return;
    }
    
    if (!taskProject) {
        showToast('Debe seleccionar un proyecto', 'error');
        return;
    }
    
    // Mostrar indicador de carga
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
    submitBtn.disabled = true;
    
    // Enviar solicitud al servidor
    fetch('?route=clan_leader/create-task', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Tarea creada exitosamente', 'success');
            closeCreateTaskModal();
            
            // Recargar la página para mostrar la nueva tarea
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('Error al crear la tarea: ' + (data.message || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al crear la tarea', 'error');
    })
    .finally(() => {
        // Restaurar el botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Función para mostrar notificaciones toast
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 350px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    `;
    
    if (type === 'success') {
        toast.style.background = '#10b981';
    } else if (type === 'error') {
        toast.style.background = '#ef4444';
    } else {
        toast.style.background = '#3b82f6';
    }
    
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Cerrar modal al hacer clic fuera de él
document.addEventListener('click', function(event) {
    const modal = document.getElementById('createTaskModal');
    if (modal && event.target === modal) {
        closeCreateTaskModal();
    }
});

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCreateTaskModal();
    }
});

// Agregar estilos para las animaciones toast
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(toastStyles);
</script>

<style>
/* Estilos para la información del calendario */
.calendar-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.calendar-hint {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6b7280;
    background: #f3f4f6;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.calendar-hint i {
    color: #3b82f6;
}

/* Estilos para el modal de tareas */
.modal-header-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-create-task {
    background: #10b981;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.btn-create-task:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    text-align: center;
}

.btn-create-task-full {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.btn-create-task-full:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

/* Estilos mejorados para los botones de vista */
.view-toggle {
    display: flex;
    gap: 0;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 4px;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.view-toggle .btn-minimal {
    border-radius: 6px;
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 500;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 120px;
    justify-content: center;
}

.view-toggle .btn-minimal:hover {
    background: rgba(25, 44, 94, 0.1);
    color: #192c5e;
    transform: translateY(-1px);
}

.view-toggle .btn-minimal.active {
    background: #192c5e;
    color: white;
    box-shadow: 0 2px 8px rgba(25, 44, 94, 0.3);
    transform: translateY(-1px);
}

.view-toggle .btn-minimal.active:hover {
    background: #0f1e3d;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 44, 94, 0.4);
}

.view-toggle .btn-minimal i {
    font-size: 16px;
}

.view-toggle .btn-minimal span {
    font-weight: 500;
    white-space: nowrap;
}

/* Animación para transiciones suaves */
.view-toggle .btn-minimal {
    position: relative;
    overflow: hidden;
}

.view-toggle .btn-minimal::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s ease;
}

.view-toggle .btn-minimal:hover::before {
    left: 100%;
}

/* Mejorar el header general */
.actions-minimal {
    display: flex;
    align-items: center;
    gap: 20px;
}

.navigation-actions {
    display: flex;
    gap: 10px;
}

/* Efectos adicionales para mejor UX */
.view-toggle .btn-minimal {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.view-toggle .btn-minimal.active {
    background: linear-gradient(135deg, #192c5e 0%, #0f1e3d 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.view-toggle .btn-minimal.active::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
    border-radius: inherit;
    pointer-events: none;
}

/* Mejorar la transición del foco */
.view-toggle .btn-minimal:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(25, 44, 94, 0.2);
}

/* Responsive para pantallas pequeñas */
@media (max-width: 768px) {
    .view-toggle .btn-minimal {
        min-width: 100px;
        padding: 6px 12px;
        font-size: 13px;
    }
    
    .view-toggle .btn-minimal span {
        display: none;
    }
    
    .view-toggle .btn-minimal i {
        font-size: 18px;
    }
    
    .view-toggle {
        gap: 2px;
        padding: 2px;
    }
}

/* Estilos para el modal de creación de tareas */
.create-task-modal {
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-body {
    padding: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-row .full-width {
    grid-column: 1 / -1;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.select-wrapper {
    position: relative;
}

.select-wrapper select {
    width: 100%;
    appearance: none;
    padding-right: 2.5rem;
}

.select-wrapper i {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.date-input-wrapper {
    position: relative;
}

.date-input-wrapper input {
    width: 100%;
    padding-right: 2.5rem;
}

.date-input-wrapper i {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.btn-minimal {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 0.9rem;
}

.btn-minimal.primary {
    background: #3b82f6;
    color: white;
}

.btn-minimal.primary:hover {
    background: #2563eb;
}

.btn-minimal.secondary {
    background: #f1f5f9;
    color: #64748b;
}

.btn-minimal.secondary:hover {
    background: #e2e8f0;
}

/* Responsive para el modal */
@media (max-width: 768px) {
    .create-task-modal {
        width: 95%;
        margin: 1rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .modal-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .btn-minimal {
        width: 100%;
        justify-content: center;
    }
    
    .calendar-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .calendar-hint {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    
    .modal-header-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-create-task span {
        display: none;
    }
    
    .btn-create-task {
        padding: 0.5rem;
        min-width: auto;
    }
    
    .btn-create-task-full {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para Clan Leader
$additionalCSS = [
    APP_URL . 'assets/css/clan-leader.css'
];

// JavaScript adicional para Clan Leader  
$additionalJS = [
    APP_URL . 'assets/js/clan-leader.js'
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?> 