<?php
ob_start();
?>

<?php $projectsCount = is_array($projects ?? null) ? count($projects) : 0; ?>

<div class="modern-dashboard" data-theme="default">
	<nav class="modern-nav glass">
		<div class="nav-container">
			<div class="nav-brand">
				<div class="brand-icon gradient-bg"><i class="fas fa-rhino"></i></div>
				<span class="brand-text">RinoTrack</span>
			</div>
			<ul class="nav-menu">
				<li class="nav-item"><a href="?route=clan_member" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
				<li class="nav-item active"><a href="?route=clan_member/projects" class="nav-link"><i class="fas fa-project-diagram"></i><span>Proyectos</span></a></li>
				<li class="nav-item"><a href="?route=clan_member/tasks" class="nav-link"><i class="fas fa-tasks"></i><span>Tareas</span></a></li>
				<li class="nav-item"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li>
				<li class="nav-item"><a href="?route=clan_member/availability" class="nav-link"><i class="fas fa-user-clock"></i><span>Disponibilidad</span></a></li>
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
				<h1 class="welcome-title">Proyectos del Clan</h1>
				<p class="welcome-subtitle">Clan: <?php echo Utils::escape($clan['clan_name'] ?? ''); ?></p>
			</div>
			<div class="welcome-stats">
				<div class="quick-stat">
					<div class="stat-icon success"><i class="fas fa-folder-open"></i></div>
					<div class="stat-text">
						<span class="stat-value"><?php echo (int)$projectsCount; ?></span>
						<span class="stat-label">Proyectos</span>
					</div>
				</div>
			</div>
		</header>

		<section class="content-section animate-fade-in">
			<div class="content-card">
				<div class="card-header"><h3><i class="fas fa-project-diagram icon-gradient"></i> Listado</h3></div>
				<?php if (empty($projects)): ?>
					<div class="empty">No hay proyectos</div>
				<?php else: ?>
					<div class="table-wrapper">
						<table class="data-table">
							<thead><tr><th>Proyecto</th><th>Descripción</th><th>Creado</th><th>Estado</th></tr></thead>
							<tbody>
								<?php foreach ($projects as $p): ?>
								<tr>
									<td>
										<a class="btn btn-secondary btn-sm" href="?route=clan_member/project-tasks&project_id=<?php echo (int)$p['project_id']; ?>">
											<i class="fas fa-eye"></i>
											<?php echo Utils::escape($p['project_name']); ?>
										</a>
									</td>
									<td><?php echo Utils::escape($p['description'] ?? ''); ?></td>
									<td><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
									<td><span class="badge status-<?php echo Utils::escape($p['status']); ?>"><?php echo Utils::escape($p['status']); ?></span></td>
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
.modern-dashboard{min-height:100vh;background:var(--bg-secondary);padding:0;position:relative}
.modern-nav{background:var(--bg-glass);backdrop-filter:var(--glass-backdrop);border-bottom:1px solid var(--glass-border);padding:var(--spacing-md) 0;position:sticky;top:0;z-index:100}
.nav-container{max-width:1400px;margin:0 auto;padding:0 var(--spacing-lg);display:flex;align-items:center;justify-content:space-between;gap:var(--spacing-xl)}
.nav-brand{display:flex;align-items:center;gap:var(--spacing-md)}
.brand-icon{width:40px;height:40px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:var(--text-white);font-size:1.2rem;background:var(--primary-gradient)}
.brand-text{font-size:1.5rem;font-weight:var(--font-weight-bold);background:var(--primary-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.nav-menu{display:flex;list-style:none;gap:var(--spacing-sm)}
.nav-item .nav-link{display:flex;align-items:center;gap:var(--spacing-sm);padding:var(--spacing-md) var(--spacing-lg);border-radius:var(--radius-md);text-decoration:none;color:var(--text-secondary);font-weight:var(--font-weight-medium);transition:all var(--transition-normal)}
.nav-item .nav-link:hover{color:var(--primary-color);background:var(--bg-primary);transform:translateY(-2px);box-shadow:var(--shadow-md)}
.nav-item.active .nav-link{background:var(--primary-gradient);color:var(--text-white);box-shadow:var(--shadow-glow)}
.user-menu{display:flex;align-items:center;gap:var(--spacing-md)}
.modern-avatar{position:relative;width:45px;height:45px;background:var(--primary-gradient);border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;color:var(--text-white);font-weight:var(--font-weight-semibold);box-shadow:var(--shadow-md)}
.status-dot{position:absolute;bottom:2px;right:2px;width:12px;height:12px;background:var(--success);border:2px solid var(--bg-primary);border-radius:var(--radius-full)}
.user-info{display:flex;flex-direction:column;gap:2px}
.user-name{font-weight:var(--font-weight-semibold);color:var(--text-primary);font-size:.95rem}
.user-role{font-size:.8rem;color:var(--text-muted)}
.action-btn{width:35px;height:35px;border:none;border-radius:var(--radius-md);background:var(--bg-primary);color:var(--text-secondary);cursor:pointer;transition:all var(--transition-normal);display:flex;align-items:center;justify-content:center;text-decoration:none;box-shadow:var(--shadow-sm)}
.action-btn.logout:hover{color:var(--error)}
.main-content{max-width:1400px;margin:0 auto;padding:var(--spacing-xl) var(--spacing-lg)}
.welcome-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-2xl);padding:var(--spacing-xl);background:var(--bg-primary);border-radius:var(--radius-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent)}
.welcome-title{font-size:2rem;font-weight:var(--font-weight-bold);color:var(--text-primary);margin-bottom:var(--spacing-sm)}
.welcome-subtitle{font-size:1.05rem;color:var(--text-secondary)}
.quick-stat{display:flex;align-items:center;gap:var(--spacing-md);padding:var(--spacing-lg);background:var(--bg-tertiary);border-radius:var(--radius-lg);border:1px solid var(--bg-accent)}
.stat-icon{width:50px;height:50px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:var(--text-white);font-size:1.2rem}
.stat-icon.success{background:var(--success)}
.content-section{margin-bottom:var(--spacing-2xl)}
.content-card{background:var(--bg-primary);border-radius:var(--radius-xl);padding:var(--spacing-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent);transition:all var(--transition-normal)}
.table-wrapper{overflow-x:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{background:var(--bg-tertiary);padding:var(--spacing-lg);text-align:left;font-weight:600;color:var(--text-primary);border-bottom:1px solid var(--bg-accent)}
.data-table td{padding:var(--spacing-lg);border-bottom:1px solid var(--bg-accent);color:var(--text-secondary)}
.badge{padding:4px 8px;border-radius:6px;font-size:.8rem;text-transform:uppercase}
.btn-sm{padding:6px 10px;font-size:.85rem}
@media (max-width:1024px){.nav-container{flex-wrap:wrap;gap:var(--spacing-md)}.user-menu{order:-1;width:100%;justify-content:space-between}}
@media (max-width:768px){.welcome-header{flex-direction:column;text-align:center;gap:var(--spacing-lg)}.nav-menu{display:none}.main-content{padding:var(--spacing-lg) var(--spacing-md)}}
</style>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


