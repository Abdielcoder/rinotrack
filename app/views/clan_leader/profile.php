<?php
ob_start();
?>

<div class="profile-container">
    <header class="profile-header">
        <div class="header-content">
            <div class="back-link">
                <a href="?route=clan_leader" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Dashboard</span>
                </a>
            </div>
            <div class="header-title">
                <h1>Mi Perfil (Líder)</h1>
                <p>Gestiona tu información personal y configuración</p>
            </div>
        </div>
    </header>

    <main class="profile-main">
        <div class="profile-grid">
            <div class="profile-card">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-user"></i></div>
                    <h3>Datos de la cuenta</h3>
                </div>
                <form id="profileForm" class="profile-form">
                    <input type="hidden" name="route" value="clan_leader/update-profile" />
                    <div class="form-group">
                        <label for="full_name">Nombre completo</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo Utils::escape($user['full_name'] ?? ''); ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="username">Nombre de usuario</label>
                        <input type="text" id="username" name="username" value="<?php echo Utils::escape($user['username'] ?? ''); ?>" disabled readonly />
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="<?php echo Utils::escape($user['email'] ?? ''); ?>" required />
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
                    </div>
                </form>
            </div>

            <div class="profile-card">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-lock"></i></div>
                    <h3>Cambiar contraseña</h3>
                </div>
                <form id="passwordForm" class="profile-form">
                    <input type="hidden" name="route" value="clan_leader/update-password-plain" />
                    <div class="form-group">
                        <label for="new_password">Nueva contraseña</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="new_password" name="new_password" required />
                            <button type="button" class="password-toggle" onclick="togglePassword('new_password')"><i class="fas fa-eye" id="eye-new"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar contraseña</label>
                        <div class="password-input-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" required />
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')"><i class="fas fa-eye" id="eye-confirm"></i></button>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-secondary"><i class="fas fa-key"></i> Actualizar contraseña</button>
                    </div>
                </form>
            </div>

            <div class="profile-card">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-camera"></i></div>
                    <h3>Foto de perfil</h3>
                </div>
                <div class="avatar-section">
                    <div class="avatar-preview">
                        <?php if (!empty($user['avatar_path'])): ?>
                            <img id="avatarPreview" src="<?php echo Utils::asset($user['avatar_path']); ?>" alt="Avatar" />
                        <?php else: ?>
                            <div id="avatarPreview" class="avatar-placeholder">
                                <?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <form id="avatarForm" enctype="multipart/form-data" class="avatar-form">
                        <div class="file-input-wrapper">
                            <input type="file" id="avatar" name="avatar" accept="image/*" required />
                            <label for="avatar" class="file-label"><i class="fas fa-upload"></i> Seleccionar imagen</label>
                        </div>
                        <button type="submit" class="btn-secondary"><i class="fas fa-cloud-upload-alt"></i> Subir foto</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById('profileForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const form = this; const btn = form.querySelector('button[type="submit"]'); const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...'; btn.disabled = true;
    const fd = new FormData(form);
    fetch('?route=clan_leader/update-profile', { method:'POST', body: fd, credentials: 'same-origin' })
    .then(r=>r.json()).then(d=>{ if(d.success){ showNotification('Perfil actualizado correctamente','success'); } else { showNotification(d.message||'Error al actualizar el perfil','error'); } })
    .catch(()=>showNotification('Error de conexión','error'))
    .finally(()=>{ btn.innerHTML = original; btn.disabled = false; });
});

document.getElementById('passwordForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const form = this; const btn = form.querySelector('button[type="submit"]'); const original = btn.innerHTML;
    const a = form.querySelector('#new_password').value; const b = form.querySelector('#confirm_password').value;
    if (a !== b) { showNotification('Las contraseñas no coinciden','error'); return; }
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...'; btn.disabled = true;
    const fd = new FormData(form);
    fetch('?route=clan_leader/update-password-plain', { method:'POST', body: fd, credentials:'same-origin' })
    .then(r=>r.json()).then(d=>{ if(d.success){ showNotification('Contraseña actualizada correctamente','success'); form.reset(); } else { showNotification(d.message||'Error al actualizar la contraseña','error'); } })
    .catch(()=>showNotification('Error de conexión','error'))
    .finally(()=>{ btn.innerHTML = original; btn.disabled = false; });
});

document.getElementById('avatarForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    const form = this; const btn = form.querySelector('button[type="submit"]'); const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...'; btn.disabled = true;
    const fd = new FormData(form);
    fetch('?route=clan_leader/upload-avatar', { method:'POST', body: fd, credentials:'same-origin' })
    .then(r=>r.json()).then(d=>{
        if(d.success && d.avatar_url){
            const preview = document.getElementById('avatarPreview');
            if (preview && preview.tagName === 'IMG') { preview.src = d.avatar_url; }
            else if (preview) { const img = new Image(); img.src = d.avatar_url; img.style = "width:100%;height:100%;object-fit:cover;border-radius:50%"; preview.replaceWith(img); img.id='avatarPreview'; }
            showNotification('Foto de perfil actualizada correctamente','success'); form.reset();
        } else { showNotification(d.message||'Error al subir la foto','error'); }
    })
    .catch(()=>showNotification('Error de conexión','error'))
    .finally(()=>{ btn.innerHTML = original; btn.disabled = false; });
});

function showNotification(message, type = 'info'){
    const n = document.createElement('div');
    n.className = `notification notification-${type}`;
    n.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':type==='error'?'exclamation-circle':'info-circle'}"></i><span>${message}</span>`;
    document.body.appendChild(n); setTimeout(()=>n.classList.add('show'), 50);
    setTimeout(()=>{ n.classList.remove('show'); setTimeout(()=>n.remove(), 300); }, 4000);
}

document.getElementById('avatar')?.addEventListener('change', function(e){
    const file = e.target.files[0]; if(!file) return; const reader = new FileReader();
    reader.onload = function(ev){
        const preview = document.getElementById('avatarPreview');
        if (preview && preview.tagName === 'IMG') { preview.src = ev.target.result; }
        else if (preview) { const img = new Image(); img.src = ev.target.result; img.style = "width:100%;height:100%;object-fit:cover;border-radius:50%"; preview.replaceWith(img); img.id='avatarPreview'; }
    }; reader.readAsDataURL(file);
});

function togglePassword(inputId){
    const input = document.getElementById(inputId);
    const icon = document.getElementById(`eye-${inputId.replace('new_', '').replace('confirm_', '')}`);
    if (input.type === 'password') { input.type = 'text'; icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
    else { input.type = 'password'; icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
}
</script>

<style>
/* Reutilizar estilos del perfil de miembro */
<?php echo file_get_contents(__DIR__ . '/../clan_member/profile.php'); /* no-op to keep styles in-sync if desired */ ?>
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>


