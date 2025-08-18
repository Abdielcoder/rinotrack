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
                <li class="nav-item"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/availability" class="nav-link"><i class="fas fa-user-clock"></i><span>Disponibilidad</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/profile" class="nav-link"><i class="fas fa-user"></i><span>Perfil</span></a></li>
            </ul>
            
            <!-- Botón hamburguesa para móvil -->
            <button class="hamburger-menu" onclick="toggleMobileMenu()">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
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
                        <li class="mobile-nav-item"><a href="?route=clan_member/kpi-dashboard" class="mobile-nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li>
                        <li class="mobile-nav-item"><a href="?route=clan_member/availability" class="mobile-nav-link"><i class="fas fa-user-clock"></i><span>Disponibilidad</span></a></li>
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

        <section class="content-section animate-fade-in">
            <div class="content-card">
                <div class="card-header"><h3><i class="fas fa-diagram-project icon-gradient"></i> Proyectos del Clan</h3></div>
                <?php if (empty($projectsSummary)): ?>
                    <div class="empty">No hay proyectos para mostrar</div>
                <?php else: ?>
                <div class="cm-project-cards">
                    <?php foreach ($projectsSummary as $p): ?>
                        <?php 
                            $pid = (int)($p['project_id'] ?? 0);
                            $prog = (float)($p['progress_percentage'] ?? 0);
                            $status = strtoupper($p['status'] ?? 'open');
                        ?>
                        <div class="cm-project-card">
                            <div class="pc-top">
                                <div class="pc-title"><?php echo Utils::escape($p['project_name'] ?? ''); ?></div>
                                <div class="pc-status"><?php echo Utils::escape($status); ?></div>
                            </div>
                            <div class="pc-metrics">
                                <div class="pc-metric"><i class="fas fa-list"></i><div><div class="num"><?php echo (int)($p['total_tasks'] ?? 0); ?></div><div class="cap">Total</div></div></div>
                                <div class="pc-metric"><i class="fas fa-check-circle"></i><div><div class="num"><?php echo (int)($p['completed_tasks'] ?? 0); ?></div><div class="cap">Completadas</div></div></div>
                                <div class="pc-metric"><i class="fas fa-chart-line"></i><div><div class="num"><?php echo number_format($prog, 2); ?>%</div><div class="cap">Progreso</div></div></div>
                            </div>
                            <div class="pc-progress"><span style="width: <?php echo $prog; ?>%"></span></div>
                            <div class="pc-actions">
                                <?php if ($pid > 0): ?>
                                <a class="btn btn-secondary" href="?route=clan_member/project-tasks&project_id=<?php echo $pid; ?>">
                                    <i class="fas fa-eye"></i> Ver Tareas
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="content-section animate-fade-in">
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-filter icon-gradient"></i> Filtros</h3>
                </div>
                <form method="get" class="filters" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                    <input type="hidden" name="route" value="clan_member/tasks" />
                    <input type="text" name="search" class="search-input" placeholder="Buscar" value="<?php echo Utils::escape($search ?? ''); ?>" />
                    <select name="status" class="filter-select">
                        <option value="">Todos</option>
                        <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="in_progress" <?php echo ($status === 'in_progress') ? 'selected' : ''; ?>>En progreso</option>
                        <option value="completed" <?php echo ($status === 'completed') ? 'selected' : ''; ?>>Completado</option>
                    </select>
                    <select name="per_page" class="filter-select" title="Resultados por página">
                        <?php $pp = (int)($perPage ?? 8); foreach ([5,8,12,20,30] as $opt): ?>
                            <option value="<?php echo $opt; ?>" <?php echo ($pp === $opt) ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-secondary" type="submit">Aplicar</button>
                </form>
            </div>
        </section>

        <section class="content-section animate-fade-in">
            <div class="content-card">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr><th>Prioridad</th><th>Tarea</th><th>Proyecto</th><th>Asignado(s)</th><th>Vence</th><th>Estado</th><th>Puntos</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tasksData['tasks'])): ?>
                                <tr><td colspan="8" class="empty">No hay tareas</td></tr>
                            <?php else: foreach ($tasksData['tasks'] as $t): ?>
                                <?php
                                    $statusClass = ($t['status'] ?? '') === 'completed' ? 'completed' : (($t['status'] ?? '') === 'in_progress' ? 'in_progress' : 'pending');
                                    $priorityClass = strtolower((string)($t['priority'] ?? 'medium'));
                                    $priorityLabel = ucfirst(str_replace(['_'], ' ', (string)($t['priority'] ?? 'medium')));
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
                                    <td><span class="badge badge-priority <?php echo $priorityClass; ?>" title="Prioridad"><i class="fas fa-flag"></i> <?php echo $priorityLabel; ?></span></td>
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
                                    <td class="cell-points"><?php echo isset($t['automatic_points']) ? number_format((float)$t['automatic_points'], 2) : '–'; ?></td>
                                    <td class="cell-actions">
                                        <a class="action-btn" href="?route=clan_member/task-details&task_id=<?php echo (int)$t['task_id']; ?>&action=edit" title="Editar tarea"><i class="fas fa-edit"></i></a>
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
.badge-priority.high{background:#fee2e2;color:#991b1b}
.badge-priority.medium{background:#ffedd5;color:#9a3412}
.badge-priority.low{background:#dcfce7;color:#166534}
.badge-due.overdue{background:#fee2e2;color:#991b1b}
.badge-due.due-soon{background:#fef3c7;color:#92400e}
.chip-status.completed{background:#dcfce7;color:#166534}
.chip-status.in_progress{background:#dbeafe;color:#1e40af}
.chip-status.pending{background:#f1f5f9;color:#334155}
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
    display: none;
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
    opacity: 1;
    visibility: visible;
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
    transform: translateX(0);
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
    
    .cm-project-cards {
        grid-template-columns: 1fr;
    }
    
    .cm-project-card {
        min-width: 100%;
    }
}

@media (max-width: 480px) {
    .nav-container {
        padding: 0 var(--spacing-sm);
    }
    
    .main-content {
        padding: var(--spacing-md) var(--spacing-sm);
    }
    
    .welcome-title {
        font-size: 1.8rem;
    }
}
</style>

<script>
// Función para el menú hamburguesa
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const hamburger = document.querySelector('.hamburger-menu');
    
    if (mobileMenu.classList.contains('active')) {
        mobileMenu.classList.remove('active');
        hamburger.classList.remove('active');
    } else {
        mobileMenu.classList.add('active');
        hamburger.classList.add('active');
    }
}

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
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


