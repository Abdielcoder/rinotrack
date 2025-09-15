<?php
// Verificar que tenemos los datos necesarios
if (!isset($project) || !isset($tasks)) {
    echo '<div style="padding: 20px; text-align: center;">Error: Datos del proyecto no disponibles</div>';
    return;
}

// Guardar el contenido en una variable
ob_start();
?>

<!-- Cargar CSS de rediseño -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/clan-leader-redesign.css">

<div class="clan-leader-tasks-container">
    <!-- Header Mejorado -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title"><?= htmlspecialchars($project['project_name']) ?></h1>
                <p class="page-subtitle">
                    <?php if (!empty($project['description'])): ?>
                        <?= htmlspecialchars($project['description']) ?>
                    <?php else: ?>
                        Gestión de tareas del proyecto
                    <?php endif; ?>
                </p>
            </div>
            <div class="header-actions">
                <a href="?route=clan_leader/tasks" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Todas las Tareas
                </a>
                <a href="?route=clan_leader/projects" class="btn-back">
                    <i class="fas fa-folder"></i>
                    Proyectos
                </a>
                <a href="?route=clan_leader/tasks&action=create&project_id=<?= $project['project_id'] ?>" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Nueva Tarea
                </a>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Estadísticas del Proyecto -->
        <div class="project-stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= count($tasks) ?></div>
                    <div class="stat-label">TOTAL TAREAS</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= count(array_filter($tasks, function($t) { return $t['status'] === 'completed'; })) ?></div>
                    <div class="stat-label">COMPLETADAS</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon in-progress">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= count(array_filter($tasks, function($t) { return $t['status'] === 'in_progress'; })) ?></div>
                    <div class="stat-label">EN PROGRESO</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= count(array_filter($tasks, function($t) { return $t['status'] === 'pending'; })) ?></div>
                    <div class="stat-label">PENDIENTES</div>
                </div>
            </div>
        </div>

        <div class="all-tasks-section">
            <div class="section-header">
                <h2 class="section-title">Tareas del Proyecto</h2>
            </div>
            
            <!-- Filtros y búsqueda -->
            <div class="filters-container">
                <div class="filters-header">
                    <form method="GET" action="?route=clan_leader/tasks&project_id=<?= $project['project_id'] ?>" class="filters-form">
                        <input type="hidden" name="route" value="clan_leader/tasks">
                        <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
                        
                        <!-- Filtros -->
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="statusFilter">Estado:</label>
                                <select name="status_filter" id="statusFilter">
                                    <option value="">Todos</option>
                                    <option value="pending" <?= (($_GET['status_filter'] ?? '') === 'pending') ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="in_progress" <?= (($_GET['status_filter'] ?? '') === 'in_progress') ? 'selected' : '' ?>>En Progreso</option>
                                    <option value="completed" <?= (($_GET['status_filter'] ?? '') === 'completed') ? 'selected' : '' ?>>Completado</option>
                                </select>
                            </div>
                            
                            <div class="filter-item">
                                <label for="perPage">Mostrar:</label>
                                <select name="per_page" id="perPage">
                                    <option value="5" <?= (($_GET['per_page'] ?? '5') === '5') ? 'selected' : '' ?>>5 por página</option>
                                    <option value="10" <?= (($_GET['per_page'] ?? '5') === '10') ? 'selected' : '' ?>>10 por página</option>
                                    <option value="25" <?= (($_GET['per_page'] ?? '5') === '25') ? 'selected' : '' ?>>25 por página</option>
                                    <option value="50" <?= (($_GET['per_page'] ?? '5') === '50') ? 'selected' : '' ?>>50 por página</option>
                                </select>
                            </div>
                            
                            <div class="search-container">
                                <div class="search-input-wrapper">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" 
                                           name="search" 
                                           value="<?= htmlspecialchars($search ?? '') ?>"
                                           placeholder="Buscar tareas..."
                                           class="search-input"
                                           id="searchInput">
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="filter-actions">
                                <button type="button" class="btn-apply-filters" onclick="applyFilters()">
                                    <i class="fas fa-filter"></i>
                                    Aplicar Filtros
                                </button>
                                <button type="button" class="btn-reset-filters" onclick="resetFilters()">
                                    <i class="fas fa-undo"></i>
                                    Resetear Filtros
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Área de selección múltiple -->
            <div id="bulk-actions-area" class="bulk-actions-area" style="display: none;">
                <div class="bulk-actions-content">
                    <span id="selected-count" class="selected-count">0 tareas seleccionadas</span>
                    <button id="bulk-delete-btn" class="btn btn-danger" onclick="showBulkDeleteModal()">
                        <i class="fas fa-trash"></i> Eliminar Seleccionadas
                    </button>
                    <button class="btn btn-secondary" onclick="clearSelection()">
                        <i class="fas fa-times"></i> Limpiar Selección
                    </button>
                </div>
            </div>
            
            <!-- Tabla de Tareas -->
            <?php if (!empty($tasks)): ?>
            <div class="tasks-table-container">
                <table class="tasks-table">
                    <thead>
                        <tr>
                            <th class="th-checkbox" style="width: 100px;">
                                Completar
                            </th>
                            <th class="th-priority">Prioridad</th>
                            <th class="th-task">Tarea</th>
                            <th class="th-assigned">Asignado</th>
                            <th class="th-due-date">Fecha Límite</th>
                            <th class="th-status">Estado</th>
                            <th class="th-progress">Progreso</th>
                            <th class="th-actions">Acciones</th>
                            <th class="th-select">Seleccionar</th>
                        </tr>
                    </thead>
                    <tbody id="tasksTableBody">
                        <?php foreach ($tasks as $task): ?>
                            <?php
                            $status = $task['status'] ?? 'pending';
                            $priority = $task['priority'] ?? 'medium';
                            $progress = $status === 'completed' ? 100 : ($task['completion_percentage'] ?? 0);
                            $dueDate = $task['due_date'] ?? null;
                            $isOverdue = $dueDate && strtotime($dueDate) < time() && $status !== 'completed';
                            ?>
                            <tr class="task-row priority-<?= $priority ?> <?= $isOverdue ? 'overdue' : '' ?> <?= $status === 'completed' ? 'completed' : '' ?>" data-task-id="<?= $task['task_id'] ?>">
                                <td class="td-checkbox">
                                    <input type="checkbox" 
                                           id="task-<?= $task['task_id'] ?>" 
                                           data-task-id="<?= $task['task_id'] ?>"
                                           <?= $status === 'completed' ? 'checked' : '' ?>
                                           onchange="toggleTaskStatus('<?= $task['task_id'] ?>', this.checked)">
                                </td>
                                <td class="td-priority">
                                    <span class="priority-badge priority-<?= $priority ?>">
                                        <?= $priority === 'critical' ? 'Urgente' : ($priority === 'high' ? 'Alta' : ($priority === 'low' ? 'Baja' : 'Media')) ?>
                                    </span>
                                </td>
                                <td class="td-task">
                                    <div class="task-info">
                                        <div class="task-name" title="<?= htmlspecialchars($task['task_name']) ?>"><?= htmlspecialchars($task['task_name']) ?></div>
                                        <?php if (!empty($task['description'])): ?>
                                            <div class="task-description" title="<?= htmlspecialchars($task['description']) ?>">
                                                <?= htmlspecialchars(substr($task['description'], 0, 100)) ?><?= strlen($task['description']) > 100 ? '...' : '' ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="td-assigned">
                                    <span class="assigned-user" title="<?= htmlspecialchars($task['assigned_to_fullname'] ?? $task['all_assigned_users'] ?? '') ?>">
                                        <?= htmlspecialchars($task['assigned_to_fullname'] ?? $task['all_assigned_users'] ?? 'Sin asignar') ?>
                                    </span>
                                </td>
                                <td class="td-due-date">
                                    <?php if ($dueDate): ?>
                                        <span class="due-date <?= $isOverdue ? 'overdue' : '' ?>" title="<?= $dueDate ?>">
                                            <?= date('d/m/Y', strtotime($dueDate)) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="no-date">Sin fecha</span>
                                    <?php endif; ?>
                                </td>
                                <td class="td-status">
                                    <span class="status-badge status-<?= $status ?>">
                                        <?= $status === 'completed' ? 'Completado' : ($status === 'in_progress' ? 'En Progreso' : 'Pendiente') ?>
                                    </span>
                                </td>
                                <td class="td-progress">
                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                                        </div>
                                        <span class="progress-text"><?= $progress ?>%</span>
                                    </div>
                                </td>
                                <td class="td-actions">
                                    <div class="action-buttons">
                                        <a href="?route=clan_leader/get-task-details&task_id=<?= $task['task_id'] ?>" class="btn-action btn-view" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?route=clan_leader/task_edit&task_id=<?= $task['task_id'] ?>" class="btn-action btn-edit" title="Editar tarea">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn-action btn-delete" onclick="deleteTask(<?= $task['task_id'] ?>)" title="Eliminar tarea">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn-action btn-clone" onclick="openCloneTaskModal(<?= $task['task_id'] ?>)" title="Clonar tarea">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="td-select">
                                    <input type="checkbox" class="task-checkbox" data-task-id="<?= $task['task_id'] ?>" data-task-name="<?= htmlspecialchars($task['task_name']) ?>" onchange="updateSelection()">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <!-- Estado vacío -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>No hay tareas en este proyecto</h3>
                <p>Crea la primera tarea para comenzar a trabajar en este proyecto.</p>
                <a href="?route=clan_leader/tasks&action=create&project_id=<?= $project['project_id'] ?>" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Crear Primera Tarea
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminación múltiple -->
<div id="bulkDeleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle text-danger"></i> Confirmar Eliminación Múltiple</h3>
            <button class="modal-close" onclick="closeBulkDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>¡Atención!</strong> Esta acción no se puede deshacer.
            </div>
            <p class="modal-description">
                <strong>Se eliminarán las siguientes tareas:</strong><br>
                <span class="text-muted">Revisa cuidadosamente la lista antes de confirmar.</span>
            </p>
            <div id="tasks-to-delete" class="tasks-list">
                <!-- Las tareas seleccionadas se mostrarán aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeBulkDeleteModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="executeBulkDelete()">
                    <i class="fas fa-trash"></i> Eliminar Tareas
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para aplicar filtros
function applyFilters() {
    const form = document.querySelector('.filters-form');
    form.submit();
}

// Función para resetear filtros
function resetFilters() {
    const url = new URL(window.location);
    url.searchParams.delete('search');
    url.searchParams.delete('status_filter');
    url.searchParams.delete('per_page');
    window.location.href = url.toString();
}

// Función para actualizar la selección
function updateSelection() {
    const checkboxes = document.querySelectorAll('.task-checkbox');
    const selectedCount = document.querySelectorAll('.task-checkbox:checked').length;
    const bulkActionsArea = document.getElementById('bulk-actions-area');
    const selectedCountSpan = document.getElementById('selected-count');
    
    if (selectedCount > 0) {
        bulkActionsArea.style.display = 'block';
        selectedCountSpan.textContent = `${selectedCount} tarea${selectedCount !== 1 ? 's' : ''} seleccionada${selectedCount !== 1 ? 's' : ''}`;
    } else {
        bulkActionsArea.style.display = 'none';
    }
}

// Función para limpiar selección
function clearSelection() {
    const checkboxes = document.querySelectorAll('.task-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelection();
}

// Función para cambiar estado de tarea
function toggleTaskStatus(taskId, isChecked) {
    const status = isChecked ? 'completed' : 'pending';
    
    fetch('?route=clan_leader/simple-toggle-task', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar la fila visualmente
            const row = document.querySelector(`tr[data-task-id="${taskId}"]`);
            if (row) {
                if (isChecked) {
                    row.classList.add('completed');
                } else {
                    row.classList.remove('completed');
                }
                
                // Actualizar el badge de estado
                const statusBadge = row.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.textContent = isChecked ? 'Completado' : 'Pendiente';
                    statusBadge.className = `status-badge status-${status}`;
                }
                
                // Actualizar el progreso
                const progressFill = row.querySelector('.progress-fill');
                const progressText = row.querySelector('.progress-text');
                if (progressFill && progressText) {
                    const newProgress = isChecked ? 100 : 0;
                    progressFill.style.width = `${newProgress}%`;
                    progressText.textContent = `${newProgress}%`;
                }
            }
            
            showToast(isChecked ? 'Tarea completada' : 'Tarea marcada como pendiente', 'success');
        } else {
            showToast('Error al actualizar la tarea: ' + data.message, 'error');
            // Revertir el checkbox
            const checkbox = document.querySelector(`#task-${taskId}`);
            if (checkbox) {
                checkbox.checked = !isChecked;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
        // Revertir el checkbox
        const checkbox = document.querySelector(`#task-${taskId}`);
        if (checkbox) {
            checkbox.checked = !isChecked;
        }
    });
}

// Función para eliminar tarea individual
function deleteTask(taskId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta tarea?')) {
        fetch('?route=clan_leader/delete-task', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'task_id=' + taskId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Tarea eliminada exitosamente', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('Error al eliminar la tarea: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        });
    }
}

// Función para mostrar modal de eliminación múltiple
function showBulkDeleteModal() {
    const checkboxes = document.querySelectorAll('.task-checkbox:checked');
    const tasksList = document.getElementById('tasks-to-delete');
    
    if (checkboxes.length === 0) {
        showToast('No hay tareas seleccionadas', 'warning');
        return;
    }
    
    // Crear lista de tareas a eliminar
    let tasksHtml = '<div class="tasks-to-delete-container">';
    
    checkboxes.forEach((checkbox) => {
        const taskId = checkbox.getAttribute('data-task-id');
        const taskName = checkbox.getAttribute('data-task-name') || '';
        
        let displayText = '';
        if (taskName && taskName.trim() !== '') {
            displayText = `${taskName} (ID: ${taskId})`;
        } else {
            displayText = `Tarea ${taskId}`;
        }
        
        tasksHtml += `
            <div class="task-item">
                <div class="task-icon">
                    <i class="fas fa-tasks text-primary"></i>
                </div>
                <div class="task-info">
                    <div class="task-name">${displayText}</div>
                </div>
            </div>
        `;
    });
    tasksHtml += '</div>';
    
    tasksList.innerHTML = tasksHtml;
    
    // Mostrar modal
    const modal = document.getElementById('bulkDeleteModal');
    modal.style.display = 'flex';
}

// Función para cerrar el modal
function closeBulkDeleteModal() {
    const modal = document.getElementById('bulkDeleteModal');
    modal.style.display = 'none';
}

// Función para ejecutar la eliminación múltiple
function executeBulkDelete() {
    const checkboxes = document.querySelectorAll('.task-checkbox:checked');
    const taskIds = Array.from(checkboxes).map(checkbox => checkbox.getAttribute('data-task-id'));
    
    if (taskIds.length === 0) {
        showToast('No hay tareas seleccionadas', 'warning');
        return;
    }
    
    // Mostrar loading
    const submitBtn = document.querySelector('.btn-danger');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
    submitBtn.disabled = true;
    
    fetch('?route=clan_leader/bulk-delete-tasks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'task_ids=' + encodeURIComponent(JSON.stringify(taskIds))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeBulkDeleteModal();
            showToast('Tareas eliminadas exitosamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('Error al eliminar las tareas: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión al eliminar las tareas', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Función para mostrar notificaciones toast
function showToast(message, type = 'info') {
    // Crear toast si no existe
    let toast = document.querySelector('.toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    
    // Configurar mensaje y tipo
    toast.textContent = message;
    toast.className = `toast toast-${type} show`;
    
    // Ocultar después de 3 segundos
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Función para clonar tarea (placeholder)
function openCloneTaskModal(taskId) {
    showToast('Funcionalidad de clonación de tareas en desarrollo', 'info');
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
    const modal = document.getElementById('bulkDeleteModal');
    if (e.target === modal) {
        closeBulkDeleteModal();
    }
});

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateSelection();
});
</script>

<style>
/* Estilos para las tarjetas de estadísticas del proyecto */
.project-stats-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.stat-card {
    flex: 1;
    min-width: 200px;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
    position: relative;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    background: #3b82f6;
    flex-shrink: 0;
}

.stat-icon.completed {
    background: #10b981;
}

.stat-icon.in-progress {
    background: #3b82f6;
}

.stat-icon.pending {
    background: #f59e0b;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive para las tarjetas */
@media (max-width: 768px) {
    .project-stats-cards {
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-card {
        min-width: auto;
    }
    
    .stat-value {
        font-size: 28px;
    }
}

@media (max-width: 480px) {
    .stat-card {
        padding: 15px;
        gap: 12px;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .stat-value {
        font-size: 24px;
    }
    
    .stat-label {
        font-size: 11px;
    }
}

/* Estilos para la tabla de tareas */
.tasks-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-top: 20px;
}

.tasks-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.tasks-table th {
    background: #f8fafc;
    padding: 1rem 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tasks-table td {
    padding: 0.875rem 0.75rem;
    vertical-align: top;
    border-right: 1px solid #f3f4f6;
    border-bottom: 1px solid #f3f4f6;
    background: white;
}

.tasks-table td:last-child {
    border-right: none;
}

/* Columnas específicas */
.th-actions, .td-actions {
    width: 100px;
    text-align: center;
    background: white !important;
}

.th-checkbox, .td-select {
    width: 50px;
    text-align: center;
}

.th-priority, .td-priority {
    width: 100px;
}

.th-status, .td-status {
    width: 120px;
}

.th-progress, .td-progress {
    width: 120px;
}

.th-due-date, .td-due-date {
    width: 120px;
}

/* Botones de acción */
.action-buttons {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-view {
    background: #e0f2fe;
    color: #0277bd;
}

.btn-view:hover {
    background: #b3e5fc;
    color: #01579b;
}

.btn-edit {
    background: #f3e5f5;
    color: #7b1fa2;
}

.btn-edit:hover {
    background: #e1bee7;
    color: #4a148c;
}

.btn-delete {
    background: #ffebee;
    color: #c62828;
}

.btn-delete:hover {
    background: #ffcdd2;
    color: #b71c1c;
}

.btn-clone {
    background: #e8f5e8;
    color: #2e7d32;
}

.btn-clone:hover {
    background: #c8e6c9;
    color: #1b5e20;
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
