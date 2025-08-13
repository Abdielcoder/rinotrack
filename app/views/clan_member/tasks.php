<?php
ob_start();
?>

<div class="clan-member-tasks minimal">
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Todas las tareas del clan</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name'] ?? ''); ?></span>
            </div>
            <div class="actions-minimal">
                <a href="?route=clan_member/projects" class="btn-minimal"><i class="fas fa-project-diagram"></i> Proyectos</a>
                <a href="?route=logout" class="btn-minimal danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>
    </header>
    <nav class="cm-subnav">
        <div class="nav-inner">
            <ul>
                <li><a class="cm-subnav-link" href="?route=clan_member/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/projects"><i class="fas fa-project-diagram"></i> Proyectos</a></li>
                <li><a class="cm-subnav-link active" href="?route=clan_member/tasks"><i class="fas fa-tasks"></i> Tareas</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/kpi-dashboard"><i class="fas fa-chart-bar"></i> KPI</a></li>
                <li><a class="cm-subnav-link" href="?route=clan_member/availability"><i class="fas fa-user-clock"></i> Disponibilidad</a></li>
            </ul>
        </div>
    </nav>

    <div class="content-minimal">
        <?php if (!empty($projectsSummary)): ?>
        <section>
            <div class="cards-compact">
                <?php foreach ($projectsSummary as $p): ?>
                    <div class="card-compact">
                        <div class="cc-head">
                            <span><?php echo htmlspecialchars($p['project_name']); ?></span>
                            <span class="chip"><?php echo htmlspecialchars(strtoupper($p['status'])); ?></span>
                        </div>
                        <div class="cc-kpis">
                            <div class="cc-kpi">
                                <span class="label">Total</span>
                                <span class="value"><?php echo (int)$p['total_tasks']; ?></span>
                            </div>
                            <div class="cc-kpi">
                                <span class="label">Completadas</span>
                                <span class="value"><?php echo (int)$p['completed_tasks']; ?></span>
                            </div>
                            <div class="cc-kpi">
                                <span class="label">Progreso</span>
                                <span class="value"><?php echo (float)$p['progress_percentage']; ?>%</span>
                            </div>
                            <div class="cc-actions">
                                <a class="btn-minimal" href="?route=clan_member/tasks&project_id=<?php echo $p['project_id']; ?>"><i class="fas fa-eye"></i> Ver Tareas</a>
                            </div>
                        </div>
                        <div class="cc-progress"><span style="width: <?php echo (float)$p['progress_percentage']; ?>%"></span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        <form method="get" class="filters-minimal">
            <input type="hidden" name="route" value="clan_member/tasks" />
            <input type="text" name="search" placeholder="Buscar" value="<?php echo htmlspecialchars($search ?? ''); ?>" />
            <select name="status">
                <option value="">Todos</option>
                <option value="pending" <?php echo ($status === 'pending') ? 'selected' : ''; ?>>Pendiente</option>
                <option value="in_progress" <?php echo ($status === 'in_progress') ? 'selected' : ''; ?>>En progreso</option>
                <option value="completed" <?php echo ($status === 'completed') ? 'selected' : ''; ?>>Completado</option>
            </select>
            <select name="per_page" title="Resultados por página">
                <?php $pp = (int)($perPage ?? 8); ?>
                <?php foreach ([5,8,12,20,30] as $opt): ?>
                    <option value="<?php echo $opt; ?>" <?php echo ($pp === $opt) ? 'selected' : ''; ?>><?php echo $opt; ?> por página</option>
                <?php endforeach; ?>
            </select>
            <button class="btn-minimal" type="submit"><i class="fas fa-filter"></i> Filtrar</button>
            <span class="spacer"></span>
            <?php $total = (int)($tasksData['total'] ?? 0); ?>
            <span class="result-count"><?php echo $total; ?> resultados</span>
        </form>

        <?php $tasksData = $tasksData ?? ['tasks'=>[], 'page'=>1, 'total_pages'=>0]; ?>
        <?php if (empty($tasksData['tasks'])): ?>
            <div class="empty-minimal">No hay tareas</div>
        <?php else: ?>
            <div class="tasks-list">
                <?php foreach ($tasksData['tasks'] as $t): ?>
                    <?php 
                        $statusClass = 'pending';
                        if (($t['status'] ?? '') === 'completed') { $statusClass = 'completed'; }
                        elseif (($t['status'] ?? '') === 'in_progress') { $statusClass = 'in_progress'; }
                        $priorityClass = strtolower((string)($t['priority'] ?? 'medium'));
                        $days = isset($t['days_until_due']) ? (int)$t['days_until_due'] : null;
                        $dueLabel = '';
                        $dueCls = '';
                        if (!empty($t['due_date'])) {
                            if ($days !== null) {
                                if ($days < 0) { $dueLabel = 'Vencida'; $dueCls = 'overdue'; }
                                elseif ($days === 0) { $dueLabel = 'Vence hoy'; $dueCls = 'due-soon'; }
                                elseif ($days === 1) { $dueLabel = 'Vence mañana'; $dueCls = 'due-soon'; }
                                else { $dueLabel = 'Vence en ' . $days . ' días'; }
                            } else {
                                $dueLabel = 'Vence: ' . date('d/m/Y', strtotime($t['due_date']));
                            }
                        }
                    ?>
                    <div class="task-item" data-task-id="<?php echo $t['task_id']; ?>">
                        <div class="task-main">
                            <div class="task-title">
                                <span class="status-dot <?php echo $statusClass; ?>" title="Estado"></span>
                                <?php echo htmlspecialchars($t['task_name']); ?>
                            </div>
                            <div class="task-meta">
                                <span class="task-project"><i class="fas fa-project-diagram"></i> <?php echo htmlspecialchars($t['project_name']); ?></span>
                                <?php if (!empty($t['all_assigned_users'])): ?>
                                    <span title="Asignados"><i class="fas fa-users"></i> <?php echo htmlspecialchars($t['all_assigned_users']); ?></span>
                                <?php elseif (!empty($t['assigned_user_name'])): ?>
                                    <span title="Asignado a"><i class="fas fa-user"></i> <?php echo htmlspecialchars($t['assigned_user_name']); ?></span>
                                <?php endif; ?>
                                <?php if ($dueLabel): ?>
                                    <span class="badge badge-due <?php echo $dueCls; ?>" title="Fecha límite"><i class="fas fa-calendar"></i> <?php echo $dueLabel; ?></span>
                                <?php endif; ?>
                                <span class="badge badge-priority <?php echo $priorityClass; ?>" title="Prioridad"><i class="fas fa-flag"></i></span>
                                <span class="chip chip-status <?php echo $statusClass; ?>" title="Estado"><?php echo str_replace('_',' ', (string)$t['status']); ?></span>
                            </div>
                        </div>
                        <div class="task-actions">
                            <button class="icon-btn" onclick="openCommentModal(<?php echo $t['task_id']; ?>)" title="Comentar"><i class="fas fa-comment"></i></button>
                            <?php $own = in_array((string)($user['user_id'] ?? 0), explode(',', (string)($t['all_assigned_user_ids'] ?? ''))) || (int)($t['assigned_to_user_id'] ?? 0) === (int)($user['user_id'] ?? -1); ?>
                            <button class="icon-btn" <?php echo $own ? '' : 'disabled'; ?> title="Cambiar estado" onclick="toggleTaskStatus(<?php echo $t['task_id']; ?>, '<?php echo $t['status']; ?>', <?php echo $own ? 'true' : 'false'; ?>)"><i class="fas fa-check"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination-minimal">
                <?php for ($i = 1; $i <= (int)$tasksData['total_pages']; $i++): ?>
                    <a class="page-link <?php echo ($i == (int)$tasksData['page']) ? 'active' : ''; ?>" href="?route=clan_member/tasks&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal" id="commentModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar comentario</h3>
            <button class="modal-close" onclick="closeCommentModal()">&times;</button>
        </div>
        <form id="commentForm" class="modal-body" enctype="multipart/form-data">
            <input type="hidden" name="task_id" id="commentTaskId" />
            <div class="form-group">
                <label>Comentario</label>
                <textarea name="comment_text" required></textarea>
            </div>
            <div class="form-group">
                <label>Adjunto (opcional)</label>
                <input type="file" name="attachment" />
            </div>
        </form>
        <div class="modal-footer">
            <button class="action-btn secondary" onclick="closeCommentModal()">Cancelar</button>
            <button class="action-btn primary" type="submit" form="commentForm">Guardar</button>
        </div>
    </div>
    </div>

<script>
function openCommentModal(taskId){
  document.getElementById('commentTaskId').value = taskId;
  document.getElementById('commentModal').style.display = 'flex';
}
function closeCommentModal(){
  document.getElementById('commentModal').style.display = 'none';
  document.getElementById('commentForm').reset();
}
document.getElementById('commentForm').addEventListener('submit', function(e){
  e.preventDefault();
  const fd = new FormData(this);
  fetch('?route=clan_member/add-task-comment', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(r=>r.json()).then(d=>{ alert(d.message|| (d.success?'OK':'Error')); if(d.success){ closeCommentModal(); } });
});

function toggleTaskStatus(taskId, currentStatus, allowed){
  if(!allowed){ alert('No puedes cambiar el estado de esta tarea'); return; }
  const isCompleted = currentStatus !== 'completed';
  const fd = new FormData(); fd.append('task_id', taskId); fd.append('is_completed', isCompleted ? 'true' : 'false');
  fetch('?route=clan_member/toggle-task-status', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(r=>r.json()).then(d=>{ alert(d.message|| (d.success?'OK':'Error')); if(d.success){ location.reload(); } });
}
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-leader.css', APP_URL . 'assets/css/clan-member.css'];
$additionalJS = [APP_URL . 'assets/js/clan-leader.js'];
require_once __DIR__ . '/../layout.php';
?>


