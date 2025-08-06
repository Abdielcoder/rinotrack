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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Simple */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 12px;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .breadcrumb {
            margin-bottom: 15px;
        }

        .breadcrumb a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb a:hover {
            color: white;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .project-name {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .header-actions {
            margin-top: 20px;
        }

        .btn-create {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-create:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Project Description */
        .project-description {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .project-description h3 {
            margin-bottom: 10px;
            color: #1f2937;
        }

        /* Filters */
        .filters {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filters h3 {
            margin-bottom: 15px;
            color: #1f2937;
        }

        .filter-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
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
            min-width: 150px;
        }

        /* Tasks Grid */
        .tasks-grid {
            display: grid;
            gap: 20px;
        }

        .task-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #e5e7eb;
        }

        .task-card.urgent {
            border-left-color: #ef4444;
        }

        .task-card.high {
            border-left-color: #f59e0b;
        }

        .task-card.medium {
            border-left-color: #3b82f6;
        }

        .task-card.low {
            border-left-color: #10b981;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .task-title {
            flex: 1;
        }

        .task-title h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #1f2937;
        }

        .task-description {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .task-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #6b7280;
        }

        .meta-item i {
            width: 16px;
        }

        .task-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
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

        .task-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
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

        .task-checkbox {
            margin-right: 15px;
        }

        .task-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: #374151;
        }

        /* Toast */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            max-width: 350px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .toast.success {
            background: #10b981;
        }

        .toast.error {
            background: #ef4444;
        }

        .toast.info {
            background: #3b82f6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header {
                padding: 20px 0;
            }

            .page-title {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .filter-row {
                flex-direction: column;
                gap: 15px;
            }

            .task-header {
                flex-direction: column;
                gap: 10px;
            }

            .task-meta {
                flex-direction: column;
                gap: 10px;
            }

            .task-actions {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="breadcrumb">
                    <a href="?route=clan_leader/tasks">
                        <i class="fas fa-arrow-left"></i>
                        Volver a Proyectos
                    </a>
                </div>
                <h1 class="page-title">Tareas del Proyecto</h1>
                <p class="project-name"><?= htmlspecialchars($project['project_name']) ?></p>
                
                <div class="header-actions">
                    <a href="?route=clan_leader/tasks&action=create&project_id=<?= $project['project_id'] ?>" class="btn-create">
                        <i class="fas fa-plus"></i>
                        Nueva Tarea
                    </a>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= count($tasks) ?></div>
                        <div class="stat-label">Total Tareas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= count(array_filter($tasks, function($task) { return $task['status'] === 'completed'; })) ?></div>
                        <div class="stat-label">Completadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= count(array_filter($tasks, function($task) { return $task['status'] === 'pending'; })) ?></div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <?php if ($project['kpi_points'] > 0): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($project['kpi_points']) ?></div>
                        <div class="stat-label">Puntos KPI</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Project Description -->
        <?php if (!empty($project['description'])): ?>
        <div class="project-description">
            <h3>Descripción del Proyecto</h3>
            <p><?= htmlspecialchars($project['description']) ?></p>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filters">
            <h3>Filtros</h3>
            <div class="filter-row">
                <div class="filter-group">
                    <label for="statusFilter">Estado:</label>
                    <select id="statusFilter" onchange="filterTasks()">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completada</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="priorityFilter">Prioridad:</label>
                    <select id="priorityFilter" onchange="filterTasks()">
                        <option value="">Todas las prioridades</option>
                        <option value="low">Baja</option>
                        <option value="medium">Media</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tasks -->
        <div class="tasks-grid">
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                <div class="task-card <?= $task['priority'] ?>" data-status="<?= $task['status'] ?>" data-priority="<?= $task['priority'] ?>">
                    <div class="task-header">
                        <div class="task-title">
                            <div class="task-checkbox">
                                <input type="checkbox" 
                                       <?= $task['status'] === 'completed' ? 'checked' : '' ?>
                                       onchange="toggleTaskStatus(<?= $task['task_id']; ?>, this.checked)">
                            </div>
                            <h3><?= htmlspecialchars($task['task_name']) ?></h3>
                        </div>
                        <span class="task-status <?= $task['status'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
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
                        
                        <?php if ($task['completion_percentage'] > 0): ?>
                        <div class="meta-item">
                            <i class="fas fa-percentage"></i>
                            <span><?= $task['completion_percentage'] ?>% completado</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="task-actions">
                        <button class="action-btn view-btn" onclick="viewTaskDetails(<?= $task['task_id'] ?>)">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="action-btn edit-btn" onclick="editTask(<?= $task['task_id'] ?>)">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteTask(<?= $task['task_id'] ?>)">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No hay tareas en este proyecto</h3>
                    <p>Crea la primera tarea para comenzar a trabajar en este proyecto.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Funciones simples y directas
        function filterTasks() {
            const statusFilter = document.getElementById('statusFilter').value;
            const priorityFilter = document.getElementById('priorityFilter').value;
            const taskCards = document.querySelectorAll('.task-card');
            
            taskCards.forEach(card => {
                const status = card.dataset.status;
                const priority = card.dataset.priority;
                
                const statusMatch = !statusFilter || status === statusFilter;
                const priorityMatch = !priorityFilter || priority === priorityFilter;
                
                if (statusMatch && priorityMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
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
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('Error al eliminar la tarea: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error al eliminar la tarea', 'error');
                });
            }
        }

        function toggleTaskStatus(taskId, isCompleted) {
            fetch('?route=clan_leader/toggle-task-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `taskId=${taskId}&isCompleted=${isCompleted}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Estado de tarea actualizado', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('Error al actualizar estado: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al actualizar estado', 'error');
            });
        }

        function showToast(message, type = 'info') {
            // Remover toast existente
            const existingToast = document.querySelector('.toast');
            if (existingToast) {
                existingToast.remove();
            }

            // Crear nuevo toast
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            // Remover después de 3 segundos
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 3000);
        }
    </script>
</body>
</html> 