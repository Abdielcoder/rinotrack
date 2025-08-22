<?php
// Verificar que tenemos los datos necesarios
if (!isset($project) || !isset($tasks)) {
    echo '<div style="padding: 20px; text-align: center;">Error: Datos del proyecto no disponibles</div>';
    return;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas del Proyecto - <?= htmlspecialchars($project['project_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Botones de Navegación - Estilo task_details */
        .navigation-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
        }

        .nav-btn {
            background: #f3f4f6;
            color: #374151;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .nav-btn:hover {
            background: #e5e7eb;
            color: #1f2937;
            text-decoration: none;
        }

        .nav-btn.primary {
            background: #1e3a8a;
            color: white;
        }

        .nav-btn.primary:hover {
            background: #1e40af;
            color: white;
        }

        .nav-btn.success {
            background: #10b981;
            color: white;
        }

        .nav-btn.success:hover {
            background: #059669;
            color: white;
        }

        /* Header del Proyecto - Estilo task_details */
        .project-header {
            background: #e0e7ff;
            color: #1e3a8a;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #c7d2fe;
        }

        .project-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .project-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .project-details h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .project-meta {
            font-size: 16px;
            opacity: 0.9;
        }

        .project-stats {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Filtros Compactos */
        .filters-section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filters-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .view-toggle {
            display: flex;
            background: #f3f4f6;
            border-radius: 8px;
            padding: 4px;
            gap: 2px;
        }

        .view-toggle-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.2s ease;
        }

        .view-toggle-btn.active {
            background: white;
            color: #1e3a8a;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .view-toggle-btn:hover:not(.active) {
            color: #374151;
        }

        .filters-row {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 150px;
        }

        .filter-group label {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
        }

        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background: white;
        }

        /* Vista de Lista/Tabla por defecto */
        .tasks-container {
            display: block;
        }

        .tasks-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .tasks-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .tasks-table th {
            background: #f8fafc;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .tasks-table td {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .tasks-table tr:hover {
            background: #f9fafb;
        }

        .task-title-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .task-title-cell input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .task-title-text {
            font-weight: 600;
            color: #1f2937;
            max-width: 300px;
        }

        .task-description-cell {
            max-width: 250px;
            color: #6b7280;
            font-size: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .status-badge-cell {
            white-space: nowrap;
        }

        .status-badge-cell .task-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-cell {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .priority-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .priority-indicator.urgent {
            background: #ef4444;
        }

        .priority-indicator.high {
            background: #f59e0b;
        }

        .priority-indicator.medium {
            background: #3b82f6;
        }

        .priority-indicator.low {
            background: #10b981;
        }

        .progress-cell {
            min-width: 120px;
        }

        .progress-table {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-bar-table {
            flex: 1;
            background: #e5e7eb;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill-table {
            background: #10b981;
            height: 100%;
            transition: width 0.3s ease;
        }

        .progress-text-table {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            min-width: 35px;
        }

        .actions-cell {
            display: flex;
            gap: 6px;
        }

        .action-btn-table {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .action-btn-table.view {
            background: #f3f4f6;
            color: #374151;
        }

        .action-btn-table.edit {
            background: #1e3a8a;
            color: white;
        }

        .action-btn-table.delete {
            background: #ef4444;
            color: white;
        }

        .action-btn-table:hover {
            opacity: 0.8;
            text-decoration: none;
        }

        /* Ocultar elementos según la vista */
        .tasks-container.cards-view .tasks-table {
            display: none !important;
        }

        .tasks-container.list-view .task-card {
            display: none !important;
        }

        /* Asegurar que solo se muestre la vista correcta */
        .tasks-container.list-view {
            display: block;
        }

        .tasks-container.cards-view {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        /* Tarjetas de Tareas Compactas */
        .task-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s ease;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .task-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .task-card-content {
            position: relative;
            z-index: 1;
        }

        /* Área clickeable que cubre toda la tarjeta */
        .task-card-clickable {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            cursor: pointer;
        }

        /* Las acciones deben estar por encima del área clickeable */
        .task-actions {
            position: relative;
            z-index: 3;
        }

        /* Prevenir click en elementos interactivos */
        .task-checkbox,
        .task-checkbox input,
        .action-btn {
            position: relative;
            z-index: 4;
        }

        .task-card.urgent {
            border-left: 4px solid #ef4444;
        }

        .task-card.high {
            border-left: 4px solid #f59e0b;
        }

        .task-card.medium {
            border-left: 4px solid #3b82f6;
        }

        .task-card.low {
            border-left: 4px solid #10b981;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .task-title-section {
            flex: 1;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .task-checkbox {
            margin-top: 4px;
        }

        .task-checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .task-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.4;
            margin: 0;
            flex: 1;
        }

        .task-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .task-status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .task-status.in_progress {
            background: #dbeafe;
            color: #1e40af;
        }

        .task-status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .task-status.blocked {
            background: #fee2e2;
            color: #991b1b;
        }

        .task-description {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #6b7280;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-item i {
            color: #9ca3af;
        }

        .task-progress {
            margin-bottom: 15px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .progress-bar {
            background: #e5e7eb;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            background: #10b981;
            height: 100%;
            transition: width 0.3s ease;
        }

        .task-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .action-btn.view {
            background: #f3f4f6;
            color: #374151;
        }

        .action-btn.view:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .action-btn.edit {
            background: #1e3a8a;
            color: white;
        }

        .action-btn.edit:hover {
            background: #1e40af;
        }

        .action-btn.delete {
            background: #ef4444;
            color: white;
        }

        .action-btn.delete:hover {
            background: #dc2626;
        }

        /* Estado Vacío */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .empty-state i {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            color: #374151;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .project-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .project-info {
                flex-direction: column;
                gap: 15px;
            }

            .project-stats {
                gap: 20px;
            }

            .tasks-container.cards-view {
                grid-template-columns: 1fr;
            }

            .navigation-buttons {
                flex-wrap: wrap;
            }

            .filters-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .filters-row {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                min-width: auto;
            }

            /* Tabla responsiva */
            .tasks-table {
                overflow-x: auto;
            }

            .tasks-table table {
                min-width: 800px;
            }

            .tasks-table th,
            .tasks-table td {
                padding: 12px 8px;
                font-size: 13px;
            }

            .task-title-text {
                max-width: 200px;
            }

            .task-description-cell {
                max-width: 150px;
            }
        }

        @media (max-width: 480px) {
            .task-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .task-actions {
                justify-content: flex-start;
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Botones de Navegación -->
        <div class="navigation-buttons">
            <a href="?route=clan_leader" class="nav-btn success">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="?route=clan_leader/tasks" class="nav-btn">
                <i class="fas fa-arrow-left"></i> Todas las Tareas
            </a>
            <a href="?route=clan_leader/projects" class="nav-btn">
                <i class="fas fa-folder"></i> Proyectos
            </a>
            <a href="?route=clan_leader/tasks&action=create&project_id=<?= $project['project_id'] ?>" class="nav-btn primary">
                <i class="fas fa-plus"></i> Nueva Tarea
            </a>
        </div>

        <!-- Header del Proyecto -->
        <div class="project-header">
            <div class="project-info">
                <div class="project-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="project-details">
                    <h1><?= htmlspecialchars($project['project_name']) ?></h1>
                    <div class="project-meta">
                        <?php if (!empty($project['description'])): ?>
                            <?= htmlspecialchars($project['description']) ?>
                        <?php else: ?>
                            Proyecto de <?= htmlspecialchars($project['clan_name'] ?? 'Equipo') ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="project-stats">
                <div class="stat-item">
                    <span class="stat-number"><?= count($tasks) ?></span>
                    <span class="stat-label">Total Tareas</span>
                </div>
                <div class="stat-item">
                    <?php
                    $completedTasks = array_filter($tasks, fn($task) => $task['status'] === 'completed');
                    $progressPercentage = count($tasks) > 0 ? round((count($completedTasks) / count($tasks)) * 100) : 0;
                    ?>
                    <span class="stat-number"><?= $progressPercentage ?>%</span>
                    <span class="stat-label">Completado</span>
                </div>
                <div class="stat-item">
                    <?php
                    $pendingTasks = array_filter($tasks, fn($task) => in_array($task['status'], ['pending', 'in_progress']));
                    ?>
                    <span class="stat-number"><?= count($pendingTasks) ?></span>
                    <span class="stat-label">Pendientes</span>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <div class="filters-header">
                <h3 class="filters-title">Filtros y Vista</h3>
                <div class="view-toggle">
                    <button class="view-toggle-btn active" onclick="switchView('list')" data-view="list">
                        <i class="fas fa-list"></i> Lista
                    </button>
                    <button class="view-toggle-btn" onclick="switchView('cards')" data-view="cards">
                        <i class="fas fa-th-large"></i> Cards
                    </button>
                </div>
            </div>
            <div class="filters-row">
                <div class="filter-group">
                    <label for="statusFilter">Estado</label>
                    <select id="statusFilter" onchange="filterTasks()">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completada</option>
                        <option value="blocked">Bloqueada</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="priorityFilter">Prioridad</label>
                    <select id="priorityFilter" onchange="filterTasks()">
                        <option value="">Todas las prioridades</option>
                        <option value="urgent">Urgente</option>
                        <option value="high">Alta</option>
                        <option value="medium">Media</option>
                        <option value="low">Baja</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="searchInput">Buscar</label>
                    <input type="text" id="searchInput" placeholder="Buscar tareas..." 
                           style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;"
                           onkeyup="filterTasks()">
                </div>
            </div>
        </div>

        <!-- Tareas -->
        <div class="tasks-container list-view">
            <?php if (!empty($tasks)): ?>
                <!-- Vista de Tabla -->
                <div class="tasks-table">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Tarea</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Asignado</th>
                                <th>Fecha</th>
                                <th>Progreso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                            <tr data-status="<?= $task['status'] ?>" data-priority="<?= $task['priority'] ?>" data-search-text="<?= htmlspecialchars(strtolower($task['task_name'] . ' ' . ($task['description'] ?? '') . ' ' . ($task['assigned_to_fullname'] ?? ''))) ?>">
                                <td>
                                    <input type="checkbox" 
                                           <?= $task['status'] === 'completed' ? 'checked' : '' ?>
                                           onchange="toggleTaskStatus(<?= $task['task_id']; ?>, this.checked)">
                                </td>
                                <td>
                                    <div class="task-title-text"><?= htmlspecialchars($task['task_name']) ?></div>
                                </td>
                                <td>
                                    <div class="task-description-cell">
                                        <?= htmlspecialchars($task['description'] ?? '') ?>
                                    </div>
                                </td>
                                <td class="status-badge-cell">
                                    <span class="task-status <?= $task['status'] ?>">
                                        <?php
                                        $statusLabels = [
                                            'pending' => 'Pendiente',
                                            'in_progress' => 'En Progreso',
                                            'completed' => 'Completada',
                                            'blocked' => 'Bloqueada'
                                        ];
                                        echo $statusLabels[$task['status']] ?? 'Desconocido';
                                        ?>
                                    </span>
                                </td>
                                <td class="priority-cell">
                                    <div class="priority-indicator <?= $task['priority'] ?>"></div>
                                    <span><?= ucfirst($task['priority']) ?></span>
                                </td>
                                <td>
                                    <?= htmlspecialchars($task['assigned_to_fullname'] ?? '-') ?>
                                </td>
                                <td>
                                    <?= !empty($task['due_date']) ? date('d/m/Y', strtotime($task['due_date'])) : '-' ?>
                                </td>
                                <td class="progress-cell">
                                    <?php if (isset($task['completion_percentage']) && $task['completion_percentage'] > 0): ?>
                                    <div class="progress-table">
                                        <div class="progress-bar-table">
                                            <div class="progress-fill-table" style="width: <?= $task['completion_percentage'] ?>%"></div>
                                        </div>
                                        <span class="progress-text-table"><?= $task['completion_percentage'] ?>%</span>
                                    </div>
                                    <?php else: ?>
                                    <span class="progress-text-table">0%</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="?route=clan_leader/get-task-details&task_id=<?= $task['task_id'] ?>" class="action-btn-table view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?route=clan_leader/tasks&action=edit&task_id=<?= $task['task_id'] ?>" class="action-btn-table edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteTask(<?= $task['task_id'] ?>)" class="action-btn-table delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vista de Cards -->
                <?php foreach ($tasks as $task): ?>
                <div class="task-card <?= $task['priority'] ?>" data-status="<?= $task['status'] ?>" data-priority="<?= $task['priority'] ?>" data-search-text="<?= htmlspecialchars(strtolower($task['task_name'] . ' ' . ($task['description'] ?? '') . ' ' . ($task['assigned_to_fullname'] ?? ''))) ?>">
                    <!-- Área clickeable para navegar a detalles -->
                    <div class="task-card-clickable" onclick="goToTaskDetails(<?= $task['task_id'] ?>)"></div>
                    
                    <div class="task-card-content">
                        <div class="task-header">
                            <div class="task-title-section">
                                <div class="task-checkbox">
                                    <input type="checkbox" 
                                           <?= $task['status'] === 'completed' ? 'checked' : '' ?>
                                           onchange="toggleTaskStatus(<?= $task['task_id']; ?>, this.checked)"
                                           onclick="event.stopPropagation()">
                                </div>
                                <h3 class="task-title"><?= htmlspecialchars($task['task_name']) ?></h3>
                            </div>
                            <span class="task-status <?= $task['status'] ?>">
                                <?php
                                $statusLabels = [
                                    'pending' => 'Pendiente',
                                    'in_progress' => 'En Progreso',
                                    'completed' => 'Completada',
                                    'blocked' => 'Bloqueada'
                                ];
                                echo $statusLabels[$task['status']] ?? 'Desconocido';
                                ?>
                            </span>
                        </div>
                    
                    <?php if (!empty($task['description'])): ?>
                    <div class="task-description">
                        <?= htmlspecialchars($task['description']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="task-meta">
                        <?php if (!empty($task['assigned_to_fullname'])): ?>
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span><?= htmlspecialchars($task['assigned_to_fullname']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($task['due_date'])): ?>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span><?= date('d/m/Y', strtotime($task['due_date'])) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="meta-item">
                            <i class="fas fa-flag"></i>
                            <span><?= ucfirst($task['priority']) ?></span>
                        </div>
                    </div>
                    
                    <?php if (isset($task['completion_percentage']) && $task['completion_percentage'] > 0): ?>
                    <div class="task-progress">
                        <div class="progress-label">
                            <span>Progreso</span>
                            <span><?= $task['completion_percentage'] ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $task['completion_percentage'] ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                        <div class="task-actions">
                            <a href="?route=clan_leader/get-task-details&task_id=<?= $task['task_id'] ?>" class="action-btn view" onclick="event.stopPropagation()">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="?route=clan_leader/tasks&action=edit&task_id=<?= $task['task_id'] ?>" class="action-btn edit" onclick="event.stopPropagation()">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button onclick="deleteTask(<?= $task['task_id'] ?>)" class="action-btn delete">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div> <!-- Cerrar task-card-content -->
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No hay tareas en este proyecto</h3>
                    <p>Crea la primera tarea para comenzar a trabajar en este proyecto.</p>
                    <a href="?route=clan_leader/tasks&action=create&project_id=<?= $project['project_id'] ?>" class="nav-btn primary">
                        <i class="fas fa-plus"></i> Crear Primera Tarea
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Función para navegar a detalles de tarea
        function goToTaskDetails(taskId) {
            window.location.href = `?route=clan_leader/get-task-details&task_id=${taskId}`;
        }

        // Función para cambiar vista
        function switchView(viewType) {
            const container = document.querySelector('.tasks-container');
            const toggleButtons = document.querySelectorAll('.view-toggle-btn');
            const table = document.querySelector('.tasks-table');
            const cards = document.querySelectorAll('.task-card');
            
            // Limpiar clases anteriores y aplicar nueva
            container.className = `tasks-container ${viewType}-view`;
            
            // Forzar visibilidad según el tipo de vista
            if (viewType === 'list') {
                // Mostrar tabla, ocultar cards
                if (table) table.style.display = 'block';
                cards.forEach(card => card.style.display = 'none');
            } else {
                // Ocultar tabla, mostrar cards
                if (table) table.style.display = 'none';
                cards.forEach(card => card.style.display = 'block');
            }
            
            // Actualizar botones activos
            toggleButtons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.view === viewType) {
                    btn.classList.add('active');
                }
            });
            
            // Guardar preferencia en localStorage
            localStorage.setItem('tasksViewType', viewType);
            
            // Re-aplicar filtros después de un breve delay
            setTimeout(() => filterTasks(), 100);
        }

        // Funciones optimizadas
        function filterTasks() {
            const statusFilter = document.getElementById('statusFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            
            // Filtrar cards
            const taskCards = document.querySelectorAll('.task-card');
            // Filtrar filas de tabla
            const tableRows = document.querySelectorAll('.tasks-table tbody tr');
            
            let visibleCount = 0;
            
            // Determinar vista activa
            const container = document.querySelector('.tasks-container');
            const isListView = container.classList.contains('list-view');
            
            if (isListView) {
                // Filtrar solo filas de tabla en vista lista
                tableRows.forEach(row => {
                    const status = row.dataset.status;
                    const priority = row.dataset.priority;
                    const searchText = row.dataset.searchText;
                    
                    const statusMatch = !statusFilter || status === statusFilter;
                    const priorityMatch = !priorityFilter || priority === priorityFilter;
                    const searchMatch = !searchInput || searchText.includes(searchInput);
                    
                    if (statusMatch && priorityMatch && searchMatch) {
                        row.style.display = 'table-row';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Asegurar que los cards estén ocultos
                taskCards.forEach(card => card.style.display = 'none');
            } else {
                // Filtrar solo cards en vista cards
                taskCards.forEach(card => {
                    const status = card.dataset.status;
                    const priority = card.dataset.priority;
                    const searchText = card.dataset.searchText;
                    
                    const statusMatch = !statusFilter || status === statusFilter;
                    const priorityMatch = !priorityFilter || priority === priorityFilter;
                    const searchMatch = !searchInput || searchText.includes(searchInput);
                    
                    if (statusMatch && priorityMatch && searchMatch) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Asegurar que la tabla esté oculta
                const table = document.querySelector('.tasks-table');
                if (table) table.style.display = 'none';
            }
            
            // Mostrar mensaje si no hay resultados
            let noResultsMsg = container.querySelector('.no-results');
            
            if (visibleCount === 0 && (taskCards.length > 0 || tableRows.length > 0)) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'no-results empty-state';
                    noResultsMsg.innerHTML = `
                        <i class="fas fa-search"></i>
                        <h3>No se encontraron tareas</h3>
                        <p>Prueba ajustando los filtros o términos de búsqueda.</p>
                    `;
                    container.appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }

        function toggleTaskStatus(taskId, isCompleted) {
            // Prevenir que el click del checkbox active la navegación del card
            event.stopPropagation();
            
            const status = isCompleted ? 'completed' : 'pending';
            
            fetch('?route=clan_leader/toggle-task-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: taskId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la UI
                    const taskCard = document.querySelector(`[data-status][data-priority]`);
                    const statusBadge = taskCard?.querySelector('.task-status');
                    if (statusBadge) {
                        statusBadge.className = `task-status ${status}`;
                        statusBadge.textContent = isCompleted ? 'Completada' : 'Pendiente';
                    }
                    
                    // Mostrar notificación
                    showNotification(data.message || 'Estado actualizado', 'success');
                } else {
                    // Revertir checkbox si hubo error
                    const checkbox = document.querySelector(`input[onchange*="${taskId}"]`);
                    if (checkbox) checkbox.checked = !isCompleted;
                    showNotification(data.message || 'Error al actualizar', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revertir checkbox
                const checkbox = document.querySelector(`input[onchange*="${taskId}"]`);
                if (checkbox) checkbox.checked = !isCompleted;
                showNotification('Error de conexión', 'error');
            });
        }

        function deleteTask(taskId) {
            // Prevenir que el click del botón active la navegación del card
            event.stopPropagation();
            
            if (confirm('¿Estás seguro de que quieres eliminar esta tarea? Esta acción no se puede deshacer.')) {
                fetch('?route=clan_leader/delete-task', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ task_id: taskId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover la tarjeta de la UI
                        const taskCard = document.querySelector(`[onclick*="${taskId}"]`)?.closest('.task-card');
                        if (taskCard) {
                            taskCard.style.opacity = '0';
                            taskCard.style.transform = 'scale(0.95)';
                            setTimeout(() => taskCard.remove(), 300);
                        }
                        showNotification('Tarea eliminada exitosamente', 'success');
                    } else {
                        showNotification(data.message || 'Error al eliminar tarea', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error de conexión', 'error');
                });
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-weight: 600;
                z-index: 1000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => notification.style.transform = 'translateX(0)', 100);
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Inicializar filtros al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar vista guardada o usar lista por defecto
            const savedView = localStorage.getItem('tasksViewType') || 'list';
            switchView(savedView);
            
            // Auto-foco en búsqueda con Ctrl+F
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    document.getElementById('searchInput').focus();
                }
            });
            
            // Responsive: cambiar a vista de cards en móvil automáticamente
            function checkResponsive() {
                if (window.innerWidth <= 768) {
                    const container = document.querySelector('.tasks-container');
                    if (container.classList.contains('list-view')) {
                        // En móvil, sugerir cambio a cards si la tabla es muy ancha
                        const table = document.querySelector('.tasks-table table');
                        if (table && table.scrollWidth > window.innerWidth) {
                            // No cambiar automáticamente, pero añadir scroll horizontal
                            document.querySelector('.tasks-table').style.overflowX = 'auto';
                        }
                    }
                }
            }
            
            checkResponsive();
            window.addEventListener('resize', checkResponsive);
        });
    </script>
</body>
</html>