<?php
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg"><i class="fas fa-rhino"></i></div>
                <span class="brand-text">RinoTrack</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="?route=clan_member" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/projects" class="nav-link"><i class="fas fa-project-diagram"></i><span>Proyectos</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/tasks" class="nav-link"><i class="fas fa-tasks"></i><span>Tareas</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li>
                <li class="nav-item active"><a href="?route=clan_member/availability" class="nav-link"><i class="fas fa-user-clock"></i><span>Disponibilidad</span></a></li>
            </ul>
            <div class="user-menu">
                <div class="user-avatar modern-avatar">
                    <span class="avatar-text"><?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?></span>
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
                <h1 class="welcome-title">Mi disponibilidad</h1>
                <p class="welcome-subtitle">Clan: <?php echo Utils::escape($clan['clan_name'] ?? ''); ?></p>
            </div>
            <div class="welcome-stats">
                <div class="quick-stat">
                    <div class="stat-icon success"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-text">
                        <span class="stat-value"><?php echo (int)($summary['task_count'] ?? 0); ?></span>
                        <span class="stat-label">Tareas próximas (15 días)</span>
                    </div>
                </div>
            </div>
        </header>

        <section class="content-section animate-fade-in">
            <div class="content-card">
                <div class="card-header"><h3><i class="fas fa-list icon-gradient"></i> Mis tareas</h3></div>
                <?php if (empty($tasks)): ?>
                    <div class="empty">Sin tareas próximas</div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Tarea</th><th>Proyecto</th><th>Vence</th><th>Estado</th></tr></thead>
                            <tbody>
                                <?php foreach ($tasks as $t): ?>
                                    <tr>
                                        <td><?php echo Utils::escape($t['task_name']); ?></td>
                                        <td><?php echo Utils::escape($t['project_name']); ?></td>
                                        <td><?php echo !empty($t['due_date']) ? date('d/m/Y', strtotime($t['due_date'])) : '–'; ?></td>
                                        <td><span class="badge status-<?php echo Utils::escape($t['status']); ?>"><?php echo Utils::escape($t['status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
.brand-icon{width:40px;height:40px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:#fff;background:var(--primary-gradient)}
.nav-menu{display:flex;list-style:none;gap:var(--spacing-sm)}
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
.badge{padding:4px 8px;border-radius:6px;font-size:.8rem;text-transform:uppercase}
.empty{padding:12px;color:#64748b}
@media (max-width:768px){.nav-menu{display:none}.main-content{padding:var(--spacing-lg) var(--spacing-md)}}
</style>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


