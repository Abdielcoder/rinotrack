<?php
// Guardar el contenido en una variable
ob_start();

// Verificar que las variables existen
if (!isset($project)) {
    $project = ['project_name' => 'Proyecto', 'status' => 'active', 'description' => '', 'project_id' => 0];
}
if (!isset($tasks)) {
    $tasks = [];
}
if (!isset($user)) {
    $user = ['user_id' => 0];
}
?>

<div class="clan-member-project-tasks-container">
    <!-- Header Mejorado -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">Tareas del Proyecto</h1>
                <p class="page-subtitle"><?php echo htmlspecialchars($project['project_name'] ?? 'Proyecto'); ?></p>
            </div>
            <div class="header-actions">
                <button onclick="history.back()" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Volver Atrás
                </button>
                <?php if (isset($project['is_personal']) && $project['is_personal'] == 1): ?>
                <button class="btn-create" onclick="openCreateTaskModal()">
                    <i class="fas fa-plus"></i>
                    Nueva Tarea
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Información del Proyecto -->
        <div class="project-info-section">
            <div class="project-info-card">
                <div class="project-header">
                    <h3 class="project-name"><?= htmlspecialchars($project['project_name'] ?? 'Proyecto') ?></h3>
                    <div class="project-status status-<?= $project['status'] ?? 'active' ?>">
                        <?= ucfirst($project['status'] ?? 'Activo') ?>
                    </div>
                </div>
                
                <?php if (!empty($project['description'])): ?>
                <div class="project-description">
                    <?= htmlspecialchars($project['description']) ?>
                </div>
                <?php endif; ?>
                
                <div class="project-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?= count($tasks ?? []) ?></div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?= count(array_filter($tasks ?? [], function($t){ return ($t['status'] ?? '')==='completed'; })) ?></div>
                            <div class="stat-label">Completadas</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon progress">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?= count(array_filter($tasks ?? [], function($t){ return ($t['status'] ?? '')==='pending'; })) ?></div>
                            <div class="stat-label">Pendientes</div>
                        </div>
                    </div>
                    <?php if (!empty($project['kpi_points'])): ?>
                    <div class="stat-item">
                        <div class="stat-icon kpi">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?= number_format((float)$project['kpi_points']) ?></div>
                            <div class="stat-label">Puntos KPI</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="project-progress">
                    <?php 
                        $totalTasks = count($tasks ?? []);
                        $completedTasks = count(array_filter($tasks ?? [], function($t){ return ($t['status'] ?? '')==='completed'; }));
                        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    ?>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $progressPercentage ?>%"></div>
                    </div>
                    <span class="progress-text"><?= $progressPercentage ?>% completado</span>
                </div>
            </div>
        </div>

        <?php if (isset($tasks) && !empty($tasks)): ?>
        <!-- Tareas del Proyecto -->
        <div class="project-tasks-section">
            <div class="section-header">
                <h2 class="section-title">
                    Tareas del Proyecto
                </h2>
                <div class="section-actions">
                    <!-- Filtros -->
                    <div class="filters-container">
                        <div class="filters-form">
                            <!-- Filtro por estado -->
                            <div class="filter-group">
                                <label for="status_filter" class="filter-label">Estado:</label>
                                <select name="status_filter" id="status_filter" class="filter-select" onchange="filterTasks()">
                                    <option value="">Todos los estados</option>
                                    <option value="pending">Pendientes</option>
                                    <option value="in_progress">En Progreso</option>
                                    <option value="completed">Completadas</option>
                                </select>
                            </div>
                            
                            <!-- Filtro por prioridad -->
                            <div class="filter-group">
                                <label for="priority_filter" class="filter-label">Prioridad:</label>
                                <select name="priority_filter" id="priority_filter" class="filter-select" onchange="filterTasks()">
                                    <option value="">Todas las prioridades</option>
                                    <option value="low">Baja</option>
                                    <option value="medium">Media</option>
                                    <option value="high">Alta</option>
                                    <option value="urgent">Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tasks-table-container">
                <table class="tasks-table">
                    <thead>
                        <tr>
                            <th class="th-priority">Prioridad</th>
                            <th class="th-task">Tarea</th>
                            <th class="th-assigned">Asignado</th>
                            <th class="th-due-date">Fecha Límite</th>
                            <th class="th-status">Estado</th>
                            <th class="th-actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                        <?php 
                            $userId = (int)($user['user_id'] ?? 0);
                            $assignedIds = array_filter(explode(',', (string)($task['all_assigned_user_ids'] ?? '')));
                            $canEditTask = in_array((string)$userId, $assignedIds, true) || (int)($task['assigned_to_user_id'] ?? 0) === $userId || (int)($task['created_by_user_id'] ?? 0) === $userId;
                        ?>
                        <tr class="task-row priority-<?= $task['priority'] ?> <?= ($task['status'] === 'completed') ? 'completed' : '' ?>" data-status="<?= $task['status'] ?>" data-priority="<?= $task['priority'] ?>">
                            <td class="td-priority">
                                <span class="priority-badge priority-<?= $task['priority'] ?>">
                                    <?php 
                                    switch($task['priority']) {
                                        case 'urgent': echo 'Urgente'; break;
                                        case 'high': echo 'Alta'; break;
                                        case 'low': echo 'Baja'; break;
                                        default: echo 'Media'; break;
                                    }
                                    ?>
                                </span>
                            </td>
                            <td class="td-task">
                                <div class="task-info">
                                    <div class="task-title"><?= htmlspecialchars($task['task_name']) ?></div>
                                    <?php if (!empty($task['description'])): ?>
                                    <div class="task-description"><?= htmlspecialchars(substr($task['description'], 0, 80)) ?><?= strlen($task['description']) > 80 ? '...' : '' ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="td-assigned">
                                <?php if (!empty($task['assigned_to_fullname'])): ?>
                                <span class="assigned-user"><?= htmlspecialchars($task['assigned_to_fullname']) ?></span>
                                <?php else: ?>
                                <span class="no-assigned">Sin asignar</span>
                                <?php endif; ?>
                            </td>
                            <td class="td-due-date">
                                <?php if ($task['due_date']): ?>
                                <div class="due-date-info">
                                    <div class="due-date-text">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                    </div>
                                </div>
                                <?php else: ?>
                                <span class="no-due-date">Sin fecha</span>
                                <?php endif; ?>
                            </td>
                            <td class="td-status">
                                <?php if ($canEditTask): ?>
                                <select class="status-select" onchange="updateTaskStatus(<?= $task['task_id'] ?>, this.value)">
                                    <option value="pending" <?= ($task['status']==='pending')?'selected':'' ?>>Pendiente</option>
                                    <option value="in_progress" <?= ($task['status']==='in_progress')?'selected':'' ?>>En Progreso</option>
                                    <option value="completed" <?= ($task['status']==='completed')?'selected':'' ?>>Completada</option>
                                </select>
                                <?php else: ?>
                                <span class="status-badge status-<?= $task['status'] ?>">
                                    <?php 
                                    switch($task['status']) {
                                        case 'in_progress': echo 'En Progreso'; break;
                                        case 'completed': echo 'Completada'; break;
                                        default: echo 'Pendiente'; break;
                                    }
                                    ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="td-actions">
                                <div class="action-buttons">
                                    <a href="<?= APP_URL ?>?route=clan_member/task-details&task_id=<?= $task['task_id'] ?>" class="btn-action btn-view" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <!-- Estado vacío - No hay tareas -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <h3>No hay tareas en este proyecto</h3>
            <?php if (isset($project['is_personal']) && $project['is_personal'] == 1): ?>
            <p>Comienza creando tu primera tarea para este proyecto.</p>
            <button class="btn-create" onclick="openCreateTaskModal()">
                <i class="fas fa-plus"></i>
                Crear Primera Tarea
            </button>
            <?php else: ?>
            <p>Las tareas de este proyecto son gestionadas por el líder de clan.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal crear tarea (solo para proyectos personales) -->
<?php if (isset($project['is_personal']) && $project['is_personal'] == 1): ?>
<div class="modal create-task-modal" id="createTaskModal">
  <div class="modal-content modal-lg">
    <div class="modal-header modal-header-gradient">
      <div class="modal-title-wrap">
        <h3>Nueva Tarea</h3>
        <span class="modal-subtitle">Agrega una tarea al proyecto y define su prioridad y fecha</span>
      </div>
      <button class="modal-close" onclick="closeCreateTaskModal()">&times;</button>
    </div>
    <form id="createTaskForm" class="modal-body create-task-body">
      <input type="hidden" name="project_id" value="<?php echo (int)($project['project_id'] ?? 0); ?>" />
      <div class="form-grid">
        <div class="form-group form-span-2">
          <label>Título</label>
          <input type="text" name="task_name" placeholder="Escribe un título claro y conciso" required />
          <small class="field-help">Ejemplo: Implementar autenticación con tokens</small>
        </div>
        <div class="form-group form-span-2">
          <label>Descripción</label>
          <textarea name="description" rows="4" placeholder="Contexto, entregables y consideraciones"></textarea>
        </div>
        <div class="form-group">
          <label>Prioridad</label>
          <select name="priority">
            <option value="low">Baja</option>
            <option value="medium" selected>Media</option>
            <option value="high">Alta</option>
            <option value="urgent">Urgente</option>
          </select>
          <small class="field-help">Afecta la visibilidad en las listas</small>
        </div>
        <div class="form-group">
          <label>Fecha límite</label>
          <input type="date" name="due_date" />
          <small class="field-help">Opcional</small>
        </div>
      </div>
      <div class="form-actions">
        <button class="action-btn secondary" type="button" onclick="closeCreateTaskModal()">Cancelar</button>
        <button id="createTaskSubmitBtn" class="action-btn primary" type="submit">
          <span class="btn-text">Crear</span>
          <span class="btn-loader" aria-hidden="true"></span>
        </button>
      </div>
      <div id="createTaskErrors" class="form-errors" style="display:none;"></div>
    </form>
  </div>
</div>
<?php endif; ?>

<style>
/* Reset y Base */
.clan-member-project-tasks-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 0;
    margin: 0;
}

/* Header Mejorado */
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #374151 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
}

.header-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: white;
}

.page-subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
    font-weight: 400;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.btn-create {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    cursor: pointer;
}

.btn-create:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

.btn-back {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    cursor: pointer;
}

.btn-back:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

/* Contenido Principal */
.main-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem 2rem 1.5rem;
}

/* Información del Proyecto */
.project-info-section {
    margin-bottom: 2rem;
}

.project-info-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.project-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    flex: 1;
}

.project-status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-completed {
    background: #dbeafe;
    color: #1e40af;
}

.project-description {
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    font-size: 1rem;
}

/* Stats de Proyecto */
.project-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    background: #f3f4f6;
    color: #6b7280;
}

.stat-icon.completed {
    background: rgba(34, 197, 94, 0.1);
    color: #10b981;
}

.stat-icon.progress {
    background: rgba(30, 58, 138, 0.1);
    color: #1e3a8a;
}

.stat-icon.kpi {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* Barra de Progreso */
.project-progress {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.progress-bar {
    flex: 1;
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #1e3a8a, #3b82f6);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
    white-space: nowrap;
}

/* Secciones */
.project-tasks-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.6rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

/* Filtros */
.filters-container {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.filters-form {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    background: white;
    color: #374151;
    transition: border-color 0.3s ease;
    min-width: 150px;
}

.filter-select:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

/* Tabla de Tareas */
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

/* Columnas específicas */
.th-priority, .td-priority {
    width: 100px;
    text-align: center;
}

.th-task, .td-task {
    width: 30%;
    min-width: 200px;
}

.th-assigned, .td-assigned {
    width: 15%;
    min-width: 120px;
}

.th-due-date, .td-due-date {
    width: 15%;
    min-width: 140px;
}

.th-status, .td-status {
    width: 120px;
    text-align: center;
}

.th-actions, .td-actions {
    width: 100px;
    text-align: center;
}

/* Badges */
.priority-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.priority-badge.priority-urgent {
    background: #dc2626;
    color: white;
}

.priority-badge.priority-high {
    background: #ea580c;
    color: white;
}

.priority-badge.priority-medium {
    background: #d97706;
    color: white;
}

.priority-badge.priority-low {
    background: #059669;
    color: white;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-badge.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.status-in_progress {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.status-completed {
    background: #d1fae5;
    color: #065f46;
}

/* Información de tareas */
.task-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.task-title {
    font-weight: 600;
    color: #1f2937;
    line-height: 1.3;
}

.task-description {
    color: #6b7280;
    font-size: 0.8rem;
    line-height: 1.4;
}

.assigned-user {
    font-weight: 500;
    color: #374151;
}

.no-assigned {
    color: #9ca3af;
    font-style: italic;
    font-size: 0.8rem;
}

.due-date-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.due-date-text {
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #374151;
}

.due-date-text i {
    font-size: 0.75rem;
    color: #9ca3af;
}

.no-due-date {
    color: #9ca3af;
    font-style: italic;
    font-size: 0.8rem;
}

/* Select de estado */
.status-select {
    padding: 0.25rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.75rem;
    background: white;
    color: #374151;
    font-weight: 600;
    text-transform: uppercase;
    cursor: pointer;
}

/* Botones de acción */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-size: 0.8rem;
}

.btn-action.btn-view {
    background: #1e3a8a;
    color: white;
}

.btn-action.btn-view:hover {
    background: #1e40af;
    transform: translateY(-1px);
}

/* Estado Vacío */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 1rem 0;
}

.empty-state p {
    color: #6b7280;
    margin: 0 0 2rem 0;
    font-size: 1rem;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal.open {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 16px;
    max-width: 90vw;
    max-height: 90vh;
    overflow: auto;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.modal-lg {
    width: 600px;
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header-gradient {
    background: linear-gradient(135deg, #1e3a8a 0%, #374151 100%);
    color: white;
}

.modal-title-wrap h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.modal-subtitle {
    font-size: 0.9rem;
    opacity: 0.9;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: inherit;
    opacity: 0.7;
    padding: 0.25rem;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.modal-close:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.2);
}

.modal-body {
    padding: 2rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-span-2 {
    grid-column: span 2;
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
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.field-help {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.action-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn.primary {
    background: #1e3a8a;
    color: white;
}

.action-btn.primary:hover {
    background: #1e40af;
    transform: translateY(-1px);
}

.action-btn.secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.action-btn.secondary:hover {
    background: #e5e7eb;
}

.action-btn.is-loading {
    pointer-events: none;
    opacity: 0.7;
}

.btn-loader {
    display: none;
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.action-btn.is-loading .btn-text {
    display: none;
}

.action-btn.is-loading .btn-loader {
    display: block;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.form-errors {
    background: #fee2e2;
    color: #991b1b;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filters-container {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .project-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .tasks-table-container {
        overflow-x: auto;
    }
    
    .tasks-table {
        min-width: 700px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-span-2 {
        grid-column: span 1;
    }
}
</style>

<script>
function openCreateTaskModal() { 
    document.getElementById('createTaskModal').classList.add('open'); 
}

function closeCreateTaskModal() { 
    document.getElementById('createTaskModal').classList.remove('open'); 
    document.getElementById('createTaskForm').reset(); 
}

document.getElementById('createTaskForm').addEventListener('submit', function(e){
  e.preventDefault();
  const title = this.querySelector('input[name="task_name"]').value.trim();
  const errorBox = document.getElementById('createTaskErrors');
  if(!title){
    errorBox.style.display = 'block';
    errorBox.textContent = 'El título es requerido.';
    return;
  }
  errorBox.style.display = 'none';
  const submitBtn = document.getElementById('createTaskSubmitBtn');
  submitBtn.classList.add('is-loading');
  const fd = new FormData(this);
  fetch('?route=clan_member/create-task', { method:'POST', body: fd, credentials:'same-origin' })
    .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false,message:'Respuesta inválida'}; } })
    .then(d=>{ if(!d.success){ errorBox.style.display='block'; errorBox.textContent=d.message||'Error al crear la tarea'; submitBtn.classList.remove('is-loading'); return; } location.reload(); })
    .catch(()=>{ errorBox.style.display='block'; errorBox.textContent='Error de red'; submitBtn.classList.remove('is-loading'); });
});

function filterTasks() {
    const statusFilter = document.getElementById('status_filter').value;
    const priorityFilter = document.getElementById('priority_filter').value;
    const taskRows = document.querySelectorAll('.task-row');
    
    taskRows.forEach(row => {
        const status = row.dataset.status;
        const priority = row.dataset.priority;
        
        const statusMatch = !statusFilter || status === statusFilter;
        const priorityMatch = !priorityFilter || priority === priorityFilter;
        
        if (statusMatch && priorityMatch) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });
}

function updateTaskStatus(taskId, newStatus) {
    const fd = new FormData();
    fd.append('task_id', taskId);
    
    if (newStatus === 'completed' || newStatus === 'pending') {
        fd.append('is_completed', newStatus === 'completed' ? 'true' : 'false');
        fetch('?route=clan_member/toggle-task-status', { method:'POST', body: fd, credentials:'same-origin' })
            .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false, message:'Respuesta inválida'}; } })
            .then(d=>{ if(!d.success){ alert(d.message||'Error'); } location.reload(); });
    } else {
        fd.append('status', newStatus);
        fetch('?route=clan_member/update-task', { method:'POST', body: fd, credentials:'same-origin' })
            .then(async r=>{ const t = await r.text(); try{ return JSON.parse(t); } catch(e){ console.error(t); return {success:false, message:'Respuesta inválida'}; } })
            .then(d=>{ if(!d.success){ alert(d.message||'Error'); } location.reload(); });
    }
}
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [];
require_once __DIR__ . '/../layout.php';
?>