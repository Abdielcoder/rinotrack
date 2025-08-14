<?php
// Capturar el contenido de la vista
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-tasks"></i>
                </div>
                <span class="brand-text">Gestión de Tareas</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="?route=admin" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin/users" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin/projects" class="nav-link">
                        <i class="fas fa-project-diagram"></i>
                        <span>Proyectos</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="?route=admin/tasks" class="nav-link">
                        <i class="fas fa-tasks"></i>
                        <span>Tareas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?route=admin/clans" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Clanes</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle">
                        <i class="fas fa-chart-line"></i>
                        <span>KPIs</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="?route=kpi/dashboard" class="dropdown-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a></li>
                        <li><a href="?route=kpi/quarters" class="dropdown-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Trimestres</span>
                        </a></li>
                        <li><a href="?route=kpi/projects" class="dropdown-link">
                            <i class="fas fa-project-diagram"></i>
                            <span>Proyectos</span>
                        </a></li>
                    </ul>
                </li>
            </ul>

            <div class="user-menu">
                <div class="user-avatar modern-avatar">
                    <span class="avatar-text"><?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?></span>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo Utils::escape($user['full_name'] ?: $user['username']); ?></span>
                    <span class="user-role">Administrador</span>
                </div>
                <div class="user-actions">
                    <a href="?route=logout" class="action-btn logout" title="Cerrar Sesión">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <header class="page-header animate-fade-in">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-tasks"></i>
                    Asignación de Tareas
                </h1>
            </div>
        </header>

        <section class="content-section animate-fade-in">
            <div class="content-grid">
                <div class="content-card">
                    <div class="card-header">
                        <h3>Crear tarea recurrente/eventual</h3>
                    </div>
                    <form id="adminCreateTaskForm" class="modal-form">
                        <input type="hidden" name="route" value="admin/add-task">

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Tipo</label>
                                <select id="taskType" onchange="onTaskTypeChange()">
                                    <option value="recurrent">Recurrente</option>
                                    <option value="eventual">Eventual</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Proyecto lógico</label>
                                <select id="projectId" name="projectId">
                                    <option value="<?php echo (int)$recurrentProject['project_id']; ?>">Tareas Recurrentes</option>
                                    <option value="<?php echo (int)$eventualProject['project_id']; ?>">Tareas Eventuales</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nombre de la tarea</label>
                                <input type="text" name="taskName" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="description" rows="4" placeholder="Describe la tarea..."></textarea>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Fecha límite</label>
                                <input type="date" name="dueDate">
                            </div>
                            <div class="form-group">
                                <label>Asignar a miembros (multiple)</label>
                                <select name="assignedUsers[]" id="assignedUsers" multiple>
                                    <?php foreach ($members as $m): ?>
                                        <option value="<?php echo (int)$m['user_id']; ?>"><?php echo Utils::escape($m['full_name'] ?: $m['username']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="modal-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Crear Tarea
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

		<!-- Panel de seguimiento del Clan Olympo -->
		<section class="content-section animate-fade-in">
			<div class="project-overview">
				<div class="overview-card">
					<h4>Resumen</h4>
					<div class="overview-grid">
						<div><span class="label">Clan:</span> <span class="value"><?php echo Utils::escape($olympo['clan_name'] ?? 'Olympo'); ?></span></div>
\t\t\t\t\t\t<div><span class="label">Progreso:</span> <span class="value"><?php echo number_format((float)($stats['progress'] ?? 0), 1); ?>%</span></div>
						<div><span class="label">Tareas totales:</span> <span class="value"><?php echo (int)($stats['total'] ?? 0); ?></span></div>
						<div><span class="label">Completadas:</span> <span class="value"><?php echo (int)($stats['completed'] ?? 0); ?></span></div>
					</div>
					<div class="progress-bar large"><div class="progress-fill" style="width: <?php echo (float)($stats['progress'] ?? 0); ?>%"></div></div>
				</div>

				<div class="overview-card">
					<h4>Estado de Tareas</h4>
					<div class="stats-row">
						<div class="stat"><span class="num"><?php echo (int)($stats['total'] ?? 0); ?></span><span class="cap">Total</span></div>
						<div class="stat"><span class="num"><?php echo (int)($stats['pending'] ?? 0); ?></span><span class="cap">Pendientes</span></div>
						<div class="stat"><span class="num"><?php echo (int)($stats['in_progress'] ?? 0); ?></span><span class="cap">En progreso</span></div>
						<div class="stat"><span class="num"><?php echo (int)($stats['completed'] ?? 0); ?></span><span class="cap">Completadas</span></div>
						<div class="stat"><span class="num warn"><?php echo (int)($stats['overdue'] ?? 0); ?></span><span class="cap">Vencidas</span></div>
					</div>
				</div>
			</div>

			<div class="two-cols">
				<div class="col">
					<div style="display:flex;align-items:center;justify-content:space-between;gap:10px">
						<h4 style="margin:0">Tareas</h4>
						<a class="btn btn-primary" href="#adminCreateTaskForm"><i class="fas fa-plus"></i> Agregar tarea</a>
					</div>
					<div class="table-wrapper">
						<table class="data-table">
							<thead>
								<tr><th>Nombre</th><th>Asignado</th><th>Proyecto</th><th>Estado</th><th>Vence</th></tr>
							</thead>
							<tbody>
								<?php if (empty($tasks ?? [])): ?>
								<tr><td colspan="5" class="empty">Sin tareas registradas</td></tr>
								<?php else: foreach (($tasks ?? []) as $t): ?>
								<tr>
									<td><?php echo Utils::escape($t['task_name']); ?></td>
									<td><?php echo Utils::escape($t['all_assigned_users'] ?: ($t['assigned_user_name'] ?? '-')); ?></td>
									<td><?php echo Utils::escape($t['project_name'] ?? ''); ?></td>
									<td><span class="badge status-<?php echo Utils::escape($t['status']); ?>"><?php echo Utils::escape($t['status']); ?></span></td>
									<td><?php echo !empty($t['due_date']) ? date('d/m/Y', strtotime($t['due_date'])) : '-'; ?></td>
								</tr>
								<?php endforeach; endif; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col">
					<h4>Actividad Reciente</h4>
					<div class="activity-list">
						<?php if (empty($history ?? [])): ?>
							<div class="empty">Sin actividad reciente</div>
						<?php else: foreach (($history ?? []) as $h): ?>
							<div class="activity-item">
								<div class="meta">
									<span class="task"><?php echo Utils::escape($h['task_name']); ?></span>
									<span class="time"><?php echo date('d/m/Y H:i', strtotime($h['created_at'])); ?></span>
								</div>
								<div class="desc">
									<span class="user"><?php echo Utils::escape($h['full_name'] ?: $h['username'] ?: 'Sistema'); ?></span>
									<span>→</span>
									<strong><?php echo Utils::escape($h['action_type']); ?></strong>
									<?php if (!empty($h['field_name'])): ?>
										<span class="field">(<?php echo Utils::escape($h['field_name']); ?>)</span>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; endif; ?>
					</div>
				</div>
			</div>
		</section>

		<style>
		.project-overview{display:grid;grid-template-columns:1fr 1fr;gap:var(--admin-spacing-xl);margin-bottom:var(--admin-spacing-xl)}
		.overview-card{background:var(--admin-bg-primary);border:1px solid var(--admin-border);border-radius:var(--admin-radius-lg);padding:var(--admin-spacing-xl)}
		.overview-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px 18px;margin-bottom:14px}
		.overview-grid .label{color:var(--admin-text-muted)}
		.overview-grid .value{color:var(--admin-text-primary);font-weight:600}
		.badge{padding:4px 8px;border-radius:8px;font-size:.8rem}
		.status-pending{background:var(--admin-bg-tertiary);color:var(--admin-text-secondary)}
		.status-in_progress{background:var(--admin-secondary);color:#fff}
		.status-completed{background:var(--success, #10b981);color:#fff}
		.status-cancelled{background:var(--error, #ef4444);color:#fff}
		.progress-bar.large{height:10px;background:var(--admin-bg-accent);border-radius:999px;overflow:hidden}
		.progress-fill{height:100%;background:linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%)}
		.stats-row{display:flex;gap:18px}
		.stat{background:var(--admin-bg-tertiary);border:1px solid var(--admin-bg-accent);border-radius:12px;padding:12px 16px;flex:1;text-align:center}
		.stat .num{font-weight:800;color:var(--admin-text-primary);font-size:1.1rem}
		.stat .num.warn{color:#e53e3e}
		.stat .cap{display:block;color:var(--admin-text-muted);font-size:.85rem;margin-top:4px}
		.two-cols{display:grid;grid-template-columns:2fr 1fr;gap:var(--admin-spacing-xl)}
		.table-wrapper{border:1px solid var(--admin-border);border-radius:12px;overflow:hidden;background:var(--admin-bg-primary)}
		.data-table{width:100%;border-collapse:collapse}
		.data-table th,.data-table td{padding:12px 14px;border-bottom:1px solid var(--admin-border)}
		.data-table th{background:var(--admin-bg-tertiary);text-align:left}
		.activity-list{display:flex;flex-direction:column;gap:12px}
		.activity-item{background:var(--admin-bg-primary);border:1px solid var(--admin-border);border-radius:12px;padding:12px}
		.activity-item .meta{display:flex;justify-content:space-between;margin-bottom:6px;color:var(--admin-text-muted)}
		.activity-item .desc{display:flex;gap:8px;flex-wrap:wrap;color:var(--admin-text-secondary)}
		.activity-item .user{font-weight:600;color:var(--admin-text-primary)}
		.empty{color:var(--admin-text-muted);text-align:center;padding:16px}
		@media(max-width:900px){.project-overview{grid-template-columns:1fr}.two-cols{grid-template-columns:1fr}}
		</style>
    </main>
</div>

<script>
function onTaskTypeChange() {
    const sel = document.getElementById('taskType');
    const projectSel = document.getElementById('projectId');
    if (!sel || !projectSel) return;
    const type = sel.value;
    const recId = <?php echo (int)$recurrentProject['project_id']; ?>;
    const evtId = <?php echo (int)$eventualProject['project_id']; ?>;
    projectSel.value = (type === 'recurrent') ? String(recId) : String(evtId);
}

document.getElementById('adminCreateTaskForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.currentTarget;
    const data = new FormData(form);
    try {
        const resp = await fetch('?route=admin/add-task', { method: 'POST', body: data });
        const json = await resp.json();
        if (json.success) {
            alert('Tarea creada exitosamente');
            form.reset();
        } else {
            alert(json.message || 'Error al crear tarea');
        }
    } catch (err) {
        alert('Error de red');
    }
});
</script>

<?php
$content = ob_get_clean();
$title = 'Gestión de Tareas - ' . APP_NAME;
include __DIR__ . '/layout.php';
?>


