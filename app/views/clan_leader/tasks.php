<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="clan-leader-tasks-container">
    <!-- Header Mejorado -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">Gestión de Tareas</h1>
                <p class="page-subtitle">Administra las tareas de todos los proyectos de tu clan</p>
            </div>
            <div class="header-actions">
                <a href="?route=clan_leader/tasks&action=create" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Nueva Tarea
                </a>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <?php if (isset($projects) && !empty($projects)): ?>
            <!-- Proyectos -->
            <div class="projects-section">
                <h2 class="section-title">Proyectos del Clan</h2>
                <div class="projects-grid">
                    <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <div class="project-header">
                            <h3 class="project-name"><?= htmlspecialchars($project['project_name']) ?></h3>
                            <div class="project-header-actions">
                                <div class="project-status status-<?= $project['status'] ?>">
                                    <?= ucfirst($project['status']) ?>
                                </div>
                                <div class="project-menu-container">
                                    <button class="project-menu-btn" onclick="toggleProjectMenu(<?= $project['project_id'] ?>)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="project-menu" id="projectMenu<?= $project['project_id'] ?>">
                                        <button class="menu-item" onclick="openCloneProjectModal(<?= $project['project_id'] ?>)">
                                            <i class="fas fa-copy"></i>
                                            Clonar Proyecto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="project-stats">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number"><?= $project['total_tasks'] ?></div>
                                    <div class="stat-label">Total</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon completed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number"><?= $project['completed_tasks'] ?></div>
                                    <div class="stat-label">Completadas</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon progress">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number"><?= $project['progress_percentage'] ?>%</div>
                                    <div class="stat-label">Progreso</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="project-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $project['progress_percentage'] ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="project-delegation">
                            <label class="delegation-checkbox">
                                <input type="checkbox" 
                                       class="delegation-toggle" 
                                       data-project-id="<?= $project['project_id'] ?>"
                                       <?= isset($project['allow_delegation']) && $project['allow_delegation'] ? 'checked' : '' ?>
                                       onchange="toggleProjectDelegation(<?= $project['project_id'] ?>, this.checked)">
                                <span class="checkbox-label">
                                    <i class="fas fa-user-plus"></i> Delegar tareas
                                </span>
                            </label>
                        </div>
                        
                        <div class="project-actions">
                            <a href="<?= APP_URL ?>?route=clan_leader/tasks&project_id=<?= $project['project_id'] ?>" class="btn-minimal primary">
                                <i class="fas fa-eye"></i> Ver Tareas
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Sistema de Tabs para Tareas -->
        <div class="tasks-tabs-container">
            <div class="tasks-tabs">
                <button class="tab-button active" onclick="switchTab('my-tasks')" id="my-tasks-tab">
                    <i class="fas fa-user"></i>
                    Mis Tareas
                </button>
                <button class="tab-button" onclick="switchTab('team-tasks')" id="team-tasks-tab">
                    <i class="fas fa-users"></i>
                    Equipo
                </button>
            </div>
        </div>

        <?php if (isset($allTasks)): ?>
            <!-- Tab: Mis Tareas -->
            <div id="my-tasks-content" class="tab-content active">
                <div class="all-tasks-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            Mis Tareas Asignadas
                            <?php if (!empty($search) || !empty($_GET['status_filter']) || (isset($_GET['per_page']) && $_GET['per_page'] != '5')): ?>
                            <span class="filters-indicator">
                                <i class="fas fa-filter"></i>
                                Filtros activos
                            </span>
                            <?php endif; ?>
                        </h2>
                    </div>
                    
                    <!-- Filtros y búsqueda para Mis Tareas -->
                    <div class="filters-container">
                        <div class="filters-header">
                            <form method="GET" action="?route=clan_leader/tasks" class="filters-form">
                                <input type="hidden" name="route" value="clan_leader/tasks">
                                <input type="hidden" name="tab" value="my-tasks">
                                
                                <!-- Filtros -->
                                <div class="filter-group">
                                    <div class="filter-item">
                                        <label for="statusFilter">Estado:</label>
                                        <select name="status_filter" id="statusFilter" onchange="this.form.submit()">
                                            <option value="">Todos</option>
                                            <option value="pending" <?= (($_GET['status_filter'] ?? '') === 'pending') ? 'selected' : '' ?>>Pendiente</option>
                                            <option value="in_progress" <?= (($_GET['status_filter'] ?? '') === 'in_progress') ? 'selected' : '' ?>>En Progreso</option>
                                            <option value="completed" <?= (($_GET['status_filter'] ?? '') === 'completed') ? 'selected' : '' ?>>Completado</option>
                                        </select>
                                    </div>
                                    
                                    <div class="filter-item">
                                        <label for="perPage">Mostrar:</label>
                                        <select name="per_page" id="perPage" onchange="this.form.submit()">
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
                                                   value="<?= htmlspecialchars($search) ?>"
                                                   placeholder="Buscar tareas..."
                                                   class="search-input"
                                                   id="searchInputMyTasks"
                                                   oninput="debounceSearch(this)">
                                        </div>
                                    </div>
                                    
                                    <!-- Botones de acción -->
                                    <div class="filter-actions">
                                        <?php if (!empty($search) || !empty($_GET['status_filter']) || (isset($_GET['per_page']) && $_GET['per_page'] != '5')): ?>
                                        <a href="?route=clan_leader/tasks&tab=my-tasks" class="clear-filters">
                                            <i class="fas fa-undo"></i>
                                            Resetear Filtros
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabla de Mis Tareas -->
                    <div class="tasks-table-container">
                        <table class="tasks-table">
                            <thead>
                                <tr>
                                    <th class="th-checkbox" style="width: 40px;">
                                        <input type="checkbox" id="select-all-my" onchange="toggleAllTasks(this, 'my')">
                                    </th>
                                    <th class="th-priority">Prioridad</th>
                                    <th class="th-task">Tarea</th>
                                    <th class="th-project">Proyecto</th>
                                    <th class="th-due-date">Fecha Límite</th>
                                    <th class="th-status">Estado</th>
                                    <th class="th-progress">Progreso</th>
                                    <th class="th-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="my-tasks-table-body">
                                <!-- Las tareas del líder se cargarán aquí -->
                                <tr><td colspan="8" class="text-center">Cargando mis tareas...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab: Equipo -->
            <div id="team-tasks-content" class="tab-content">
                <div class="all-tasks-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            Tareas del Equipo
                            <?php if (!empty($search) || !empty($_GET['status_filter']) || (isset($_GET['per_page']) && $_GET['per_page'] != '5')): ?>
                            <span class="filters-indicator">
                                <i class="fas fa-filter"></i>
                                Filtros activos
                            </span>
                            <?php endif; ?>
                        </h2>
                    </div>
                    
                    <!-- Filtros y búsqueda para Equipo -->
                    <div class="filters-container">
                        <div class="filters-header">
                            <form method="GET" action="?route=clan_leader/tasks" class="filters-form">
                                <input type="hidden" name="route" value="clan_leader/tasks">
                                <input type="hidden" name="tab" value="team-tasks">
                                
                                <!-- Filtros -->
                                <div class="filter-group">
                                    <div class="filter-item">
                                        <label for="statusFilterTeam">Estado:</label>
                                        <select name="status_filter" id="statusFilterTeam" onchange="this.form.submit()">
                                            <option value="">Todos</option>
                                            <option value="pending" <?= (($_GET['status_filter'] ?? '') === 'pending') ? 'selected' : '' ?>>Pendiente</option>
                                            <option value="in_progress" <?= (($_GET['status_filter'] ?? '') === 'in_progress') ? 'selected' : '' ?>>En Progreso</option>
                                            <option value="completed" <?= (($_GET['status_filter'] ?? '') === 'completed') ? 'selected' : '' ?>>Completado</option>
                                        </select>
                                    </div>
                                    
                                    <div class="filter-item">
                                        <label for="perPageTeam">Mostrar:</label>
                                        <select name="per_page" id="perPageTeam" onchange="this.form.submit()">
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
                                                   value="<?= htmlspecialchars($search) ?>"
                                                   placeholder="Buscar tareas del equipo..."
                                                   class="search-input"
                                                   id="searchInputTeam"
                                                   oninput="debounceSearch(this)">
                                        </div>
                                    </div>
                                    
                                    <!-- Botones de acción -->
                                    <div class="filter-actions">
                                        <?php if (!empty($search) || !empty($_GET['status_filter']) || (isset($_GET['per_page']) && $_GET['per_page'] != '5')): ?>
                                        <a href="?route=clan_leader/tasks&tab=team-tasks" class="clear-filters">
                                            <i class="fas fa-undo"></i>
                                            Resetear Filtros
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabla de Tareas del Equipo -->
                    <div class="tasks-table-container">

            <!-- Tab: Equipo -->
            <div id="team-tasks-content" class="tab-content">
                <div class="all-tasks-section">
                <div class="section-header">
                    <h2 class="section-title">
                        Todas las Tareas del Clan
                        <?php if (!empty($search) || !empty($_GET['status_filter']) || (isset($_GET['per_page']) && $_GET['per_page'] != '5')): ?>
                        <span class="filters-indicator">
                            <i class="fas fa-filter"></i>
                            Filtros activos
                        </span>
                        <?php endif; ?>
                    </h2>
                    <div class="section-actions">
                        <!-- Solo Filtros -->
                        <div class="filters-container">
                            <form method="GET" action="?route=clan_leader/tasks" class="filters-form">
                                <input type="hidden" name="route" value="clan_leader/tasks">
                                
                                <!-- Filtro por estado -->
                                <div class="filter-group">
                                    <label for="status_filter" class="filter-label">Estado:</label>
                                    <select name="status_filter" id="status_filter" class="filter-select" onchange="this.form.submit()">
                                        <option value="">Todos los estados</option>
                                        <option value="pending" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] === 'pending') ? 'selected' : '' ?>>Pendientes</option>
                                        <option value="in_progress" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] === 'in_progress') ? 'selected' : '' ?>>En Progreso</option>
                                        <option value="completed" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] === 'completed') ? 'selected' : '' ?>>Completadas</option>
                                        <option value="cancelled" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] === 'cancelled') ? 'selected' : '' ?>>Canceladas</option>
                                    </select>
                                </div>
                                
                                <!-- Filtro por registros por página -->
                                <div class="filter-group">
                                    <label for="per_page" class="filter-label">Mostrar:</label>
                                    <select name="per_page" id="per_page" class="filter-select" onchange="this.form.submit()">
                                        <option value="5" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '5') ? 'selected' : '' ?>>5 por página</option>
                                        <option value="10" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '10') ? 'selected' : '' ?>>10 por página</option>
                                        <option value="20" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '20') ? 'selected' : '' ?>>20 por página</option>
                                        <option value="50" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '50') ? 'selected' : '' ?>>50 por página</option>
                                        <option value="100" <?= (isset($_GET['per_page']) && $_GET['per_page'] == '100') ? 'selected' : '' ?>>100 por página</option>
                                    </select>
                                </div>
                                
                                <!-- Barra de búsqueda -->
                                <div class="search-container">
                                    <div class="search-input-group">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" 
                                               name="search" 
                                               value="<?= htmlspecialchars($search ?? '') ?>" 
                                               placeholder="Buscar tareas, proyectos o usuarios..."
                                               class="search-input"
                                               id="searchInput"
                                               oninput="debounceSearch(this)">
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="filter-actions">
                                    <?php if (!empty($search) || !empty($_GET['status_filter']) || (isset($_GET['per_page']) && $_GET['per_page'] != '5')): ?>
                                    <a href="?route=clan_leader/tasks" class="clear-filters">
                                        <i class="fas fa-undo"></i>
                                        Resetear Filtros
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                     
                    </div>
                </div>
                
                <div class="tasks-table-container">
                    <table class="tasks-table">
                        <thead>
                            <tr>
                                <th class="th-checkbox" style="width: 40px;">
                                    <input type="checkbox" id="select-all" onchange="toggleAllTasks(this)">
                                </th>
                                <th class="th-priority">Prioridad</th>
                                <th class="th-task">Tarea</th>
                                <th class="th-project">Proyecto</th>
                                <th class="th-assigned">Asignado</th>
                                <th class="th-due-date">Fecha Límite</th>
                                <th class="th-status">Estado</th>
                                <th class="th-progress">Progreso</th>
                                <th class="th-actions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Debug: verificar si allTasks existe
                            error_log("DEBUG VIEW - allTasks isset: " . (isset($allTasks) ? 'YES' : 'NO'));
                            error_log("DEBUG VIEW - allTasks type: " . gettype($allTasks ?? null));
                            if (isset($allTasks)) {
                                error_log("DEBUG VIEW - allTasks count: " . count($allTasks));
                                if (!empty($allTasks)) {
                                    error_log("DEBUG VIEW - First item type: " . gettype($allTasks[0]));
                                    error_log("DEBUG VIEW - First item: " . print_r($allTasks[0], true));
                                }
                            }
                            
                            if (!isset($allTasks) || empty($allTasks)) {
                                echo '<tr><td colspan="9">No hay tareas disponibles</td></tr>';
                            } else {
                                foreach ($allTasks as $index => $task) {
                                    // Verificar si $task es un array o solo un número
                                    if (!is_array($task)) {
                                        error_log("ERROR: Task at index $index is not an array: " . print_r($task, true));
                                        continue;
                                    }
                                    
                                    // Valores por defecto para evitar errores
                                    $taskId = $task['task_id'] ?? 0;
                                    $taskName = $task['task_name'] ?? 'Sin nombre';
                                    $priority = $task['priority'] ?? 'medium';
                                    $status = $task['status'] ?? 'pending';
                                    $daysUntilDue = $task['days_until_due'] ?? 0;
                                    $projectName = $task['project_name'] ?? 'Sin proyecto';
                                    $dueDate = $task['due_date'] ?? null;
                                    $completionPercentage = $task['completion_percentage'] ?? 0;
                                    
                                    error_log("DEBUG HTML - Task ID: $taskId, Task name: $taskName");
                            ?>
                            <tr class="task-row priority-<?= $priority ?> <?= ($daysUntilDue < 0) ? 'overdue' : '' ?> <?= ($status === 'completed') ? 'completed' : '' ?>" data-task-id="<?= $taskId ?>">
                                <td class="td-checkbox">
                                    <input type="checkbox" 
                                           id="task-<?= $taskId ?>" 
                                           data-task-id="<?= $taskId ?>"
                                           <?= ($status === 'completed') ? 'checked' : '' ?>
                                           onchange="console.log('HTML task_id: <?= $taskId ?>, tipo: <?= gettype($taskId) ?>'); toggleTaskStatus('<?= $taskId ?>', this.checked)">
                                </td>
                                <td class="td-priority">
                                    <span class="priority-badge priority-<?= $priority ?>">
                                        <?php 
                                        switch($priority) {
                                            case 'critical': echo 'Urgente'; break;
                                            case 'high': echo 'Alta'; break;
                                            case 'low': echo 'Baja'; break;
                                            default: echo 'Media'; break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td class="td-task">
                                    <div class="task-info">
                                        <div class="task-title"><?= htmlspecialchars($taskName) ?></div>
                                        <?php if (!empty($task['description'])): ?>
                                        <div class="task-description"><?= htmlspecialchars(substr($task['description'], 0, 80)) ?><?= strlen($task['description']) > 80 ? '...' : '' ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="td-project">
                                    <span class="project-name"><?= htmlspecialchars($projectName) ?></span>
                                </td>
                                <td class="td-assigned">
                                    <?php if (!empty($task['all_assigned_users'])): ?>
                                    <span class="assigned-users"><?= htmlspecialchars($task['all_assigned_users']) ?></span>
                                    <?php elseif (!empty($task['assigned_user_name'])): ?>
                                    <span class="assigned-user"><?= htmlspecialchars($task['assigned_user_name']) ?></span>
                                    <?php else: ?>
                                    <span class="no-assigned">Sin asignar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="td-due-date">
                                    <?php if ($task['due_date']): ?>
                                    <div class="due-date-info <?= ($task['days_until_due'] < 0) ? 'overdue' : '' ?>">
                                        <div class="due-date-text">
                                            <?php if ($task['days_until_due'] < 0): ?>
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Vencida hace <?= abs($task['days_until_due']) ?> días
                                            <?php elseif ($task['days_until_due'] == 0): ?>
                                                <i class="fas fa-clock"></i>
                                                Vence hoy
                                            <?php elseif ($task['days_until_due'] == 1): ?>
                                                <i class="fas fa-clock"></i>
                                                Vence mañana
                                            <?php else: ?>
                                                <i class="fas fa-calendar"></i>
                                                Vence en <?= $task['days_until_due'] ?> días
                                            <?php endif; ?>
                                        </div>
                                        <div class="due-date-full"><?= date('d/m/Y', strtotime($task['due_date'])) ?></div>
                                    </div>
                                    <?php else: ?>
                                    <span class="no-due-date">Sin fecha</span>
                                    <?php endif; ?>
                                </td>
                                <td class="td-status">
                                    <span class="status-badge status-<?= $task['status'] ?>">
                                        <?php 
                                        switch($task['status']) {
                                            case 'in_progress': echo 'En Progreso'; break;
                                            case 'completed': echo 'Completada'; break;
                                            case 'cancelled': echo 'Cancelada'; break;
                                            default: echo 'Pendiente'; break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td class="td-progress">
                                    <div class="progress-container" style="display: flex; align-items: center; gap: 10px;">
                                        <div class="progress-bar-clickable" 
                                             style="flex: 1; height: 20px; background: #e5e7eb; border-radius: 10px; cursor: pointer; position: relative; overflow: visible;"
                                             onclick="updateTaskProgressFromClick(event, <?= $task['task_id'] ?>)"
                                             data-task-id="<?= $task['task_id'] ?>">
                                            <div class="progress-fill" 
                                                 style="height: 100%; background: linear-gradient(90deg, #10b981, #22c55e); border-radius: 10px; width: <?= ($task['status'] === 'completed') ? '100' : ($task['completion_percentage'] ?? 0) ?>%; transition: width 0.3s ease;"></div>
                                            <span class="progress-text" 
                                                  style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 11px; font-weight: 600; color: #374151; text-shadow: 0 0 3px white; white-space: nowrap;">
                                                <?= ($task['status'] === 'completed') ? '100' : intval($task['completion_percentage'] ?? 0) ?>%
                                            </span>
                                        </div>
                                        <input type="range" 
                                               class="task-progress-slider" 
                                               data-task-id="<?= $task['task_id'] ?>"
                                               min="0" 
                                               max="100" 
                                               value="<?= ($task['status'] === 'completed') ? '100' : intval($task['completion_percentage'] ?? 0) ?>" 
                                               oninput="updateTaskProgress(<?= $task['task_id'] ?>, this.value)"
                                               style="width: 80px; cursor: pointer;">
                                    </div>
                                </td>
                                <td class="td-actions">
                                    <div class="action-buttons">
                                        <a href="<?= APP_URL ?>?route=clan_leader/get-task-details&task_id=<?= $task['task_id'] ?>" class="btn-action btn-view" title="Ver proyecto">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= APP_URL ?>?route=clan_leader/tasks&action=edit&task_id=<?= $task['task_id'] ?>" class="btn-action btn-edit" title="Editar tarea">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php 
                                            // Verificar si el usuario es dueño de la tarea para mostrar botón de clonar
                                            $isOwner = ((int)($task['created_by_user_id'] ?? 0) === (int)$user['user_id']);
                                            
                                            $isPersonal = false;
                                            if (isset($task['is_personal'])) {
                                                $isPersonal = ((int)$task['is_personal'] === 1);
                                            } elseif (isset($task['project_is_personal'])) {
                                                $isPersonal = ((int)$task['project_is_personal'] === 1);
                                            } elseif (isset($task['project_name']) && $task['project_name'] === 'Tareas Personales') {
                                                $isPersonal = true;
                                            }
                                        ?>
                                        <?php if ($isOwner): ?>
                                        <button class="btn-action btn-clone" onclick="openCloneTaskModal(<?= (int)$task['task_id'] ?>)" title="Clonar tarea">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($isPersonal): ?>
                                        <button type="button" onclick="deleteTask(<?= (int)$task['task_id'] ?>)" class="btn-action btn-delete" title="Eliminar tarea">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                } // end foreach
                            } // end if/else
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="pagination-container">
                    <div class="pagination-info">
                        <span class="pagination-text">
                            Mostrando <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> 
                            a <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_records']) ?> 
                            de <?= $pagination['total_records'] ?> tareas
                        </span>
                    </div>
                    
                    <div class="pagination-controls">
                        <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?route=clan_leader/tasks&page=<?= $pagination['current_page'] - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($_GET['status_filter']) ? '&status_filter=' . urlencode($_GET['status_filter']) : '' ?><?= isset($_GET['per_page']) ? '&per_page=' . urlencode($_GET['per_page']) : '' ?>" 
                           class="pagination-btn pagination-prev">
                            <i class="fas fa-chevron-left"></i>
                            Anterior
                        </a>
                        <?php endif; ?>
                        
                        <div class="pagination-pages">
                            <?php
                            $startPage = max(1, $pagination['current_page'] - 2);
                            $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
                            
                            if ($startPage > 1): ?>
                            <a href="?route=clan_leader/tasks&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($_GET['status_filter']) ? '&status_filter=' . urlencode($_GET['status_filter']) : '' ?><?= isset($_GET['per_page']) ? '&per_page=' . urlencode($_GET['per_page']) : '' ?>" 
                               class="pagination-btn">1</a>
                            <?php if ($startPage > 2): ?>
                            <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <a href="?route=clan_leader/tasks&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($_GET['status_filter']) ? '&status_filter=' . urlencode($_GET['status_filter']) : '' ?><?= isset($_GET['per_page']) ? '&per_page=' . urlencode($_GET['per_page']) : '' ?>" 
                               class="pagination-btn <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                            <?php endfor; ?>
                            
                            <?php if ($endPage < $pagination['total_pages']): ?>
                            <?php if ($endPage < $pagination['total_pages'] - 1): ?>
                            <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                            <a href="?route=clan_leader/tasks&page=<?= $pagination['total_pages'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($_GET['status_filter']) ? '&status_filter=' . urlencode($_GET['status_filter']) : '' ?><?= isset($_GET['per_page']) ? '&per_page=' . urlencode($_GET['per_page']) : '' ?>" 
                               class="pagination-btn"><?= $pagination['total_pages'] ?></a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?route=clan_leader/tasks&page=<?= $pagination['current_page'] + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($_GET['status_filter']) ? '&status_filter=' . urlencode($_GET['status_filter']) : '' ?><?= isset($_GET['per_page']) ? '&per_page=' . urlencode($_GET['per_page']) : '' ?>" 
                           class="pagination-btn pagination-next">
                            Siguiente
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <!-- Estado vacío - No hay tareas o no hay resultados de búsqueda -->
            <div class="no-results">
                <div class="no-results-icon">
                    <i class="fas fa-search"></i>
                </div>
                <?php if (!empty($search)): ?>
                <h3>No se encontraron resultados</h3>
                <p>No hay tareas que coincidan con tu búsqueda: "<strong><?= htmlspecialchars($search) ?></strong>"</p>
                <a href="?route=clan_leader/tasks" class="btn-create">
                    <i class="fas fa-times"></i>
                    Limpiar búsqueda
                </a>
                <?php else: ?>
                <h3>No hay tareas en el clan</h3>
                <p>No se han creado tareas en este clan todavía. ¡Comienza creando tu primera tarea!</p>
                <a href="?route=clan_leader/tasks&action=create" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Crear Primera Tarea
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($projects) && empty($projects)): ?>
            <!-- Estado Vacío - No hay proyectos -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h3>No hay proyectos disponibles</h3>
                <p>Crea tu primer proyecto para comenzar a gestionar tareas.</p>
                <a href="?route=clan_leader/projects" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Crear Primer Proyecto
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Reset y Base */
.clan-leader-tasks-container {
    min-height: 100vh;
    background: #f5f7fa;
    padding: 0;
    margin: 0;
}

/* Header Mejorado */
.page-header {
    background: transparent;
    color: inherit;
    padding: 1.5rem 0;
    margin-bottom: 1.5rem;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #e0e7ff;
    border: 1px solid #c7d2fe;
    border-radius: 12px;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    color: #1e3a8a;
}

.page-subtitle {
    font-size: 0.95rem;
    color: #374151;
    margin: 0;
    font-weight: 400;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.btn-create {
    background: #1e3a8a;
    color: #ffffff !important;
    padding: 0.6rem 1.1rem;
    border-radius: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    border: 1px solid #1e3a8a;
    transition: all 0.2s ease;
}

.btn-create:hover {
    background: #1e40af;
    border-color: #1e40af;
    transform: translateY(-1px);
    color: #ffffff !important;
    text-decoration: none;
}

.btn-back {
    background: #f3f4f6;
    color: #374151;
    padding: 0.6rem 1.1rem;
    border-radius: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.btn-back:hover {
    background: #e5e7eb;
    border-color: #d1d5db;
    transform: translateY(-1px);
    color: #111827;
    text-decoration: none;
}

/* Contenido Principal */
.main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem 2rem 1.5rem;
}

/* Secciones */
.projects-section, .tasks-section {
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

.filters-indicator {
    font-size: 0.9rem;
    font-weight: 600;
    color: #667eea;
    background: #eef2ff;
    padding: 6px 12px;
    border-radius: 20px;
    border: 2px solid #c7d2fe;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
    100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

/* Grid de Proyectos */
.projects-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.15rem;
    margin: 0 auto;
}

.project-card {
    background: white;
    border-radius: 16px;
    padding: 0.9rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.4rem;
}

.project-header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.project-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    flex: 1;
    line-height: 1.2;
}

.project-status {
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
    font-size: 0.65rem;
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

/* Menú de proyecto */
.project-menu-container {
    position: relative;
}

.project-menu-btn {
    background: none;
    border: none;
    padding: 0.4rem;
    border-radius: 6px;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.project-menu-btn:hover {
    background: #f3f4f6;
    color: #374151;
}

.project-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    min-width: 160px;
    z-index: 1000;
    display: none;
}

.project-menu.show {
    display: block;
}

.project-menu .menu-item {
    width: 100%;
    padding: 0.75rem 1rem;
    background: none;
    border: none;
    text-align: left;
    color: #374151;
    cursor: pointer;
    transition: background-color 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.project-menu .menu-item:hover {
    background: #f3f4f6;
}

.project-menu .menu-item:first-child {
    border-radius: 8px 8px 0 0;
}

.project-menu .menu-item:last-child {
    border-radius: 0 0 8px 8px;
}

/* Stats de Proyecto */
.project-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stat-icon {
    width: 28px;
    height: 28px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    background: #f3f4f6;
    color: #6b7280;
}

.stat-icon.completed {
    background: rgba(34, 197, 94, 0.1);
    color: #10b981;
}

.stat-icon.progress {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    font-size: 0.65rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* Barra de Progreso */
.project-progress {
    margin-bottom: 0.5rem;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #34d399);
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Delegación de Proyecto */
.project-delegation {
    padding: 0.75rem 0 0.5rem;
    border-top: 1px solid #e5e7eb;
    margin-top: 0.5rem;
}

.delegation-checkbox {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    user-select: none;
}

.delegation-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 8px;
    cursor: pointer;
    accent-color: #3B82F6;
}

.delegation-checkbox .checkbox-label {
    font-size: 0.875rem;
    color: #4b5563;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
}

.delegation-checkbox:hover .checkbox-label {
    color: #3B82F6;
}

.delegation-checkbox input[type="checkbox"]:checked + .checkbox-label {
    color: #3B82F6;
    font-weight: 500;
}

/* Acciones de Proyecto */
.project-actions {
    display: flex;
    justify-content: center;
}

/* Estilos exactos de los botones de task_details.php */
.btn-minimal {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid;
}

.btn-minimal.primary {
    background: #1e3a8a !important;
    color: #ffffff !important;
    border-color: #1e3a8a !important;
}

.btn-minimal.primary:hover {
    background: #1e40af !important;
    border-color: #1e40af !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(30, 58, 138, 0.22);
}

/* Filtros */
.filters-container {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-group label {
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.filter-group select {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    background: white;
    color: #374151;
    transition: border-color 0.3s ease;
    min-width: 150px;
}

.filter-group select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

.tasks-table tbody tr.completed .task-name {
    text-decoration: line-through;
    color: #6b7280;
}

/* Columnas de la tabla */
.checkbox-col {
    width: 50px;
    text-align: center;
}

.task-col {
    width: 30%;
}

.assigned-col {
    width: 15%;
}

.due-date-col {
    width: 12%;
}

.priority-col {
    width: 10%;
}

.status-col {
    width: 12%;
}

.points-col {
    width: 8%;
    text-align: center;
}

.actions-col {
    width: 13%;
    text-align: center;
}

/* Checkbox personalizado */
.checkbox-col input[type="checkbox"] {
    display: none;
}

.checkbox-col label {
    width: 18px;
    height: 18px;
    border: 2px solid #d1d5db;
    border-radius: 3px;
    display: block;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
    background: white;
    margin: 0 auto;
}

.checkbox-col input[type="checkbox"]:checked + label {
    background: #10b981;
    border-color: #10b981;
}

.checkbox-col input[type="checkbox"]:checked + label::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 11px;
    font-weight: bold;
}

/* Información de la tarea */
.task-info {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.task-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9rem;
    line-height: 1.2;
}

.task-description {
    color: #6b7280;
    font-size: 0.75rem;
    line-height: 1.3;
}

/* Usuario asignado */
.assigned-user {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.assigned-users {
    color: #4f46e5;
    font-weight: 500;
    font-size: 0.85rem;
    line-height: 1.3;
    max-width: 200px;
    word-wrap: break-word;
    display: block;
}

.user-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.7rem;
}

.no-assigned {
    color: #9ca3af;
    font-style: italic;
    font-size: 0.85rem;
}

/* Fecha límite */
.due-date {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.8rem;
    color: #374151;
}

.due-date.overdue {
    color: #dc2626;
    font-weight: 600;
}

.due-date i {
    color: #9ca3af;
    font-size: 0.8rem;
}

.no-date {
    color: #9ca3af;
    font-style: italic;
    font-size: 0.85rem;
}

/* Badges */
.status-badge, .priority-badge {
    padding: 0.2rem 0.6rem;
    border-radius: 16px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
    text-align: center;
    min-width: 70px;
}

/* Puntos */
.points {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    font-weight: 600;
    color: #f59e0b;
}

.points i {
    font-size: 0.8rem;
}

.no-points {
    color: #9ca3af;
    font-size: 0.85rem;
}

/* Acciones en tabla */
.table-actions {
    display: flex;
    gap: 0.4rem;
    justify-content: center;
}

.table-actions .action-btn {
    width: 26px;
    height: 26px;
    font-size: 0.75rem;
}

/* Responsive para tabla */
@media (max-width: 1024px) {
    .tasks-table-container {
        overflow-x: auto;
    }
    
    .tasks-table {
        min-width: 900px;
    }
}

/* Tarjetas de Tareas */
.task-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.task-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.task-card.completed {
    opacity: 0.7;
    background: #f9fafb;
}

.task-card.completed .task-title h3 {
    text-decoration: line-through;
    color: #6b7280;
}

/* Header de Tarea */
.task-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.task-checkbox {
    flex-shrink: 0;
}

.task-checkbox input[type="checkbox"] {
    display: none;
}

.task-checkbox label {
    width: 24px;
    height: 24px;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    display: block;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
    background: white;
}

.task-checkbox input[type="checkbox"]:checked + label {
    background: #10b981;
    border-color: #10b981;
}

.task-checkbox input[type="checkbox"]:checked + label::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.task-title {
    flex: 1;
}

.task-title h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    line-height: 1.4;
}

.task-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.view-btn {
    background: #dbeafe;
    color: #1e40af;
}

.view-btn:hover {
    background: #bfdbfe;
}

.edit-btn {
    background: #fef3c7;
    color: #92400e;
}

.edit-btn:hover {
    background: #fde68a;
}

.delete-btn {
    background: #fee2e2;
    color: #dc2626;
}

.delete-btn:hover {
    background: #fecaca;
}

/* Contenido de Tarea */
.task-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.task-description p {
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0;
}

/* Meta Información */
.task-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6b7280;
}

.meta-item i {
    width: 14px;
    color: #9ca3af;
}

/* Estado y Prioridad */
.task-status-bar {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.status-badge, .priority-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-in_progress {
    background: #dbeafe;
    color: #1e40af;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.priority-low {
    background: #f3f4f6;
    color: #6b7280;
}

.priority-medium {
    background: #fef3c7;
    color: #92400e;
}

.priority-high {
    background: #fee2e2;
    color: #dc2626;
}

.priority-critical {
    background: #dc2626;
    color: white;
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

/* Responsive */
@media (max-width: 1024px) {
    .projects-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

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
    
    .projects-grid {
        grid-template-columns: 1fr;
    }
    
    .project-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .tasks-grid {
        grid-template-columns: 1fr;
    }
    
    .task-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .task-status-bar {
        flex-direction: column;
        gap: 0.5rem;
    }
}

/* Estilos para Tareas Pendientes Importantes */
.pending-tasks-section {
    margin-bottom: 2rem;
}

.tasks-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-top: 1.5rem;
}

.tasks-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.tasks-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.tasks-table th {
    padding: 1rem 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-right: 1px solid #e5e7eb;
}

.tasks-table th:last-child {
    border-right: none;
}

.tasks-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s ease;
}

.tasks-table tbody tr:hover {
    background-color: #f9fafb;
}

.tasks-table tbody tr.overdue {
    background-color: #fef2f2;
    border-left: 4px solid #dc2626;
}

.tasks-table tbody tr.overdue:hover {
    background-color: #fee2e2;
}

.tasks-table tbody tr.priority-urgent {
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

/* Estilos para tareas completadas */
.tasks-table tbody tr.completed {
    opacity: 0.7;
    background-color: #f8fafc;
}

.tasks-table tbody tr.completed:hover {
    opacity: 0.9;
    background-color: #f1f5f9;
}

/* Corregir colores de texto para filas urgentes */
.tasks-table tbody tr.priority-critical .task-title,
.tasks-table tbody tr.priority-critical .task-description,
.tasks-table tbody tr.priority-critical .project-name,
.tasks-table tbody tr.priority-critical .assigned-user,
.tasks-table tbody tr.priority-critical .no-assigned,
.tasks-table tbody tr.priority-critical .due-date-text,
.tasks-table tbody tr.priority-critical .due-date-full,
.tasks-table tbody tr.priority-critical .no-due-date,
.tasks-table tbody tr.priority-critical .points-value,
.tasks-table tbody tr.priority-critical .no-points {
    color: #1f2937 !important;
}

.tasks-table tbody tr.priority-critical .due-date-text.overdue {
    color: #dc2626 !important;
    font-weight: 600;
}

.tasks-table tbody tr.priority-critical .due-date-text.overdue i {
    color: #dc2626 !important;
}

.tasks-table td {
    padding: 0.875rem 0.75rem;
    vertical-align: top;
    border-right: 1px solid #f3f4f6;
}

.tasks-table td:last-child {
    border-right: none;
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

.th-project, .td-project {
    width: 15%;
    min-width: 120px;
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

/* Estilos para checkbox */
.th-checkbox, .td-checkbox {
    width: 40px;
    text-align: center;
}

.td-checkbox input[type="checkbox"] {
    cursor: pointer;
    width: 18px;
    height: 18px;
}

/* Estilos para la barra de progreso */
.progress-container {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.progress-bar {
    width: 70px;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #10b981;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    min-width: 35px;
}

.th-actions, .td-actions {
    width: 100px;
    text-align: center;
}

/* Estilos de contenido */
.priority-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.priority-badge.priority-critical {
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

.project-name {
    font-weight: 500;
    color: #374151;
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
}

.due-date-text.overdue {
    color: #dc2626;
    font-weight: 600;
}

.due-date-text i {
    font-size: 0.75rem;
}

.due-date-full {
    font-size: 0.75rem;
    color: #6b7280;
}

.no-due-date {
    color: #9ca3af;
    font-style: italic;
    font-size: 0.8rem;
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

.status-badge.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.clickable-status {
    cursor: pointer;
    transition: all 0.2s ease;
}

.clickable-status:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.points-value {
    font-weight: 600;
    color: #f59e0b;
    font-size: 0.9rem;
}

.no-points {
    color: #9ca3af;
    font-size: 0.8rem;
}

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
    background: #3b82f6;
    color: white;
}

.btn-action.btn-view:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-action.btn-edit {
    background: #f3f4f6;
    color: #6b7280;
}

.btn-action.btn-edit:hover {
    background: #e5e7eb;
    color: #374151;
    transform: translateY(-1px);
}

.btn-action.btn-delete {
    background: #fee2e2;
    color: #dc2626;
}

.btn-action.btn-delete:hover {
    background: #fecaca;
    color: #b91c1c;
    transform: translateY(-1px);
}

.section-actions {
    display: flex;
    gap: 1rem;
}

/* Responsive para tabla */
@media (max-width: 1200px) {
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
    
    .due-date-full {
        display: none;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-action {
        width: 28px;
        height: 28px;
        font-size: 0.7rem;
    }
    
    .section-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .filters-container {
        width: 100%;
    }
    
    .filters-form {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .filter-group {
        justify-content: space-between;
    }
    
    .search-container {
        width: 100%;
    }
    
    .search-input-group {
        width: 100%;
    }
    
    .pagination-controls {
        flex-direction: column;
        gap: 1rem;
    }
    
    .pagination-pages {
        justify-content: center;
    }
}

/* Estilos para filtros y búsqueda */
.filters-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.filters-form {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
    white-space: nowrap;
}

.filter-select {
    padding: 8px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    color: #374151;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 140px;
}

.filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.clear-filters {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #ef4444;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 10px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 2px solid #ef4444;
    background: white;
}

.clear-filters:hover {
    background: #ef4444;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

/* Estilos para búsqueda */
.search-container {
    position: relative;
    min-width: 300px;
    margin-top: 30px;
}

.search-form {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.search-input-group {
    position: relative;
    display: flex;
    align-items: center;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.search-input-group:focus-within {
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    transform: translateY(-1px);
}

.search-icon {
    position: absolute;
    left: 12px;
    color: #9ca3af;
    font-size: 0.9rem;
    z-index: 1;
}

.search-input {
    flex: 1;
    padding: 12px 12px 12px 40px;
    border: none;
    outline: none;
    font-size: 0.9rem;
    background: transparent;
    color: #374151;
    margin-top: 10px;
}

.search-input::placeholder {
    color: #9ca3af;
}

.search-btn {
    background: #667eea;
    color: white;
    border: none;
    padding: 10px 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    border-radius: 8px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.search-btn:hover {
    background: #5a67d8;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.clear-search {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    color: #ef4444;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.clear-search:hover {
    background: #fef2f2;
    color: #dc2626;
}

/* Estilos para el slider de progreso */
.task-progress-slider {
    -webkit-appearance: none;
    appearance: none;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    outline: none;
    transition: opacity 0.2s;
}

.task-progress-slider:hover {
    opacity: 0.8;
}

.task-progress-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    background: #10b981;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.2s ease;
}

.task-progress-slider::-webkit-slider-thumb:hover {
    background: #059669;
    transform: scale(1.2);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
}

.task-progress-slider::-moz-range-thumb {
    width: 16px;
    height: 16px;
    background: #10b981;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.2s ease;
    border: none;
}

.task-progress-slider::-moz-range-thumb:hover {
    background: #059669;
    transform: scale(1.2);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
}

.progress-bar-clickable {
    transition: all 0.2s ease;
}

.progress-bar-clickable:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

/* Estilos para paginación */
.pagination-container {
    margin-top: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    flex: 1;
}

.pagination-text {
    color: #6b7280;
    font-size: 0.9rem;
    font-weight: 500;
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.pagination-pages {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    border: 2px solid #e5e7eb;
    background: white;
    color: #374151;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.pagination-btn:hover {
    border-color: #667eea;
    color: #667eea;
    background: #f8fafc;
}

.pagination-btn.active {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

.pagination-btn.active:hover {
    background: #5a67d8;
    border-color: #5a67d8;
}

.pagination-prev,
.pagination-next {
    gap: 0.5rem;
    font-weight: 600;
}

.pagination-ellipsis {
    color: #9ca3af;
    font-weight: 600;
    padding: 0 8px;
}

/* Estado vacío cuando no hay resultados de búsqueda */
.no-results {
    text-align: center;
    padding: 3rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-top: 2rem;
}

.no-results-icon {
    font-size: 4rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.no-results h3 {
    color: #374151;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.no-results p {
    color: #6b7280;
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

/* Estilos para Tabs */
.tasks-tabs-container {
    margin-bottom: 2rem;
}

.tasks-tabs {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 2rem;
}

.tab-button {
    background: none;
    border: none;
    padding: 1rem 1.5rem;
    color: #6b7280;
    font-weight: 600;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
}

.tab-button:hover {
    color: #374151;
    background: #f9fafb;
}

.tab-button.active {
    color: #1e3a8a;
    border-bottom-color: #1e3a8a;
    background: #f0f4ff;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Responsive para tabs */
@media (max-width: 768px) {
    .tasks-tabs {
        flex-direction: column;
        gap: 0;
        border-bottom: none;
    }
    
    .tab-button {
        border-bottom: none;
        border-left: 3px solid transparent;
        justify-content: flex-start;
    }
    
    .tab-button.active {
        border-left-color: #1e3a8a;
        border-bottom-color: transparent;
    }
}
</style>

</div> <!-- Cierre de clan-leader-tasks-container -->

<script>
// Función para cambiar el estado de delegación de un proyecto
function toggleProjectDelegation(projectId, allowDelegation) {
    console.log('Cambiando delegación para proyecto:', projectId, 'a:', allowDelegation);
    
    // Mostrar indicador de carga
    const checkbox = document.querySelector(`input[data-project-id="${projectId}"]`);
    const label = checkbox ? checkbox.nextElementSibling : null;
    
    if (label) {
        const originalText = label.innerHTML;
        label.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
        checkbox.disabled = true;
    }
    
    // Enviar petición al servidor
    fetch('?route=clan_leader/update-project-delegation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `project_id=${projectId}&allow_delegation=${allowDelegation ? 1 : 0}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar notificación de éxito
            showNotification(data.message || 'Delegación actualizada correctamente', 'success');
            
            // Actualizar el estado visual
            if (label) {
                label.innerHTML = '<i class="fas fa-user-plus"></i> Delegar tareas';
                checkbox.disabled = false;
            }
        } else {
            // Revertir el checkbox si hay error
            if (checkbox) {
                checkbox.checked = !allowDelegation;
                checkbox.disabled = false;
            }
            if (label) {
                label.innerHTML = '<i class="fas fa-user-plus"></i> Delegar tareas';
            }
            showNotification(data.message || 'Error al actualizar delegación', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revertir el checkbox en caso de error
        if (checkbox) {
            checkbox.checked = !allowDelegation;
            checkbox.disabled = false;
        }
        if (label) {
            label.innerHTML = '<i class="fas fa-user-plus"></i> Delegar tareas';
        }
        showNotification('Error de conexión', 'error');
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
        padding: 15px 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 300px;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Agregar animaciones CSS
if (!document.getElementById('notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
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
    document.head.appendChild(style);
}

function filterTasks() {
    const statusFilter = document.getElementById('statusFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
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

function toggleAllTasks(checkbox) {
    const taskCheckboxes = document.querySelectorAll('.task-row input[type="checkbox"]');
    taskCheckboxes.forEach(taskCheckbox => {
        taskCheckbox.checked = checkbox.checked;
        if (checkbox.checked) {
            taskCheckbox.closest('tr').classList.add('completed');
        } else {
            taskCheckbox.closest('tr').classList.remove('completed');
        }
    });
}

function viewTaskDetails(taskId) {
    window.location.href = `?route=clan_leader/get-task-details&task_id=${taskId}`;
}

function editTask(taskId) {
    window.location.href = `?route=clan_leader/tasks&action=edit&task_id=${taskId}`;
}

function deleteTask(taskId) {
    confirmDelete('¿Estás seguro de que quieres eliminar esta tarea?', () => {
        fetch('?route=clan_leader/delete-task', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'task_id=' + encodeURIComponent(taskId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remover la fila de la tabla sin recargar
                const row = document.querySelector('tr.task-row[data-task-id="' + taskId + '"]');
                if (row && row.parentNode) {
                    row.parentNode.removeChild(row);
                }

                // Si no quedan filas, mostrar estado vacío en la tabla
                const remaining = document.querySelectorAll('.tasks-table tbody tr.task-row').length;
                if (remaining === 0) {
                    const tbody = document.querySelector('.tasks-table tbody');
                    if (tbody) {
                        const emptyRow = document.createElement('tr');
                        emptyRow.innerHTML = '<td colspan="9">No hay tareas disponibles</td>';
                        tbody.appendChild(emptyRow);
                    }
                }

                // Limpiar checkbox de seleccionar todo
                const selectAll = document.getElementById('select-all');
                if (selectAll) selectAll.checked = false;

                showToast('Tarea eliminada exitosamente', 'success');
            } else {
                showToast('Error al eliminar la tarea: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al eliminar la tarea', 'error');
        });
    });
}

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

// Estilos para animaciones
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
    
    /* Estilos adicionales para asegurar que el botón btn-minimal.primary sea visible */
    .btn-minimal {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 10px 14px !important;
        border-radius: 8px !important;
        text-decoration: none !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        border: 1px solid !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    .btn-minimal.primary {
        background: #1e3a8a !important;
        color: #ffffff !important;
        border-color: #1e3a8a !important;
    }
    
    .btn-minimal.primary:hover {
        background: #1e40af !important;
        border-color: #1e40af !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 18px rgba(30, 58, 138, 0.22) !important;
        color: #ffffff !important;
        text-decoration: none !important;
    }
`;
document.head.appendChild(style);

// Función para búsqueda con debounce
let searchTimeout;
function debounceSearch(input) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        input.form.submit();
    }, 500); // 500ms de delay
}
// Función para cambiar el estado de una tarea - VERSIÓN CORREGIDA
function toggleTaskStatus(taskId, isChecked) {
    console.log('=== toggleTaskStatus Debug V3.0 ===');
    console.log('taskId recibido:', taskId, 'Type:', typeof taskId);
    console.log('isChecked:', isChecked);
    
    // Validación más robusta del ID de tarea
    let numericTaskId;
    if (typeof taskId === 'string') {
        numericTaskId = parseInt(taskId, 10);
    } else if (typeof taskId === 'number') {
        numericTaskId = taskId;
    } else {
        console.error('taskId no es string ni number:', taskId);
        alert('Error: ID de tarea inválido (tipo incorrecto): ' + typeof taskId);
        // Revertir el checkbox
        const checkbox = document.querySelector(`#task-${taskId}`);
        if (checkbox) checkbox.checked = !isChecked;
        return;
    }
    
    console.log('numericTaskId procesado:', numericTaskId);
    
    // Validar que el ID es un número válido y positivo
    if (isNaN(numericTaskId) || numericTaskId <= 0) {
        console.error('ID de tarea inválido después de conversión:', numericTaskId);
        alert('Error: ID de tarea inválido. ID recibido: ' + taskId + ', convertido a: ' + numericTaskId);
        // Revertir el checkbox
        const checkbox = document.querySelector(`#task-${taskId}`);
        if (checkbox) checkbox.checked = !isChecked;
        return;
    }
    
    const newStatus = isChecked ? 'completed' : 'pending';
    console.log('newStatus:', newStatus);
    
    const requestBody = 'task_id=' + numericTaskId + '&status=' + encodeURIComponent(newStatus);
    console.log('Request body:', requestBody);
    
    fetch('?route=clan_leader/simple-toggle-task', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: requestBody
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data completa:', data);
        
        if (data.success) {
            console.log('Actualizando UI para tarea:', numericTaskId);
            
            // Buscar la fila de la tabla usando el ID numérico
            const checkbox = document.querySelector(`#task-${numericTaskId}`);
            if (!checkbox) {
                console.error('No se encontró checkbox para task ID:', numericTaskId);
                alert('Error: No se pudo encontrar la tarea en la interfaz');
                return;
            }
            
            const row = checkbox.closest('tr');
            if (!row) {
                console.error('No se encontró fila para el checkbox');
                alert('Error: No se pudo encontrar la fila de la tarea');
                return;
            }
            
            // Actualizar la barra de progreso
            const progressFill = row.querySelector('.progress-fill');
            const progressText = row.querySelector('.progress-text');
            
            if (progressFill && progressText) {
                if (isChecked) {
                    progressFill.style.width = '100%';
                    progressText.textContent = '100%';
                    row.classList.add('completed');
                } else {
                    const percentage = data.completion_percentage || 0;
                    progressFill.style.width = percentage + '%';
                    progressText.textContent = percentage + '%';
                    row.classList.remove('completed');
                }
            }
            
            // Actualizar el badge de estado
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = 'status-badge status-' + newStatus;
                statusBadge.textContent = isChecked ? 'Completada' : 'Pendiente';
            }
            
            // Mostrar mensaje de éxito con información detallada
            const message = data.message || 'Estado actualizado correctamente';
            const progressInfo = isChecked ? ' (Progreso: 100%)' : ' (Progreso: 0%)';
            console.log('Operación exitosa:', message);
            showToast(message + progressInfo, 'success');
            
        } else {
            console.error('Error del servidor:', data.message);
            console.error('Debug info:', data.debug);
            
            // Revertir el checkbox si hay error
            const checkbox = document.querySelector(`#task-${numericTaskId}`);
            if (checkbox) {
                checkbox.checked = !isChecked;
            }
            
            const errorMessage = data.message || 'Error al actualizar el estado';
            console.error('Mostrando error:', errorMessage);
            showToast(errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Error de red o parsing:', error);
        
        // Revertir el checkbox si hay error
        const checkbox = document.querySelector(`#task-${numericTaskId}`);
        if (checkbox) {
            checkbox.checked = !isChecked;
        }
        
        showToast('Error de conexión al actualizar la tarea: ' + error.message, 'error');
    });
}

// Función alternativa que usa data-attribute como respaldo
function toggleTaskStatusByElement(checkbox) {
    const taskId = checkbox.dataset.taskId || checkbox.id.replace('task-', '');
    const isChecked = checkbox.checked;
    
    console.log('toggleTaskStatusByElement - taskId from data-attribute:', taskId);
    toggleTaskStatus(taskId, isChecked);
}

// Función para seleccionar/deseleccionar todas las tareas
function toggleAllTasks(checkbox) {
    const taskCheckboxes = document.querySelectorAll('.td-checkbox input[type="checkbox"]');
    taskCheckboxes.forEach(taskCheckbox => {
        if (taskCheckbox.checked !== checkbox.checked) {
            taskCheckbox.checked = checkbox.checked;
            // No llamamos a toggleTaskStatus aquí para evitar múltiples llamadas al servidor
        }
    });
}

// Función para actualizar el progreso de una tarea desde el slider
function updateTaskProgress(taskId, newProgress) {
    console.log('Updating task progress:', taskId, newProgress);
    
    // Actualizar la UI inmediatamente
    const progressBar = document.querySelector(`.progress-bar-clickable[data-task-id="${taskId}"] .progress-fill`);
    const progressText = document.querySelector(`.progress-bar-clickable[data-task-id="${taskId}"] .progress-text`);
    
    if (progressBar) {
        progressBar.style.width = newProgress + '%';
    }
    if (progressText) {
        progressText.textContent = newProgress + '%';
    }
    
    // Enviar al servidor
    fetch('?route=clan_leader/update-task-progress', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&completion_percentage=${newProgress}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Progreso actualizado', 'success');
        } else {
            showToast('Error al actualizar progreso: ' + data.message, 'error');
            // Revertir cambios si hay error
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al actualizar progreso', 'error');
    });
}

// Función para actualizar el progreso haciendo click en la barra
function updateTaskProgressFromClick(event, taskId) {
    const progressBar = event.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const clickX = event.clientX - rect.left;
    const percentage = Math.round((clickX / rect.width) * 100);
    
    if (percentage >= 0 && percentage <= 100) {
        // Actualizar el slider también
        const slider = document.querySelector(`.task-progress-slider[data-task-id="${taskId}"]`);
        if (slider) {
            slider.value = percentage;
        }
        updateTaskProgress(taskId, percentage);
    }
}

// Función para abrir el modal de clonación
function openCloneTaskModal(taskId) {
    fetch('?route=clan_leader/get-task-data&task_id=' + taskId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCloneTaskModal(data.task, data.projects);
            } else {
                alert('Error al cargar los datos de la tarea: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al cargar los datos de la tarea');
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
    const form = document.getElementById('cloneTaskForm');
    const formData = new FormData(form);
    
    // Agregar campos adicionales
    formData.append('originalTaskId', document.getElementById('originalTaskId').value);
    formData.append('clone_subtasks', document.getElementById('cloneSubtasks').checked ? '1' : '0');
    
    fetch('?route=clan_leader/clone-task', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCloneTaskModal();
            showToast('Tarea clonada exitosamente', 'success');
            setTimeout(() => location.reload(), 1500); // Recargar para mostrar los cambios
        } else {
            alert('Error al clonar la tarea: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión al clonar la tarea');
    });
}

// Función para mostrar/ocultar menú de proyecto
function toggleProjectMenu(projectId) {
    const menu = document.getElementById('projectMenu' + projectId);
    const allMenus = document.querySelectorAll('.project-menu');
    
    // Cerrar todos los otros menús
    allMenus.forEach(m => {
        if (m.id !== 'projectMenu' + projectId) {
            m.classList.remove('show');
        }
    });
    
    // Toggle del menú actual
    menu.classList.toggle('show');
}

// Cerrar menús al hacer click fuera
document.addEventListener('click', function(e) {
    if (!e.target.closest('.project-menu-container')) {
        document.querySelectorAll('.project-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Función para abrir el modal de clonación de proyectos
function openCloneProjectModal(projectId) {
    fetch('?route=clan_leader/get-project-data&project_id=' + projectId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCloneProjectModal(data.project);
            } else {
                alert('Error al cargar los datos del proyecto: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al cargar los datos del proyecto');
        });
}

// Función para mostrar el modal de clonación de proyectos
function showCloneProjectModal(project) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content clone-project-modal">
            <div class="modal-header">
                <h3><i class="fas fa-copy"></i> Clonar Proyecto</h3>
                <button class="modal-close" onclick="closeCloneProjectModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="cloneProjectForm">
                    <input type="hidden" id="originalProjectId" value="${project.project_id}">
                    
                    <div class="form-group">
                        <label for="cloneProjectName">Nombre del proyecto</label>
                        <input type="text" id="cloneProjectName" name="project_name" value="${project.project_name}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cloneProjectDescription">Descripción</label>
                        <textarea id="cloneProjectDescription" name="description" rows="4">${project.description || ''}</textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cloneProjectStartDate">Fecha de inicio</label>
                            <input type="date" id="cloneProjectStartDate" name="start_date" value="${project.start_date || ''}">
                        </div>
                        
                        <div class="form-group">
                            <label for="cloneProjectEndDate">Fecha de fin</label>
                            <input type="date" id="cloneProjectEndDate" name="end_date" value="${project.end_date || ''}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="cloneTasks" name="clone_tasks" checked>
                            Clonar también las tareas del proyecto
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeCloneProjectModal()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="cloneProject()">
                    <i class="fas fa-copy"></i> Clonar Proyecto
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'flex';
}

// Función para cerrar el modal de clonación de proyectos
function closeCloneProjectModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}

// Función para ejecutar la clonación de proyectos
function cloneProject() {
    const form = document.getElementById('cloneProjectForm');
    const formData = new FormData(form);
    
    // Agregar campos adicionales
    formData.append('originalProjectId', document.getElementById('originalProjectId').value);
    formData.append('clone_tasks', document.getElementById('cloneTasks').checked ? '1' : '0');
    
    fetch('?route=clan_leader/clone-project', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCloneProjectModal();
            alert('Proyecto clonado exitosamente');
            location.reload(); // Recargar para mostrar los cambios
        } else {
            alert('Error al clonar el proyecto: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión al clonar el proyecto');
    });
}

// Función para cambiar entre tabs
function switchTab(tabName) {
    // Ocultar todos los tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remover active de todos los tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Mostrar el tab content seleccionado
    document.getElementById(tabName + '-content').classList.add('active');
    
    // Activar el tab button seleccionado
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Cargar datos según el tab
    if (tabName === 'my-tasks') {
        loadMyTasks();
    } else if (tabName === 'team-tasks') {
        loadTeamTasks();
    }
}

// Función para cargar mis tareas
function loadMyTasks() {
    const tbody = document.getElementById('my-tasks-table-body');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="8" class="text-center">Cargando mis tareas...</td></tr>';
    
    fetch('?route=clan_leader/get-my-tasks')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTasksTable(data.tasks, 'my-tasks-table-body');
            } else {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error al cargar tareas: ' + data.message + '</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error de conexión</td></tr>';
        });
}

// Función para cargar tareas del equipo
function loadTeamTasks() {
    // Por ahora mantener la tabla actual del equipo
    console.log('Cargando tareas del equipo...');
}

// Función para renderizar tabla de tareas
function renderTasksTable(tasks, tbodyId) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    
    if (tasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay tareas disponibles</td></tr>';
        return;
    }
    
    let html = '';
    tasks.forEach(task => {
        const priority = task.priority || 'medium';
        const status = task.status || 'pending';
        const progress = task.completion_percentage || 0;
        const daysUntilDue = task.days_until_due || 0;
        
        html += `
            <tr class="task-row priority-${priority} ${daysUntilDue < 0 ? 'overdue' : ''} ${status === 'completed' ? 'completed' : ''}" data-task-id="${task.task_id}">
                <td class="td-checkbox">
                    <input type="checkbox" 
                           id="task-${task.task_id}" 
                           data-task-id="${task.task_id}"
                           ${status === 'completed' ? 'checked' : ''}
                           onchange="toggleTaskStatus('${task.task_id}', this.checked)">
                </td>
                <td class="td-priority">
                    <span class="priority-badge priority-${priority}">
                        ${priority === 'critical' ? 'Urgente' : priority === 'high' ? 'Alta' : priority === 'low' ? 'Baja' : 'Media'}
                    </span>
                </td>
                <td class="td-task">
                    <div class="task-info">
                        <div class="task-name">${task.task_name || 'Sin nombre'}</div>
                        ${task.description ? '<div class="task-description">' + task.description.substring(0, 100) + (task.description.length > 100 ? '...' : '') + '</div>' : ''}
                    </div>
                </td>
                <td class="td-project">
                    <span class="project-name">${task.project_name || 'Sin proyecto'}</span>
                </td>
                <td class="td-due-date">
                    ${task.due_date ? '<span class="due-date">' + task.due_date + '</span>' : '<span class="no-date">Sin fecha</span>'}
                </td>
                <td class="td-status">
                    <span class="status-badge status-${status}">
                        ${status === 'completed' ? 'Completado' : status === 'in_progress' ? 'En Progreso' : 'Pendiente'}
                    </span>
                </td>
                <td class="td-progress">
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${progress}%"></div>
                        </div>
                        <span class="progress-text">${progress}%</span>
                    </div>
                </td>
                <td class="td-actions">
                    <a href="?route=clan_leader/get-task-details&task_id=${task.task_id}" class="btn-action btn-view" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="?route=clan_leader/tasks&action=edit&task_id=${task.task_id}" class="btn-action btn-edit" title="Editar tarea">
                        <i class="fas fa-edit"></i>
                    </a>
                    ${task.created_by_user_id == <?= $user['user_id'] ?? 0 ?> ? '<button class="btn-action btn-clone" onclick="openCloneTaskModal(' + task.task_id + ')" title="Clonar tarea"><i class="fas fa-copy"></i></button>' : ''}
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Inicializar tabs al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar qué tab está activo por URL o por defecto
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'my-tasks';
    
    switchTab(activeTab);
});

// Timestamp para forzar recarga: <?= time() ?>
// Cache-bust version: v3.1.<?= date('His') ?>
</script>

<!-- Estilos para el modal de clonación -->
<style>
.clone-task-modal {
    max-width: 600px;
    width: 90%;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-clone {
    background-color: #8b5cf6;
    color: white;
}

.btn-clone:hover {
    background-color: #7c3aed;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .clone-task-modal {
        width: 95%;
        margin: 1rem;
    }
}

/* Estilos para el modal de clonación de proyectos */
.clone-project-modal {
    max-width: 600px;
    width: 90%;
}

@media (max-width: 768px) {
    .clone-project-modal {
        width: 95%;
        margin: 1rem;
    }
}
</style>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css'];
require_once __DIR__ . '/../layout.php';
?> 