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

        <!-- Calendario de tareas personales (integrado como en líder) -->
        <section class="content-section animate-fade-in">
            <div class="content-card">
                <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
                    <h3><i class="fas fa-calendar-alt icon-gradient"></i> Calendario de entregables</h3>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button class="btn btn-secondary" onclick="previousMonth()"><i class="fas fa-chevron-left"></i></button>
                        <strong id="currentMonth">Mes Año</strong>
                        <button class="btn btn-secondary" onclick="nextMonth()"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="calendar-grid">
                    <div class="calendar-weekdays">
                        <div class="weekday">Dom</div>
                        <div class="weekday">Lun</div>
                        <div class="weekday">Mar</div>
                        <div class="weekday">Mié</div>
                        <div class="weekday">Jue</div>
                        <div class="weekday">Vie</div>
                        <div class="weekday">Sáb</div>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>
                </div>
            </div>
        </section>

        <!-- Modal tareas del día -->
        <div class="task-modal" id="taskModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modalTitle">Tareas del día</h3>
                    <button class="modal-close" onclick="closeTaskModal()">&times;</button>
                </div>
                <div class="task-list" id="modalTaskList"></div>
            </div>
        </div>
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
/* Calendario inspirado en perfil líder */
.calendar-grid{display:grid;gap:12px}
.calendar-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}
.weekday{font-weight:700;color:var(--text-secondary);text-align:center}
.calendar-days{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}
.calendar-day{background:var(--bg-primary);border:1px solid var(--bg-accent);border-radius:10px;min-height:90px;padding:8px;cursor:pointer;transition:.2s}
.calendar-day:hover{box-shadow:var(--shadow-md);transform:translateY(-1px)}
.calendar-day.other-month{opacity:.45}
.calendar-day.today{border-color: var(--primary-color)}
.day-number{font-weight:800;color:var(--text-primary)}
.day-tasks{font-size:.8rem;color:var(--text-secondary)}
.task-indicator{display:inline-block;width:8px;height:8px;border-radius:999px;margin-right:4px}
.task-indicator.completed{background:#10b981}
.task-indicator.pending{background:#f59e0b}
.task-indicator.in_progress{background:#3b82f6}
.task-indicator.overdue{background:#ef4444}
.task-modal{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;z-index:1000}
.task-modal.show{display:flex}
.task-modal .modal-content{background:#fff;border-radius:12px;max-width:640px;width:92%;box-shadow:var(--shadow-lg)}
.task-modal .modal-header{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;border-bottom:1px solid var(--bg-accent)}
.task-modal .task-list{padding:14px 16px;max-height:60vh;overflow:auto}
.task-item{border:1px solid var(--bg-accent);border-radius:10px;padding:10px;margin-bottom:8px}
.task-status{padding:2px 8px;border-radius:999px;font-size:.75rem}
.task-status.completed{background:rgba(16,185,129,.15);color:#065f46}
.task-status.pending{background:rgba(245,158,11,.15);color:#7c2d12}
.task-status.in_progress{background:rgba(59,130,246,.15);color:#1e3a8a}
.task-status.overdue{background:rgba(239,68,68,.15);color:#7f1d1d}
@media (max-width:768px){.nav-menu{display:none}.main-content{padding:var(--spacing-lg) var(--spacing-md)}}
</style>

<script>
// Reutilizar motor del calendario del líder
let tasksData = [];
let currentDate = new Date();

function setTasksData(data){ tasksData = data; if (document.getElementById('calendarDays')) generateCalendar(); }

function generateCalendar(){
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();
  const monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  const mEl = document.getElementById('currentMonth'); if (mEl) mEl.textContent = monthNames[month] + ' ' + year;
  const first = new Date(year, month, 1); const start = new Date(first); start.setDate(start.getDate() - first.getDay());
  const daysEl = document.getElementById('calendarDays'); if (!daysEl) return; daysEl.innerHTML = '';
  const today = new Date(); today.setHours(0,0,0,0);
  for (let i=0;i<42;i++){
    const d = new Date(start); d.setDate(start.getDate()+i);
    const day = document.createElement('div'); day.className='calendar-day';
    if (d.getMonth()!==month) day.classList.add('other-month');
    if (d.getTime()===today.getTime()) day.classList.add('today');
    const ds = d.toISOString().split('T')[0];
    const dayTasks = tasksData.filter(t=>t && t.due_date && t.due_date===ds);
    if (dayTasks.length>0) day.classList.add('has-tasks');
    const num = document.createElement('div'); num.className='day-number'; num.textContent=d.getDate();
    const info = document.createElement('div'); info.className='day-tasks'; info.textContent = dayTasks.length+' tarea'+(dayTasks.length!==1?'s':'');
    day.appendChild(num); day.appendChild(info);
    if (dayTasks.length>0){
      const indicators=document.createElement('div');
      const counts={}; dayTasks.forEach(td=>{ const st=td.status; counts[st]=(counts[st]||0)+1; });
      Object.keys(counts).forEach(st=>{ const b=document.createElement('span'); b.className='task-indicator '+st; b.title=counts[st]+' '+st; indicators.appendChild(b); });
      day.appendChild(indicators);
    }
    day.addEventListener('click',()=>showTasksForDate(d, dayTasks));
    daysEl.appendChild(day);
  }
}
function previousMonth(){ currentDate.setMonth(currentDate.getMonth()-1); generateCalendar(); }
function nextMonth(){ currentDate.setMonth(currentDate.getMonth()+1); generateCalendar(); }
function showTasksForDate(date, list){
  const modal=document.getElementById('taskModal'); const title=document.getElementById('modalTitle'); const body=document.getElementById('modalTaskList');
  if(!modal||!title||!body) return; body.innerHTML='';
  title.textContent = 'Tareas del '+ date.toLocaleDateString('es-ES',{weekday:'long', year:'numeric', month:'long', day:'numeric'});
  if(!list||list.length===0){ body.innerHTML='<p>No hay tareas programadas para este día.</p>'; modal.classList.add('show'); return; }
  list.forEach(t=>{
    const overdue = t.due_date && (new Date(t.due_date) < new Date()) && t.status!=='completed';
    const st = overdue? 'overdue' : (t.status||'');
    const el=document.createElement('div'); el.className='task-item';
    el.innerHTML = `<div class=\"task-header\"><h4 class=\"task-title\">${(t.task_name||'')}</h4><span class=\"task-status ${st}\">${overdue?'Vencida':(t.status||'')}</span></div>
    <div class=\"task-details\"><div class=\"task-project\"><i class=\"fas fa-folder\"></i> ${(t.project_name||'')}</div>${t.description?`<div style='margin-top:.5rem'><i class='fas fa-align-left'></i> ${t.description}</div>`:''}</div>`;
    body.appendChild(el);
  });
  modal.classList.add('show');
}
function closeTaskModal(){ document.getElementById('taskModal')?.classList.remove('show'); }
document.addEventListener('DOMContentLoaded',()=>{ window.calendarTasksData = <?= json_encode($tasks ?? []) ?>; setTasksData(calendarTasksData); });
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


