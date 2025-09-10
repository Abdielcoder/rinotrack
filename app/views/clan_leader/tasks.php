<?php
// Guardar el contenido en una variable
ob_start();
?>

<!-- Cargar CSS de rediseño -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/clan-leader-redesign.css">

<style>
/* Banner informativo */
.info-banner {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 1px solid #90caf9;
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.1);
}

.info-content {
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: space-between;
}

.info-content i {
    color: #1976d2;
    font-size: 20px;
}

.info-content span {
    color: #0d47a1;
    font-weight: 500;
    flex: 1;
}

.btn-info {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #2196f3;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-info:hover {
    background: #1976d2;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
}

@media (max-width: 768px) {
    .info-content {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
}
</style>

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
        <!-- Nota: La sección de proyectos se ha movido a /projects para mejor organización -->
        <div class="info-banner">
            <div class="info-content">
                <i class="fas fa-info-circle"></i>
                <span>La gestión de proyectos se ha movido a una sección dedicada.</span>
                <a href="?route=clan_leader/projects" class="btn-info">
                    <i class="fas fa-project-diagram"></i>
                    Ir a Proyectos
                </a>
            </div>
        </div>

        <!-- Debug: Verificar si allTasks está definido -->
        <?php 
        error_log("DEBUG TABS - allTasks isset: " . (isset($allTasks) ? 'YES' : 'NO'));
        if (isset($allTasks)) {
            error_log("DEBUG TABS - allTasks count: " . count($allTasks));
        }
        ?>

        <!-- Tabs Consistentes con Dashboard -->
        <div class="tasks-section-minimal">
            <div class="tasks-tabs-minimal">
                <button class="tasks-tab-button active" onclick="switchTab('my-tasks')" id="my-tasks-tab">
                    <i class="fas fa-user"></i>
                    Mis Tareas
                </button>
                <button class="tasks-tab-button" onclick="switchTab('team-tasks')" id="team-tasks-tab">
                    <i class="fas fa-users"></i>
                    Equipo
                </button>
            </div>
        </div>

        <!-- Tab: Mis Tareas -->
        <div id="my-tasks-content" class="tab-content active">
            <div class="all-tasks-section">
                <div class="section-header">
                    <h2 class="section-title">Mis Tareas Asignadas</h2>
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
                                               id="searchInputMyTasks">
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="filter-actions">
                                    <button type="button" class="btn-apply-filters" onclick="applyFilters()">
                                        <i class="fas fa-filter"></i>
                                        Aplicar Filtros
                                    </button>
                                    <button type="button" class="btn-reset-filters" onclick="resetFilters('my-tasks')">
                                        <i class="fas fa-undo"></i>
                                        Resetear Filtros
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Tabla de Mis Tareas (cargada por JavaScript) -->
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
                            <!-- Las tareas del líder se cargarán por JavaScript -->
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
                    <h2 class="section-title">Tareas del Equipo</h2>
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
                                    <select name="status_filter" id="statusFilterTeam">
                                        <option value="">Todos</option>
                                        <option value="pending" <?= (($_GET['status_filter'] ?? '') === 'pending') ? 'selected' : '' ?>>Pendiente</option>
                                        <option value="in_progress" <?= (($_GET['status_filter'] ?? '') === 'in_progress') ? 'selected' : '' ?>>En Progreso</option>
                                        <option value="completed" <?= (($_GET['status_filter'] ?? '') === 'completed') ? 'selected' : '' ?>>Completado</option>
                                    </select>
                                </div>
                                
                                <div class="filter-item">
                                    <label for="perPageTeam">Mostrar:</label>
                                    <select name="per_page" id="perPageTeam">
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
                                               placeholder="Buscar tareas del equipo..."
                                               class="search-input"
                                               id="searchInputTeam">
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="filter-actions">
                                    <button type="button" class="btn-apply-filters" onclick="applyFilters()">
                                        <i class="fas fa-filter"></i>
                                        Aplicar Filtros
                                    </button>
                                    <button type="button" class="btn-reset-filters" onclick="resetFilters('team-tasks')">
                                        <i class="fas fa-undo"></i>
                                        Resetear Filtros
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Tabla de Tareas del Equipo (cargada por JavaScript) -->
                <div class="tasks-table-container">
                    <table class="tasks-table">
                        <thead>
                            <tr>
                                <th class="th-checkbox" style="width: 40px;">
                                    <input type="checkbox" id="select-all-team" onchange="toggleAllTasks(this, 'team')">
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
                        <tbody id="team-tasks-table-body">
                            <!-- Las tareas del equipo se cargarán por JavaScript -->
                            <tr><td colspan="9" class="text-center">Cargando tareas del equipo...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
    background: transparent;
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

// Las funciones de búsqueda automática fueron removidas
// Ahora se usan los botones "Aplicar Filtros" para mejor control
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
    console.log('🔄 switchTab llamado con:', tabName);
    
    // Ocultar todos los tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
        content.style.display = 'none';
    });
    
    // Remover active de todos los tab buttons
    document.querySelectorAll('.tasks-tab-button, .tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Mostrar el tab content seleccionado
    const targetContent = document.getElementById(tabName + '-content');
    if (targetContent) {
        targetContent.classList.add('active');
        targetContent.style.display = 'block';
    }
    
    // Activar el tab button seleccionado
    const targetButton = document.getElementById(tabName + '-tab');
    if (targetButton) {
        targetButton.classList.add('active');
    }
    
    // Cargar datos según el tab (usando misma lógica del kanban)
    if (tabName === 'my-tasks') {
        console.log('🔵 Cargando MIS tareas con lógica del kanban...');
        loadMyTasksTable();
    } else if (tabName === 'team-tasks') {
        console.log('🟡 Cargando tareas del EQUIPO con lógica del kanban...');
        loadTeamTasksTable();
    }
}

// Función para cargar mis tareas
function loadMyTasks() {
    console.log('🔵 loadMyTasks() iniciado');
    const tbody = document.getElementById('my-tasks-table-body');
    if (!tbody) {
        console.error('🔴 No se encontró tbody: my-tasks-table-body');
        return;
    }
    
    console.log('✅ tbody encontrado, cargando...');
    tbody.innerHTML = '<tr class="loading"><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando mis tareas...</td></tr>';
    
    // Agregar timestamp para evitar cache
    const timestamp = new Date().getTime();
    fetch(`?route=clan_leader/get-my-tasks&_=${timestamp}`, {
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache'
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log('🔵 === DEBUG loadMyTasks RESPUESTA ===');
            console.log('🔵 Respuesta completa:', data);
            console.log('🔵 Success:', data.success);
            console.log('🔵 Tasks count:', data.tasks ? data.tasks.length : 'NO TASKS');
            console.log('🔵 Debug backend:', data.debug_backend);
            if (data.success) {
                console.log('Tasks count from backend:', data.tasks.length);
                console.log('Tasks received:');
                data.tasks.forEach((task, index) => {
                    console.log(`  [${index}] ID=${task.task_id}, Name="${task.task_name}", Project="${task.project_name}", Assigned="${task.assigned_user_name || 'None'}"`);
                });
                console.log('Calling renderTasksTable...');
                renderTasksTable(data.tasks, 'my-tasks-table-body');
                console.log('=== END loadMyTasks FRONTEND ===');
            } else {
                console.error('Error loading my tasks:', data.message);
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error al cargar tareas: ' + data.message + '</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No se pudieron cargar las tareas</td></tr>';
        });
}

// Función para cargar tareas del equipo
function loadTeamTasks() {
    console.log('🟡 loadTeamTasks() iniciado');
    const tbody = document.getElementById('team-tasks-table-body');
    if (!tbody) {
        console.error('🔴 No se encontró tbody: team-tasks-table-body');
        return;
    }
    console.log('✅ tbody del equipo encontrado, cargando...');
    
    tbody.innerHTML = '<tr class="loading"><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando tareas del equipo...</td></tr>';
    
    // Agregar timestamp para evitar cache
    const timestamp = new Date().getTime();
    fetch(`?route=clan_leader/get-team-tasks&_=${timestamp}`, {
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache'
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log('🟡 === DEBUG loadTeamTasks RESPUESTA ===');
            console.log('🟡 Respuesta completa:', data);
            console.log('🟡 Success:', data.success);
            console.log('🟡 Tasks count:', data.tasks ? data.tasks.length : 'NO TASKS');
            console.log('🟡 Debug backend:', data.debug_backend);
            if (data.success) {
                console.log('Team tasks count from backend:', data.tasks.length);
                console.log('Team tasks received:');
                data.tasks.forEach((task, index) => {
                    console.log(`  [${index}] ID=${task.task_id}, Name="${task.task_name}", Project="${task.project_name}", Assigned="${task.assigned_user_name || 'None'}"`);
                });
                console.log('Calling renderTeamTasksTable...');
                renderTeamTasksTable(data.tasks, 'team-tasks-table-body');
                console.log('=== END loadTeamTasks FRONTEND ===');
            } else {
                console.error('Error loading team tasks:', data.message);
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error al cargar tareas del equipo: ' + data.message + '</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No se pudieron cargar las tareas del equipo</td></tr>';
        });
}

// Función para renderizar tabla de tareas del equipo
function renderTeamTasksTable(tasks, tbodyId) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    
    if (tasks.length === 0) {
        tbody.innerHTML = '<tr class="empty"><td colspan="9" class="text-center"><i class="fas fa-users-slash"></i><br>No hay tareas del equipo disponibles</td></tr>';
        return;
    }
    
    let html = '';
    const renderedTaskIds = new Set(); // Prevenir duplicados
    
    tasks.forEach(task => {
        // Verificar duplicados
        if (renderedTaskIds.has(task.task_id)) {
            console.warn('Tarea duplicada detectada y omitida:', task.task_id);
            return;
        }
        renderedTaskIds.add(task.task_id);
        
        const priority = task.priority || 'medium';
        const status = task.status || 'pending';
        // CORREGIDO: Si está completado, progreso debe ser 100%
        const progress = status === 'completed' ? 100 : (task.completion_percentage || 0);
        const daysUntilDue = task.days_until_due || 0;
        
        html += `
            <tr class="task-row priority-${priority} ${daysUntilDue < 0 ? 'overdue' : ''} ${status === 'completed' ? 'completed' : ''}" data-task-id="${task.task_id}">
                <td class="td-checkbox">
                    <input type="checkbox" 
                           id="team-task-${task.task_id}" 
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
                        <div class="task-name" title="${task.task_name || ''}">${task.task_name || 'Sin nombre'}</div>
                        ${task.description ? '<div class="task-description" title="' + task.description.replace(/"/g, '&quot;') + '\">' + task.description.substring(0, 100) + (task.description.length > 100 ? '...' : '') + '</div>' : ''}
                    </div>
                </td>
                <td class="td-project">
                    <span class="project-name" title="${task.project_name || ''}">${task.project_name || 'Sin proyecto'}</span>
                </td>
                <td class="td-assigned">
                    <span class="assigned-users" title="${task.assigned_user_name || task.all_assigned_users || ''}">${task.assigned_user_name || task.all_assigned_users || 'Sin asignar'}</span>
                </td>
                <td class="td-due-date">
                    ${task.due_date ? '<span class="due-date" title="' + task.due_date + '\">' + task.due_date + '</span>' : '<span class="no-date">Sin fecha</span>'}
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
                    <div class="action-buttons">
                        <a href="?route=clan_leader/get-task-details&task_id=${task.task_id}" class="btn-action btn-view" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="?route=clan_leader/tasks&action=edit&task_id=${task.task_id}" class="btn-action btn-edit" title="Editar tarea">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${task.created_by_user_id == <?= $user['user_id'] ?? 0 ?> ? '<button class="btn-action btn-clone" onclick="openCloneTaskModal(' + task.task_id + ')" title="Clonar tarea"><i class="fas fa-copy"></i></button>' : ''}
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Función para renderizar tabla de tareas (mis tareas)
function renderTasksTable(tasks, tbodyId) {
    console.log('=== DEBUG renderTasksTable FRONTEND ===');
    console.log('Rendering', tasks.length, 'tasks to tbody:', tbodyId);
    
    const tbody = document.getElementById(tbodyId);
    if (!tbody) {
        console.error('tbody not found:', tbodyId);
        return;
    }
    
    if (tasks.length === 0) {
        console.log('No tasks to render, showing empty message');
        tbody.innerHTML = '<tr class="empty"><td colspan="8" class="text-center"><i class="fas fa-inbox"></i><br>No hay tareas disponibles</td></tr>';
        return;
    }
    
    let html = '';
    const renderedTaskIds = new Set(); // Prevenir duplicados
    
    tasks.forEach(task => {
        // Verificar duplicados
        if (renderedTaskIds.has(task.task_id)) {
            console.warn('Tarea duplicada detectada y omitida:', task.task_id);
            return;
        }
        renderedTaskIds.add(task.task_id);
        
        const priority = task.priority || 'medium';
        const status = task.status || 'pending';
        // CORREGIDO: Si está completado, progreso debe ser 100%
        const progress = status === 'completed' ? 100 : (task.completion_percentage || 0);
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
                        <div class="task-name" title="${task.task_name || ''}">${task.task_name || 'Sin nombre'}</div>
                        ${task.description ? '<div class="task-description" title="' + task.description.replace(/"/g, '&quot;') + '\">' + task.description.substring(0, 100) + (task.description.length > 100 ? '...' : '') + '</div>' : ''}
                    </div>
                </td>
                <td class="td-project">
                    <span class="project-name" title="${task.project_name || ''}">${task.project_name || 'Sin proyecto'}</span>
                </td>
                <td class="td-due-date">
                    ${task.due_date ? '<span class="due-date" title="' + task.due_date + '\">' + task.due_date + '</span>' : '<span class="no-date">Sin fecha</span>'}
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
                    <div class="action-buttons">
                        <a href="?route=clan_leader/get-task-details&task_id=${task.task_id}" class="btn-action btn-view" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="?route=clan_leader/tasks&action=edit&task_id=${task.task_id}" class="btn-action btn-edit" title="Editar tarea">
                            <i class="fas fa-edit"></i>
                        </a>
                        ${task.created_by_user_id == <?= $user['user_id'] ?? 0 ?> ? '<button class="btn-action btn-clone" onclick="openCloneTaskModal(' + task.task_id + ')" title="Clonar tarea"><i class="fas fa-copy"></i></button>' : ''}
                    </div>
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
// Cache-bust version: v6.0.<?= date('His') ?>
// Updated: <?= date('Y-m-d H:i:s') ?>
// DEBUG MODE: ENABLED
console.log('🚀 Tasks.php cargado - Versión 6.0 - Debug activo');
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

/* ========== ESTILOS PARA TABS CONSISTENTES ========== */

/* Contenedor de tabs - FORZADO */
.tasks-section-minimal .tasks-tabs-minimal {
    display: flex !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-radius: 15px 15px 0 0 !important;
    padding: 12px !important;
    margin-bottom: 0 !important;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
    position: relative;
    overflow: hidden;
    border: none !important;
}

.tasks-tabs-minimal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

/* Botones de los tabs - FORZADO */
.tasks-section-minimal .tasks-tab-button {
    flex: 1 !important;
    padding: 18px 28px !important;
    border: none !important;
    background: rgba(255, 255, 255, 0.15) !important;
    color: rgba(255, 255, 255, 0.9) !important;
    cursor: pointer !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border-radius: 12px !important;
    margin: 0 6px !important;
    font-weight: 700 !important;
    font-size: 16px !important;
    letter-spacing: 0.8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 12px !important;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(15px) !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    text-transform: uppercase;
}

.tasks-tab-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.tasks-tab-button:hover::before {
    left: 100%;
}

.tasks-tab-button:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Tab activo - FORZADO */
.tasks-section-minimal .tasks-tab-button.active {
    background: linear-gradient(135deg, #ffffff 0%, #f1f3f4 100%) !important;
    color: #2c3e50 !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25) !important;
    transform: translateY(-4px) !important;
    border: 2px solid rgba(255, 255, 255, 0.8) !important;
    font-weight: 800 !important;
}

.tasks-section-minimal .tasks-tab-button.active i {
    color: #3498db !important;
    transform: scale(1.2) !important;
    text-shadow: 0 2px 4px rgba(52, 152, 219, 0.3) !important;
}

/* Iconos de los tabs - FORZADO */
.tasks-section-minimal .tasks-tab-button i {
    font-size: 20px !important;
    transition: all 0.3s ease !important;
    color: rgba(255, 255, 255, 0.9) !important;
}

.tasks-section-minimal .tasks-tab-button:hover i {
    transform: scale(1.1) !important;
    color: white !important;
}

/* Contenido de los tabs - FORZADO */
.tasks-section-minimal .tasks-tab-content {
    background: white !important;
    border-radius: 0 0 15px 15px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
    overflow: hidden;
    transition: all 0.4s ease !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    margin-top: 0 !important;
    min-height: 400px !important;
    padding: 20px !important;
}

.tasks-tab-content.active {
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Contenido de tareas */
.tasks-content-minimal {
    background: transparent;
    border-radius: 12px;
    padding: 20px;
    min-height: 350px;
}

/* Mensaje de carga */
.loading-message {
    text-align: center;
    padding: 80px 30px;
    color: #5a6c7d;
    font-size: 18px;
    font-weight: 500;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    margin: 30px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.loading-message i {
    font-size: 48px;
    margin-bottom: 20px;
    color: #3498db;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
}

/* ========== ESTILOS PARA LISTA DE TAREAS ========== */

/* Lista de tareas minimalista */
.tasks-list-minimal {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Item de tarea en lista */
.task-item-list {
    background: white;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-left: 4px solid #dee2e6;
}

.task-item-list:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.task-item-list.completed {
    border-left-color: #27ae60;
    opacity: 0.8;
}

.task-item-list.in_progress {
    border-left-color: #3498db;
}

.task-item-list.pending {
    border-left-color: #f39c12;
}

.task-item-list.subtask {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    margin-left: 20px;
    border-left-width: 3px;
}

/* Información de la tarea */
.task-info-list {
    flex: 1;
    min-width: 0;
}

.task-header-list {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.task-name-list {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
    display: flex;
    align-items: center;
    gap: 8px;
}

.subtask-indicator {
    font-size: 12px;
    color: #6c757d;
    opacity: 0.7;
}

.parent-hint {
    font-size: 12px;
    color: #6c757d;
    margin-left: 6px;
    cursor: help;
}

.task-badges-list {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}

.status-badge, .priority-badge {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-badge.status-completed { background: #d4edda; color: #155724; }
.status-badge.status-in_progress { background: #cce7ff; color: #004085; }
.status-badge.status-pending { background: #fff3cd; color: #856404; }

.priority-badge.priority-high { background: #f8d7da; color: #721c24; }
.priority-badge.priority-medium { background: #fff3cd; color: #856404; }
.priority-badge.priority-low { background: #d1ecf1; color: #0c5460; }

.task-description-list {
    font-size: 13px;
    color: #666;
    margin: 0 0 10px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.task-meta-list {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: #666;
    flex-wrap: wrap;
}

.task-meta-list span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.task-project {
    color: #764ba2;
    font-weight: 600;
}

/* Acciones de tarea en lista */
.task-actions-list {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}

.btn-task-list {
    padding: 8px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.btn-task-list.view {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.btn-task-list.view:hover {
    background: rgba(52, 152, 219, 0.2);
    transform: scale(1.05);
}

.btn-task-list.edit {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
}

.btn-task-list.edit:hover {
    background: rgba(243, 156, 18, 0.2);
    transform: scale(1.05);
}

.btn-task-list.delete {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.btn-task-list.delete:hover {
    background: rgba(231, 76, 60, 0.2);
    transform: scale(1.05);
}

/* Estado sin tareas */
.no-tasks-message {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.no-tasks-message i {
    font-size: 48px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-tasks-message h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
}

.no-tasks-message p {
    margin: 0;
    font-size: 14px;
}

/* ========== ESTILOS PARA TABLA CON LÓGICA KANBAN ========== */

/* Filas de subtareas */
.subtask-row {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
}

/* Iconos en celdas de tarea */
.subtask-icon-table {
    font-size: 12px;
    color: #6c757d;
    margin-right: 6px;
}

.personal-icon {
    color: #e74c3c;
    margin-right: 6px;
}

.team-icon {
    color: #3498db;
    margin-right: 6px;
}

.parent-task-indicator {
    font-size: 12px;
    color: #6c757d;
    margin-left: 6px;
    cursor: help;
}

/* Badges de proyecto */
.project-badge.personal {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.project-badge.clan {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    border: 1px solid rgba(52, 152, 219, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

/* Badges de fecha límite con urgencia */
.due-date-badge.overdue {
    background: rgba(231, 76, 60, 0.2);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.4);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    animation: pulse-red 2s infinite;
}

.due-date-badge.today {
    background: rgba(243, 156, 18, 0.2);
    color: #f39c12;
    border: 1px solid rgba(243, 156, 18, 0.4);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    animation: pulse-orange 2s infinite;
}

.due-date-badge.week1 {
    background: rgba(52, 152, 219, 0.15);
    color: #3498db;
    border: 1px solid rgba(52, 152, 219, 0.3);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.due-date-badge.week2 {
    background: rgba(39, 174, 96, 0.15);
    color: #27ae60;
    border: 1px solid rgba(39, 174, 96, 0.3);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.no-due-date {
    color: #6c757d;
    font-style: italic;
    font-size: 11px;
}

/* Badges de prioridad */
.priority-badge.priority-high {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.priority-badge.priority-medium {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
    border: 1px solid rgba(243, 156, 18, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.priority-badge.priority-low {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    border: 1px solid rgba(52, 152, 219, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

/* Badges de estado */
.status-badge.status-completed {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
    border: 1px solid rgba(39, 174, 96, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.status-badge.status-in_progress {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    border: 1px solid rgba(52, 152, 219, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.status-badge.status-pending {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
    border: 1px solid rgba(243, 156, 18, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

/* Progreso en tabla */
.progress-container-table {
    display: flex;
    align-items: center;
    gap: 8px;
}

.progress-bar-table {
    flex: 1;
    height: 6px;
    background: #ecf0f1;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill-table {
    height: 100%;
    background: linear-gradient(90deg, #27ae60 0%, #2ecc71 100%);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.progress-text-table {
    font-size: 11px;
    font-weight: 600;
    color: #2c3e50;
    min-width: 35px;
}

/* Acciones en tabla */
.actions-group {
    display: flex;
    gap: 4px;
}

.btn-action-table {
    padding: 6px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 11px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
}

.btn-action-table.view {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.btn-action-table.view:hover {
    background: rgba(52, 152, 219, 0.2);
    transform: scale(1.05);
}

.btn-action-table.edit {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
}

.btn-action-table.edit:hover {
    background: rgba(243, 156, 18, 0.2);
    transform: scale(1.05);
}

.btn-action-table.delete {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.btn-action-table.delete:hover {
    background: rgba(231, 76, 60, 0.2);
    transform: scale(1.05);
}

/* Descripción de tarea en tabla */
.task-description-table {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animaciones de urgencia */
@keyframes pulse-red {
    0%, 100% { 
        background: rgba(231, 76, 60, 0.2);
        transform: scale(1); 
    }
    50% { 
        background: rgba(231, 76, 60, 0.35);
        transform: scale(1.05); 
    }
}

@keyframes pulse-orange {
    0%, 100% { 
        background: rgba(243, 156, 18, 0.2);
        transform: scale(1); 
    }
    50% { 
        background: rgba(243, 156, 18, 0.35);
        transform: scale(1.05); 
    }
}

/* Toast para tareas */
.task-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    z-index: 10000;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.task-toast.show {
    opacity: 1;
    transform: translateX(0);
}

.task-toast-success { background: #27ae60; }
.task-toast-error { background: #e74c3c; }
.task-toast-info { background: #3498db; }
</style>

<script>
console.log('🚀 Tasks.php JavaScript cargado - Tabs implementados');

// Función switchTasksTab removida - se usa switchTab existente

// ========== FUNCIONES CON LÓGICA DEL KANBAN ==========

// Función para cargar mis tareas usando endpoint del kanban
function loadMyTasksTable() {
    console.log('🔵 loadMyTasksTable() iniciado - usando lógica del kanban');
    const tbody = document.getElementById('my-tasks-table-body');
    if (!tbody) {
        console.error('🔴 No se encontró tbody: my-tasks-table-body');
        return;
    }
    
    tbody.innerHTML = '<tr class="loading"><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando mis tareas...</td></tr>';
    
    // Usar el MISMO endpoint del kanban
    fetch('?route=clan_leader/get-my-kanban-tasks')
        .then(response => response.json())
        .then(data => {
            console.log('🔵 === RESPUESTA KANBAN PARA TABLA ===');
            console.log('🔵 Success:', data.success);
            console.log('🔵 Kanban Tasks:', data.kanbanTasks);
            
            if (data.success) {
                // Convertir datos del kanban a formato tabla
                const allTasks = [];
                Object.keys(data.kanbanTasks).forEach(column => {
                    data.kanbanTasks[column].forEach(task => {
                        allTasks.push(task);
                    });
                });
                
                renderTasksTableFromKanban(allTasks, 'my-tasks-table-body');
            } else {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error: ' + (data.message || 'Error desconocido') + '</td></tr>';
            }
        })
        .catch(error => {
            console.error('🔴 Error:', error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error de conexión</td></tr>';
        });
}

// Función para cargar tareas del equipo usando endpoint del kanban
function loadTeamTasksTable() {
    console.log('🟡 loadTeamTasksTable() iniciado - usando lógica del kanban');
    const tbody = document.getElementById('team-tasks-table-body');
    if (!tbody) {
        console.error('🔴 No se encontró tbody: team-tasks-table-body');
        return;
    }
    
    tbody.innerHTML = '<tr class="loading"><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando tareas del equipo...</td></tr>';
    
    // Usar el MISMO endpoint del kanban
    fetch('?route=clan_leader/get-team-kanban-tasks')
        .then(response => response.json())
        .then(data => {
            console.log('🟡 === RESPUESTA TEAM KANBAN PARA TABLA ===');
            console.log('🟡 Success:', data.success);
            console.log('🟡 Kanban Tasks:', data.kanbanTasks);
            
            if (data.success && data.kanbanTasks) {
                // Convertir datos del kanban a formato tabla
                const allTasks = [];
                Object.keys(data.kanbanTasks).forEach(column => {
                    data.kanbanTasks[column].forEach(task => {
                        allTasks.push(task);
                    });
                });
                
                renderTasksTableFromKanban(allTasks, 'team-tasks-table-body');
            } else {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error: ' + (data.message || 'Error desconocido') + '</td></tr>';
            }
        })
        .catch(error => {
            console.error('🔴 Error:', error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error de conexión</td></tr>';
        });
}

// Función para renderizar tareas del kanban en formato tabla
function renderTasksTableFromKanban(tasks, tbodyId) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    
    if (!tasks || tasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No hay tareas disponibles</td></tr>';
        return;
    }
    
    // Aplicar filtros antes de renderizar
    const filteredTasks = applyCurrentFilters(tasks);
    
    if (filteredTasks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No hay tareas que coincidan con los filtros</td></tr>';
        return;
    }
    
    let html = '';
    
    filteredTasks.forEach(task => {
        const isPersonal = (task.is_personal == 1);
        const isSubtask = (task.item_type === 'subtask');
        
        // Calcular días hasta vencimiento para mostrar urgencia
        let urgencyClass = '';
        let urgencyText = '';
        if (task.due_date) {
            const dueDate = new Date(task.due_date);
            const today = new Date();
            const diffTime = dueDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays < 0) {
                urgencyClass = 'overdue';
                urgencyText = 'Vencida';
            } else if (diffDays === 0) {
                urgencyClass = 'today';
                urgencyText = 'Hoy';
            } else if (diffDays <= 7) {
                urgencyClass = 'week1';
                urgencyText = 'Esta semana';
            } else {
                urgencyClass = 'week2';
                urgencyText = diffDays + ' días';
            }
        }
        
        html += `
            <tr class="task-row ${isSubtask ? 'subtask-row' : ''}" data-task-id="${task.task_id}">
                <td class="checkbox-cell">
                    <input type="checkbox" class="task-checkbox" ${task.status === 'completed' ? 'checked' : ''} 
                           onchange="toggleTaskStatus(${task.task_id}, this.checked, '${isSubtask ? 'subtask' : 'task'}')">
                </td>
                <td class="priority-cell">
                    <span class="priority-badge priority-${task.priority || 'medium'}">${task.priority || 'medium'}</span>
                </td>
                <td class="task-cell">
                    <div class="task-name-table">
                        ${isSubtask ? '<i class="fas fa-arrow-right subtask-icon-table"></i>' : ''}
                        ${isPersonal ? '<i class="fas fa-user-circle personal-icon" title="Tarea Personal"></i>' : '<i class="fas fa-users team-icon" title="Tarea de Clan"></i>'}
                        ${task.task_name || 'Sin nombre'}
                        ${isSubtask && task.parent_task_name ? `<span class="parent-task-indicator" title="Tarea padre: ${task.parent_task_name}">↑</span>` : ''}
                    </div>
                    ${task.description ? `<div class="task-description-table">${task.description}</div>` : ''}
                </td>
                <td class="project-cell">
                    <span class="project-badge ${isPersonal ? 'personal' : 'clan'}">${task.project_name || 'Sin proyecto'}</span>
                </td>
                <td class="due-date-cell">
                    ${task.due_date ? `<span class="due-date-badge ${urgencyClass}">${urgencyText}</span>` : '<span class="no-due-date">Sin fecha</span>'}
                </td>
                <td class="status-cell">
                    <span class="status-badge status-${task.status}">${task.status}</span>
                </td>
                <td class="progress-cell">
                    <div class="progress-container-table">
                        <div class="progress-bar-table">
                            <div class="progress-fill-table" style="width: ${task.completion_percentage || 0}%"></div>
                        </div>
                        <span class="progress-text-table">${task.completion_percentage || 0}%</span>
                    </div>
                </td>
                <td class="actions-cell">
                    <div class="actions-group">
                        <a href="?route=clan_leader/task_details&task_id=${task.task_id}" class="btn-action-table view" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="?route=clan_leader/tasks&action=edit&task_id=${task.task_id}" class="btn-action-table edit" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn-action-table delete" onclick="deleteTaskTable(${task.task_id}, '${task.task_name}')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Función para aplicar filtros actuales
function applyCurrentFilters(tasks) {
    const activeTab = document.querySelector('.tasks-tab-button.active');
    const tabType = activeTab ? activeTab.id.replace('-tab', '') : 'my-tasks';
    
    // Obtener valores de filtros del formulario correspondiente
    let statusFilter = '';
    let searchFilter = '';
    
    if (tabType === 'my-tasks') {
        const statusSelect = document.getElementById('statusFilter');
        const searchInput = document.getElementById('searchInputMyTasks');
        statusFilter = statusSelect ? statusSelect.value : '';
        searchFilter = searchInput ? searchInput.value.toLowerCase() : '';
    } else {
        const statusSelect = document.getElementById('statusFilterTeam');
        const searchInput = document.getElementById('searchInputTeam');
        statusFilter = statusSelect ? statusSelect.value : '';
        searchFilter = searchInput ? searchInput.value.toLowerCase() : '';
    }
    
    console.log('🔍 Aplicando filtros:', { statusFilter, searchFilter });
    
    return tasks.filter(task => {
        // Filtro por estado
        if (statusFilter && task.status !== statusFilter) {
            return false;
        }
        
        // Filtro por búsqueda
        if (searchFilter) {
            const searchText = (
                (task.task_name || '') + ' ' +
                (task.description || '') + ' ' +
                (task.project_name || '') + ' ' +
                (task.assigned_user_name || '')
            ).toLowerCase();
            
            if (!searchText.includes(searchFilter)) {
                return false;
            }
        }
        
        return true;
    });
}

// Función para recargar con filtros
function applyFilters() {
    console.log('🔍 Aplicando filtros...');
    const activeTab = document.querySelector('.tasks-tab-button.active');
    if (activeTab) {
        const tabId = activeTab.id.replace('-tab', '');
        if (tabId === 'my-tasks') {
            loadMyTasksTable();
        } else {
            loadTeamTasksTable();
        }
    }
}

// Función para resetear filtros
function resetFilters(tabType) {
    console.log('🔄 Reseteando filtros para:', tabType);
    
    if (tabType === 'my-tasks') {
        const statusSelect = document.getElementById('statusFilter');
        const searchInput = document.getElementById('searchInputMyTasks');
        if (statusSelect) statusSelect.value = '';
        if (searchInput) searchInput.value = '';
    } else {
        const statusSelect = document.getElementById('statusFilterTeam');
        const searchInput = document.getElementById('searchInputTeam');
        if (statusSelect) statusSelect.value = '';
        if (searchInput) searchInput.value = '';
    }
    
    // Recargar datos sin filtros
    applyFilters();
}

// Función para eliminar tarea desde la tabla
function deleteTaskTable(taskId, taskName) {
    if (confirm(`¿Estás seguro de que quieres eliminar la tarea "${taskName}"?`)) {
        const formData = new FormData();
        formData.append('task_id', taskId);
        
        fetch('?route=clan_leader/delete-task', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showTaskToast(data.message || 'Tarea eliminada exitosamente', 'success');
                // Recargar el tab actual
                const activeTab = document.querySelector('.tasks-tab-button.active');
                if (activeTab) {
                    const tabId = activeTab.id.replace('-tab', '');
                    if (tabId === 'my-tasks') {
                        loadMyTasksTable();
                    } else {
                        loadTeamTasksTable();
                    }
                }
            } else {
                showTaskToast(data.message || 'Error al eliminar la tarea', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showTaskToast('Error de conexión', 'error');
        });
    }
}

// Función para cambiar estado de tarea
function toggleTaskStatus(taskId, isChecked, itemType) {
    console.log('🔧 toggleTaskStatus:', taskId, isChecked, itemType);
    
    const newStatus = isChecked ? 'completed' : 'pending';
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('status', newStatus);
    formData.append('item_type', itemType);
    
    fetch('?route=clan_leader/update-task-status', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showTaskToast(data.message || `Tarea ${isChecked ? 'completada' : 'marcada como pendiente'}`, 'success');
            
            // Actualizar visualmente la fila (SIN desaparecer - solo cambios visuales)
            const taskRow = document.querySelector(`tr[data-task-id="${taskId}"]`);
            if (taskRow) {
                if (isChecked) {
                    taskRow.classList.add('completed');
                    taskRow.style.opacity = '0.7';
                } else {
                    taskRow.classList.remove('completed');
                    taskRow.style.opacity = '1';
                }
                
                // Actualizar badge de estado
                const statusBadge = taskRow.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.className = `status-badge status-${newStatus}`;
                    statusBadge.textContent = newStatus;
                }
                
                // Actualizar barra de progreso
                const progressFill = taskRow.querySelector('.progress-fill-table');
                const progressText = taskRow.querySelector('.progress-text-table');
                if (progressFill && progressText) {
                    if (isChecked) {
                        progressFill.style.width = '100%';
                        progressText.textContent = '100%';
                    } else {
                        progressFill.style.width = '0%';
                        progressText.textContent = '0%';
                    }
                }
            }
        } else {
            showTaskToast(data.message || 'Error al actualizar la tarea', 'error');
            // Revertir checkbox si hay error
            const checkbox = document.querySelector(`tr[data-task-id="${taskId}"] .task-checkbox`);
            if (checkbox) {
                checkbox.checked = !isChecked;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showTaskToast('Error de conexión', 'error');
        // Revertir checkbox si hay error
        const checkbox = document.querySelector(`tr[data-task-id="${taskId}"] .task-checkbox`);
        if (checkbox) {
            checkbox.checked = !isChecked;
        }
    });
}

// Función para mostrar mensajes toast
function showTaskToast(message, type = 'info') {
    let toast = document.getElementById('taskToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'taskToast';
        toast.className = 'task-toast';
        document.body.appendChild(toast);
    }
    
    toast.textContent = message;
    toast.className = `task-toast task-toast-${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM listo - Iniciando tasks page');
    
    // Agregar eventos a los filtros para aplicar automáticamente
    const statusFilter = document.getElementById('statusFilter');
    const statusFilterTeam = document.getElementById('statusFilterTeam');
    const searchInputMyTasks = document.getElementById('searchInputMyTasks');
    const searchInputTeam = document.getElementById('searchInputTeam');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    if (statusFilterTeam) {
        statusFilterTeam.addEventListener('change', applyFilters);
    }
    if (searchInputMyTasks) {
        searchInputMyTasks.addEventListener('input', debounce(applyFilters, 500));
    }
    if (searchInputTeam) {
        searchInputTeam.addEventListener('input', debounce(applyFilters, 500));
    }
    
    // Inicializar con "Mis Tareas"
    switchTab('my-tasks');
});

// Función debounce para búsqueda
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css'];
require_once __DIR__ . '/../layout.php';
?> 