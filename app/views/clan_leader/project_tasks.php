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
                                <select name="status_filter" id="statusFilter" onchange="filterTasks()">
                                    <option value="">Todos</option>
                                    <option value="pending" <?= (($_GET['status_filter'] ?? '') === 'pending') ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="in_progress" <?= (($_GET['status_filter'] ?? '') === 'in_progress') ? 'selected' : '' ?>>En Progreso</option>
                                    <option value="completed" <?= (($_GET['status_filter'] ?? '') === 'completed') ? 'selected' : '' ?>>Completado</option>
                                </select>
                            </div>
                            
                            <div class="filter-item">
                                <label for="perPage">Mostrar:</label>
                                <select name="per_page" id="perPage" onchange="filterTasks()">
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
                                           id="searchInput"
                                           onkeyup="filterTasks()">
                                </div>
                            </div>
                            
                            <!-- Botón de resetear -->
                            <div class="filter-actions">
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
                    <div class="selection-indicator">
                        <div class="selection-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span id="selected-count" class="selected-count">0 tareas seleccionadas</span>
                    </div>
                    <div class="bulk-actions-buttons">
                        <button id="bulk-delete-btn" class="btn-delete-selected" onclick="showBulkDeleteModal()">
                            <i class="fas fa-trash"></i>
                            <span>Eliminar Seleccionadas</span>
                        </button>
                        <button class="btn-clear-selection" onclick="clearSelection()">
                            <i class="fas fa-times"></i>
                            <span>Limpiar Selección</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de Tareas -->
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
                        <?php if (!empty($tasks)): ?>
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
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">
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
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
// Función para filtrar tareas en tiempo real
function filterTasks() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const perPage = parseInt(document.getElementById('perPage').value);
    
    const rows = document.querySelectorAll('.task-row');
    let visibleCount = 0;
    let completedCount = 0;
    let inProgressCount = 0;
    let pendingCount = 0;
    
    // Primero, filtrar todas las tareas
    const filteredRows = [];
    
    rows.forEach((row) => {
        const taskName = row.querySelector('.task-name').textContent.toLowerCase();
        const taskDescription = row.querySelector('.task-description') ? row.querySelector('.task-description').textContent.toLowerCase() : '';
        const statusBadge = row.querySelector('.status-badge');
        const status = statusBadge ? statusBadge.textContent.toLowerCase() : '';
        
        // Aplicar filtros
        const matchesSearch = !searchTerm || taskName.includes(searchTerm) || taskDescription.includes(searchTerm);
        
        let matchesStatus = true;
        if (statusFilter) {
            switch (statusFilter) {
                case 'pending':
                    matchesStatus = status.includes('pendiente');
                    break;
                case 'in_progress':
                    matchesStatus = status.includes('progreso');
                    break;
                case 'completed':
                    matchesStatus = status.includes('completado');
                    break;
            }
        }
        
        if (matchesSearch && matchesStatus) {
            filteredRows.push(row);
            visibleCount++;
            
            // Contar por estado para las estadísticas
            if (status.includes('completado')) {
                completedCount++;
            } else if (status.includes('progreso')) {
                inProgressCount++;
            } else {
                pendingCount++;
            }
        }
    });
    
    // Aplicar paginación
    const currentPage = 1; // Por ahora siempre página 1, se puede expandir después
    const startIndex = (currentPage - 1) * perPage;
    const endIndex = startIndex + perPage;
    
    rows.forEach((row, index) => {
        if (filteredRows.includes(row)) {
            const filteredIndex = filteredRows.indexOf(row);
            if (filteredIndex >= startIndex && filteredIndex < endIndex) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        } else {
            row.style.display = 'none';
        }
    });
    
    // Actualizar información de paginación (si existe)
    updatePaginationInfo(filteredRows.length, perPage, currentPage);
    
    // Actualizar contadores de tareas
    updateTaskCounts(visibleCount, completedCount, inProgressCount, pendingCount);
}

// Función para actualizar información de paginación
function updatePaginationInfo(totalFiltered, perPage, currentPage) {
    // Crear o actualizar indicador de resultados
    let paginationInfo = document.getElementById('pagination-info');
    if (!paginationInfo) {
        paginationInfo = document.createElement('div');
        paginationInfo.id = 'pagination-info';
        paginationInfo.className = 'pagination-info';
        
        // Insertar después de los filtros
        const filtersContainer = document.querySelector('.filters-container');
        if (filtersContainer) {
            filtersContainer.insertAdjacentElement('afterend', paginationInfo);
        }
    }
    
    const startItem = (currentPage - 1) * perPage + 1;
    const endItem = Math.min(currentPage * perPage, totalFiltered);
    
    if (totalFiltered > 0) {
        paginationInfo.innerHTML = `
            <div class="pagination-info-content">
                <span class="pagination-text">
                    Mostrando ${startItem}-${endItem} de ${totalFiltered} tareas
                </span>
            </div>
        `;
        paginationInfo.style.display = 'block';
    } else {
        paginationInfo.style.display = 'none';
    }
}

// Función para actualizar los contadores de tareas
function updateTaskCounts(total, completed, inProgress, pending) {
    // Actualizar todas las estadísticas en las tarjetas
    const statCards = document.querySelectorAll('.stat-card .stat-value');
    
    if (statCards.length >= 4) {
        // Total tareas (primera tarjeta)
        statCards[0].textContent = total;
        // Completadas (segunda tarjeta)
        statCards[1].textContent = completed;
        // En progreso (tercera tarjeta)
        statCards[2].textContent = inProgress;
        // Pendientes (cuarta tarjeta)
        statCards[3].textContent = pending;
    }
}

// Función para resetear filtros
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('perPage').value = '5';
    
    // Mostrar todas las tareas
    const rows = document.querySelectorAll('.task-row');
    let completedCount = 0;
    let inProgressCount = 0;
    let pendingCount = 0;
    
    rows.forEach((row) => {
        row.style.display = '';
        const statusBadge = row.querySelector('.status-badge');
        const status = statusBadge ? statusBadge.textContent.toLowerCase() : '';
        
        if (status.includes('completado')) {
            completedCount++;
        } else if (status.includes('progreso')) {
            inProgressCount++;
        } else {
            pendingCount++;
        }
    });
    
    // Actualizar contadores con todos los datos
    updateTaskCounts(rows.length, completedCount, inProgressCount, pendingCount);
    
    // Actualizar información de paginación
    const perPage = parseInt(document.getElementById('perPage').value);
    updatePaginationInfo(rows.length, perPage, 1);
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
    showConfirmationModal({
        title: 'Eliminar Tarea',
        message: '¿Estás seguro de que quieres eliminar esta tarea?\n\nEsta acción no se puede deshacer.',
        type: 'danger',
        confirmText: 'Eliminar',
        cancelText: 'Cancelar',
        onConfirm: () => {
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
    });
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

// Función para mostrar modal de confirmación personalizado
function showConfirmationModal(options) {
    const {
        title = 'Confirmar Acción',
        message = '¿Estás seguro de que quieres realizar esta acción?',
        type = 'warning',
        confirmText = 'Confirmar',
        cancelText = 'Cancelar',
        onConfirm = null,
        onCancel = null
    } = options;

    // Crear el HTML del modal
    const modalHTML = `
        <div class="confirmation-modal-overlay" id="confirmationModalOverlay">
            <div class="confirmation-modal" id="confirmationModal">
                <div class="confirmation-modal-header">
                    <h3 class="confirmation-modal-title">
                        <i class="fas fa-${getIconForType(type)}"></i>
                        ${title}
                    </h3>
                </div>
                <div class="confirmation-modal-body">
                    <i class="fas fa-${getIconForType(type)} confirmation-modal-icon ${type}"></i>
                    <p class="confirmation-modal-message">${message}</p>
                    <div class="confirmation-modal-actions">
                        <button class="confirmation-modal-btn cancel" id="confirmationCancelBtn">
                            <i class="fas fa-times"></i>
                            ${cancelText}
                        </button>
                        <button class="confirmation-modal-btn confirm" id="confirmationConfirmBtn">
                            <i class="fas fa-trash"></i>
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
}

// Función auxiliar para obtener el icono según el tipo
function getIconForType(type) {
    switch (type) {
        case 'danger':
            return 'exclamation-triangle';
        case 'warning':
            return 'exclamation-triangle';
        case 'info':
            return 'info-circle';
        case 'success':
            return 'check-circle';
        default:
            return 'question-circle';
    }
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
    }, 5000);
}

// Función para abrir el modal de clonación
function openCloneTaskModal(taskId) {
    console.log('═══════════════════════════════════════════════════════════════');
    console.log('🔄 FUNCIÓN openCloneTaskModal LLAMADA (project_tasks.php)');
    console.log('  Task ID recibido:', taskId);
    console.log('  Tipo de taskId:', typeof taskId);
    console.log('═══════════════════════════════════════════════════════════════');
    
    // Verificar que taskId es válido
    if (!taskId || taskId === 'undefined' || taskId === 'null') {
        console.error('❌ ERROR: taskId inválido:', taskId);
        showToast('Error: ID de tarea inválido', 'error');
        return;
    }
    
    // Mostrar indicador de carga
    const loadingToast = showToast('Cargando datos de la tarea...', 'info');
    
    const url = '?route=clan_leader/get-task-data&task_id=' + taskId;
    console.log('📡 Haciendo fetch a:', url);
    
    fetch(url)
        .then(response => {
            console.log('📡 Respuesta recibida, status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📦 Datos recibidos:', data);
            
            // Ocultar indicador de carga
            if (loadingToast) loadingToast.remove();
            
            if (data.success) {
                console.log('✅ Datos válidos, mostrando modal');
                showCloneTaskModal(data.task, data.projects);
            } else {
                console.error('❌ Error del servidor:', data.message);
                showToast('Error al cargar los datos de la tarea: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('💥 Error de conexión:', error);
            
            // Ocultar indicador de carga
            if (loadingToast) loadingToast.remove();
            
            showToast('Error de conexión al cargar los datos de la tarea', 'error');
        });
}

// Función para mostrar el modal de clonación
function showCloneTaskModal(task, projects) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content clone-task-modal">
            <div class="modal-header">
                <h3><i class="fas fa-copy"></i> Clonar Tarea</h3>
                <button class="modal-close" onclick="closeCloneTaskModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="cloneTaskForm">
                    <input type="hidden" id="originalTaskId" value="${task.task_id}">
                    
                    <div class="form-group">
                        <label for="cloneTaskName">Nombre de la tarea</label>
                        <input type="text" id="cloneTaskName" name="task_name" value="${task.task_name}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cloneTaskDescription">Descripción</label>
                        <textarea id="cloneTaskDescription" name="description" rows="4">${task.description || ''}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="cloneTaskProject">Proyecto destino</label>
                        <select id="cloneTaskProject" name="project_id" required>
                            <option value="">Seleccionar proyecto...</option>
                            ${projects.map(project => 
                                `<option value="${project.project_id}" ${project.project_id == task.project_id ? 'selected' : ''}>
                                    ${project.project_name} (${project.clan_name})
                                </option>`
                            ).join('')}
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cloneTaskPriority">Prioridad</label>
                            <select id="cloneTaskPriority" name="priority">
                                <option value="low" ${task.priority === 'low' ? 'selected' : ''}>Baja</option>
                                <option value="medium" ${task.priority === 'medium' ? 'selected' : ''}>Media</option>
                                <option value="high" ${task.priority === 'high' ? 'selected' : ''}>Alta</option>
                                <option value="critical" ${task.priority === 'critical' ? 'selected' : ''}>Crítica</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cloneTaskDueDate">Fecha límite</label>
                            <input type="date" id="cloneTaskDueDate" name="due_date" value="${task.due_date || ''}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="cloneSubtasks" name="clone_subtasks" checked>
                            Clonar también las subtareas
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeCloneTaskModal()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="cloneTask()">
                    <i class="fas fa-copy"></i> Clonar Tarea
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'flex';
}

// Función para cerrar el modal de clonación
function closeCloneTaskModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}

// Función para ejecutar la clonación
function cloneTask() {
    console.log('🔄 Iniciando proceso de clonación');
    
    const form = document.getElementById('cloneTaskForm');
    const formData = new FormData(form);
    
    // Validaciones del lado del cliente
    const taskName = document.getElementById('cloneTaskName').value.trim();
    const projectId = document.getElementById('cloneTaskProject').value;
    
    if (!taskName) {
        showToast('El nombre de la tarea es requerido', 'error');
        return;
    }
    
    if (!projectId) {
        showToast('Debes seleccionar un proyecto destino', 'error');
        return;
    }
    
    // Agregar campos adicionales
    formData.append('originalTaskId', document.getElementById('originalTaskId').value);
    formData.append('clone_subtasks', document.getElementById('cloneSubtasks').checked ? '1' : '0');
    
    console.log('📤 Enviando datos de clonación');
    
    // Deshabilitar el botón para evitar clics múltiples
    const cloneButton = document.querySelector('.btn-primary');
    const originalText = cloneButton.innerHTML;
    cloneButton.disabled = true;
    cloneButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Clonando...';
    
    fetch('?route=clan_leader/clone-task', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📡 Respuesta de clonación recibida, status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📦 Resultado de clonación:', data);
        
        // Rehabilitar el botón
        cloneButton.disabled = false;
        cloneButton.innerHTML = originalText;
        
        if (data.success) {
            console.log('✅ Tarea clonada exitosamente');
            closeCloneTaskModal();
            showToast('Tarea clonada exitosamente - ID: ' + (data.new_task_id || 'N/A'), 'success');
            setTimeout(() => location.reload(), 1500); // Recargar para mostrar los cambios
        } else {
            console.error('❌ Error al clonar:', data.message);
            showToast('Error al clonar la tarea: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('💥 Error de conexión al clonar:', error);
        
        // Rehabilitar el botón
        cloneButton.disabled = false;
        cloneButton.innerHTML = originalText;
        
        showToast('Error de conexión al clonar la tarea', 'error');
    });
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
    filterTasks(); // Aplicar filtros iniciales
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
    width: 140px;
    text-align: center;
    background: white !important;
    white-space: nowrap;
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
    flex-direction: row;
    gap: 4px;
    justify-content: center;
    align-items: center;
    flex-wrap: nowrap;
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
    flex-shrink: 0;
    margin: 0;
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

/* Estilos específicos para el botón Nueva Tarea */
.btn-create {
    color: #ffffff !important;
}

.btn-create:hover {
    color: #ffffff !important;
}

/* Filtros */
.filters-container {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.filter-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-item label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
    white-space: nowrap;
}

.filter-item select {
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: white;
    font-size: 0.9rem;
    color: #374151;
    min-width: 120px;
}

.search-container {
    flex: 1;
    min-width: 200px;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 12px;
    color: #6b7280;
    font-size: 0.9rem;
    z-index: 1;
}

.search-input {
    width: 100%;
    padding: 0.5rem 0.75rem 0.5rem 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #374151;
}

.search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-reset-filters {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #6b7280;
    color: white;
}

.btn-reset-filters:hover {
    background: #4b5563;
}

/* Estilos para información de paginación */
.pagination-info {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 16px;
    margin: 16px 0;
    display: none;
}

.pagination-info-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.pagination-text {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

/* Estilos para la barra de selección múltiple */
.bulk-actions-area {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 12px 20px;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    min-width: 400px;
    max-width: 90vw;
}

.bulk-actions-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.selection-indicator {
    display: flex;
    align-items: center;
    gap: 12px;
}

.selection-icon {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.selection-icon i {
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.selected-count {
    color: white;
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
}

.bulk-actions-buttons {
    display: flex;
    gap: 12px;
    align-items: center;
}

.btn-delete-selected {
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
}

.btn-delete-selected:hover {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
}

.btn-delete-selected i {
    font-size: 14px;
}

.btn-clear-selection {
    background: white;
    color: #374151;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-clear-selection:hover {
    background: #f9fafb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-clear-selection i {
    font-size: 14px;
    color: #6b7280;
}

/* Animación de entrada */
.bulk-actions-area {
    animation: slideUpIn 0.3s ease-out;
}

@keyframes slideUpIn {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .bulk-actions-area {
        bottom: 10px;
        left: 10px;
        right: 10px;
        transform: none;
        min-width: auto;
        max-width: none;
    }
    
    .bulk-actions-content {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .bulk-actions-buttons {
        width: 100%;
        justify-content: center;
    }
    
    .btn-delete-selected,
    .btn-clear-selection {
        flex: 1;
        justify-content: center;
    }
}

/* Estilos para el modal de confirmación personalizado */
.confirmation-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.confirmation-modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

.confirmation-modal-overlay.show .confirmation-modal {
    transform: translateY(0) scale(1);
    opacity: 1;
}

.confirmation-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 480px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    transform: translateY(20px) scale(0.95);
    opacity: 0;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.confirmation-modal-header {
    padding: 24px 24px 16px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.confirmation-modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 12px;
}

.confirmation-modal-title i {
    font-size: 20px;
    color: #dc2626;
}

.confirmation-modal-body {
    padding: 24px;
    text-align: center;
}

.confirmation-modal-icon {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
}

.confirmation-modal-icon.danger {
    color: #dc2626;
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

.confirmation-modal-message {
    font-size: 16px;
    color: #374151;
    line-height: 1.5;
    margin: 0 0 24px 0;
    white-space: pre-line;
}

.confirmation-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.confirmation-modal-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 120px;
    justify-content: center;
}

.confirmation-modal-btn.cancel {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.confirmation-modal-btn.cancel:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

.confirmation-modal-btn.confirm {
    background: #dc2626;
    color: white;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
}

.confirmation-modal-btn.confirm:hover {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
}

.confirmation-modal-btn i {
    font-size: 14px;
}

/* Responsive para el modal */
@media (max-width: 480px) {
    .confirmation-modal {
        width: 95%;
        margin: 20px;
    }
    
    .confirmation-modal-header,
    .confirmation-modal-body {
        padding: 20px;
    }
    
    .confirmation-modal-actions {
        flex-direction: column;
    }
    
    .confirmation-modal-btn {
        width: 100%;
        min-width: auto;
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
