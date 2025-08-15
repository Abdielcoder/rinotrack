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
                <li class="nav-item active"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li>
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
                <h1 class="welcome-title">Dashboard KPI</h1>
                <p class="welcome-subtitle">Clan: <?php echo Utils::escape($clan['clan_name'] ?? ''); ?></p>
            </div>
        </header>

        <section class="content-section animate-fade-in">
            <div class="content-card">
                <?php if (!$currentKPI): ?>
                    <div class="empty">No hay trimestre KPI activo</div>
                <?php else: ?>
                    <div class="card-header"><h3><i class="fas fa-gauge-high icon-gradient"></i> Mis indicadores</h3></div>
                    <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:center;justify-content:space-between">
                        <div style="display:flex;gap:14px;color:#475569;font-size:.95rem;flex:1">
                            <div><strong>Meta:</strong> <?php echo number_format($userKPI['target_points'] ?? 1000); ?> pts</div>
                            <div><strong>Ganados:</strong> <?php echo number_format((float)($userKPI['earned_points'] ?? 0),2); ?> pts</div>
                            <div><strong>Tareas:</strong> <?php echo (int)($userKPI['completed_tasks'] ?? 0); ?>/<?php echo (int)($userKPI['total_tasks'] ?? 0); ?></div>
                        </div>
                        <div class="progress-container" style="width:100%;max-width:420px">
                            <div class="progress-bar large"><div class="progress-fill" style="width: <?php echo (float)($userKPI['progress_percentage'] ?? 0); ?>%"></div></div>
                            <div style="text-align:right;font-weight:600;color:#0f766e;margin-top:6px;">
                                <?php echo number_format((float)($userKPI['progress_percentage'] ?? 0),1); ?>%
                            </div>
                        </div>
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
.progress-bar.large{width:100%;height:14px;background:var(--bg-tertiary);border-radius:9999px;overflow:hidden}
.progress-fill{height:100%;background:var(--primary-gradient)}
.empty{padding:12px;color:#64748b}
@media (max-width:768px){.nav-menu{display:none}.main-content{padding:var(--spacing-lg) var(--spacing-md)}}
</style>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


