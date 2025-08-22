<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="clan-leader-dashboard minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <div class="clan-icon-large"><?php echo $clanIcon ?? ''; ?></div>
                <h1><?php echo htmlspecialchars($clan['clan_name'] ?? ''); ?></h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_departamento'] ?? ''); ?></span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=clan_leader/kpi-dashboard" class="btn-minimal primary">
                    <i class="fas fa-chart-line"></i>
                    KPIs del Clan
                </a>
                <a href="?route=logout" class="btn-minimal danger" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Tablero Kanban de Tareas del Clan -->
    <div class="content-minimal">
        <section class="kanban-section animate-fade-in">
            <div class="kanban-header">
                <div class="kanban-title">
                    <h3><i class="fas fa-tasks icon-gradient"></i> Tareas</h3>
                </div>
                <div class="kanban-actions">
                    <button class="btn-add-task" onclick="openAddTaskModal()">
                        <i class="fas fa-plus"></i>
                        Agregar Tarea
                    </button>
                </div>
            </div>
            <div class="kanban-board-compact">
                <!-- Columna: Vencidas -->
                <div class="kanban-column-compact">
                    <div class="column-header overdue">
                        <h4>Vencidas</h4>
                        <span class="task-count"><?php echo count($kanbanTasks['vencidas'] ?? []); ?></span>
                    </div>
                    <div class="column-content-compact">
                        <?php foreach ($kanbanTasks['vencidas'] ?? [] as $task): ?>
                            <div class="task-card-compact overdue" data-task-id="<?php echo $task['task_id']; ?>">
                                <div class="task-compact-row">
                                    <input type="checkbox" class="task-checkbox-compact" <?php echo ($task['status'] === 'completed' || ($task['is_completed'] ?? 0) == 1) ? 'checked' : ''; ?> onchange="toggleTaskStatus(<?php echo $task['task_id']; ?>, this.checked)">
                                    <div class="task-name-compact"><?php echo htmlspecialchars($task['task_name']); ?></div>
                                    <a href="?route=clan_leader/get-task-details&task_id=<?php echo $task['task_id']; ?>" class="btn-compact-action" title="Ver proyecto">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <div class="task-due-compact overdue">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Vencida hace <?php echo abs($task['days_until_due']); ?> días
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: Hoy -->
                <div class="kanban-column-compact">
                    <div class="column-header today">
                        <h4>Hoy</h4>
                        <span class="task-count"><?php echo count($kanbanTasks['hoy'] ?? []); ?></span>
                    </div>
                    <div class="column-content-compact">
                        <?php foreach ($kanbanTasks['hoy'] ?? [] as $task): ?>
                            <div class="task-card-compact today" data-task-id="<?php echo $task['task_id']; ?>">
                                <div class="task-compact-row">
                                    <input type="checkbox" class="task-checkbox-compact" <?php echo ($task['status'] === 'completed' || ($task['is_completed'] ?? 0) == 1) ? 'checked' : ''; ?> onchange="toggleTaskStatus(<?php echo $task['task_id']; ?>, this.checked)">
                                    <div class="task-name-compact"><?php echo htmlspecialchars($task['task_name']); ?></div>
                                    <a href="?route=clan_leader/get-task-details&task_id=<?php echo $task['task_id']; ?>" class="btn-compact-action" title="Ver proyecto">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <div class="task-due-compact today">
                                    <i class="fas fa-clock"></i>
                                    Vence hoy
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: 1 Semana -->
                <div class="kanban-column-compact">
                    <div class="column-header week1">
                        <h4>1 Semana</h4>
                        <span class="task-count"><?php echo count($kanbanTasks['1_semana'] ?? []); ?></span>
                    </div>
                    <div class="column-content-compact">
                        <?php foreach ($kanbanTasks['1_semana'] ?? [] as $task): ?>
                            <div class="task-card-compact week1" data-task-id="<?php echo $task['task_id']; ?>">
                                <div class="task-compact-row">
                                    <input type="checkbox" class="task-checkbox-compact" <?php echo ($task['status'] === 'completed' || ($task['is_completed'] ?? 0) == 1) ? 'checked' : ''; ?> onchange="toggleTaskStatus(<?php echo $task['task_id']; ?>, this.checked)">
                                    <div class="task-name-compact"><?php echo htmlspecialchars($task['task_name']); ?></div>
                                    <a href="?route=clan_leader/get-task-details&task_id=<?php echo $task['task_id']; ?>" class="btn-compact-action" title="Ver proyecto">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                <div class="task-due-compact week1">
                                    <i class="fas fa-calendar"></i>
                                    En <?php echo $task['days_until_due']; ?> días
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Progreso General del Equipo -->
        <section class="team-progress-section">
            <div class="progress-header">
                <h3>Progreso General del Equipo</h3>
                <button id="toggleSectionsBtn" class="btn-toggle-sections" onclick="toggleSections()">
                    <i class="fas fa-eye-slash"></i>
                    <span>Ocultar Secciones</span>
                </button>
            </div>
            <div class="progress-card">
                <div class="progress-info">
                    <div class="progress-left">
                        <span class="progress-label">Tareas Completadas Total</span>
                        <span class="progress-value"><?php echo number_format($taskStats['completed_tasks']); ?> tareas</span>
                    </div>
                    <div class="progress-right">
                        <div class="completion-box">
                            <span class="completion-percentage"><?php echo $taskStats['completion_percentage']; ?>%</span>
                            <span class="completion-label">Completado</span>
                        </div>
                    </div>
                </div>
                
                <div class="progress-bar-container">
                    <div class="progress-bar-main">
                        <?php 
                        $colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
                        $totalMembers = count($memberContributions);
                        $colorIndex = 0;
                        
                        foreach ($memberContributions as $member) {
                            if ($member['completed_tasks'] > 0) {
                                $width = ($member['completed_tasks'] / max($taskStats['completed_tasks'], 1)) * 100;
                                echo '<div class="progress-segment" style="width: ' . $width . '%; background-color: ' . $colors[$colorIndex % count($colors)] . ';" title="' . htmlspecialchars($member['full_name']) . ' - ' . $member['completed_tasks'] . ' tareas"></div>';
                                $colorIndex++;
                            }
                        }
                        ?>
                    </div>
                    <span class="remaining-text"><?php echo (100 - $taskStats['completion_percentage']); ?>% restante</span>
                </div>
            </div>
        </section>
        
        <!-- Contenedor de secciones que se pueden ocultar -->
        <div id="hideable-sections" style="display: none;">
        
        <!-- Contribuciones por Colaborador -->
        <section class="contributions-section">
            <h3>Contribuciones por Colaborador</h3>
            <div class="contributions-grid">
                <?php 
                $colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
                $colorIndex = 0;
                
                if (empty($memberContributions)): ?>
                    <div style="text-align: center; padding: 20px; color: #666;">
                        <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <p>No hay miembros en el clan o no se pudieron cargar las contribuciones.</p>
                    </div>
                <?php else:
                foreach ($memberContributions as $member): 
                    $memberColor = $colors[$colorIndex % count($colors)];
                    $colorIndex++;
                ?>
                    <div class="contribution-card clickable" data-user-id="<?php echo $member['user_id']; ?>" onclick="showUserStats(<?php echo $member['user_id']; ?>)">
                        <div class="member-avatar">
                            <?php if ($member['profile_picture']): ?>
                                <img src="<?php echo htmlspecialchars($member['profile_picture']); ?>" alt="<?php echo htmlspecialchars($member['full_name']); ?>">
                            <?php else: ?>
                                <div class="avatar-initial" style="background-color: <?php echo $memberColor; ?>">
                                    <?php echo $member['initial']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="member-info">
                            <div class="member-name"><?php echo htmlspecialchars($member['full_name']); ?></div>
                            <div class="member-tasks">
                                <?php if ($member['total_tasks'] > 0): ?>
                                    <span class="task-count"><?php echo $member['completed_tasks']; ?> tareas (<?php echo $member['contribution_percentage']; ?>%)</span>
                                    <span class="contribution-dot" style="background-color: <?php echo $memberColor; ?>"></span>
                                <?php else: ?>
                                    <span class="task-count">Sin tareas asignadas</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; 
                endif; ?>
            </div>
        </section>
        
        <!-- Acciones Rápidas -->
        <section class="quick-actions-minimal">
            <h3>Acciones Rápidas</h3>
            <div class="actions-grid">
                <a href="?route=clan_leader/members" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-content">
                        <h4>Gestionar Miembros</h4>
                        <p>Agregar o remover miembros del clan</p>
                    </div>
                </a>
                
                <a href="?route=clan_leader/projects" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="action-content">
                        <h4>Gestionar Proyectos</h4>
                        <p>Crear y administrar proyectos del clan</p>
                    </div>
                </a>
                
                <a href="?route=clan_leader/tasks" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="action-content">
                        <h4>Gestionar Tareas</h4>
                        <p>Asignar y supervisar tareas</p>
                    </div>
                </a>
                
                <a href="?route=clan_leader/kpi-dashboard" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-content">
                        <h4>Dashboard KPI</h4>
                        <p>Ver métricas y rendimiento del clan</p>
                    </div>
                </a>
                
                <a href="?route=clan_leader/collaborator-availability" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-content">
                        <h4>Disponibilidad</h4>
                        <p>Ver disponibilidad de colaboradores</p>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- Resumen de Actividad -->
        <section class="activity-summary-minimal">
            <h3>Resumen de Actividad</h3>
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-header">
                        <h4>Miembros Activos</h4>
                        <span class="summary-value"><?php echo $userStats['active_members']; ?> / <?php echo $userStats['total_members']; ?></span>
                    </div>
                    <div class="summary-progress">
                        <div class="progress-bar-small">
                            <div class="progress-fill-small" style="width: <?php echo $userStats['total_members'] > 0 ? ($userStats['active_members'] / $userStats['total_members']) * 100 : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="summary-header">
                        <h4>Proyectos Activos</h4>
                        <span class="summary-value"><?php echo $projectStats['active_projects']; ?> / <?php echo $projectStats['total_projects']; ?></span>
                    </div>
                    <div class="summary-progress">
                        <div class="progress-bar-small">
                            <div class="progress-fill-small" style="width: <?php echo $projectStats['total_projects'] > 0 ? ($projectStats['active_projects'] / $projectStats['total_projects']) * 100 : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="summary-header">
                        <h4>Proyectos con KPI</h4>
                        <span class="summary-value"><?php echo $projectStats['kpi_projects']; ?> / <?php echo $projectStats['total_projects']; ?></span>
                    </div>
                    <div class="summary-progress">
                        <div class="progress-bar-small">
                            <div class="progress-fill-small" style="width: <?php echo $projectStats['total_projects'] > 0 ? ($projectStats['kpi_projects'] / $projectStats['total_projects']) * 100 : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        </div> <!-- Fin del contenedor hideable-sections -->
    </div>
</div>

<!-- Modal de Estadísticas de Usuario -->
<div id="userStatsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="userStatsTitle">Estadísticas del Usuario</h2>
            <span class="close" onclick="closeUserStatsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="userStatsContent">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Tarea Personal -->
<div id="addTaskModal" class="modal-overlay">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>
                <i class="fas fa-plus-circle"></i> 
                Agregar Nueva Tarea Personal
            </h3>
            <button class="modal-close" onclick="closeAddTaskModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="addTaskForm" class="modal-form">
            <div class="form-group">
                <label for="taskName">Nombre de la Tarea *</label>
                <input type="text" id="taskName" name="task_name" required 
                       placeholder="Escribe el nombre de la tarea">
            </div>
            
            <div class="form-group">
                <label for="taskDescription">Descripción</label>
                <textarea id="taskDescription" name="description" rows="3" 
                          placeholder="Describe la tarea (opcional)"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="priority">Prioridad:</label>
                    <select name="priority" id="priority" required>
                        <option value="low">Baja</option>
                        <option value="medium" selected>Media</option>
                        <option value="high">Alta</option>
                        <option value="critical">Crítica</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="taskDueDate">Fecha de Vencimiento</label>
                    <input type="date" id="taskDueDate" name="due_date" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="taskStatus">Estado</label>
                <select name="status" id="taskStatus" required>
                    <option value="pending" selected>Pendiente</option>
                    <option value="in_progress">En Progreso</option>
                    <option value="completed">Completada</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeAddTaskModal()">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Crear Tarea
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Función para cambiar el estado de una tarea
function toggleTaskStatus(taskId, isChecked) {
    console.log('Cambiando estado de tarea:', taskId, 'a:', isChecked);
    
    // Mostrar indicador de carga
    const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
    if (taskCard) {
        taskCard.style.opacity = '0.7';
    }
    
    // Enviar petición AJAX para cambiar el estado
    fetch('?route=clan_leader/toggle-task-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'taskId=' + taskId + '&isCompleted=' + (isChecked ? '1' : '0')
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Estado de tarea actualizado exitosamente');
            
            // Si la tarea se marcó como completada, removerla del tablero
            if (isChecked) {
                if (taskCard) {
                    // Agregar animación de desvanecimiento
                    taskCard.style.transition = 'all 0.5s ease-out';
                    taskCard.style.opacity = '0';
                    taskCard.style.transform = 'scale(0.8)';
                    
                    // Remover la tarea del DOM después de la animación
                    setTimeout(() => {
                        taskCard.remove();
                        
                        // Actualizar contadores de tareas por columna
                        updateColumnCounts();
                    }, 500);
                }
            } else {
                // Si se desmarcó, restaurar el estilo normal
                if (taskCard) {
                    taskCard.style.opacity = '1';
                    taskCard.style.textDecoration = 'none';
                    taskCard.style.transform = 'scale(1)';
                }
            }
            
            // Mostrar notificación de éxito
            showNotification('Estado de tarea actualizado', 'success');
        } else {
            console.error('Error al actualizar estado de tarea:', data.message);
            showNotification('Error al actualizar estado de tarea: ' + data.message, 'error');
            
            // Revertir el checkbox si hubo error
            const checkbox = document.querySelector(`[data-task-id="${taskId}"] input[type="checkbox"]`);
            if (checkbox) {
                checkbox.checked = !isChecked;
            }
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
        showNotification('Error al actualizar estado de tarea', 'error');
        
        // Revertir el checkbox si hubo error
        const checkbox = document.querySelector(`[data-task-id="${taskId}"] input[type="checkbox"]`);
        if (checkbox) {
            checkbox.checked = !isChecked;
        }
    })
    .finally(() => {
        // Restaurar opacidad normal
        if (taskCard) {
            taskCard.style.opacity = '1';
        }
    });
}

// Función para actualizar contadores de tareas por columna
function updateColumnCounts() {
    const columns = ['urgent', 'this-week', 'next-week', 'later'];
    
    columns.forEach(columnId => {
        const column = document.getElementById(`kanban-${columnId}`);
        if (column) {
            const taskCount = column.querySelectorAll('.task-card').length;
            const countElement = column.querySelector('.column-header .task-count');
            if (countElement) {
                countElement.textContent = `(${taskCount})`;
            }
        }
    });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Agregar estilos básicos
    const styles = {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '12px 20px',
        borderRadius: '6px',
        color: 'white',
        fontWeight: '500',
        zIndex: '10000',
        opacity: '0',
        transform: 'translateX(100%)',
        transition: 'all 0.3s ease-out'
    };
    
    // Aplicar estilos
    Object.assign(notification.style, styles);
    
    // Estilos según el tipo
    if (type === 'success') {
        notification.style.backgroundColor = '#10b981';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#ef4444';
    } else {
        notification.style.backgroundColor = '#3b82f6';
    }
    
    // Agregar al DOM
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Funciones para el modal de agregar tarea
function openAddTaskModal() {
    const modal = document.getElementById('addTaskModal');
    modal.style.display = 'flex';
    
    // Establecer fecha mínima como hoy
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('taskDueDate').min = today;
    
    // Limpiar formulario
    document.getElementById('addTaskForm').reset();
    
    // Enfocar en el primer campo
    setTimeout(() => {
        document.getElementById('taskName').focus();
    }, 100);
}

function closeAddTaskModal() {
    const modal = document.getElementById('addTaskModal');
    modal.style.display = 'none';
}

    // Función para crear tarea personal
    function createPersonalTask() {
        const form = document.getElementById('addTaskForm');
        const formData = new FormData(form);
        
        // Agregar campos adicionales para tarea personal
        formData.append('route', 'clan_leader/create-personal-task');
        formData.append('user_id', '<?php echo $user['user_id'] ?? 0; ?>');
        
        // Debug: mostrar datos que se van a enviar
        console.log('=== DEBUG: Datos a enviar ===');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        console.log('User ID desde PHP: <?php echo $user['user_id'] ?? 0; ?>');
        console.log('=== FIN DEBUG ===');
        
        // Mostrar estado de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
        submitBtn.disabled = true;
    
    fetch('?route=clan_leader/create-personal-task', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('La respuesta del servidor no es JSON válido. Status: ' + response.status);
        }
        
        return response.text().then(text => {
            console.log('Response text:', text);
            try {
                return JSON.parse(text);
            } catch (parseError) {
                console.error('Error parsing JSON:', parseError);
                console.error('Raw response:', text);
                throw new Error('Respuesta del servidor no es JSON válido: ' + text.substring(0, 200));
            }
        });
    })
    .then(data => {
        console.log('Parsed data:', data);
        if (data.success) {
            showNotification('Tarea creada exitosamente', 'success');
            closeAddTaskModal();
            
            // Recargar la página para mostrar la nueva tarea
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Error al crear la tarea', 'error');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        showNotification('Error: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
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
        notification.style.background = '#10b981';
    } else if (type === 'error') {
        notification.style.background = '#ef4444';
    } else {
        notification.style.background = '#3b82f6';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Función para ocultar/mostrar secciones
function toggleSections() {
    const hideableSections = document.getElementById('hideable-sections');
    const toggleBtn = document.getElementById('toggleSectionsBtn');
    const icon = toggleBtn.querySelector('i');
    const text = toggleBtn.querySelector('span');
    
    if (hideableSections.style.display === 'none') {
        // Mostrar secciones
        hideableSections.style.display = 'block';
        icon.className = 'fas fa-eye-slash';
        text.textContent = 'Ocultar Secciones';
        localStorage.setItem('sectionsVisible', 'true');
    } else {
        // Ocultar secciones
        hideableSections.style.display = 'none';
        icon.className = 'fas fa-eye';
        text.textContent = 'Mostrar Secciones';
        localStorage.setItem('sectionsVisible', 'false');
    }
}

// Inicializar el estado de las secciones al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const hideableSections = document.getElementById('hideable-sections');
    const toggleBtn = document.getElementById('toggleSectionsBtn');
    const icon = toggleBtn.querySelector('i');
    const text = toggleBtn.querySelector('span');
    
    // Por defecto las secciones están ocultas (como se especificó)
    const sectionsVisible = localStorage.getItem('sectionsVisible') === 'true';
    
    if (sectionsVisible) {
        hideableSections.style.display = 'block';
        icon.className = 'fas fa-eye-slash';
        text.textContent = 'Ocultar Secciones';
    } else {
        hideableSections.style.display = 'none';
        icon.className = 'fas fa-eye';
        text.textContent = 'Mostrar Secciones';
    }
    
    // Event listeners para el modal de agregar tarea
    const addTaskModal = document.getElementById('addTaskModal');
    
    // Cerrar modal al hacer click fuera de él
    addTaskModal.addEventListener('click', function(e) {
        if (e.target === addTaskModal) {
            closeAddTaskModal();
        }
    });
    
    // Manejar envío del formulario
    document.getElementById('addTaskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createPersonalTask();
    });
});

// Estilos para animaciones de notificaciones
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Funciones específicas para el dashboard del clan leader
document.addEventListener('DOMContentLoaded', function() {
    // Inicialización del dashboard compacto
    console.log('Dashboard compacto cargado');
});
</script>

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