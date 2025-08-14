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
				<li class="nav-item"><a href="?route=clan_member/availability" class="nav-link"><i class="fas fa-user-clock"></i><span>Disponibilidad</span></a></li>
				<li class="nav-item active"><a href="?route=clan_member/profile" class="nav-link"><i class="fas fa-user"></i><span>Perfil</span></a></li>
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
				<h1 class="welcome-title">Mi Perfil</h1>
				<p class="welcome-subtitle">Actualiza tus datos, contraseña y foto de perfil</p>
			</div>
		</header>

		<section class="content-section animate-fade-in">
			<div class="content-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:20px">
				<div class="content-card">
					<div class="card-header"><h3><i class="fas fa-id-badge icon-gradient"></i> Datos de la cuenta</h3></div>
					<form id="profileForm" class="form-grid" style="display:grid;gap:12px">
						<input type="hidden" name="route" value="clan_member/update-profile" />
						<label>Nombre completo<input type="text" name="full_name" value="<?php echo Utils::escape($user['full_name'] ?? ''); ?>" required /></label>
						<label>Username<input type="text" name="username" value="<?php echo Utils::escape($user['username'] ?? ''); ?>" required /></label>
						<label>Email<input type="email" name="email" value="<?php echo Utils::escape($user['email'] ?? ''); ?>" required /></label>
						<div style="display:flex;gap:8px;justify-content:flex-end"><button class="btn btn-primary" type="submit">Guardar</button></div>
					</form>
				</div>

				<div class="content-card">
					<div class="card-header"><h3><i class="fas fa-key icon-gradient"></i> Cambiar contraseña</h3></div>
					<form id="passwordForm" style="display:grid;gap:12px">
						<input type="hidden" name="route" value="clan_member/update-password-plain" />
						<label>Nueva contraseña<input type="text" name="new_password" required /></label>
						<label>Confirmar contraseña<input type="text" name="confirm_password" required /></label>
						<div style="display:flex;gap:8px;justify-content:flex-end"><button class="btn btn-secondary" type="submit">Actualizar</button></div>
					</form>
				</div>

				<div class="content-card">
					<div class="card-header"><h3><i class="fas fa-user-circle icon-gradient"></i> Foto de perfil</h3></div>
					<div style="display:flex;gap:16px;align-items:center">
						<div class="modern-avatar" style="width:72px;height:72px">
							<?php if (!empty($user['avatar_path'])): ?>
								<img id="avatarPreview" src="<?php echo Utils::asset($user['avatar_path']); ?>" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:999px"/>
							<?php else: ?>
								<div id="avatarPreview" class="avatar-text" style="display:flex;align-items:center;justify-content:center;background:var(--primary-gradient);color:#fff;border-radius:999px;width:100%;height:100%">
									<?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?>
								</div>
							<?php endif; ?>
						</div>
						<form id="avatarForm" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:center">
							<input type="file" name="avatar" accept="image/*" required />
							<button class="btn btn-secondary" type="submit">Subir</button>
						</form>
					</div>
				</div>
			</div>
		</section>
	</main>
</div>

<script>
// Guardar datos
document.getElementById('profileForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const fd = new FormData(this);
  fetch('?route=clan_member/update-profile', { method:'POST', body: fd, credentials:'same-origin' })
    .then(r=>r.json()).then(d=>{ alert(d.message|| (d.success?'Guardado':'Error')); if(d.success){ location.reload(); } });
});

// Cambiar contraseña (texto plano, por requerimiento)
document.getElementById('passwordForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const fd = new FormData(this);
  fetch('?route=clan_member/update-password-plain', { method:'POST', body: fd, credentials:'same-origin' })
    .then(r=>r.json()).then(d=>{ alert(d.message|| (d.success?'Actualizado':'Error')); });
});

// Subir avatar
document.getElementById('avatarForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const fd = new FormData(this);
  fetch('?route=clan_member/upload-avatar', { method:'POST', body: fd, credentials:'same-origin' })
    .then(r=>r.json()).then(d=>{ if(d.success && d.avatar_url){ const p=document.getElementById('avatarPreview'); if(p && p.tagName==='IMG'){ p.src=d.avatar_url; } else if(p){ const img=new Image(); img.src=d.avatar_url; img.style="width:100%;height:100%;object-fit:cover;border-radius:999px"; p.replaceWith(img); img.id='avatarPreview'; } }
      alert(d.message|| (d.success?'Avatar actualizado':'Error al subir')); });
});
</script>

<style>
.modern-dashboard{min-height:100vh;background:var(--bg-secondary)}
.modern-nav{background:var(--bg-glass);backdrop-filter:var(--glass-backdrop);border-bottom:1px solid var(--glass-border);padding:var(--spacing-md) 0;position:sticky;top:0;z-index:100}
.nav-container{max-width:1400px;margin:0 auto;padding:0 var(--spacing-lg);display:flex;align-items:center;justify-content:space-between;gap:var(--spacing-xl)}
.nav-menu{display:flex;list-style:none;gap:var(--spacing-sm)}
.modern-avatar{position:relative;background:var(--bg-tertiary)}
.status-dot{position:absolute;bottom:2px;right:2px;width:12px;height:12px;background:var(--success);border:2px solid var(--bg-primary);border-radius:999px}
.main-content{max-width:1400px;margin:0 auto;padding:var(--spacing-xl) var(--spacing-lg)}
.welcome-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-2xl);padding:var(--spacing-xl);background:var(--bg-primary);border-radius:var(--radius-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent)}
.content-card{background:var(--bg-primary);border-radius:var(--radius-xl);padding:var(--spacing-xl);box-shadow:var(--shadow-md);border:1px solid var(--bg-accent)}
.form-grid label{display:grid;gap:6px;color:var(--text-secondary)}
.form-grid input{padding:10px;border:1px solid var(--bg-accent);border-radius:10px}
</style>

<?php
$content = ob_get_clean();
$additionalCSS = [APP_URL . 'assets/css/clan-member.css'];
require_once __DIR__ . '/../layout.php';
?>


