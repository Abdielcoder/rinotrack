<?php
// Guardar el contenido de la vista
ob_start();
?>

<?php
    $projectsCount = is_array($projects ?? null) ? count($projects) : 0;
    $totalTasks = (int)($userTaskStats['total_tasks'] ?? 0);
    $completedTasks = (int)($userTaskStats['completed_tasks'] ?? 0);
    $inProgress = max(0, $totalTasks - $completedTasks);
    $progressPct = (float)($userTaskStats['completion_percentage'] ?? 0);
?>

<div class="modern-dashboard" data-theme="default">
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-star"></i>
                </div>
                <span class="brand-text" style="color: #1e3a8a; !important">Polaris</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item active"><a href="?route=clan_member" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/tasks" class="nav-link"><i class="fas fa-tasks"></i><span>Tareas</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/availability" class="nav-link"><i class="fas fa-user-clock"></i><span>Disponibilidad</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/profile" class="nav-link"><i class="fas fa-user"></i><span>Perfil</span></a></li>
            </ul>
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
                <h1 class="welcome-title">¡Hola, <?php echo Utils::escape($user['full_name'] ?: $user['username']); ?>! 👋</h1>
                <p class="welcome-subtitle">Clan: <?php echo Utils::escape($clan['clan_name'] ?? 'Sin clan'); ?></p>
                <div class="motivation" style="display:flex;align-items:center;gap:10px;margin-top:10px">
                    <div class="motivation-icon" style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:var(--primary-gradient);color:#fff">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="motivation-text">
                        <div id="motQuote" style="font-weight:600;color:#1e3a8a">Cargando frase motivacional...</div>
                        <div id="motAuthor" style="font-size:.9rem;color:#6b7280"></div>
                    </div>
                </div>
            </div>
            <div class="welcome-stats">
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-header"><h3>Proyectos</h3><i class="fas fa-folder-open"></i></div>
                            <div class="stat-number"><?php echo $projectsCount; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-header"><h3>Tareas</h3><i class="fas fa-tasks"></i></div>
                            <div class="stat-number"><?php echo $totalTasks; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><h3>Completadas</h3><i class="fas fa-check-circle"></i></div>
                        <div class="stat-number"><?php echo $completedTasks; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header"><h3>En Progreso</h3><i class="fas fa-spinner"></i></div>
                        <div class="stat-number"><?php echo $inProgress; ?></div>
                    </div>
                    <a href="?route=clan_member/tasks" class="btn btn-secondary btn-stats">
                        <i class="fas fa-tasks"></i>
                        Ver mis tareas
                    </a>
                </div>
            </div>
        </header>

        <!-- Tablero Kanban de Tareas -->
        <section class="kanban-section animate-fade-in">
            <div class="kanban-header">
                <h2><i class="fas fa-tasks icon-gradient"></i> Tareas</h2>
            </div>
            <div class="kanban-board">
                <!-- Columna: Vencidas -->
                <div class="kanban-column">
                    <div class="column-header overdue">
                        <h3>Vencidas</h3>
                        <span class="task-count"><?php echo count($kanbanTasks['vencidas'] ?? []); ?></span>
                    </div>
                    <div class="column-content">
                        <?php foreach ($kanbanTasks['vencidas'] ?? [] as $task): ?>
                            <div class="task-card overdue" data-task-id="<?php echo $task['task_id']; ?>">
                                <div class="task-header">
                                    <label class="task-checkbox">
                                        <input type="checkbox" onchange="toggleTaskStatus(<?php echo $task['task_id']; ?>, this.checked)">
                                        <span class="checkmark"></span>
                                    </label>
                                    <span class="task-priority <?php echo strtolower($task['priority'] ?? 'medium'); ?>">
                                        <i class="fas fa-flag"></i>
                                    </span>
                                </div>
                                <div class="task-title"><?php echo Utils::escape($task['task_name']); ?></div>
                                <div class="task-project"><?php echo Utils::escape($task['project_name']); ?></div>
                                <div class="task-due-date overdue">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Vencida hace <?php echo abs((int)$task['days_until_due']); ?> días
                                </div>
                                <div class="task-actions">
                                    <a href="?route=clan_member/task-details&task_id=<?php echo $task['task_id']; ?>" class="btn-edit" title="Editar Tarea">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: Hoy -->
                <div class="kanban-column">
                    <div class="column-header today">
                        <h3>Hoy</h3>
                        <span class="task-count"><?php echo count($kanbanTasks['hoy'] ?? []); ?></span>
                    </div>
                    <div class="column-content">
                        <?php foreach ($kanbanTasks['hoy'] ?? [] as $task): ?>
                            <div class="task-card today" data-task-id="<?php echo $task['task_id']; ?>">
                                <div class="task-header">
                                    <label class="task-checkbox">
                                        <input type="checkbox" onchange="toggleTaskStatus(<?php echo $task['task_id']; ?>, this.checked)">
                                        <span class="checkmark"></span>
                                    </label>
                                    <span class="task-priority <?php echo strtolower($task['priority'] ?? 'medium'); ?>">
                                        <i class="fas fa-flag"></i>
                                    </span>
                                </div>
                                <div class="task-title"><?php echo Utils::escape($task['task_name']); ?></div>
                                <div class="task-project"><?php echo Utils::escape($task['project_name']); ?></div>
                                <div class="task-due-date today">
                                    <i class="fas fa-clock"></i>
                                    Vence hoy
                                </div>
                                <div class="task-actions">
                                    <a href="?route=clan_member/task-details&task_id=<?php echo $task['task_id']; ?>" class="btn-edit" title="Editar Tarea">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: 1 Semana -->
                <div class="kanban-column">
                    <div class="column-header week1">
                        <h3>1 Semana</h3>
                        <span class="task-count"><?php echo count($kanbanTasks['1_semana'] ?? []); ?></span>
                    </div>
                    <div class="column-content">
                        <?php foreach ($kanbanTasks['1_semana'] ?? [] as $task): ?>
                            <div class="task-card week1" data-task-id="<?php echo $task['task_id']; ?>">
                                <div class="task-header">
                                    <label class="task-checkbox">
                                        <input type="checkbox" onchange="toggleTaskStatus(<?php echo $task['task_id']; ?>, this.checked)">
                                        <span class="checkmark"></span>
                                    </label>
                                    <span class="task-priority <?php echo strtolower($task['priority'] ?? 'medium'); ?>">
                                        <i class="fas fa-flag"></i>
                                    </span>
                                </div>
                                <div class="task-title"><?php echo Utils::escape($task['task_name']); ?></div>
                                <div class="task-project"><?php echo Utils::escape($task['project_name']); ?></div>
                                <div class="task-due-date week1">
                                    <i class="fas fa-calendar"></i>
                                    En <?php echo (int)$task['days_until_due']; ?> días
                                </div>
                                <div class="task-actions">
                                    <a href="?route=clan_member/task-details&task_id=<?php echo $task['task_id']; ?>" class="btn-edit" title="Editar Tarea">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: 2 Semanas -->
                <div class="kanban-column">
                    <div class="column-header week2">
                        <h3>2 Semanas</h3>
                        <span class="task-count"><?php echo count($kanbanTasks['2_semanas'] ?? []); ?></span>
                    </div>
                    <div class="column-content">
                        <?php foreach ($kanbanTasks['2_semanas'] ?? [] as $task): ?>
                            <div class="task-card week2" data-task-id="<?php echo $task['task_id']; ?>">
                                <div class="task-header">
                                    <label class="task-checkbox">
                                        <input type="checkbox" onchange="toggleTaskStatus(<?php echo $task['task_id']; ?>, this.checked)">
                                        <span class="checkmark"></span>
                                    </label>
                                    <span class="task-priority <?php echo strtolower($task['priority'] ?? 'medium'); ?>">
                                        <i class="fas fa-flag"></i>
                                    </span>
                                </div>
                                <div class="task-title"><?php echo Utils::escape($task['task_name']); ?></div>
                                <div class="task-project"><?php echo Utils::escape($task['project_name']); ?></div>
                                <div class="task-due-date week2">
                                    <i class="fas fa-calendar"></i>
                                    En <?php echo (int)$task['days_until_due']; ?> días
                                </div>
                                <div class="task-actions">
                                    <a href="?route=clan_member/task-details&task_id=<?php echo $task['task_id']; ?>" class="btn-edit" title="Editar Tarea">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Animación de estrellas de fondo -->
        <div class="stars-container">
            <div class="star star-1"></div>
            <div class="star star-2"></div>
            <div class="star star-3"></div>
            <div class="star star-4"></div>
            <div class="star star-5"></div>
            <div class="star star-6"></div>
            <div class="star star-7"></div>
            <div class="star star-8"></div>
            <div class="star star-9"></div>
            <div class="star star-10"></div>
        </div>




    </main>
</div>

<style>
.modern-dashboard{min-height:100vh;background:#ffffff;padding:0;position:relative}
.modern-nav{background:rgba(255, 255, 255, 0.9);backdrop-filter:blur(10px);border-bottom:1px solid #e5e7eb;padding:var(--spacing-md) 0;position:sticky;top:0;z-index:100}
.nav-container{max-width:1400px;margin:0 auto;padding:0 var(--spacing-lg);display:flex;align-items:center;justify-content:space-between;gap:var(--spacing-xl)}
.nav-brand{display:flex;align-items:center;gap:var(--spacing-md)}
.brand-icon{width:40px;height:40px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:#ffffff;font-size:1.2rem;background:var(--primary-gradient)}
.brand-text{font-size:1.5rem;font-weight:var(--font-weight-bold);color:#1e3a8a}
.nav-menu{display:flex;list-style:none;gap:var(--spacing-sm)}
.nav-item .nav-link{display:flex;align-items:center;gap:var(--spacing-sm);padding:var(--spacing-md) var(--spacing-lg);border-radius:var(--radius-md);text-decoration:none;color:#1e3a8a;font-weight:var(--font-weight-medium);transition:all var(--transition-normal);position:relative;overflow:hidden}
.nav-item .nav-link:hover{color:#ffffff;background:var(--primary-gradient);transform:translateY(-2px);box-shadow:var(--shadow-md)}
.nav-item.active .nav-link{background:var(--primary-gradient);color:#ffffff;box-shadow:var(--shadow-glow)}
.user-menu{display:flex;align-items:center;gap:var(--spacing-md)}
.modern-avatar{position:relative;width:45px;height:45px;background:var(--primary-gradient);border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;color:#ffffff;font-weight:var(--font-weight-semibold);box-shadow:var(--shadow-md)}
.status-dot{position:absolute;bottom:2px;right:2px;width:12px;height:12px;background:var(--success);border:2px solid #ffffff;border-radius:var(--radius-full)}
.user-info{display:flex;flex-direction:column;gap:2px}
.user-name{font-weight:var(--font-weight-semibold);color:#1e3a8a;font-size:.95rem}
.user-role{font-size:.8rem;color:#6b7280}
.action-btn{width:35px;height:35px;border:none;border-radius:var(--radius-md);background:#ffffff;color:#1e3a8a;cursor:pointer;transition:all var(--transition-normal);display:flex;align-items:center;justify-content:center;text-decoration:none;box-shadow:var(--shadow-sm);border:1px solid #e5e7eb}
.action-btn.logout:hover{color:var(--error)}
.main-content{max-width:1400px;margin:0 auto;padding:var(--spacing-xl) var(--spacing-lg)}
.welcome-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:var(--spacing-2xl);padding:var(--spacing-xl);background:#ffffff;border-radius:var(--radius-xl);box-shadow:var(--shadow-md);border:1px solid #e5e7eb;gap:var(--spacing-xl)}
.welcome-title{font-size:2.2rem;font-weight:var(--font-weight-bold);color:#1e3a8a;margin-bottom:var(--spacing-sm)}
.welcome-subtitle{font-size:1.05rem;color:#6b7280}
.welcome-stats{display:flex;flex-direction:column;align-items:stretch;min-width:fit-content}
.quick-stat{display:flex;align-items:center;gap:var(--spacing-md);padding:var(--spacing-lg);background:#f3f4f6;border-radius:var(--radius-lg);border:1px solid #e5e7eb}
.stat-icon{width:50px;height:50px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:#ffffff;font-size:1.2rem}
.stat-icon.success{background:var(--success)}
.stats-section{margin-bottom:var(--spacing-2xl)}
.stats-row{display:flex;align-items:center;gap:var(--spacing-md);flex-wrap:wrap;justify-content:flex-start}
.stat-card{background:#ffffff;border-radius:var(--radius-lg);padding:var(--spacing-md);box-shadow:var(--shadow-md);border:1px solid #e5e7eb;transition:all var(--transition-normal);min-width:140px;flex-shrink:0}
.stat-card.gradient-bg{background:var(--primary-gradient);color:#ffffff}
.stat-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-sm)}
.stat-header h3{font-size:0.9rem;margin:0;font-weight:600;color:#1e3a8a}
.stat-header i{font-size:1rem;color:#1e3a8a}
.stat-number{font-size:1.5rem;font-weight:var(--font-weight-bold);margin-bottom:0;line-height:1;color:#1e3a8a}
.btn-stats{display:flex;align-items:center;gap:var(--spacing-sm);padding:var(--spacing-md);background:#ffffff;border:1px solid #e5e7eb;border-radius:var(--radius-lg);text-decoration:none;color:#1e3a8a;font-weight:600;font-size:0.9rem;min-width:140px;justify-content:center;box-shadow:var(--shadow-md);transition:all var(--transition-normal);flex-shrink:0}
.btn-stats:hover{background:#f3f4f6;color:#1e3a8a;transform:translateY(-2px);box-shadow:var(--shadow-lg)}

/* === ANIMACIÓN DE ESTRELLAS === */
.stars-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.star {
    position: absolute;
    width: 3px;
    height: 3px;
    background: rgba(30, 58, 138, 0.6);
    border-radius: 50%;
    animation: twinkle 3s infinite ease-in-out;
}

.star-1 { top: 15%; left: 10%; animation-delay: 0s; }
.star-2 { top: 25%; left: 25%; animation-delay: 0.3s; }
.star-3 { top: 35%; left: 40%; animation-delay: 0.6s; }
.star-4 { top: 45%; left: 55%; animation-delay: 0.9s; }
.star-5 { top: 55%; left: 70%; animation-delay: 1.2s; }
.star-6 { top: 65%; left: 85%; animation-delay: 1.5s; }
.star-7 { top: 75%; left: 15%; animation-delay: 1.8s; }
.star-8 { top: 85%; left: 30%; animation-delay: 2.1s; }
.star-9 { top: 20%; left: 80%; animation-delay: 2.4s; }
.star-10 { top: 60%; left: 5%; animation-delay: 2.7s; }

@keyframes twinkle {
    0%, 100% { 
        opacity: 0.3; 
        transform: scale(1);
    }
    50% { 
        opacity: 1; 
        transform: scale(1.2);
    }
}

/* === TABLERO KANBAN === */
.kanban-section {
    margin: var(--spacing-2xl) 0;
}

.kanban-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.kanban-header h2 {
    font-size: 2rem;
    font-weight: var(--font-weight-bold);
    color: #1e3a8a;
    margin: 0;
}

.kanban-board {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-lg);
    margin: 0 auto;
    max-width: 1400px;
}

.kanban-column {
    background: #ffffff;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    border: 1px solid #e5e7eb;
    min-height: 400px;
}

.column-header {
    padding: var(--spacing-lg);
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.column-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: var(--font-weight-semibold);
    color: #1e3a8a;
}

.task-count {
    background: var(--primary-color);
    color: #ffffff;
    padding: 6px 10px;
    border-radius: var(--radius-full);
    font-size: 0.8rem;
    font-weight: var(--font-weight-bold);
    min-width: 24px;
    text-align: center;
}

.column-header.overdue h3 { color: #1e3a8a; }
.column-header.today h3 { color: #1e3a8a; }
.column-header.week1 h3 { color: #1e3a8a; }
.column-header.week2 h3 { color: #1e3a8a; }

.column-content {
    padding: var(--spacing-md);
    min-height: 300px;
}

.task-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-normal);
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.task-card.overdue {
    border-left: 4px solid #ef4444;
    min-height: 60px;
    padding: calc(var(--spacing-xs) + 2px);
    margin-bottom: calc(var(--spacing-xs) - 2px);
}

.task-card.overdue .task-title {
    font-size: 0.85rem;
    margin-bottom: calc(var(--spacing-xs) - 2px);
}

.task-card.overdue .task-project {
    font-size: 0.7rem;
    margin-bottom: calc(var(--spacing-xs) - 2px);
}

.task-card.overdue .task-due-date {
    font-size: 0.7rem;
    margin-bottom: calc(var(--spacing-xs) - 2px);
}

.task-card.today {
    border-left: 4px solid #f59e0b;
}

.task-card.week1 {
    border-left: 4px solid #3b82f6;
}

.task-card.week2 {
    border-left: 4px solid #10b981;
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-xs);
}

.task-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.task-checkbox input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-sm);
    background: #ffffff;
    position: relative;
    transition: all var(--transition-normal);
}

.task-checkbox input[type="checkbox"]:checked + .checkmark {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.task-checkbox input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.task-priority {
    padding: 4px 8px;
    border-radius: var(--radius-sm);
    font-size: 0.7rem;
    font-weight: var(--font-weight-medium);
}

.task-priority.urgent { background: #fef2f2; color: #dc2626; }
.task-priority.high { background: #fffbeb; color: #d97706; }
.task-priority.medium { background: #eff6ff; color: #2563eb; }
.task-priority.low { background: #f0fdf4; color: #059669; }

.task-title {
    font-weight: var(--font-weight-semibold);
    color: #1e3a8a;
    margin-bottom: var(--spacing-xs);
    font-size: 0.9rem;
    line-height: 1.2;
}

.task-project {
    color: #6b7280;
    font-size: 0.75rem;
    margin-bottom: var(--spacing-xs);
}

.task-due-date {
    font-size: 0.75rem;
    margin-bottom: var(--spacing-xs);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.task-due-date.overdue { color: #ef4444; }
.task-due-date.today { color: #f59e0b; }
.task-due-date.week1 { color: #3b82f6; }
.task-due-date.week2 { color: #10b981; }

.task-actions {
    display: flex;
    justify-content: center;
}

.btn-edit {
    background: var(--primary-color);
    color: #ffffff;
    padding: var(--spacing-xs);
    border-radius: var(--radius-md);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: var(--font-weight-medium);
    transition: all var(--transition-normal);
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: 1px solid var(--primary-color);
}

.btn-edit:hover {
    background: var(--primary-dark);
    transform: scale(1.05);
    box-shadow: var(--shadow-md);
}

/* Responsive para el Kanban */
@media (max-width: 1200px) {
    .kanban-board {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .kanban-board {
        grid-template-columns: 1fr;
    }
    
    .kanban-column {
        min-height: 300px;
    }
}
.content-section{margin-bottom:var(--spacing-2xl)}
.content-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:var(--spacing-xl)}
.content-card{background:#ffffff;border-radius:var(--radius-xl);padding:var(--spacing-xl);box-shadow:var(--shadow-md);border:1px solid #e5e7eb;transition:all var(--transition-normal)}
.table-wrapper{overflow-x:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{background:#f3f4f6;padding:var(--spacing-lg);text-align:left;font-weight:600;color:#1e3a8a;border-bottom:1px solid #e5e7eb}
.data-table td{padding:var(--spacing-lg);border-bottom:1px solid #e5e7eb;color:#6b7280}
.badge{padding:4px 8px;border-radius:6px;font-size:.8rem;text-transform:uppercase}
.btn-sm{padding:6px 10px;font-size:.85rem}
.progress-bar.large{width:100%;height:14px;background:#f3f4f6;border-radius:9999px;overflow:hidden}
.progress-fill{height:100%;background:var(--primary-gradient)}
@media (max-width:1024px){.nav-container{flex-wrap:wrap;gap:var(--spacing-md)}.user-menu{order:-1;width:100%;justify-content:space-between}.content-grid{grid-template-columns:1fr}}
@media (max-width:768px){.welcome-header{flex-direction:column;text-align:center;gap:var(--spacing-lg);align-items:center}.welcome-stats{width:100%}.stats-row{gap:var(--spacing-sm);justify-content:center}.stat-card{min-width:120px}.btn-stats{min-width:120px}.nav-menu{display:none}.main-content{padding:var(--spacing-lg) var(--spacing-md)}}
</style>

<script>
// Frase motivacional en dashboard (una por sesión)
(function(){
  const qEl = document.getElementById('motQuote');
  const aEl = document.getElementById('motAuthor');
  if (!qEl || !aEl) return;
  const apis=[
    {url:'https://api.quotable.io/random', map:d=>({text:d.content, author:d.author})},
    {url:'https://zenquotes.io/api/random', map:d=>{const x=(Array.isArray(d)?d[0]:{})||{}; return {text:x.q, author:x.a};}},
    {url:'https://type.fit/api/quotes', map:d=>{const arr=Array.isArray(d)?d:[]; const r=arr[Math.floor(Math.random()*arr.length)]||{}; return {text:r.text, author:r.author||'Anónimo'};}}
  ];
  const localFallback=[
    {text:'La excelencia no es un acto, es un hábito.', author:'Aristóteles'},
    {text:'La disciplina es el puente entre metas y logros.', author:'Jim Rohn'},
    {text:'Haz hoy lo que otros no harán y mañana vivirás como otros no pueden.', author:'Jerry Rice'}
  ];
  function applyQuote(q){ if(!q||!q.text) q=localFallback[Math.floor(Math.random()*localFallback.length)]; qEl.textContent='“'+(q.text||'')+'”'; aEl.textContent=q.author?('— '+q.author):''; }
  (async function(){
    for(const api of apis){
      try { const r=await fetch(api.url,{credentials:'omit'}); if(!r.ok) continue; const d=await r.json(); const q=api.map(d); if(q&&q.text){ applyQuote(q); return; } } catch(_){ }
    }
    applyQuote(null);
  })();
})();



// Función para cambiar el estado de las tareas
function toggleTaskStatus(taskId, isCompleted) {
  fetch('?route=clan_member/toggle-task-status', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `task_id=${taskId}&is_completed=${isCompleted}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Si la tarea se completó, removerla del tablero
      if (isCompleted) {
        const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
        if (taskCard) {
          taskCard.style.opacity = '0.5';
          taskCard.style.transform = 'scale(0.95)';
          setTimeout(() => {
            taskCard.remove();
            // Actualizar contadores
            updateTaskCounts();
          }, 300);
        }
      }
      // Mostrar notificación
      showNotification(data.message, 'success');
    } else {
      showNotification(data.message, 'error');
      // Revertir checkbox si falló
      const checkbox = document.querySelector(`[data-task-id="${taskId}"] input[type="checkbox"]`);
      if (checkbox) checkbox.checked = !isCompleted;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Error al actualizar la tarea', 'error');
    // Revertir checkbox si falló
    const checkbox = document.querySelector(`[data-task-id="${taskId}"] input[type="checkbox"]`);
    if (checkbox) checkbox.checked = !isCompleted;
  });
}

// Función para actualizar contadores de tareas
function updateTaskCounts() {
  const columns = ['vencidas', 'hoy', '1_semana', '2_semanas'];
  columns.forEach(columnType => {
    const column = document.querySelector(`.kanban-column:has(.column-header.${columnType})`);
    if (column) {
      const taskCount = column.querySelectorAll('.task-card').length;
      const countElement = column.querySelector('.task-count');
      if (countElement) {
        countElement.textContent = taskCount;
      }
    }
  });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  
  // Estilos de la notificación
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
  `;
  
  // Colores según el tipo
  if (type === 'success') notification.style.background = '#10b981';
  else if (type === 'error') notification.style.background = '#ef4444';
  else notification.style.background = '#3b82f6';
  
  document.body.appendChild(notification);
  
  // Mostrar notificación
  setTimeout(() => {
    notification.style.transform = 'translateX(0)';
  }, 100);
  
  // Ocultar después de 3 segundos
  setTimeout(() => {
    notification.style.transform = 'translateX(100%)';
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }, 3000);
}
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
$additionalJS = [];
require_once __DIR__ . '/../layout.php';
?>


