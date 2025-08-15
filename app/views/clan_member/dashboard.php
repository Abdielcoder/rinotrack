<?php
// Guardar el contenido de la vista
ob_start();
?>

<?php
    $projectsCount = is_array($projects ?? null) ? count($projects) : 0;
    $totalTasks = (int)($userTaskStats['total_tasks'] ?? 0);
    $completedTasks = (int)($userTaskStats['completed_tasks'] ?? 0);
    $inProgress = max(0, $totalTasks - $completedTasks);
    $progressPct = (float)($userTaskStats['completion_percentage'] ?? 0);
?>

<div class="modern-dashboard" data-theme="default">
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-rhino"></i>
                </div>
                <span class="brand-text">RinoTrack</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item active"><a href="?route=clan_member" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/tasks" class="nav-link"><i class="fas fa-tasks"></i><span>Tareas</span></a></li>
                <li class="nav-item"><a href="?route=clan_member/kpi-dashboard" class="nav-link"><i class="fas fa-chart-line"></i><span>KPI</span></a></li>
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
                <h1 class="welcome-title">¡Hola, <?php echo Utils::escape($user['full_name'] ?: $user['username']); ?>! 👋</h1>
                <p class="welcome-subtitle">Clan: <?php echo Utils::escape($clan['clan_name'] ?? 'Sin clan'); ?></p>
                <div class="motivation" style="display:flex;align-items:center;gap:10px;margin-top:10px">
                    <div class="motivation-icon" style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:var(--primary-gradient);color:#fff">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="motivation-text">
                        <div id="motQuote" style="font-weight:600;color:var(--text-primary)">Cargando frase motivacional...</div>
                        <div id="motAuthor" style="font-size:.9rem;color:var(--text-secondary)"></div>
                    </div>
                </div>
            </div>
            <div class="welcome-stats">
                <div class="stats-row">
                    <div class="stat-card gradient-bg">
                        <div class="stat-content">
                            <div class="stat-header"><h3>Proyectos</h3><i class="fas fa-folder-open"></i></div>
                            <div class="stat-number"><?php echo $projectsCount; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-header"><h3>Tareas</h3><i class="fas fa-tasks"></i></div>
                            <div class="stat-number"><?php echo $totalTasks; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-header"><h3>Completadas</h3><i class="fas fa-check-circle"></i></div>
                            <div class="stat-number"><?php echo $completedTasks; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-header"><h3>En Progreso</h3><i class="fas fa-spinner"></i></div>
                            <div class="stat-number"><?php echo $inProgress; ?></div>
                        </div>
                    </div>
                    <a href="?route=clan_member/tasks" class="btn btn-secondary btn-stats">
                        <i class="fas fa-tasks"></i>
                        Ver mis tareas
                    </a>
                </div>
            </div>
        </header>




    </main>
</div>

<style>
.modern-dashboard{min-height:100vh;background:var(--bg-secondary);padding:0;position:relative}
.modern-nav{background:var(--bg-glass);backdrop-filter:var(--glass-backdrop);border-bottom:1px solid var(--glass-border);padding:var(--spacing-md) 0;position:sticky;top:0;z-index:100}
.nav-container{max-width:1400px;margin:0 auto;padding:0 var(--spacing-lg);display:flex;align-items:center;justify-content:space-between;gap:var(--spacing-xl)}
.nav-brand{display:flex;align-items:center;gap:var(--spacing-md)}
.brand-icon{width:40px;height:40px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:var(--text-white);font-size:1.2rem;background:var(--primary-gradient)}
.brand-text{font-size:1.5rem;font-weight:var(--font-weight-bold);color:var(--text-primary);background:var(--primary-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.nav-menu{display:flex;list-style:none;gap:var(--spacing-sm)}
.nav-item .nav-link{display:flex;align-items:center;gap:var(--spacing-sm);padding:var(--spacing-md) var(--spacing-lg);border-radius:var(--radius-md);text-decoration:none;color:var(--text-secondary);font-weight:var(--font-weight-medium);transition:all var(--transition-normal);position:relative;overflow:hidden}
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
.welcome-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:var(--spacing-2xl);padding:var(--spacing-xl);background:var(--bg-primary);border-radius:var(--radius-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent);gap:var(--spacing-xl)}
.welcome-title{font-size:2.2rem;font-weight:var(--font-weight-bold);color:var(--text-primary);margin-bottom:var(--spacing-sm)}
.welcome-subtitle{font-size:1.05rem;color:var(--text-secondary)}
.welcome-stats{display:flex;flex-direction:column;align-items:stretch;min-width:fit-content}
.quick-stat{display:flex;align-items:center;gap:var(--spacing-md);padding:var(--spacing-lg);background:var(--bg-tertiary);border-radius:var(--radius-lg);border:1px solid var(--bg-accent)}
.stat-icon{width:50px;height:50px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:var(--text-white);font-size:1.2rem}
.stat-icon.success{background:var(--success)}
.stats-section{margin-bottom:var(--spacing-2xl)}
.stats-row{display:flex;align-items:center;gap:var(--spacing-md);flex-wrap:wrap;justify-content:flex-start}
.stat-card{background:var(--bg-primary);border-radius:var(--radius-lg);padding:var(--spacing-md);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent);transition:all var(--transition-normal);min-width:140px;flex-shrink:0}
.stat-card.gradient-bg{background:var(--primary-gradient);color:var(--text-white)}
.stat-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-sm)}
.stat-header h3{font-size:0.9rem;margin:0;font-weight:600}
.stat-header i{font-size:1rem}
.stat-number{font-size:1.5rem;font-weight:var(--font-weight-bold);margin-bottom:0;line-height:1}
.btn-stats{display:flex;align-items:center;gap:var(--spacing-sm);padding:var(--spacing-md);background:var(--bg-primary);border:1px solid var(--bg-accent);border-radius:var(--radius-lg);text-decoration:none;color:var(--text-secondary);font-weight:600;font-size:0.9rem;min-width:140px;justify-content:center;box-shadow:var(--shadow-md);transition:all var(--transition-normal);flex-shrink:0}
.btn-stats:hover{background:var(--bg-tertiary);color:var(--text-primary);transform:translateY(-2px);box-shadow:var(--shadow-lg)}
.content-section{margin-bottom:var(--spacing-2xl)}
.content-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:var(--spacing-xl)}
.content-card{background:var(--bg-primary);border-radius:var(--radius-xl);padding:var(--spacing-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent);transition:all var(--transition-normal)}
.table-wrapper{overflow-x:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{background:var(--bg-tertiary);padding:var(--spacing-lg);text-align:left;font-weight:600;color:var(--text-primary);border-bottom:1px solid var(--bg-accent)}
.data-table td{padding:var(--spacing-lg);border-bottom:1px solid var(--bg-accent);color:var(--text-secondary)}
.badge{padding:4px 8px;border-radius:6px;font-size:.8rem;text-transform:uppercase}
.btn-sm{padding:6px 10px;font-size:.85rem}
.progress-bar.large{width:100%;height:14px;background:var(--bg-tertiary);border-radius:9999px;overflow:hidden}
.progress-fill{height:100%;background:var(--primary-gradient)}
@media (max-width:1024px){.nav-container{flex-wrap:wrap;gap:var(--spacing-md)}.user-menu{order:-1;width:100%;justify-content:space-between}.content-grid{grid-template-columns:1fr}}
@media (max-width:768px){.welcome-header{flex-direction:column;text-align:center;gap:var(--spacing-lg);align-items:center}.welcome-stats{width:100%}.stats-row{gap:var(--spacing-sm);justify-content:center}.stat-card{min-width:120px}.btn-stats{min-width:120px}.nav-menu{display:none}.main-content{padding:var(--spacing-lg) var(--spacing-md)}}
</style>

<script>
// Frase motivacional en dashboard (una por sesión)
(function(){
  const qEl = document.getElementById('motQuote');
  const aEl = document.getElementById('motAuthor');
  if (!qEl || !aEl) return;
  const apis=[
    {url:'https://api.quotable.io/random', map:d=>({text:d.content, author:d.author})},
    {url:'https://zenquotes.io/api/random', map:d=>{const x=(Array.isArray(d)?d[0]:{})||{}; return {text:x.q, author:x.a};}},
    {url:'https://type.fit/api/quotes', map:d=>{const arr=Array.isArray(d)?d:[]; const r=arr[Math.floor(Math.random()*arr.length)]||{}; return {text:r.text, author:r.author||'Anónimo'};}}
  ];
  const localFallback=[
    {text:'La excelencia no es un acto, es un hábito.', author:'Aristóteles'},
    {text:'La disciplina es el puente entre metas y logros.', author:'Jim Rohn'},
    {text:'Haz hoy lo que otros no harán y mañana vivirás como otros no pueden.', author:'Jerry Rice'}
  ];
  function applyQuote(q){ if(!q||!q.text) q=localFallback[Math.floor(Math.random()*localFallback.length)]; qEl.textContent='“'+(q.text||'')+'”'; aEl.textContent=q.author?('— '+q.author):''; }
  (async function(){
    for(const api of apis){
      try { const r=await fetch(api.url,{credentials:'omit'}); if(!r.ok) continue; const d=await r.json(); const q=api.map(d); if(q&&q.text){ applyQuote(q); return; } } catch(_){ }
    }
    applyQuote(null);
  })();
})();
</script>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
$additionalJS = [];
require_once __DIR__ . '/../layout.php';
?>


