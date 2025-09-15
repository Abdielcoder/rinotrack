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

<div class="clan-leader-tasks minimal">
    <!-- Header Minimalista Consistente -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <div class="clan-icon-large">📋</div>
                <h1><?= htmlspecialchars($project['project_name']) ?></h1>
                <span class="subtitle">Tareas del proyecto</span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=clan_leader/tasks" class="btn-minimal secondary">
                    <i class="fas fa-arrow-left"></i>
                    Todas las Tareas
                </a>
                <a href="?route=clan_leader/projects" class="btn-minimal secondary">
                    <i class="fas fa-folder"></i>
                    Proyectos
                </a>
                <a href="?route=clan_leader/tasks&action=create&project_id=<?= $project['project_id'] ?>" class="btn-minimal primary">
                    <i class="fas fa-plus"></i>
                    Nueva Tarea
                </a>
            </div>
        </div>
        
        <!-- Información del proyecto -->
        <div class="project-info-minimal">
            <?php if (!empty($project['description'])): ?>
                <p class="project-description"><?= htmlspecialchars($project['description']) ?></p>
            <?php endif; ?>
            
            <div class="project-stats-minimal">
                <div class="stat-item-minimal">
                    <span class="stat-number"><?= count($tasks) ?></span>
                    <span class="stat-label">Total Tareas</span>
                </div>
                <div class="stat-item-minimal">
                    <span class="stat-number"><?= count(array_filter($tasks, function($t) { return $t['status'] === 'completed'; })) ?></span>
                    <span class="stat-label">Completadas</span>
                </div>
                <div class="stat-item-minimal">
                    <span class="stat-number"><?= count(array_filter($tasks, function($t) { return $t['status'] === 'in_progress'; })) ?></span>
                    <span class="stat-label">En Progreso</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Filtros y búsqueda -->
    <div class="filters-section-minimal">
        <form method="GET" action="?route=clan_leader/tasks&project_id=<?= $project['project_id'] ?>" class="filters-form-minimal">
            <input type="hidden" name="route" value="clan_leader/tasks">
            <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
            
            <div class="filters-row-minimal">
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

    <!-- Contenido principal -->
    <div class="content-minimal">
        <?php if (!empty($tasks)): ?>
            <!-- Tabla de tareas -->
            <div class="tasks-section-minimal">
                <div class="tasks-header-minimal">
                    <h2>Tareas del Proyecto</h2>
                    <div class="tasks-actions-minimal">
                        <button class="btn-minimal secondary" onclick="selectAllTasks()">
                            <i class="fas fa-check-square"></i>
                            Seleccionar Todas
                        </button>
                        <button class="btn-minimal danger" onclick="bulkDeleteTasks()" id="bulkDeleteBtn" style="display: none;">
                            <i class="fas fa-trash"></i>
                            Eliminar Seleccionadas
                        </button>
                    </div>
                </div>

                <!-- Tabla de Tareas del Proyecto -->
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
                                $progress = $task['completion_percentage'] ?? 0;
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
                                        <span class="assigned-users" title="<?= htmlspecialchars($task['assigned_to_fullname'] ?? $task['all_assigned_users'] ?? '') ?>">
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
            </div>
        <?php else: ?>
            <!-- Estado vacío -->
            <div class="empty-state-minimal">
                <div class="empty-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3>No hay tareas en este proyecto</h3>
                <p>Crea la primera tarea para comenzar a trabajar en este proyecto.</p>
                <a href="?route=clan_leader/tasks&action=create&project_id=<?= $project['project_id'] ?>" class="btn-minimal primary">
                    <i class="fas fa-plus"></i>
                    Crear Primera Tarea
                </a>
            </div>
        <?php endif; ?>
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

<style>
/* Estilos específicos para la vista de tareas de proyecto */
.project-info-minimal {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 24px;
    margin: 20px 0;
    color: white;
}

.project-description {
    font-size: 16px;
    margin-bottom: 16px;
    opacity: 0.9;
}

.project-stats-minimal {
    display: flex;
    gap: 32px;
    flex-wrap: wrap;
}

.stat-item-minimal {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Tabla de Tareas - ESTILOS EXACTOS DE LA PÁGINA PRINCIPAL */
.tasks-table-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.tasks-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.tasks-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e5e7eb;
}

.tasks-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tasks-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.tasks-table tbody tr {
    transition: all 0.2s ease;
}

.tasks-table tbody tr:hover {
    background: #f9fafb;
}

.tasks-table tbody tr.completed {
    opacity: 0.7;
    background: #f9fafb;
}

.tasks-table tbody tr.completed .task-name {
    text-decoration: line-through;
    color: #6b7280;
}

.tasks-table tbody tr.overdue {
    background-color: #fef2f2;
    border-left: 4px solid #dc2626;
}

.tasks-table tbody tr.overdue:hover {
    background-color: #fee2e2;
}

.tasks-table tbody tr.priority-critical {
    border-left: 4px solid #dc2626;
}

.tasks-table tbody tr.priority-high {
    border-left: 4px solid #ea580c;
}

.tasks-table tbody tr.priority-medium {
    border-left: 4px solid #d97706;
}

.tasks-table tbody tr.priority-low {
    border-left: 4px solid #059669;
}

/* Columnas específicas */
.th-priority, .td-priority {
    width: 100px;
    text-align: center;
}

.th-task, .td-task {
    width: 25%;
    min-width: 200px;
}

.th-assigned, .td-assigned {
    width: 12%;
    min-width: 100px;
}

.th-due-date, .td-due-date {
    width: 15%;
    min-width: 140px;
}

.th-status, .td-status {
    width: 100px;
    text-align: center;
}

.th-progress, .td-progress {
    width: 200px;
    text-align: center;
}

.th-checkbox, .td-checkbox {
    width: 100px;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
}

.th-actions, .td-actions {
    width: 100px;
    text-align: center;
}

.th-select, .td-select {
    width: 80px;
    text-align: center;
    padding: 8px;
}

/* Estilos de contenido */
.task-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.task-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9rem;
}

.task-description {
    font-size: 0.8rem;
    color: #6b7280;
    line-height: 1.4;
}

.priority-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.priority-badge.priority-critical {
    background: #fef2f2;
    color: #dc2626;
}

.priority-badge.priority-high {
    background: #fff7ed;
    color: #ea580c;
}

.priority-badge.priority-medium {
    background: #fffbeb;
    color: #d97706;
}

.priority-badge.priority-low {
    background: #f0fdf4;
    color: #059669;
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.status-completed {
    background: #f0fdf4;
    color: #059669;
}

.status-badge.status-in_progress {
    background: #dbeafe;
    color: #2563eb;
}

.status-badge.status-pending {
    background: #fffbeb;
    color: #d97706;
}

.progress-container {
    display: flex;
    align-items: center;
    gap: 8px;
}

.progress-bar {
    flex: 1;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: #374151;
    min-width: 35px;
}

.action-buttons {
    display: flex;
    gap: 4px;
    justify-content: center;
}

.btn-action {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-action.btn-view {
    background: #dbeafe;
    color: #2563eb;
}

.btn-action.btn-edit {
    background: #fef3c7;
    color: #d97706;
}

.btn-action.btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-action.btn-clone {
    background: #f3f4f6;
    color: #6b7280;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Checkboxes de selección */
.task-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #d1d5db;
    background: #ffffff;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    position: relative;
    transition: all 0.2s ease;
}

.task-checkbox:hover {
    border-color: #6b7280;
    transform: scale(1.05);
}

.task-checkbox:checked {
    background: #ffffff;
    border-color: #6b7280;
}

.task-checkbox:checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #6b7280;
    font-size: 12px;
    font-weight: bold;
}

/* Checkbox de completar - ESTILO ESPECÍFICO */
.td-checkbox input[type="checkbox"] {
    display: none !important;
}

.td-checkbox::after {
    content: "Completar" !important;
    display: block !important;
    padding: 4px 8px !important;
    border-radius: 4px !important;
    background: #f3f4f6 !important;
    border: 1px solid #d1d5db !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #6b7280 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}

.td-checkbox:hover::after {
    background: #e5e7eb !important;
    border-color: #9ca3af !important;
}

/* Responsive */
@media (max-width: 1024px) {
    .tasks-table-container {
        overflow-x: auto;
    }
    
    .tasks-table {
        min-width: 900px;
    }
}

@media (max-width: 768px) {
    .tasks-table th,
    .tasks-table td {
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .task-description {
        display: none;
    }
}

/* Estilos para el modal de eliminación múltiple */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 18px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
}

.modal-body {
    padding: 24px;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
}

.modal-description {
    font-size: 14px;
    line-height: 1.5;
    margin: 16px 0;
    color: #374151;
}

.text-muted {
    color: #6b7280;
    font-size: 13px;
}

.modal-footer {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

/* Estilos específicos para el contenedor de tareas en el modal de eliminación múltiple */
#bulkDeleteModal #tasks-to-delete {
    max-height: 850px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    background: #f9fafb;
    margin: 16px 0;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
}

/* Estilos específicos para los elementos de tarea en el modal de eliminación */
#bulkDeleteModal .task-item {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 12px 16px !important;
    margin-bottom: 10px !important;
    background: #ffffff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
}

#bulkDeleteModal .task-item:hover {
    background: #f9fafb !important;
    border-color: #d1d5db !important;
}

#bulkDeleteModal .task-item .task-icon {
    color: #3b82f6 !important;
    font-size: 18px !important;
}

#bulkDeleteModal .task-item .task-info {
    flex: 1 !important;
}

#bulkDeleteModal .task-item .task-name {
    color: #1f2937 !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
}

/* Scrollbar personalizado para el contenedor de tareas */
#bulkDeleteModal #tasks-to-delete::-webkit-scrollbar {
    width: 6px;
}

#bulkDeleteModal #tasks-to-delete::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

#bulkDeleteModal #tasks-to-delete::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#bulkDeleteModal #tasks-to-delete::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Responsive */
@media (max-width: 768px) {
    .project-stats-minimal {
        gap: 16px;
    }
    
    .stat-number {
        font-size: 24px;
    }
    
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
}
</style>

<script>
// Función para aplicar filtros
function applyFilters() {
    const form = document.querySelector('.filters-form-minimal');
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

// Función para seleccionar todas las tareas
function selectAllTasks() {
    const checkboxes = document.querySelectorAll('.task-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    selectAllCheckbox.checked = true;
    
    updateSelection();
}

// Función para toggle de selección de todas las tareas
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.task-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelection();
}

// Función para actualizar la selección
function updateSelection() {
    const checkboxes = document.querySelectorAll('.task-checkbox');
    const selectedCount = document.querySelectorAll('.task-checkbox:checked').length;
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    if (selectedCount > 0) {
        bulkDeleteBtn.style.display = 'inline-flex';
        bulkDeleteBtn.innerHTML = `<i class="fas fa-trash"></i> Eliminar ${selectedCount} Tarea${selectedCount !== 1 ? 's' : ''}`;
    } else {
        bulkDeleteBtn.style.display = 'none';
    }
    
    // Actualizar el estado del checkbox "Seleccionar todas"
    selectAllCheckbox.checked = selectedCount === checkboxes.length;
    selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < checkboxes.length;
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

// Función para eliminar tareas múltiples
function bulkDeleteTasks() {
    const checkboxes = document.querySelectorAll('.task-checkbox:checked');
    const tasksList = document.getElementById('tasks-to-delete');
    
    if (checkboxes.length === 0) {
        showToast('No hay tareas seleccionadas', 'warning');
        return;
    }
    
    // Crear lista de tareas a eliminar
    let tasksHtml = '<div class="tasks-to-delete-container">';
    console.log('🔍 Debug: checkboxes encontrados:', checkboxes.length);
    
    checkboxes.forEach((checkbox, index) => {
        const taskId = checkbox.getAttribute('data-task-id');
        // SOLUCIÓN DEFINITIVA: Obtener el nombre directamente del atributo data-task-name
        const taskName = checkbox.getAttribute('data-task-name') || '';
        
        console.log(`🔍 Debug: Procesando tarea ${index + 1}`);
        console.log(`🔍 Debug: taskId:`, taskId);
        console.log(`🔍 Debug: taskName del atributo:`, taskName);
        
        // Crear el texto final: Nombre + ID
        let displayText = '';
        if (taskName && taskName.trim() !== '') {
            displayText = `${taskName} (ID: ${taskId})`;
        } else {
            displayText = `Tarea ${taskId}`;
        }
        
        console.log(`🔍 Debug: displayText final:`, displayText);
        
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
    
    console.log('🔍 Debug: HTML generado:', tasksHtml);
    console.log('🔍 Debug: tasksList element:', tasksList);
    
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
