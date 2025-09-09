<?php
ob_start();
?>

<?php
    $total = (int)($tasksData['total'] ?? 0);
    $page = (int)($tasksData['page'] ?? 1);
    $totalPages = (int)($tasksData['total_pages'] ?? 1);
?>

<div class="modern-dashboard" data-theme="default">
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg"><i class="fas fa-star"></i></div>
                <span class="brand-text">Polaris</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="?route=clan_member" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li class="nav-item active"><a href="?route=clan_member/tasks" class="nav-link"><i class="fas fa-tasks"></i><span>Tareas</span></a></li>
                <!-- <li class="nav-item"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li> -->
                <li class="nav-item"><a href="?route=clan_member/availability" class="nav-link"><i class="fas fa-user-clock"></i><span>Agenda</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/profile" class="nav-link"><i class="fas fa-user"></i><span>Perfil</span></a></li>
            </ul>
            
            <!-- Botón hamburguesa para móvil -->
            <button class="hamburger-menu" onclick="toggleMobileMenu()" style="display: flex !important; flex-direction: column; justify-content: space-around; width: 30px; height: 25px; background: transparent; border: none; cursor: pointer; padding: 0; z-index: 1000; position: relative; margin-left: auto; margin-right: 15px;">
                <span class="hamburger-line" style="width: 100%; height: 3px; background: #1e3a8a !important; border-radius: 2px; transition: all 0.3s ease; display: block !important; margin: 2px 0;"></span>
                <span class="hamburger-line" style="width: 100%; height: 3px; background: #1e3a8a !important; border-radius: 2px; transition: all 0.3s ease; display: block !important; margin: 2px 0;"></span>
                <span class="hamburger-line" style="width: 100%; height: 3px; background: #1e3a8a !important; border-radius: 2px; transition: all 0.3s ease; display: block !important; margin: 2px 0;"></span>
            </button>
            
            <!-- Menú móvil -->
            <div class="mobile-menu" id="mobileMenu">
                <div class="mobile-menu-content">
                    <div class="mobile-menu-header">
                        <span class="mobile-menu-title">Menú</span>
                        <button class="mobile-menu-close" onclick="toggleMobileMenu()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <ul class="mobile-nav-menu">
                        <li class="mobile-nav-item"><a href="?route=clan_member" class="mobile-nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                        <li class="mobile-nav-item active"><a href="?route=clan_member/tasks" class="mobile-nav-link"><i class="fas fa-tasks"></i><span>Tareas</span></a></li>
                        <!-- <li class="mobile-nav-item"><a href="?route=clan_member/kpi-dashboard" class="mobile-nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li> -->
                        <li class="mobile-nav-item"><a href="?route=clan_member/availability" class="mobile-nav-link"><i class="fas fa-user-clock"></i><span>Agenda</span></a></li>
                        <li class="mobile-nav-item"><a href="?route=clan_member/profile" class="mobile-nav-link"><i class="fas fa-user"></i><span>Perfil</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="user-menu">
                <div class="user-avatar modern-avatar">
                    <?php if (!empty($user['avatar_path'])): ?>
                        <img src="<?php echo Utils::asset($user['avatar_path']); ?>" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:999px"/>
                    <?php else: ?>
                        <span class="avatar-text"><?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?></span>
                    <?php endif; ?>
                    <div class="status-dot"></div>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo Utils::escape($user['full_name'] ?: $user['username']); ?></span>
                    <span class="user-role">Miembro de Clan</span>
                </div>
                <div class="user-actions">
                    <a href="?route=logout" class="action-btn logout" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <header class="welcome-header animate-fade-in">
            <div class="welcome-content">
                <h1 class="welcome-title">Tareas del Clan</h1>
                <p class="welcome-subtitle">Clan: <?php echo Utils::escape($clan['clan_name'] ?? ''); ?></p>
            </div>
            <div class="welcome-stats">
                <div class="quick-stat">
                    <div class="stat-icon success"><i class="fas fa-list-check"></i></div>
                    <div class="stat-text">
                        <span class="stat-value"><?php echo (int)$total; ?></span>
                        <span class="stat-label">Tareas</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Proyectos del Clan -->
        <?php if (!empty($projectsSummary)): ?>
        <section class="projects-section animate-fade-in">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-project-diagram"></i>
                    Mis Proyectos
                </h2>
            </div>
            <div class="projects-grid">
                <?php foreach ($projectsSummary as $project): ?>
                <?php 
                    // Mostrar TODOS los proyectos en la parte superior
                    // No filtrar ningún proyecto
                ?>
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
                        <a href="<?= APP_URL ?>?route=clan_member/project-tasks&project_id=<?= $project['project_id'] ?>" class="btn-minimal primary">
                            <i class="fas fa-eye"></i> Ver Tareas
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>


        <section class="content-section animate-fade-in">
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-filter icon-gradient"></i> Filtros</h3>
                </div>
                <form method="get" class="filters" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                    <input type="hidden" name="route" value="clan_member/tasks" />
                    <input type="text" name="search" class="search-input" placeholder="Buscar" value="<?php echo Utils::escape($search ?? ''); ?>" />
                    <select name="status" class="filter-select">
                        <option value="">Todos los estados</option>
                        <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="in_progress" <?php echo ($status === 'in_progress') ? 'selected' : ''; ?>>En progreso</option>
                        <option value="completed" <?php echo ($status === 'completed') ? 'selected' : ''; ?>>Completado</option>
                    </select>
                    <select name="project" class="filter-select">
                        <option value="">Todos los proyectos</option>
                        <?php if (!empty($projectsSummary)): ?>
                            <?php foreach ($projectsSummary as $project): ?>
                                <option value="<?php echo htmlspecialchars($project['project_name']); ?>" 
                                        <?php echo ($projectFilter === $project['project_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($project['project_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <select name="per_page" class="filter-select" title="Resultados por página">
                        <?php $pp = (int)($perPage ?? 8); foreach ([5,8,12,20,30] as $opt): ?>
                            <option value="<?php echo $opt; ?>" <?php echo ($pp === $opt) ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-secondary" type="submit">Aplicar</button>
                    <?php if (!empty($search) || !empty($status) || !empty($projectFilter)): ?>
                        <a href="?route=clan_member/tasks" class="btn btn-outline" style="text-decoration: none;">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </section>

        <section class="content-section animate-fade-in">
            <div class="content-card">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr><th>Tarea</th><th>Proyecto</th><th>Asignado(s)</th><th>Vence</th><th>Estado</th><th>Progreso</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tasksData['tasks'])): ?>
                                <tr><td colspan="7" class="empty">No hay tareas</td></tr>
                            <?php else: foreach ($tasksData['tasks'] as $t): ?>
                                <?php
                                    $statusClass = ($t['status'] ?? '') === 'completed' ? 'completed' : (($t['status'] ?? '') === 'in_progress' ? 'in_progress' : 'pending');
                                    $days = isset($t['days_until_due']) ? (int)$t['days_until_due'] : null;
                                    $dueLabel = '';
                                    $dueCls = '';
                                    if (!empty($t['due_date'])) {
                                        if ($days !== null) {
                                            if ($days < 0) { $dueLabel = 'Vencida'; $dueCls = 'overdue'; }
                                            elseif ($days === 0) { $dueLabel = 'Vence hoy'; $dueCls = 'due-soon'; }
                                            elseif ($days === 1) { $dueLabel = 'Vence mañana'; $dueCls = 'due-soon'; }
                                            else { $dueLabel = 'En ' . $days . ' días'; }
                                        } else { $dueLabel = date('d/m/Y', strtotime($t['due_date'])); }
                                    }
                                ?>
                                <tr>
                                    <td><a href="?route=clan_member/task-details&task_id=<?php echo (int)$t['task_id']; ?>" title="Ver detalles"><?php echo Utils::escape($t['task_name']); ?></a></td>
                                    <td><?php echo Utils::escape($t['project_name']); ?></td>
                                    <td>
                                        <?php if (!empty($t['all_assigned_users'])): ?>
                                            <?php echo Utils::escape($t['all_assigned_users']); ?>
                                        <?php elseif (!empty($t['assigned_user_name'])): ?>
                                            <?php echo Utils::escape($t['assigned_user_name']); ?>
                                        <?php else: ?>–<?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($dueLabel): ?><span class="badge badge-due <?php echo $dueCls; ?>"><i class="fas fa-calendar"></i> <?php echo $dueLabel; ?></span><?php else: ?>–<?php endif; ?>
                                    </td>
                                    <td><span class="chip chip-status <?php echo $statusClass; ?>"><?php echo str_replace('_',' ', (string)$t['status']); ?></span></td>
                                    <td class="cell-progress">
                                        <?php $progress = (int)($t['completion_percentage'] ?? 0); ?>
                                        <div class="progress-container">
                                            <div class="progress-bar-small">
                                                <div class="progress-fill-small" style="width: <?php echo $progress; ?>%"></div>
                                            </div>
                                            <span class="progress-text-small"><?php echo $progress; ?>%</span>
                                        </div>
                                    </td>
                                    <td class="cell-actions">
                                        <a class="action-btn" href="?route=clan_member/task-details&task_id=<?php echo (int)$t['task_id']; ?>&action=edit" title="Editar tarea"><i class="fas fa-edit"></i></a>
                                        <?php 
                                        // Verificar si el usuario es dueño de la tarea para mostrar botón de clonar
                                        $isOwner = ((int)($t['created_by_user_id'] ?? 0) === (int)$user['user_id']);
                                        ?>
                                        <?php if ($isOwner): ?>
                                        <button class="action-btn" onclick="openCloneTaskModal(<?php echo (int)$t['task_id']; ?>)" title="Clonar tarea"><i class="fas fa-copy"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="pagination" style="display:flex;gap:8px;justify-content:flex-end;margin-top:10px">
                    <span>Página <?php echo $page; ?> de <?php echo $totalPages; ?></span>
                </div>
                <?php endif; ?>
            </div>
        </section>

    </main>
</div>

<style>
.modern-dashboard{min-height:100vh;background:var(--bg-secondary)}

/* Estilos para la sección de proyectos */
.projects-section {
    margin-bottom: 2rem;
}

.section-header {
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    flex: 1;
    line-height: 1.3;
}

.project-status {
    padding: 0.2rem 0.6rem;
    border-radius: 16px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-active, .status-open {
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
    width: 32px;
    height: 32px;
    border-radius: 8px;
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
    font-size: 1rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    font-size: 0.7rem;
    color: #6b7280;
    margin-top: 0.2rem;
}

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

.project-actions {
    display: flex;
    justify-content: center;
}

.btn-minimal {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 6px;
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

/* Estilos para filtros */
.filters {
    gap: 12px !important;
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
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.search-input {
    padding: 8px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    color: #374151;
    font-size: 0.9rem;
    min-width: 200px;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: 2px solid #ef4444;
    background: white;
    color: #ef4444;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-outline:hover {
    background: #ef4444;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

/* Estilos para tareas recurrentes */
.recurrent-tasks-section {
    margin-bottom: 2rem;
}

.recurrent-tasks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.recurrent-task-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    border-left: 4px solid #f59e0b;
    transition: all 0.3s ease;
}

.recurrent-task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.recurrent-task-card .task-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.recurrent-task-card .task-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    flex: 1;
    line-height: 1.3;
}

.recurrent-task-card .task-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.recurrent-task-card .btn-view {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #3b82f6;
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    font-size: 0.8rem;
}

.recurrent-task-card .btn-view:hover {
    background: #2563eb;
    transform: scale(1.1);
}

.recurrent-task-card .task-description {
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 0.75rem;
}

.recurrent-task-card .task-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
}

.recurrent-task-card .status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.recurrent-task-card .status-badge.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.recurrent-task-card .status-badge.status-in_progress {
    background: #dbeafe;
    color: #1e40af;
}

.recurrent-task-card .status-badge.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.recurrent-task-card .task-due-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #6b7280;
    font-size: 0.8rem;
}
.modern-nav{background:var(--bg-glass);backdrop-filter:var(--glass-backdrop);border-bottom:1px solid var(--glass-border);padding:var(--spacing-md) 0;position:sticky;top:0;z-index:100}
.nav-container{max-width:1400px;margin:0 auto;padding:0 var(--spacing-lg);display:flex;align-items:center;justify-content:space-between;gap:var(--spacing-xl)}
.nav-menu{display:flex;list-style:none;gap:var(--spacing-sm)}
.brand-icon{width:40px;height:40px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:#fff;background:var(--primary-gradient)}
.brand-text{font-size:1.5rem;font-weight:700;background:var(--primary-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.user-menu{display:flex;align-items:center;gap:var(--spacing-md)}
.modern-avatar{position:relative;width:45px;height:45px;background:var(--primary-gradient);border-radius:999px;display:flex;align-items:center;justify-content:center;color:#fff}
.status-dot{position:absolute;bottom:2px;right:2px;width:12px;height:12px;background:var(--success);border:2px solid var(--bg-primary);border-radius:999px}
.main-content{max-width:1400px;margin:0 auto;padding:var(--spacing-xl) var(--spacing-lg)}
.welcome-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-2xl);padding:var(--spacing-xl);background:var(--bg-primary);border-radius:var(--radius-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent)}
.content-card{background:var(--bg-primary);border-radius:var(--radius-xl);padding:var(--spacing-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent)}
.table-wrapper{overflow-x:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{background:var(--bg-tertiary);padding:var(--spacing-lg);text-align:left;font-weight:600;color:var(--text-primary);border-bottom:1px solid var(--bg-accent)}
.data-table td{padding:var(--spacing-lg);border-bottom:1px solid var(--bg-accent);color:var(--text-secondary)}
.badge,.chip{padding:4px 8px;border-radius:6px;font-size:.8rem}
.badge-due.overdue{background:#fee2e2;color:#991b1b}
.badge-due.due-soon{background:#fef3c7;color:#92400e}
.chip-status.completed{background:#dcfce7;color:#166534}
.chip-status.in_progress{background:#dbeafe;color:#1e40af}
.chip-status.pending{background:#f1f5f9;color:#334155}

/* Estilos para barra de progreso en tabla */
.cell-progress {
    min-width: 120px;
}

.progress-container {
    display: flex;
    align-items: center;
    gap: 8px;
}

.progress-bar-small {
    width: 60px;
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.progress-fill-small {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #22c55e);
    transition: width 0.3s ease;
    border-radius: 3px;
}

.progress-text-small {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    min-width: 35px;
}
/* Cards de proyectos (resumen) */
.cm-project-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:18px}
.cm-project-card{border:1px solid var(--bg-accent);background:#fff;border-radius:16px;box-shadow:0 10px 20px rgba(2,6,23,.06);padding:16px;display:grid;gap:14px}
.pc-top{display:flex;justify-content:space-between;align-items:flex-start}
.pc-title{font-weight:800;color:var(--text-primary)}
.pc-status{font-size:.8rem;font-weight:800;color:var(--text-secondary)}
.pc-metrics{display:flex;gap:16px;align-items:center}
.pc-metric{display:flex;gap:10px;align-items:center;color:var(--text-secondary)}
.pc-metric .num{font-weight:800;color:var(--text-primary)}
.pc-metric .cap{font-size:.8rem;color:var(--text-secondary)}
.pc-progress{width:100%;height:8px;background:var(--bg-tertiary);border-radius:9999px;overflow:hidden}
.pc-progress>span{display:block;height:100%;background:linear-gradient(90deg,#10b981,#22c55e)}
.pc-actions{display:flex;justify-content:center}
@media (max-width:768px){.nav-menu{display:none}.main-content{padding:var(--spacing-lg) var(--spacing-md)}}

/* === MENÚ HAMBURGUESA RESPONSIVE === */
.hamburger-menu {
    display: none;
    flex-direction: column;
    justify-content: space-around;
    width: 30px;
    height: 25px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    z-index: 1000;
    position: relative;
}

.hamburger-line {
    width: 100%;
    height: 3px;
    background: #1e3a8a;
    border-radius: 2px;
    transition: all 0.3s ease;
    display: block;
}

.hamburger-menu.active .hamburger-line:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.hamburger-menu.active .hamburger-line:nth-child(2) {
    opacity: 0;
}

.hamburger-menu.active .hamburger-line:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}

.mobile-menu {
    display: block !important;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background: rgba(0, 0, 0, 0.8);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.mobile-menu.active {
    opacity: 1 !important;
    visibility: visible !important;
}

.mobile-menu-content {
    position: absolute;
    top: 0;
    right: 0;
    width: 280px;
    height: 100%;
    background: #ffffff;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
}

.mobile-menu.active .mobile-menu-content {
    transform: translateX(0) !important;
}

.mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg);
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.mobile-menu-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e3a8a;
}

.mobile-menu-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.mobile-menu-close:hover {
    background: #e5e7eb;
    color: #1e3a8a;
}

.mobile-nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mobile-nav-item {
    border-bottom: 1px solid #e5e7eb;
}

.mobile-nav-link {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    text-decoration: none;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.2s ease;
}

.mobile-nav-link:hover,
.mobile-nav-item.active .mobile-nav-link {
    background: #f3f4f6;
    color: #1e3a8a;
}

.mobile-nav-link i {
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 768px) {
    .nav-menu {
        display: none !important;
    }
    
    .hamburger-menu {
        display: flex !important;
    }
    
    .nav-container {
        padding: 0 var(--spacing-md);
    }
    
    .main-content {
        padding: var(--spacing-lg) var(--spacing-md);
    }
    
    .welcome-header {
        flex-direction: column;
        gap: var(--spacing-lg);
        text-align: center;
        padding: var(--spacing-lg);
    }
    
    .welcome-title {
        font-size: 1.8rem;
    }
    
    .welcome-subtitle {
        font-size: 1rem;
    }
    
    .cm-project-cards {
        grid-template-columns: 1fr;
        gap: var(--spacing-md);
    }
    
    .cm-project-card {
        min-width: 100%;
        padding: var(--spacing-lg);
        transform: scale(0.6);
        transform-origin: top center;
        margin: -20px 0;
    }
    
    .pc-top {
        flex-direction: column;
        gap: var(--spacing-sm);
        text-align: center;
    }
    
    .pc-title {
        font-size: 1.1rem;
    }
    
    .pc-status {
        font-size: 0.9rem;
    }
    
    .pc-metrics {
        grid-template-columns: 1fr;
        gap: var(--spacing-md);
    }
    
    .pc-metric {
        justify-content: center;
        text-align: center;
    }
    
    .pc-progress {
        margin-top: var(--spacing-md);
    }
    
    .pc-actions {
        justify-content: center;
    }
    
    .filters {
        flex-direction: column;
        gap: var(--spacing-md);
        align-items: stretch;
    }
    
    .search-input,
    .filter-select {
        width: 100%;
        padding: var(--spacing-md);
        font-size: 1rem;
    }
    
    .btn {
        width: 100%;
        padding: var(--spacing-md);
        font-size: 1rem;
        justify-content: center;
    }
    
    .table-wrapper {
        overflow-x: auto;
        margin-top: var(--spacing-md);
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .data-table {
        min-width: 100%;
        font-size: 0.9rem;
        border-collapse: collapse;
    }
    
    .data-table th,
    .data-table td {
        padding: var(--spacing-md);
        border-bottom: 1px solid #e5e7eb;
    }
    
    .data-table th {
        background: #f9fafb;
        font-weight: 600;
        color: #1e3a8a;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .data-table tr:hover {
        background: #f3f4f6;
    }
    
    .badge {
        font-size: 0.8rem;
        padding: 6px 10px;
    }
}

@media (max-width: 480px) {
    .nav-container {
        padding: 0 var(--spacing-sm);
    }
    
    .main-content {
        padding: var(--spacing-md) var(--spacing-sm);
    }
    
    .welcome-header {
        padding: var(--spacing-md);
    }
    
    .welcome-title {
        font-size: 1.6rem;
    }
    
    .cm-project-card {
        padding: var(--spacing-md);
        transform: scale(0.6);
        transform-origin: top center;
        margin: -15px 0;
    }
    
    .pc-title {
        font-size: 1rem;
    }
    
    .pc-metric .num {
        font-size: 1.2rem;
    }
    
    .pc-metric .cap {
        font-size: 0.8rem;
    }
    
    .filters {
        gap: var(--spacing-sm);
    }
    
    .search-input,
    .filter-select,
    .btn {
        padding: var(--spacing-sm);
        font-size: 0.9rem;
    }
    
    .table-wrapper {
        margin-top: var(--spacing-sm);
        border-radius: 8px;
    }
    
    .data-table {
        min-width: 100%;
        font-size: 0.8rem;
    }
    
    .data-table th,
    .data-table td {
        padding: var(--spacing-sm);
        font-size: 0.8rem;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
}
</style>

<script>
// Función para el menú hamburguesa (global)
window.toggleMobileMenu = function() {
    const mobileMenu = document.getElementById('mobileMenu');
    const hamburger = document.querySelector('.hamburger-menu');
    
    if (mobileMenu.classList.contains('active')) {
        mobileMenu.classList.remove('active');
        hamburger.classList.remove('active');
    } else {
        mobileMenu.classList.add('active');
        hamburger.classList.add('active');
    }
};

// Cerrar menú móvil al hacer click en un enlace
document.addEventListener('DOMContentLoaded', function() {
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            toggleMobileMenu();
        });
    });
    
    // Cerrar menú móvil al hacer click fuera
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.addEventListener('click', function(e) {
        if (e.target === mobileMenu) {
            toggleMobileMenu();
        }
    });
});

// Función para abrir el modal de clonación
function openCloneTaskModal(taskId) {
    fetch('?route=clan_member/get-task-data&task_id=' + taskId)
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
    
    fetch('?route=clan_member/clone-task', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCloneTaskModal();
            alert('Tarea clonada exitosamente');
            location.reload(); // Recargar para mostrar los cambios
        } else {
            alert('Error al clonar la tarea: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión al clonar la tarea');
    });
}
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
</style>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


