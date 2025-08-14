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
                    Asignación de Tareas (Clan Olympo)
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


