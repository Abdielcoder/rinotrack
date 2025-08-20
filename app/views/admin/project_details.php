<?php
// Buffer de salida
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
  <nav class="modern-nav glass">
    <div class="nav-container">
      <div class="nav-brand">
        <div class="brand-icon gradient-bg"><i class="fas fa-project-diagram"></i></div>
        <span class="brand-text">Detalle de Proyecto</span>
      </div>
      <ul class="nav-menu">
        <li class="nav-item"><a href="?route=admin" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <li class="nav-item active"><a href="?route=admin/projects" class="nav-link"><i class="fas fa-project-diagram"></i><span>Proyectos</span></a></li>
      </ul>
      <div class="user-menu">
        <div class="user-avatar modern-avatar"><span class="avatar-text"><?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?></span></div>
        <div class="user-info"><span class="user-name"><?php echo Utils::escape($user['full_name'] ?: $user['username']); ?></span><span class="user-role">Administrador</span></div>
        <div class="user-actions"><a href="?route=logout" class="action-btn logout" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></a></div>
      </div>
    </div>
  </nav>

  <main class="main-content">
    <header class="page-header animate-fade-in">
      <div class="header-content">
        <h1 class="page-title"><i class="fas fa-folder-open"></i> <?php echo Utils::escape($project['project_name']); ?></h1>
        <div class="header-actions"><a class="btn btn-secondary" href="?route=admin/projects"><i class="fas fa-arrow-left"></i> Volver</a></div>
      </div>
    </header>

    <section class="content-section animate-fade-in">
      <div class="project-overview">
        <div class="overview-card">
          <h4>Resumen</h4>
          <div class="overview-grid">
            <div><span class="label">Clan:</span> <span class="value"><?php echo Utils::escape($project['clan_name'] ?? ''); ?></span></div>
            <div><span class="label">Estado:</span> <span class="value badge status-<?php echo $project['status']; ?>"><?php echo $project['status']; ?></span></div>
            <div><span class="label">Progreso:</span> <span class="value"><?php echo number_format($stats['progress'], 1); ?>%</span></div>
            <div><span class="label">Creado:</span> <span class="value"><?php echo date('d/m/Y', strtotime($project['created_at'])); ?></span></div>
          </div>
          <div class="progress-bar large"><div class="progress-fill" style="width: <?php echo (float)$stats['progress']; ?>%"></div></div>
        </div>

        <div class="overview-card">
          <h4>Estado de Tareas</h4>
          <div class="stats-row">
            <div class="stat"><span class="num"><?php echo (int)$stats['total']; ?></span><span class="cap">Total</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats['pending']; ?></span><span class="cap">Pendientes</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats['in_progress']; ?></span><span class="cap">En progreso</span></div>
            <div class="stat"><span class="num"><?php echo (int)$stats['completed']; ?></span><span class="cap">Completadas</span></div>
            <div class="stat"><span class="num warn"><?php echo (int)$stats['overdue']; ?></span><span class="cap">Vencidas</span></div>
          </div>
        </div>
      </div>

      <div class="two-cols">
        <div class="col">
          <h4>Tareas</h4>
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr><th>Nombre</th><th>Asignado</th><th>Estado</th><th>Vence</th><th>Acciones</th></tr>
              </thead>
              <tbody>
                <?php if (empty($tasks)): ?>
                <tr><td colspan="5" class="empty">Sin tareas</td></tr>
                <?php else: foreach ($tasks as $t): ?>
                <tr>
                  <td>
                    <?php echo Utils::escape($t['task_name']); ?>
                    <?php if (($t['subtasks_count'] ?? 0) > 0): ?>
                      <span class="subtask-count-badge"><?php echo $t['subtasks_count']; ?> subtareas</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo Utils::escape($t['assigned_to_fullname'] ?: ($t['all_assigned_users'] ?? '-')); ?></td>
                  <td><span class="badge status-<?php echo $t['status']; ?>"><?php echo $t['status']; ?></span></td>
                  <td><?php echo $t['due_date'] ? date('d/m/Y', strtotime($t['due_date'])) : '-'; ?></td>
                  <td>
                    <?php if (($t['subtasks_count'] ?? 0) > 0): ?>
                      <button class="btn-subtasks" data-task-id="<?php echo intval($t['task_id']); ?>" title="Ver subtareas">
                        <i class="fas fa-list-ul"></i> Subtareas
                      </button>
                    <?php else: ?>
                      <span class="no-subtasks">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col">
          <h4>Actividad Reciente</h4>
          <div class="activity-list">
            <?php if (empty($history)): ?>
              <div class="empty">Sin actividad reciente</div>
            <?php else: foreach ($history as $h): ?>
              <div class="activity-item">
                <div class="meta">
                  <span class="task"><?php echo Utils::escape($h['task_name']); ?></span>
                  <span class="time"><?php echo date('d/m/Y H:i', strtotime($h['created_at'])); ?></span>
                </div>
                <div class="desc">
                  <span class="user"><?php echo Utils::escape($h['full_name'] ?: $h['username'] ?: 'Sistema'); ?></span>
                  <span>→</span>
                  <strong><?php echo Utils::escape($h['action_type']); ?></strong>
                  <?php if ($h['field_name']): ?>
                    <span class="field">(<?php echo Utils::escape($h['field_name']); ?>)</span>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<!-- Modal para mostrar subtareas -->
<div id="subtasksModal" class="modal" style="display: none;">
  <div class="modal-content modal-large">
    <div class="modal-header">
      <h3 id="subtasksModalTitle">Subtareas</h3>
      <button class="modal-close" onclick="closeSubtasksModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <div class="modal-body">
      <div class="task-info">
        <h4 id="taskName">Cargando...</h4>
        <span class="project-name" id="projectName"></span>
      </div>
      
      <div class="subtasks-container" id="subtasksContainer">
        <div class="loading">
          <i class="fas fa-spinner fa-spin"></i>
          Cargando subtareas...
        </div>
      </div>
    </div>
    
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeSubtasksModal()">
        Cerrar
      </button>
    </div>
  </div>
</div>

<style>
.project-overview {display:grid;grid-template-columns:1fr 1fr;gap:var(--spacing-xl);margin-bottom:var(--spacing-xl)}
.overview-card{background:var(--bg-primary);border:1px solid var(--bg-accent);border-radius:var(--radius-xl);padding:var(--spacing-xl)}
.overview-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px 18px;margin-bottom:14px}
.overview-grid .label{color:var(--text-muted)}
.overview-grid .value{color:var(--text-primary);font-weight:600}
.badge{padding:4px 8px;border-radius:8px;font-size:.8rem}
.status-open{background:var(--info);color:#fff}
.status-completed{background:var(--success);color:#fff}
.status-paused{background:var(--warning);color:#fff}
.status-cancelled{background:var(--error);color:#fff}
.progress-bar.large{height:10px;background:var(--bg-accent);border-radius:999px;overflow:hidden}
.progress-fill{height:100%;background:var(--primary-gradient)}
.stats-row{display:flex;gap:18px}
.stat{background:var(--bg-tertiary);border:1px solid var(--bg-accent);border-radius:12px;padding:12px 16px;flex:1;text-align:center}
.stat .num{font-weight:800;color:var(--text-primary);font-size:1.1rem}
.stat .num.warn{color:#e53e3e}
.stat .cap{display:block;color:var(--text-muted);font-size:.85rem;margin-top:4px}
.two-cols{display:grid;grid-template-columns:2fr 1fr;gap:var(--spacing-xl)}
.table-wrapper{border:1px solid var(--bg-accent);border-radius:12px;overflow:hidden;background:var(--bg-primary)}
.data-table{width:100%;border-collapse:collapse}
.data-table th,.data-table td{padding:12px 14px;border-bottom:1px solid var(--bg-accent)}
.data-table th{background:var(--bg-tertiary);text-align:left}
.activity-list{display:flex;flex-direction:column;gap:12px}
.activity-item{background:var(--bg-primary);border:1px solid var(--bg-accent);border-radius:12px;padding:12px}
.activity-item .meta{display:flex;justify-content:space-between;margin-bottom:6px;color:var(--text-muted)}
.activity-item .desc{display:flex;gap:8px;flex-wrap:wrap;color:var(--text-secondary)}
.activity-item .user{font-weight:600;color:var(--text-primary)}
.empty{color:var(--text-muted);text-align:center;padding:16px}
@media(max-width:900px){.project-overview{grid-template-columns:1fr}.two-cols{grid-template-columns:1fr}}

/* Estilos para subtareas */
.subtask-count-badge {
  display: inline-block;
  background: var(--primary-color);
  color: white;
  font-size: 0.7rem;
  padding: 2px 6px;
  border-radius: 10px;
  margin-left: 8px;
  font-weight: 500;
}

.btn-subtasks {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.btn-subtasks:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
}

.no-subtasks {
  color: var(--text-muted);
  font-style: italic;
}

/* Modal de subtareas */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--bg-primary);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-lg);
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal-large {
  max-width: 800px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--spacing-lg);
  border-bottom: 1px solid var(--bg-accent);
  background: var(--bg-tertiary);
}

.modal-header h3 {
  margin: 0;
  color: var(--text-primary);
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: var(--text-muted);
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
}

.modal-close:hover {
  background: var(--bg-accent);
  color: var(--text-primary);
}

.modal-body {
  padding: var(--spacing-lg);
  overflow-y: auto;
  flex: 1;
}

.modal-footer {
  padding: var(--spacing-md) var(--spacing-lg);
  border-top: 1px solid var(--bg-accent);
  display: flex;
  justify-content: flex-end;
  gap: var(--spacing-sm);
}

.task-info {
  margin-bottom: var(--spacing-lg);
  padding-bottom: var(--spacing-md);
  border-bottom: 1px solid var(--bg-accent);
}

.task-info h4 {
  margin: 0 0 var(--spacing-xs) 0;
  color: var(--text-primary);
}

.project-name {
  color: var(--text-muted);
  font-size: 0.9rem;
}

.loading {
  text-align: center;
  padding: var(--spacing-xl);
  color: var(--text-muted);
}

.loading i {
  margin-right: var(--spacing-sm);
}

.subtask-item {
  background: var(--bg-tertiary);
  border: 1px solid var(--bg-accent);
  border-radius: var(--radius-md);
  padding: var(--spacing-md);
  margin-bottom: var(--spacing-sm);
}

.subtask-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--spacing-xs);
}

.subtask-title {
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.subtask-status {
  font-size: 0.8rem;
  padding: 2px 6px;
  border-radius: 4px;
}

.subtask-meta {
  display: flex;
  gap: var(--spacing-md);
  font-size: 0.85rem;
  color: var(--text-muted);
}

.subtask-description {
  margin-top: var(--spacing-sm);
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.subtasks-empty {
  text-align: center;
  padding: var(--spacing-xl);
  color: var(--text-muted);
  font-style: italic;
}

.btn {
  padding: var(--spacing-sm) var(--spacing-md);
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: var(--bg-accent);
  color: var(--text-primary);
}

.btn-secondary:hover {
  background: var(--bg-tertiary);
}
</style>

<script>
// JavaScript para manejar el modal de subtareas
document.addEventListener('DOMContentLoaded', function() {
    
    // Event listeners para botones de subtareas
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-subtasks')) {
            const button = e.target.closest('.btn-subtasks');
            const taskId = button.dataset.taskId;
            if (taskId) {
                openSubtasksModal(taskId);
            }
        }
    });

    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('subtasksModal');
        if (e.target === modal) {
            closeSubtasksModal();
        }
    });

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSubtasksModal();
        }
    });
});

function openSubtasksModal(taskId) {
    const modal = document.getElementById('subtasksModal');
    const container = document.getElementById('subtasksContainer');
    const taskNameEl = document.getElementById('taskName');
    const projectNameEl = document.getElementById('projectName');
    
    // Mostrar modal con loading
    modal.style.display = 'flex';
    container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando subtareas...</div>';
    taskNameEl.textContent = 'Cargando...';
    projectNameEl.textContent = '';
    
    // Cargar subtareas por AJAX
    fetch(`?route=admin/get-task-subtasks&taskId=${encodeURIComponent(taskId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar información de la tarea
                taskNameEl.textContent = data.task.task_name;
                projectNameEl.textContent = `Proyecto: ${data.task.project_name}`;
                
                // Renderizar subtareas
                renderSubtasks(data.subtasks);
            } else {
                container.innerHTML = `<div class="subtasks-empty">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error al cargar subtareas:', error);
            container.innerHTML = '<div class="subtasks-empty">Error al cargar las subtareas. Inténtalo de nuevo.</div>';
        });
}

function renderSubtasks(subtasks) {
    const container = document.getElementById('subtasksContainer');
    
    if (!subtasks || subtasks.length === 0) {
        container.innerHTML = '<div class="subtasks-empty">Esta tarea no tiene subtareas.</div>';
        return;
    }
    
    let html = '';
    subtasks.forEach((subtask, index) => {
        const statusText = getStatusText(subtask.status);
        const dueDate = subtask.due_date ? formatDate(subtask.due_date) : 'Sin fecha';
        const assignedUser = subtask.assigned_to_fullname || subtask.assigned_to_username || 'Sin asignar';
        const progress = subtask.completion_percentage ? `${subtask.completion_percentage}%` : '0%';
        
        html += `
            <div class="subtask-item">
                <div class="subtask-header">
                    <h5 class="subtask-title">${escapeHtml(subtask.title)}</h5>
                    <span class="badge subtask-status status-${subtask.status}">${statusText}</span>
                </div>
                
                <div class="subtask-meta">
                    <span><i class="fas fa-user"></i> ${escapeHtml(assignedUser)}</span>
                    <span><i class="fas fa-calendar"></i> ${dueDate}</span>
                    <span><i class="fas fa-chart-pie"></i> ${progress}</span>
                    <span><i class="fas fa-flag"></i> ${getPriorityText(subtask.priority)}</span>
                </div>
                
                ${subtask.description ? `<div class="subtask-description">${escapeHtml(subtask.description)}</div>` : ''}
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function closeSubtasksModal() {
    const modal = document.getElementById('subtasksModal');
    modal.style.display = 'none';
}

function getStatusText(status) {
    const statusMap = {
        'pending': 'Pendiente',
        'in_progress': 'En progreso',
        'completed': 'Completada',
        'cancelled': 'Cancelada'
    };
    return statusMap[status] || status;
}

function getPriorityText(priority) {
    const priorityMap = {
        'low': 'Baja',
        'medium': 'Media',
        'high': 'Alta',
        'urgent': 'Urgente'
    };
    return priorityMap[priority] || priority;
}

function formatDate(dateString) {
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>





<?php
$content = ob_get_clean();
$title = 'Detalle de Proyecto - ' . APP_NAME;
include __DIR__ . '/layout.php';
?>


