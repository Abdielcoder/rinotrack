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
          <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:15px;">
            <h4 style="margin:0">Tareas</h4>
          </div>
          
          <!-- Filtros -->
          <div class="filters" style="display:flex;gap:10px;align-items:center;margin:12px 0;flex-wrap:wrap">
            <input type="text" id="taskSearch" class="search-input" placeholder="Buscar tarea..." value="" style="min-width:200px;padding:8px 12px;border:1px solid var(--bg-accent);border-radius:6px;background:var(--bg-primary);">
            <select id="statusFilter" class="filter-select" style="padding:8px 12px;border:1px solid var(--bg-accent);border-radius:6px;background:var(--bg-primary);">
              <option value="">Todos los estados</option>
              <option value="pending">Pendientes</option>
              <option value="in_progress">En progreso</option>
              <option value="completed">Completadas</option>
              <option value="cancelled">Canceladas</option>
            </select>
            <select id="assignedFilter" class="filter-select" style="padding:8px 12px;border:1px solid var(--bg-accent);border-radius:6px;background:var(--bg-primary);">
              <option value="">Asignado: todos</option>
              <?php if (!empty($clanMembers)): ?>
                <?php foreach ($clanMembers as $m): ?>
                  <option value="<?php echo (int)$m['user_id']; ?>"><?php echo Utils::escape($m['full_name'] ?: $m['username']); ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
            <button id="applyFilters" class="btn btn-secondary" style="padding:8px 16px;">Aplicar</button>
            <button id="resetFilters" class="btn btn-secondary" style="padding:8px 16px;">Reset</button>
          </div>
          
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
                    <a href="javascript:void(0)" onclick="openTaskDetailsModal(<?php echo intval($t['task_id']); ?>)" 
                       style="color: #1e3a8a; text-decoration: none; font-weight: 500; cursor: pointer;"
                       onmouseover="this.style.textDecoration='underline'" 
                       onmouseout="this.style.textDecoration='none'">
                      <?php echo Utils::escape($t['task_name']); ?>
                    </a>
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
                      <button class="btn-add-subtasks" data-task-id="<?php echo intval($t['task_id']); ?>" data-task-name="<?php echo htmlspecialchars($t['task_name']); ?>" title="Añadir subtareas">
                        <i class="fas fa-plus"></i> Añadir Subtareas
                      </button>
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

<!-- Modal para añadir subtareas -->
<div id="addSubtasksModal" class="modal" style="display: none;">
  <div class="modal-content modal-large">
    <div class="modal-header">
      <h3 id="addSubtasksModalTitle">
        <i class="fas fa-tasks" style="margin-right: 8px; color: #3b82f6;"></i>
        Añadir Subtareas
      </h3>
      <button class="modal-close" onclick="closeAddSubtasksModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <form id="addSubtasksForm">
      <div class="modal-body">
        <div class="task-info">
          <h4 id="addSubtasksTaskName">Tarea: Cargando...</h4>
          <input type="hidden" id="addSubtasksTaskId" name="taskId">
        </div>
        
        <div class="subtasks-container">
          <div class="subtasks-header">
            <span>Organiza esta tarea en pasos más pequeños</span>
            <button type="button" class="btn btn-small btn-add-more" id="addSubtaskBtnModal">
              <i class="fas fa-plus"></i> Agregar Subtarea
            </button>
          </div>
          <div id="addSubtasksList" class="subtasks-list">
            <!-- Las subtareas se agregarán aquí dinámicamente -->
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeAddSubtasksModal()">
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary" id="saveSubtasksBtn">
          <span id="saveSubtasksText">Guardar Subtareas</span>
          <span id="saveSubtasksLoader" class="btn-loader" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i>
          </span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal de Detalles Completos de Tarea -->
<div id="taskDetailsModal" class="modal" style="display: none;">
  <div class="modal-content modal-task-details">
    <div class="modal-header">
      <h3 id="taskDetailsModalTitle">
        <i class="fas fa-tasks" style="margin-right: 8px; color: #3b82f6;"></i>
        Detalles de Tarea
      </h3>
      <button class="modal-close" onclick="closeTaskDetailsModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <div class="modal-body" id="taskDetailsContent">
      <div class="loading">
        <i class="fas fa-spinner fa-spin"></i>
        Cargando detalles de la tarea...
      </div>
    </div>
    
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeTaskDetailsModal()">
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
  background: #1e3a8a;
  color: white;
  font-size: 0.7rem;
  padding: 2px 6px;
  border-radius: 10px;
  margin-left: 8px;
  font-weight: 500;
}

.btn-subtasks {
  background: #1e3a8a;
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
  background: #1e40af;
  transform: translateY(-1px);
}

.btn-add-subtasks {
  background: #059669;
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

.btn-add-subtasks:hover {
  background: #047857;
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

/* Estilos mejorados para el modal de añadir subtareas */
.btn-loader {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.btn-small {
  padding: 6px 12px;
  font-size: 0.85rem;
  border-radius: 6px;
  font-weight: 500;
}

/* Mejorar el contenedor de subtareas en el modal */
#addSubtasksModal .subtasks-container {
  background: var(--bg-primary);
  border: 1px solid var(--bg-accent);
  border-radius: 12px;
  padding: 20px;
  margin-top: 16px;
}

#addSubtasksModal .subtasks-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid var(--bg-accent);
}

#addSubtasksModal .subtasks-header span {
  color: var(--text-secondary);
  font-size: 0.95rem;
  font-weight: 500;
}

#addSubtasksModal .subtasks-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-height: 60px;
}

/* Estilos mejorados para items de subtarea */
#addSubtasksModal .subtask-item {
  background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-primary) 100%);
  border: 2px solid var(--bg-accent);
  border-radius: 12px;
  padding: 16px;
  position: relative;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

#addSubtasksModal .subtask-item:hover {
  border-color: #1e3a8a;
  box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);
  transform: translateY(-2px);
}

#addSubtasksModal .subtask-counter {
  position: absolute;
  top: -8px;
  left: 12px;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  color: white;
  font-size: 0.75rem;
  padding: 4px 8px;
  border-radius: 12px;
  font-weight: 600;
  box-shadow: 0 2px 4px rgba(30, 58, 138, 0.3);
}

#addSubtasksModal .subtask-drag-handle {
  cursor: grab;
  color: var(--text-muted);
  font-size: 1.2rem;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s ease;
}

#addSubtasksModal .subtask-drag-handle:hover {
  color: #1e3a8a;
}

#addSubtasksModal .subtask-drag-handle:active {
  cursor: grabbing;
}

#addSubtasksModal .subtask-input {
  flex: 1;
  border: 2px solid transparent;
  background: var(--bg-primary);
  color: var(--text-primary);
  font-size: 0.95rem;
  padding: 12px 16px;
  border-radius: 8px;
  transition: all 0.2s ease;
  font-weight: 500;
}

#addSubtasksModal .subtask-input:focus {
  outline: none;
  border-color: #1e3a8a;
  background: white;
  box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

#addSubtasksModal .subtask-input::placeholder {
  color: var(--text-muted);
  font-style: italic;
}

#addSubtasksModal .subtask-remove {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border: none;
  color: white;
  cursor: pointer;
  padding: 8px 10px;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: all 0.2s ease;
  opacity: 0.8;
  box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
}

#addSubtasksModal .subtask-remove:hover {
  opacity: 1;
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(239, 68, 68, 0.4);
}

/* Mejorar el estado vacío */
#addSubtasksModal .subtasks-empty {
  text-align: center;
  padding: 40px 20px;
  color: var(--text-muted);
  font-style: italic;
  background: var(--bg-tertiary);
  border: 2px dashed var(--bg-accent);
  border-radius: 12px;
  font-size: 0.95rem;
}

/* Mejorar botones del modal */
#addSubtasksModal .btn-primary {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  border: none;
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

#addSubtasksModal .btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(30, 58, 138, 0.4);
}

#addSubtasksModal .btn-primary:disabled {
  opacity: 0.7;
  transform: none;
  cursor: not-allowed;
}

#addSubtasksModal .btn-secondary {
  background: var(--bg-accent);
  border: 2px solid var(--bg-accent);
  color: var(--text-primary);
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s ease;
}

#addSubtasksModal .btn-secondary:hover {
  background: var(--bg-tertiary);
  border-color: var(--bg-tertiary);
  transform: translateY(-1px);
}

/* Mejorar el header del modal */
#addSubtasksModal .modal-header {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-bottom: 2px solid var(--bg-accent);
  padding: 20px 24px;
}

#addSubtasksModal .modal-header h3 {
  color: #1e3a8a;
  font-weight: 700;
  font-size: 1.25rem;
  margin: 0;
}

#addSubtasksModal .task-info {
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  border: 1px solid #bfdbfe;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;
}

#addSubtasksModal .task-info h4 {
  color: #1e3a8a;
  font-weight: 600;
  margin: 0;
  font-size: 1.1rem;
}

/* Animaciones suaves */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

#addSubtasksModal .modal-content {
  animation: modalFadeIn 0.3s ease;
}

#addSubtasksModal .subtask-item {
  animation: slideInUp 0.3s ease;
}

/* Botón especial para agregar más subtareas */
.btn-add-more {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  border: none;
  color: white;
  font-weight: 600;
  transition: all 0.2s ease;
  box-shadow: 0 3px 8px rgba(5, 150, 105, 0.3);
}

.btn-add-more:hover {
  background: linear-gradient(135deg, #047857 0%, #059669 100%);
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(5, 150, 105, 0.4);
}

.btn-add-more i {
  transition: transform 0.2s ease;
}

.btn-add-more:hover i {
  transform: rotate(90deg);
}

/* Estilos para formulario unificado */
.task-interactions-header {
  border-bottom: 2px solid var(--bg-accent);
  margin-bottom: 20px;
  padding-bottom: 12px;
}

.task-interactions-header h3 {
  margin: 0;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 1.1rem;
}

.task-interactions-header i {
  color: var(--primary);
}

/* Estilos para formularios */
.comment-form, .attachment-form, .unified-comment-form {
  background: var(--bg-tertiary);
  border: 1px solid var(--bg-accent);
  border-radius: 12px;
  padding: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: var(--text-primary);
  font-weight: 600;
}

.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--bg-accent);
  border-radius: 8px;
  background: var(--bg-primary);
  color: var(--text-primary);
  font-family: inherit;
  resize: vertical;
  transition: border-color 0.3s ease;
}

.form-group textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.file-input-wrapper {
  position: relative;
}

.file-input-wrapper input[type="file"] {
  width: 100%;
  padding: 12px;
  border: 2px dashed var(--bg-accent);
  border-radius: 8px;
  background: var(--bg-primary);
  color: var(--text-primary);
  transition: border-color 0.3s ease;
  cursor: pointer;
}

.file-input-wrapper input[type="file"]:hover {
  border-color: var(--primary);
  background: var(--bg-tertiary);
}

.file-input-info {
  font-size: 0.85rem;
  color: var(--text-muted);
  margin-top: 8px;
  padding: 8px;
  background: var(--bg-accent);
  border-radius: 6px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}

.form-actions .btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.form-actions .btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Estilos para sección de adjuntos en formulario unificado */
.attachment-section {
  background: var(--bg-primary);
  border: 1px solid var(--bg-accent);
  border-radius: 8px;
  padding: 16px;
  margin-top: 12px;
}

.attachment-section label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 0;
}

.attachment-section input[type="checkbox"] {
  margin: 0;
  transform: scale(1.1);
}

.file-input-container {
  margin-top: 12px;
  padding: 12px;
  background: var(--bg-tertiary);
  border: 1px dashed var(--bg-accent);
  border-radius: 6px;
  transition: all 0.3s ease;
}

.file-input-container:hover {
  border-color: var(--primary);
  background: var(--bg-primary);
}

.file-input-container input[type="file"] {
  width: 100%;
  padding: 8px;
  border: none;
  background: none;
  color: var(--text-primary);
  font-size: 0.9rem;
}

/* Estilos para notificaciones */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: var(--bg-primary);
  border: 1px solid var(--bg-accent);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 400px;
  box-shadow: var(--shadow-lg);
  z-index: 9999;
  animation: slideInRight 0.3s ease;
}

.notification-success {
  border-left: 4px solid #10b981;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);
}

.notification-error {
  border-left: 4px solid #ef4444;
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
}

.notification-info {
  border-left: 4px solid #3b82f6;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
}

.notification-content {
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--text-primary);
}

.notification-success .notification-content i {
  color: #10b981;
}

.notification-error .notification-content i {
  color: #ef4444;
}

.notification-info .notification-content i {
  color: #3b82f6;
}

.notification-close {
  background: none;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.notification-close:hover {
  background: var(--bg-accent);
  color: var(--text-primary);
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Estilos para comentarios y respuestas */
.comment-item-detail {
  background: var(--bg-tertiary);
  border: 1px solid var(--bg-accent);
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
  transition: all 0.3s ease;
}

.comment-item-detail:hover {
  border-color: var(--primary);
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
}

.comment-header-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid var(--bg-accent);
}

.comment-author-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.comment-author-detail {
  font-weight: 600;
  color: var(--text-primary);
}

.comment-attachment-icon {
  color: var(--primary);
  font-size: 0.85rem;
  opacity: 0.8;
}

.comment-date-detail {
  color: var(--text-muted);
  font-size: 0.85rem;
}

.comment-actions {
  display: flex;
  gap: 8px;
}

.btn-reply {
  background: none;
  border: 1px solid var(--bg-accent);
  color: var(--text-muted);
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 4px;
}

.btn-reply:hover {
  background: var(--primary);
  border-color: var(--primary);
  color: white;
  transform: translateY(-1px);
}

.comment-content {
  color: var(--text-primary);
  line-height: 1.5;
  margin-bottom: 12px;
}

.comment-attachments {
  margin-top: 12px;
  padding: 12px;
  background: var(--bg-primary);
  border: 1px solid var(--bg-accent);
  border-radius: 8px;
}

.attachments-header {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-muted);
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.comment-attachments .attachment-item {
  padding: 6px 0;
  border-bottom: 1px solid var(--bg-accent);
  font-size: 0.9rem;
}

.comment-attachments .attachment-item:last-child {
  border-bottom: none;
}

.comment-attachments .attachment-link {
  color: var(--primary);
  text-decoration: none;
  margin-left: 6px;
}

.comment-attachments .attachment-link:hover {
  text-decoration: underline;
}

/* Estilos para respuestas anidadas */
.comment-replies {
  margin-top: 16px;
  padding-left: 20px;
  border-left: 3px solid var(--bg-accent);
}

.subtask-replies {
  padding-left: 30px;
  border-left: 3px solid #10b981;
}

/* Estilos para comentarios cronológicos */
.chronological-comments {
  position: relative;
}

.chronological-comment-item {
  display: flex;
  margin-bottom: 24px;
  position: relative;
}

.comment-timeline-marker {
  position: relative;
  flex-shrink: 0;
  width: 24px;
  margin-right: 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.timeline-dot {
  width: 12px;
  height: 12px;
  background: #10b981;
  border: 3px solid var(--bg-primary);
  border-radius: 50%;
  box-shadow: 0 0 0 2px #10b981;
  z-index: 2;
}

.timeline-line {
  width: 2px;
  flex: 1;
  background: linear-gradient(to bottom, #10b981, #d1fae5);
  margin-top: 8px;
  min-height: 40px;
}

.comment-content-wrapper {
  flex: 1;
  position: relative;
}

.subtask-context {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 8px;
  padding: 4px 8px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 6px;
  font-size: 0.85rem;
  color: #059669;
  font-weight: 500;
}

.subtask-context i {
  color: #10b981;
}

.subtask-name {
  color: #047857;
  font-weight: 600;
}

.chronological-comment {
  background: var(--bg-primary);
  border: 1px solid var(--bg-accent);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.chronological-comment:hover {
  border-color: #10b981;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
  transform: translateY(-1px);
}

.reply-item {
  background: var(--bg-primary);
  border: 1px solid var(--bg-accent);
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 12px;
  position: relative;
}

.reply-item::before {
  content: '';
  position: absolute;
  left: -23px;
  top: 16px;
  width: 20px;
  height: 2px;
  background: var(--bg-accent);
}

.reply-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 0.9rem;
}

.reply-author {
  font-weight: 600;
  color: var(--primary);
}

.reply-date {
  color: var(--text-muted);
  font-size: 0.8rem;
}

.reply-content {
  color: var(--text-primary);
  font-size: 0.9rem;
  line-height: 1.4;
}

.reply-attachment {
  margin-top: 8px;
  padding: 8px;
  background: var(--bg-tertiary);
  border-radius: 6px;
  font-size: 0.85rem;
}

.reply-attachment a {
  color: var(--primary);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 6px;
}

.reply-attachment a:hover {
  text-decoration: underline;
}

/* Estilos para formulario de respuesta */
.reply-form-container {
  margin-top: 16px;
  padding: 16px;
  background: var(--bg-primary);
  border: 1px solid var(--bg-accent);
  border-radius: 8px;
  animation: slideDown 0.3s ease;
}

.reply-form .form-group {
  margin-bottom: 16px;
}

.reply-form label {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 6px;
  display: block;
}

.reply-form textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid var(--bg-accent);
  border-radius: 6px;
  background: var(--bg-tertiary);
  color: var(--text-primary);
  font-family: inherit;
  font-size: 0.9rem;
  resize: vertical;
  transition: border-color 0.3s ease;
}

.reply-form textarea:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.reply-attachments-section {
  margin-bottom: 16px;
}

.attachment-toggle {
  margin-bottom: 8px;
}

.attachment-toggle label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-size: 0.85rem;
  color: var(--text-muted);
}

.attachment-toggle input[type="checkbox"] {
  margin: 0;
}

.reply-attachment-input {
  padding: 12px;
  background: var(--bg-tertiary);
  border: 1px dashed var(--bg-accent);
  border-radius: 6px;
  transition: all 0.3s ease;
}

.reply-attachment-input:hover {
  border-color: var(--primary);
  background: var(--bg-primary);
}

.reply-attachment-input input[type="file"] {
  width: 100%;
  padding: 8px;
  border: none;
  background: none;
  color: var(--text-primary);
  font-size: 0.85rem;
}

.reply-form .form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 16px;
}

.reply-form .btn {
  padding: 8px 16px;
  font-size: 0.9rem;
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.2s ease;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Estilos para modal de detalles de tarea */
.modal-task-details {
  max-width: 1200px;
  max-height: 90vh;
}

.task-details-header {
  background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
  border: 1px solid #c7d2fe;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.task-details-info h2 {
  margin: 0 0 5px 0;
  color: #1e3a8a;
  font-size: 1.5rem;
  font-weight: 700;
}

.task-details-info .project-info {
  color: #6b7280;
  font-size: 0.9rem;
}

.task-status-badge {
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
}

.task-details-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
  margin-top: 20px;
}

.task-section {
  background: var(--bg-primary);
  border: 1px solid var(--bg-accent);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
}

.task-section h3 {
  margin: 0 0 15px 0;
  color: var(--text-primary);
  font-size: 1.1rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

.subtask-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.subtask-item-detail {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
  transition: all 0.2s ease;
}

.subtask-item-detail:hover {
  border-color: #1e3a8a;
  box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
}

.subtask-header-detail {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.subtask-title-detail {
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.subtask-meta-detail {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 0.85rem;
  color: #6b7280;
  margin: 8px 0;
}

.comment-item-detail {
  background: #f9fafb;
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 12px;
}

.comment-header-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.comment-author-detail {
  font-weight: 600;
  color: #374151;
}

.comment-date-detail {
  font-size: 0.8rem;
  color: #9ca3af;
}

.attachment-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.attachment-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: #f3f4f6;
  border-radius: 6px;
  font-size: 0.9rem;
}

.attachment-icon {
  color: #6b7280;
}

.attachment-link {
  color: #1e3a8a;
  text-decoration: none;
}

.attachment-link:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  #addSubtasksModal .modal-content {
    width: 95%;
    margin: 10px;
  }
  
  #addSubtasksModal .subtasks-header {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }
  
  #addSubtasksModal .subtask-item {
    padding: 12px;
  }
  
  #addSubtasksModal .modal-footer {
    flex-direction: column;
    gap: 8px;
  }
  
  #addSubtasksModal .modal-footer button {
    width: 100%;
  }
  
  .task-details-grid {
    grid-template-columns: 1fr;
  }
  
  .task-details-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
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
        
        // Event listener para botón "Añadir Subtareas"
        if (e.target.closest('.btn-add-subtasks')) {
            const button = e.target.closest('.btn-add-subtasks');
            const taskId = button.dataset.taskId;
            const taskName = button.dataset.taskName;
            if (taskId) {
                openAddSubtasksModal(taskId, taskName);
            }
        }
        
        // Event listener para añadir subtarea en modal
        if (e.target.id === 'addSubtaskBtnModal' || e.target.closest('#addSubtaskBtnModal')) {
            e.preventDefault();
            addSubtaskToForm();
        }
    });

    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('subtasksModal');
        const addModal = document.getElementById('addSubtasksModal');
        
        if (e.target === modal) {
            closeSubtasksModal();
        }
        
        if (e.target === addModal) {
            closeAddSubtasksModal();
        }
        
        const taskModal = document.getElementById('taskDetailsModal');
        if (e.target === taskModal) {
            closeTaskDetailsModal();
        }
    });

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSubtasksModal();
            closeAddSubtasksModal();
            closeTaskDetailsModal();
        }
    });

    // Manejar envío del formulario de añadir subtareas
    const addSubtasksForm = document.getElementById('addSubtasksForm');
    if (addSubtasksForm) {
        addSubtasksForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const saveBtn = document.getElementById('saveSubtasksBtn');
            const saveText = document.getElementById('saveSubtasksText');
            const saveLoader = document.getElementById('saveSubtasksLoader');
            
            // Mostrar loader
            if (saveBtn) saveBtn.disabled = true;
            if (saveText) saveText.style.display = 'none';
            if (saveLoader) saveLoader.style.display = 'inline-block';
            
            // Preparar datos del formulario
            const formData = new FormData(addSubtasksForm);
            
            // Enviar petición AJAX
            fetch('?route=admin/add-subtasks-to-task', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddSubtasksModal();
                    // Recargar la página para mostrar los cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert(data.message || 'Error al añadir subtareas');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al añadir subtareas');
            })
            .finally(() => {
                // Ocultar loader
                if (saveBtn) saveBtn.disabled = false;
                if (saveText) saveText.style.display = 'inline';
                if (saveLoader) saveLoader.style.display = 'none';
            });
        });
    }
    
    // Event listeners para filtros
    const applyFiltersBtn = document.getElementById('applyFilters');
    const resetFiltersBtn = document.getElementById('resetFilters');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyTaskFilters();
        });
    }
    
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            resetTaskFilters();
        });
    }
    
    // Aplicar filtros al presionar Enter en el campo de búsqueda
    const searchInput = document.getElementById('taskSearch');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyTaskFilters();
            }
        });
    }
});

function applyTaskFilters() {
    const search = document.getElementById('taskSearch')?.value.toLowerCase() || '';
    const status = document.getElementById('statusFilter')?.value || '';
    const assigned = document.getElementById('assignedFilter')?.value || '';
    
    const tableBody = document.querySelector('.data-table tbody');
    if (!tableBody) return;
    
    const rows = tableBody.querySelectorAll('tr:not(.empty)');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const taskName = row.cells[0]?.textContent.toLowerCase() || '';
        const assignedUser = row.cells[1]?.textContent || '';
        const taskStatus = row.querySelector('.badge')?.textContent.trim() || '';
        
        // Mapear estados para comparación
        const statusMap = {
            'pending': 'Pendientes',
            'in_progress': 'En progreso',
            'completed': 'Completadas',
            'cancelled': 'Canceladas'
        };
        
        let statusMatch = !status || taskStatus === statusMap[status] || taskStatus.toLowerCase() === status;
        let searchMatch = !search || taskName.includes(search);
        let assignedMatch = !assigned || assignedUser.includes(assigned);
        
        if (searchMatch && statusMatch && assignedMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const emptyRow = tableBody.querySelector('.empty');
    if (visibleCount === 0 && !emptyRow) {
        const newRow = document.createElement('tr');
        newRow.className = 'empty-filter';
        newRow.innerHTML = '<td colspan="5" class="empty">No se encontraron tareas que coincidan con los filtros</td>';
        tableBody.appendChild(newRow);
    } else if (visibleCount > 0) {
        const emptyFilterRow = tableBody.querySelector('.empty-filter');
        if (emptyFilterRow) {
            emptyFilterRow.remove();
        }
    }
}

function resetTaskFilters() {
    document.getElementById('taskSearch').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('assignedFilter').value = '';
    
    const tableBody = document.querySelector('.data-table tbody');
    if (tableBody) {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            if (!row.classList.contains('empty-filter')) {
                row.style.display = '';
            } else {
                row.remove();
            }
        });
    }
}

function openTaskDetailsModal(taskId) {
    const modal = document.getElementById('taskDetailsModal');
    const content = document.getElementById('taskDetailsContent');
    
    // Mostrar modal con loading
    modal.style.display = 'flex';
    content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando detalles de la tarea...</div>';
    
    // Cargar detalles de la tarea por AJAX
    fetch(`?route=admin/get-task-details&taskId=${encodeURIComponent(taskId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTaskDetails(data);
            } else {
                content.innerHTML = `<div class="task-section">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error al cargar detalles de la tarea:', error);
            content.innerHTML = '<div class="task-section">Error al cargar los detalles de la tarea. Inténtalo de nuevo.</div>';
        });
}

function closeTaskDetailsModal() {
    const modal = document.getElementById('taskDetailsModal');
    modal.style.display = 'none';
}

function renderTaskDetails(data) {
    const content = document.getElementById('taskDetailsContent');
    const task = data.task;
    const subtasks = data.subtasks || [];
    const comments = data.comments || [];
    const attachments = data.attachments || [];
    
    const statusColors = {
        'pending': '#fbbf24',
        'in_progress': '#3b82f6',
        'completed': '#10b981',
        'cancelled': '#ef4444',
        'blocked': '#f97316'
    };
    
    const statusNames = {
        'pending': 'PENDIENTE',
        'in_progress': 'EN PROGRESO',
        'completed': 'COMPLETADA',
        'cancelled': 'CANCELADA',
        'blocked': 'BLOQUEADA'
    };
    
    let html = `
        <div class="task-details-header">
            <div class="task-details-info">
                <h2>${escapeHtml(task.task_name)}</h2>
                <div class="project-info">Proyecto: ${escapeHtml(task.project_name || 'N/A')}</div>
            </div>
            <div>
                <span class="task-status-badge" style="background: ${statusColors[task.status] || '#6b7280'}; color: white;">
                    ${statusNames[task.status] || task.status.toUpperCase()}
                </span>
            </div>
        </div>
        
        <div class="task-details-grid">
            <div class="main-details">
    `;
    
    // Descripción
    if (task.description) {
        html += `
            <div class="task-section">
                <h3><i class="fas fa-align-left"></i> Descripción</h3>
                <div>${escapeHtml(task.description).replace(/\n/g, '<br>')}</div>
            </div>
        `;
    }
    
    // Subtareas
    if (subtasks.length > 0) {
        html += `
            <div class="task-section">
                <h3><i class="fas fa-tasks"></i> Subtareas (${subtasks.length})</h3>
                <div class="subtask-list">
        `;
        
        subtasks.forEach(subtask => {
            const subtaskComments = subtask.comments || [];
            const subtaskAttachments = subtask.attachments || [];
            
            html += `
                <div class="subtask-item-detail">
                    <div class="subtask-header-detail">
                        <h4 class="subtask-title-detail">${escapeHtml(subtask.title)}</h4>
                        <span class="badge status-${subtask.status}">${getStatusText(subtask.status)}</span>
                    </div>
                    <div class="subtask-meta-detail">
                        <span><i class="fas fa-user"></i> ${escapeHtml(subtask.assigned_to_fullname || subtask.assigned_to_username || 'Sin asignar')}</span>
                        <span><i class="fas fa-calendar"></i> ${subtask.due_date ? formatDate(subtask.due_date) : 'Sin fecha'}</span>
                        <span><i class="fas fa-chart-pie"></i> ${subtask.completion_percentage || 0}%</span>
                        <span><i class="fas fa-flag"></i> ${getPriorityText(subtask.priority)}</span>
                        <span><i class="fas fa-comments"></i> ${subtaskComments.length} comentarios</span>
                        <span><i class="fas fa-paperclip"></i> ${subtaskAttachments.length} adjuntos</span>
                    </div>
                    ${subtask.description ? `<div class="subtask-description">${escapeHtml(subtask.description)}</div>` : ''}
                    
                    ${subtaskAttachments.length > 0 ? `
                        <div class="subtask-attachments" style="margin-top: 12px;">
                            <h5 style="margin: 0 0 8px 0; font-size: 0.9rem; color: #374151;">Adjuntos:</h5>
                            <div class="attachment-list">
                                ${subtaskAttachments.map(attachment => `
                                    <div class="attachment-item" style="font-size: 0.85rem;">
                                        <i class="fas fa-file attachment-icon"></i>
                                        <a href="${attachment.file_path}" target="_blank" class="attachment-link">
                                            ${escapeHtml(attachment.filename)}
                                        </a>
                                        <span style="color: #9ca3af; margin-left: 8px;">por ${escapeHtml(attachment.full_name || attachment.username || 'Usuario')}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    // Comentarios (separar tarea principal y subtareas)
    const taskComments = data.taskComments || [];
    const allComments = data.comments || [];
    const totalComments = allComments.length;
    
    html += `
        <div class="task-section">
            <h3><i class="fas fa-comments"></i> Todos los Comentarios (${totalComments})</h3>
    `;
    
    if (totalComments === 0) {
        html += '<div style="color: #6b7280; font-style: italic; text-align: center; padding: 20px;">Sin comentarios</div>';
    } else {
        // Comentarios de la tarea principal
        if (taskComments.length > 0) {
            html += `
                <div style="margin-bottom: 20px;">
                    <h4 style="margin: 0 0 12px 0; color: #1f2937; font-size: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px;">
                        <i class="fas fa-task" style="color: #3b82f6;"></i> Comentarios de la Tarea Principal (${taskComments.length})
                    </h4>
            `;
            taskComments.forEach(comment => {
                // Verificar si el comentario tiene adjuntos
                const hasAttachments = data.taskAttachments && data.taskAttachments.some(att => att.comment_id == comment.comment_id);
                
                html += `
                    <div class="comment-item-detail" data-comment-id="${comment.comment_id}">
                        <div class="comment-header-detail">
                            <div class="comment-author-info">
                                <span class="comment-author-detail">${escapeHtml(comment.full_name || comment.username || 'Usuario')}</span>
                                ${hasAttachments ? '<i class="fas fa-paperclip comment-attachment-icon" title="Tiene archivos adjuntos"></i>' : ''}
                            </div>
                            <span class="comment-date-detail">${formatDate(comment.created_at)}</span>
                            <div class="comment-actions">
                                <button class="btn-reply" onclick="showReplyForm(${comment.comment_id}, ${task.task_id})" title="Responder">
                                    <i class="fas fa-reply"></i> Responder
                                </button>
                            </div>
                        </div>
                        <div class="comment-content">${comment.comment_text || ''}</div>
                        
                        ${hasAttachments ? `
                            <div class="comment-attachments">
                                <div class="attachments-header">
                                    <i class="fas fa-paperclip"></i> Archivos adjuntos:
                                </div>
                                ${data.taskAttachments.filter(att => att.comment_id == comment.comment_id).map(attachment => `
                                    <div class="attachment-item">
                                        <i class="fas fa-file attachment-icon"></i>
                                        <a href="${attachment.file_path}" target="_blank" class="attachment-link">
                                            ${escapeHtml(attachment.file_name)}
                                        </a>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                        
                        <!-- Área para mostrar respuestas -->
                        <div class="comment-replies" id="replies-${comment.comment_id}">
                            <!-- Las respuestas se cargarán aquí -->
                        </div>
                        
                        <!-- Formulario de respuesta unificado (inicialmente oculto) -->
                        <div class="reply-form-container" id="reply-form-${comment.comment_id}" style="display: none;">
                            <form class="reply-form" onsubmit="submitReply(event, ${comment.comment_id}, ${task.task_id})" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Responder a ${escapeHtml(comment.full_name || comment.username || 'Usuario')}:</label>
                                    <textarea name="reply_text" rows="3" placeholder="Escribe tu respuesta..." required></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="attachment-section">
                                        <label>
                                            <input type="checkbox" id="reply-attachment-check-${comment.comment_id}" onchange="toggleReplyAttachment(${comment.comment_id})"> 
                                            Adjuntar archivo (opcional)
                                        </label>
                                        <div class="file-input-container" id="reply-attachment-${comment.comment_id}" style="display: none;">
                                            <input type="file" name="reply_attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar">
                                            <div class="file-input-info">Opcional: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG, GIF, ZIP, RAR (máx. 10MB)</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary" onclick="hideReplyForm(${comment.comment_id})">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Enviar Respuesta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        // Comentarios de subtareas reorganizados cronológicamente
        const subtaskCommentsGrouped = data.subtaskComments || {};
        const hasSubtaskComments = Object.keys(subtaskCommentsGrouped).length > 0;
        
        if (hasSubtaskComments) {
            // Crear lista plana de todos los comentarios con información de subtarea
            let allSubtaskComments = [];
            Object.entries(subtaskCommentsGrouped).forEach(([subtaskId, comments]) => {
                comments.forEach(comment => {
                    allSubtaskComments.push({
                        ...comment,
                        subtask_id: subtaskId,
                        subtask_title: comment.subtask_title || `Subtarea ${subtaskId}`
                    });
                });
            });
            
            // Ordenar todos los comentarios por fecha
            allSubtaskComments.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            
            html += `
                <div>
                    <h4 style="margin: 0 0 16px 0; color: #1f2937; font-size: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px;">
                        <i class="fas fa-tasks" style="color: #10b981;"></i> Comentarios de Subtareas (${allSubtaskComments.length} total)
                    </h4>
                    <div class="chronological-comments">
            `;
            
            allSubtaskComments.forEach((comment, index) => {
                // Verificar si el comentario de subtarea tiene adjuntos
                const hasAttachments = data.subtaskAttachments && data.subtaskAttachments[comment.subtask_id] && 
                                     data.subtaskAttachments[comment.subtask_id].some(att => att.comment_id == comment.comment_id);
                
                html += `
                    <div class="chronological-comment-item" data-comment-id="${comment.comment_id}" data-subtask-id="${comment.subtask_id}">
                        <div class="comment-timeline-marker">
                            <div class="timeline-dot"></div>
                            ${index < allSubtaskComments.length - 1 ? '<div class="timeline-line"></div>' : ''}
                        </div>
                        <div class="comment-content-wrapper">
                            <div class="subtask-context">
                                <i class="fas fa-layer-group"></i>
                                <span class="subtask-name">${escapeHtml(comment.subtask_title)}</span>
                            </div>
                            <div class="comment-item-detail chronological-comment" style="margin-bottom: 0;">
                                <div class="comment-header-detail">
                                    <div class="comment-author-info">
                                        <span class="comment-author-detail">${escapeHtml(comment.full_name || comment.username || 'Usuario')}</span>
                                        ${hasAttachments ? '<i class="fas fa-paperclip comment-attachment-icon" title="Tiene archivos adjuntos"></i>' : ''}
                                    </div>
                                    <span class="comment-date-detail">${formatDate(comment.created_at)}</span>
                                    <div class="comment-actions">
                                        <button class="btn-reply" onclick="showSubtaskReplyForm(${comment.comment_id}, ${comment.subtask_id}, ${task.task_id})" title="Responder">
                                            <i class="fas fa-reply"></i> Responder
                                        </button>
                                    </div>
                                </div>
                                <div class="comment-content">${comment.comment_text || ''}</div>
                                
                                ${hasAttachments ? `
                                    <div class="comment-attachments">
                                        <div class="attachments-header">
                                            <i class="fas fa-paperclip"></i> Archivos adjuntos:
                                        </div>
                                        ${data.subtaskAttachments[comment.subtask_id].filter(att => att.comment_id == comment.comment_id).map(attachment => `
                                            <div class="attachment-item">
                                                <i class="fas fa-file attachment-icon"></i>
                                                <a href="${attachment.file_path}" target="_blank" class="attachment-link">
                                                    ${escapeHtml(attachment.filename)}
                                                </a>
                                            </div>
                                        `).join('')}
                                    </div>
                                ` : ''}
                                
                                <!-- Área para mostrar respuestas de subtarea -->
                                <div class="comment-replies subtask-replies" id="subtask-replies-${comment.comment_id}">
                                    <!-- Las respuestas se cargarán aquí -->
                                </div>
                                
                                <!-- Formulario de respuesta para subtarea (inicialmente oculto) -->
                                <div class="reply-form-container" id="subtask-reply-form-${comment.comment_id}" style="display: none;">
                                    <form class="reply-form" onsubmit="submitSubtaskReply(event, ${comment.comment_id}, ${comment.subtask_id}, ${task.task_id})" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Responder a ${escapeHtml(comment.full_name || comment.username || 'Usuario')}:</label>
                                            <textarea name="reply_text" rows="3" placeholder="Escribe tu respuesta..." required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <div class="attachment-section">
                                                <label>
                                                    <input type="checkbox" id="subtask-reply-attachment-check-${comment.comment_id}" onchange="toggleSubtaskReplyAttachment(${comment.comment_id})"> 
                                                    Adjuntar archivo (opcional)
                                                </label>
                                                <div class="file-input-container" id="subtask-reply-attachment-${comment.comment_id}" style="display: none;">
                                                    <input type="file" name="reply_attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar">
                                                    <div class="file-input-info">Opcional: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG, GIF, ZIP, RAR (máx. 10MB)</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button type="button" class="btn btn-secondary" onclick="hideSubtaskReplyForm(${comment.comment_id})">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Enviar Respuesta
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }
    }
    
    html += `
        </div>
            </div>
            
            <div class="sidebar-details">
    `;
    
    // Información adicional
    html += `
        <div class="task-section">
            <h3><i class="fas fa-info-circle"></i> Información</h3>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div><strong>Creado:</strong> ${formatDate(task.created_at)}</div>
                ${task.due_date ? `<div><strong>Vence:</strong> ${formatDate(task.due_date)}</div>` : ''}
                <div><strong>Asignado:</strong> ${escapeHtml(task.assigned_to_fullname || task.all_assigned_users || 'Sin asignar')}</div>
                <div><strong>Creado por:</strong> ${escapeHtml(task.created_by_name || 'Sistema')}</div>
            </div>
        </div>
    `;
    
    // Adjuntos (separar tarea principal y subtareas)
    const taskAttachments = data.taskAttachments || [];
    const allAttachments = data.attachments || [];
    const totalAttachments = allAttachments.length;
    
    html += `
        <div class="task-section">
            <h3><i class="fas fa-paperclip"></i> Todos los Adjuntos (${totalAttachments})</h3>
    `;
    
    if (totalAttachments === 0) {
        html += '<div style="color: #6b7280; font-style: italic;">Sin adjuntos</div>';
    } else {
        // Adjuntos de la tarea principal
        if (taskAttachments.length > 0) {
            html += `
                <div style="margin-bottom: 20px;">
                    <h4 style="margin: 0 0 12px 0; color: #1f2937; font-size: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px;">
                        <i class="fas fa-task" style="color: #3b82f6;"></i> Adjuntos de la Tarea Principal (${taskAttachments.length})
                    </h4>
                    <div class="attachment-list">
            `;
            taskAttachments.forEach(attachment => {
                html += `
                    <div class="attachment-item">
                        <i class="fas fa-file attachment-icon"></i>
                        <a href="${attachment.file_path}" target="_blank" class="attachment-link">
                            ${escapeHtml(attachment.filename)}
                        </a>
                        <span style="color: #9ca3af; margin-left: 8px;">por ${escapeHtml(attachment.full_name || attachment.username || 'Usuario')}</span>
                    </div>
                `;
            });
            html += '</div></div>';
        }
        
        // Adjuntos de subtareas agrupados
        const subtaskAttachmentsGrouped = data.subtaskAttachments || {};
        const hasSubtaskAttachments = Object.keys(subtaskAttachmentsGrouped).length > 0;
        
        if (hasSubtaskAttachments) {
            html += `
                <div>
                    <h4 style="margin: 0 0 12px 0; color: #1f2937; font-size: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px;">
                        <i class="fas fa-tasks" style="color: #10b981;"></i> Adjuntos de Subtareas
                    </h4>
            `;
            
            Object.entries(subtaskAttachmentsGrouped).forEach(([subtaskId, attachments]) => {
                if (attachments.length > 0) {
                    const subtaskTitle = attachments[0].subtask_title || `Subtarea ${subtaskId}`;
                    html += `
                        <div style="margin-bottom: 16px; background: #f8fafc; border-left: 3px solid #10b981; padding: 12px;">
                            <h5 style="margin: 0 0 8px 0; color: #059669; font-size: 0.9rem;">
                                ${escapeHtml(subtaskTitle)} (${attachments.length} adjuntos)
                            </h5>
                            <div class="attachment-list">
                    `;
                    
                    attachments.forEach(attachment => {
                        html += `
                            <div class="attachment-item" style="font-size: 0.9rem;">
                                <i class="fas fa-file attachment-icon"></i>
                                <a href="${attachment.file_path}" target="_blank" class="attachment-link">
                                    ${escapeHtml(attachment.filename)}
                                </a>
                                <span style="color: #9ca3af; margin-left: 8px;">por ${escapeHtml(attachment.full_name || attachment.username || 'Usuario')}</span>
                            </div>
                        `;
                    });
                    
                    html += '</div></div>';
                }
            });
            
            html += '</div>';
        }
    }
    
    // Agregar formularios para comentarios y adjuntos
    html += `
        </div>
            </div>
        </div>
        
        <!-- Formulario unificado para comentarios y adjuntos -->
        <div class="task-section" style="margin-top: 20px;">
            <div class="task-interactions-header">
                <h3><i class="fas fa-comment-plus"></i> Agregar Comentario</h3>
            </div>
            
            <form id="unified-comment-form-${task.task_id}" class="unified-comment-form" onsubmit="submitUnifiedComment(event, ${task.task_id})" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="comment-text-${task.task_id}">Comentario:</label>
                    <textarea 
                        id="comment-text-${task.task_id}" 
                        name="comment_text" 
                        rows="4" 
                        placeholder="Escribe tu comentario aquí..."
                        required
                    ></textarea>
                </div>
                
                <div class="form-group">
                    <div class="attachment-section">
                        <label>
                            <input type="checkbox" id="include-attachment-${task.task_id}" onchange="toggleAttachmentInput(${task.task_id})"> 
                            Adjuntar archivo (opcional)
                        </label>
                        <div class="file-input-container" id="file-input-${task.task_id}" style="display: none;">
                            <input 
                                type="file" 
                                name="attachment_file" 
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar"
                            >
                            <div class="file-input-info">
                                Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG, GIF, ZIP, RAR (máx. 10MB)
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Enviar Comentario
                    </button>
                </div>
            </form>
        </div>
    `;
    
    content.innerHTML = html;
    
    // Cargar respuestas existentes para todos los comentarios
    setTimeout(() => {
        loadAllCommentReplies(task.task_id);
    }, 100);
}

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

// Función para mostrar/ocultar input de archivo
function toggleAttachmentInput(taskId) {
    const checkbox = document.getElementById(`include-attachment-${taskId}`);
    const fileContainer = document.getElementById(`file-input-${taskId}`);
    
    if (fileContainer) {
        fileContainer.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            // Limpiar el input de archivo si se desmarca
            const fileInput = fileContainer.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.value = '';
            }
        }
    }
}

// Función para enviar comentario unificado (con archivo opcional)
function submitUnifiedComment(event, taskId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    const checkbox = document.getElementById(`include-attachment-${taskId}`);
    const hasAttachment = checkbox && checkbox.checked;
    
    // Validar que hay comentario
    const commentText = formData.get('comment_text');
    if (!commentText || commentText.trim() === '') {
        showNotification('Por favor escribe un comentario', 'error');
        return;
    }
    
    // Si hay archivo adjunto, validar el archivo
    if (hasAttachment) {
        const fileInput = form.querySelector('input[type="file"]');
        if (!fileInput || !fileInput.files[0]) {
            showNotification('Por favor selecciona un archivo o desmarca la opción de adjuntar', 'error');
            return;
        }
        
        // Validar tamaño del archivo (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB en bytes
        if (fileInput.files[0].size > maxSize) {
            showNotification('El archivo es muy grande. Máximo 10MB permitidos.', 'error');
            return;
        }
    } else {
        // Si no hay archivo, removerlo del FormData para evitar enviar vacío
        formData.delete('attachment_file');
    }
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    
    // Elegir endpoint según si hay archivo o no
    const endpoint = hasAttachment 
        ? `?route=admin/add-unified-comment&taskId=${taskId}`
        : `?route=admin/add-task-comment&taskId=${taskId}`;
    
    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Limpiar formulario
            form.reset();
            checkbox.checked = false;
            toggleAttachmentInput(taskId);
            
            // Mostrar mensaje de éxito
            const message = hasAttachment 
                ? 'Comentario con archivo agregado exitosamente' 
                : 'Comentario agregado exitosamente';
            showNotification(message, 'success');
            
            // Recargar detalles de la tarea
            setTimeout(() => {
                openTaskDetailsModal(taskId);
            }, 1000);
        } else {
            showNotification(data.message || 'Error al agregar comentario', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar comentario', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Función para enviar comentario (mantenida para compatibilidad)
function submitComment(event, taskId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    
    fetch(`?route=admin/add-task-comment&taskId=${taskId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Limpiar formulario
            form.reset();
            
            // Mostrar mensaje de éxito
            showNotification('Comentario agregado exitosamente', 'success');
            
            // Recargar detalles de la tarea
            setTimeout(() => {
                openTaskDetailsModal(taskId);
            }, 1000);
        } else {
            showNotification(data.message || 'Error al agregar comentario', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar comentario', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Función para enviar adjunto
function submitAttachment(event, taskId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    const fileInput = form.querySelector('input[type="file"]');
    
    // Validar archivo
    if (!fileInput.files[0]) {
        showNotification('Por favor selecciona un archivo', 'error');
        return;
    }
    
    // Validar tamaño del archivo (10MB max)
    const maxSize = 10 * 1024 * 1024; // 10MB en bytes
    if (fileInput.files[0].size > maxSize) {
        showNotification('El archivo es muy grande. Máximo 10MB permitidos.', 'error');
        return;
    }
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
    
    fetch(`?route=admin/add-task-attachment&taskId=${taskId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Limpiar formulario
            form.reset();
            
            // Mostrar mensaje de éxito
            showNotification('Archivo adjuntado exitosamente', 'success');
            
            // Recargar detalles de la tarea
            setTimeout(() => {
                openTaskDetailsModal(taskId);
            }, 1000);
        } else {
            showNotification(data.message || 'Error al adjuntar archivo', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al subir archivo', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Agregar al DOM
    document.body.appendChild(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Funciones para manejar respuestas a comentarios
function showReplyForm(commentId, taskId) {
    // Ocultar todos los formularios de respuesta abiertos
    const openForms = document.querySelectorAll('.reply-form-container');
    openForms.forEach(form => {
        if (form.id !== `reply-form-${commentId}`) {
            form.style.display = 'none';
        }
    });
    
    // Mostrar el formulario específico
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    if (replyForm) {
        replyForm.style.display = 'block';
        // Hacer focus en el textarea
        const textarea = replyForm.querySelector('textarea[name="reply_text"]');
        if (textarea) {
            textarea.focus();
        }
    }
}

function hideReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    if (replyForm) {
        replyForm.style.display = 'none';
        // Limpiar el formulario
        const form = replyForm.querySelector('form');
        if (form) {
            form.reset();
            // Ocultar adjunto si estaba visible
            const attachmentInput = document.getElementById(`reply-attachment-${commentId}`);
            if (attachmentInput) {
                attachmentInput.style.display = 'none';
            }
        }
    }
}

function toggleReplyAttachment(commentId) {
    const checkbox = document.getElementById(`reply-attachment-check-${commentId}`);
    const attachmentInput = document.getElementById(`reply-attachment-${commentId}`);
    
    if (attachmentInput && checkbox) {
        attachmentInput.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            // Limpiar el input de archivo si se desmarca
            const fileInput = attachmentInput.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.value = '';
            }
        }
    }
}

function submitReply(event, parentCommentId, taskId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Agregar ID del comentario padre
    formData.append('parent_comment_id', parentCommentId);
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    
    fetch(`?route=admin/add-task-reply&taskId=${taskId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Limpiar y ocultar formulario
            form.reset();
            hideReplyForm(parentCommentId);
            
            // Mostrar mensaje de éxito
            showNotification('Respuesta agregada exitosamente', 'success');
            
            // Cargar respuestas del comentario padre
            loadCommentReplies(parentCommentId, taskId);
        } else {
            showNotification(data.message || 'Error al agregar respuesta', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar respuesta', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function loadCommentReplies(commentId, taskId) {
    const repliesContainer = document.getElementById(`replies-${commentId}`);
    if (!repliesContainer) return;
    
    // Mostrar loading
    repliesContainer.innerHTML = '<div style="text-align: center; padding: 12px; color: var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Cargando respuestas...</div>';
    
    fetch(`?route=admin/get-comment-replies&commentId=${commentId}&taskId=${taskId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCommentReplies(data.replies, repliesContainer);
            } else {
                repliesContainer.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error al cargar respuestas:', error);
            repliesContainer.innerHTML = '';
        });
}

function renderCommentReplies(replies, container) {
    if (!replies || replies.length === 0) {
        container.innerHTML = '';
        return;
    }
    
    let html = '';
    replies.forEach(reply => {
        html += `
            <div class="reply-item">
                <div class="reply-header">
                    <span class="reply-author">${escapeHtml(reply.full_name || reply.username || 'Usuario')}</span>
                    <span class="reply-date">${formatDate(reply.created_at)}</span>
                </div>
                <div class="reply-content">${escapeHtml(reply.comment_text)}</div>
                ${reply.attachment_path ? `
                    <div class="reply-attachment">
                        <a href="${reply.attachment_path}" target="_blank">
                            <i class="fas fa-file"></i>
                            ${escapeHtml(reply.attachment_name || 'Archivo adjunto')}
                        </a>
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Cargar respuestas existentes al renderizar el modal
function loadAllCommentReplies(taskId) {
    const commentElements = document.querySelectorAll('.comment-item-detail[data-comment-id]');
    commentElements.forEach(commentEl => {
        const commentId = commentEl.getAttribute('data-comment-id');
        if (commentId) {
            loadCommentReplies(commentId, taskId);
        }
    });
}

// Funciones para manejar respuestas de comentarios de subtareas
function showSubtaskReplyForm(commentId, subtaskId, taskId) {
    // Ocultar todos los formularios de respuesta abiertos
    const openForms = document.querySelectorAll('.reply-form-container');
    openForms.forEach(form => {
        if (form.id !== `subtask-reply-form-${commentId}`) {
            form.style.display = 'none';
        }
    });
    
    // Mostrar el formulario específico
    const replyForm = document.getElementById(`subtask-reply-form-${commentId}`);
    if (replyForm) {
        replyForm.style.display = 'block';
        // Hacer focus en el textarea
        const textarea = replyForm.querySelector('textarea[name="reply_text"]');
        if (textarea) {
            textarea.focus();
        }
    }
}

function hideSubtaskReplyForm(commentId) {
    const replyForm = document.getElementById(`subtask-reply-form-${commentId}`);
    if (replyForm) {
        replyForm.style.display = 'none';
        // Limpiar el formulario
        const form = replyForm.querySelector('form');
        if (form) {
            form.reset();
            // Ocultar adjunto si estaba visible
            const attachmentInput = document.getElementById(`subtask-reply-attachment-${commentId}`);
            if (attachmentInput) {
                attachmentInput.style.display = 'none';
            }
        }
    }
}

function toggleSubtaskReplyAttachment(commentId) {
    const checkbox = document.getElementById(`subtask-reply-attachment-check-${commentId}`);
    const attachmentInput = document.getElementById(`subtask-reply-attachment-${commentId}`);
    
    if (attachmentInput && checkbox) {
        attachmentInput.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            // Limpiar el input de archivo si se desmarca
            const fileInput = attachmentInput.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.value = '';
            }
        }
    }
}

function submitSubtaskReply(event, parentCommentId, subtaskId, taskId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Agregar IDs necesarios
    formData.append('parent_comment_id', parentCommentId);
    formData.append('subtask_id', subtaskId);
    
    // Deshabilitar botón y mostrar loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    
    fetch(`?route=admin/add-subtask-reply&taskId=${taskId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Limpiar y ocultar formulario
            form.reset();
            hideSubtaskReplyForm(parentCommentId);
            
            // Mostrar mensaje de éxito
            showNotification('Respuesta agregada exitosamente', 'success');
            
            // Recargar detalles de la tarea
            setTimeout(() => {
                openTaskDetailsModal(taskId);
            }, 1000);
        } else {
            showNotification(data.message || 'Error al agregar respuesta', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar respuesta', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// -------- Modal para añadir subtareas --------
let addSubtaskCounter = 0;

function openAddSubtasksModal(taskId, taskName) {
    const modal = document.getElementById('addSubtasksModal');
    const taskNameEl = document.getElementById('addSubtasksTaskName');
    const taskIdEl = document.getElementById('addSubtasksTaskId');
    const listEl = document.getElementById('addSubtasksList');
    
    // Configurar modal
    taskNameEl.textContent = `Tarea: ${taskName}`;
    taskIdEl.value = taskId;
    
    // Limpiar lista y reiniciar contador
    listEl.innerHTML = '';
    addSubtaskCounter = 0;
    updateAddSubtasksDisplay();
    
    // Añadir una subtarea inicial
    addSubtaskToForm();
    
    // Mostrar modal
    modal.style.display = 'flex';
}

function closeAddSubtasksModal() {
    const modal = document.getElementById('addSubtasksModal');
    modal.style.display = 'none';
    
    // Limpiar formulario
    const listEl = document.getElementById('addSubtasksList');
    if (listEl) {
        listEl.innerHTML = '';
        addSubtaskCounter = 0;
    }
}

function addSubtaskToForm() {
    addSubtaskCounter++;
    const subtaskId = 'add_subtask_' + addSubtaskCounter;
    const listEl = document.getElementById('addSubtasksList');
    
    // Crear elemento de subtarea
    const subtaskItem = document.createElement('div');
    subtaskItem.className = 'subtask-item';
    subtaskItem.dataset.subtaskId = subtaskId;
    
    subtaskItem.innerHTML = `
        <span class="subtask-counter">${addSubtaskCounter}</span>
        <i class="fas fa-grip-vertical subtask-drag-handle" title="Arrastrar para reordenar"></i>
        <input type="text" class="subtask-input" name="subtasks[]" placeholder="Nombre de la subtarea..." required>
        <button type="button" class="subtask-remove" onclick="removeSubtaskFromForm('${subtaskId}')" title="Eliminar subtarea">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    if (listEl) {
        listEl.appendChild(subtaskItem);
        updateAddSubtasksDisplay();
        
        // Enfocar el input de la nueva subtarea
        const input = subtaskItem.querySelector('.subtask-input');
        if (input) {
            input.focus();
        }
    }
}

function removeSubtaskFromForm(subtaskId) {
    const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
    if (subtaskItem) {
        subtaskItem.remove();
        updateAddSubtasksDisplay();
        renumberAddSubtasks();
    }
}

function updateAddSubtasksDisplay() {
    const listEl = document.getElementById('addSubtasksList');
    const subtaskItems = listEl ? listEl.querySelectorAll('.subtask-item') : [];
    
    if (subtaskItems.length === 0 && listEl) {
        listEl.innerHTML = '<div class="subtasks-empty">No hay subtareas. Haz clic en "Agregar Subtarea" para comenzar.</div>';
    } else if (listEl && listEl.querySelector('.subtasks-empty')) {
        listEl.querySelector('.subtasks-empty').remove();
    }
}

function renumberAddSubtasks() {
    const subtaskItems = document.querySelectorAll('#addSubtasksList .subtask-item');
    subtaskItems.forEach((item, index) => {
        const counter = item.querySelector('.subtask-counter');
        if (counter) {
            counter.textContent = index + 1;
        }
    });
    addSubtaskCounter = subtaskItems.length;
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


