<?php
// Guardar el contenido en una variable
ob_start();
?>

<?php include __DIR__ . '/../layout.php'; ?>

<div class="clan-leader-tasks-container">
    <!-- Header Mejorado -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">Gestión de Tareas</h1>
                <p class="page-subtitle">Administra las tareas de todos los proyectos de tu clan</p>
            </div>
            <div class="header-actions">
                <a href="?route=clan_leader" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
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
                            <div class="project-status status-<?= $project['status'] ?>">
                                <?= ucfirst($project['status']) ?>
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
                        
                        <div class="project-actions">
                            <a href="<?= APP_URL ?>?route=clan_leader/tasks&project_id=<?= $project['project_id'] ?>" class="btn-view-tasks" style="background:rgb(255, 255, 255); !important">
                                <i class="fas fa-eye"></i>
                                Ver Tareas
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($allTasks)): ?>
            <?php if (!empty($allTasks)): ?>
            <!-- Todas las Tareas del Clan -->
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
                                <th class="th-priority">Prioridad</th>
                                <th class="th-task">Tarea</th>
                                <th class="th-project">Proyecto</th>
                                <th class="th-assigned">Asignado</th>
                                <th class="th-due-date">Fecha Límite</th>
                                <th class="th-status">Estado</th>
                                <th class="th-points">Puntos</th>
                                <th class="th-actions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allTasks as $task): ?>
                            <tr class="task-row priority-<?= $task['priority'] ?> <?= ($task['days_until_due'] < 0) ? 'overdue' : '' ?> <?= ($task['status'] === 'completed') ? 'completed' : '' ?>">
                                <td class="td-priority">
                                    <span class="priority-badge priority-<?= $task['priority'] ?>">
                                        <?php 
                                        switch($task['priority']) {
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
                                        <div class="task-title"><?= htmlspecialchars($task['task_name']) ?></div>
                                        <?php if (!empty($task['description'])): ?>
                                        <div class="task-description"><?= htmlspecialchars(substr($task['description'], 0, 80)) ?><?= strlen($task['description']) > 80 ? '...' : '' ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="td-project">
                                    <span class="project-name"><?= htmlspecialchars($task['project_name']) ?></span>
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
                                <td class="td-points">
                                    <?php if ($task['automatic_points'] > 0): ?>
                                    <span class="points-value"><?= $task['automatic_points'] ?></span>
                                    <?php else: ?>
                                    <span class="no-points">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="td-actions">
                                    <div class="action-buttons">
                                        <a href="<?= APP_URL ?>?route=clan_leader/tasks&project_id=<?= $task['project_id'] ?>" class="btn-action btn-view" title="Ver proyecto">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= APP_URL ?>?route=clan_leader/tasks&action=edit&task_id=<?= $task['task_id'] ?>" class="btn-action btn-edit" title="Editar tarea">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 0;
    margin: 0;
}

/* Header Mejorado */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    gap: 1.5rem;
}

.project-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
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
    margin-bottom: 1rem;
}

.project-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    flex: 1;
}

.project-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
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

/* Stats de Proyecto */
.project-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
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
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.2rem;
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
    margin-bottom: 1rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
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

/* Acciones de Proyecto */
.project-actions {
    display: flex;
    justify-content: center;
}

.btn-view-tasks {
    background:rgb(243, 246, 250);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-view-tasks:hover {
    background:rgb(22, 22, 24);
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
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

.th-points, .td-points {
    width: 80px;
    text-align: center;
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
</style>

<script>
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
</script> 