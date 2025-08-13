<?php
// Capturar el contenido de la vista
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-bell"></i>
                </div>
                <span class="brand-text">Notificaciones</span>
            </div>

            <ul class="nav-menu">
                <li class="nav-item"><a href="?route=admin" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li class="nav-item"><a href="?route=admin/users" class="nav-link"><i class="fas fa-users"></i><span>Usuarios</span></a></li>
                <li class="nav-item"><a href="?route=admin/projects" class="nav-link"><i class="fas fa-project-diagram"></i><span>Proyectos</span></a></li>
                <li class="nav-item"><a href="?route=admin/clans" class="nav-link"><i class="fas fa-users-cog"></i><span>Clanes</span></a></li>
                <li class="nav-item active"><a href="?route=admin/notifications" class="nav-link"><i class="fas fa-bell"></i><span>Notificaciones</span></a></li>
            </ul>

            <div class="user-menu">
                <div class="user-avatar modern-avatar">
                    <span class="avatar-text"><?php echo strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1)); ?></span>
                    <div class="status-dot"></div>
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
        <header class="welcome-header animate-fade-in">
            <div class="welcome-content">
                <h1 class="welcome-title">Centro de Notificaciones</h1>
                <p class="welcome-subtitle">Activa y configura alertas por correo con un diseño profesional.</p>
            </div>
        </header>

        

        <section class="content-section animate-fade-in">
            <div class="content-grid">
                <div class="content-card" style="grid-column: span 2;">
                    <div class="card-header">
                        <h3><i class="fas fa-sliders-h icon-gradient"></i> Configuración</h3>
                    </div>
                    <form id="notificationSettingsForm" class="settings-form">
                        <?php
                        // Base toggles
                        $configList = [
                            'project_assigned_to_clan' => 'Proyecto asignado a un clan',
                            'task_overdue' => 'Tareas vencidas'
                        ];
                        foreach ($configList as $key => $label):
                            $enabled = (int)($settings[$key]['is_enabled'] ?? 0);
                            $rec = $settings[$key]['recipients'] ?? '';
                        ?>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4><?php echo Utils::escape($label); ?></h4>
                                <p>Correos adicionales (opcional, separados por coma)</p>
                            </div>
                            <div class="setting-controls">
                                <label class="switch">
                                    <input type="checkbox" name="<?php echo $key; ?>" <?php echo $enabled ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <input class="recipients-input" type="text" name="<?php echo $key; ?>_recipients" placeholder="email1@dominio.com, email2@dominio.com" value="<?php echo Utils::escape($rec); ?>">
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php 
                        // Próximas a vencer: hasta 3 slots configurables de días
                        for ($i=1; $i<=3; $i++):
                            $key = 'task_due_soon_' . $i;
                            $label = 'Tareas próximas a vencer (' . $i . ')';
                            $enabled = (int)($settings[$key]['is_enabled'] ?? 0);
                            $rec = $settings[$key]['recipients'] ?? '';
                            $days = (int)($settings[$key]['value'] ?? ( $i===1?5:($i===2?3:1) ));
                        ?>
                        <div class="setting-item">
                            <div class="setting-info">
                                <h4><?php echo Utils::escape($label); ?></h4>
                                <p>Correos adicionales (opcional) y días antes del vencimiento</p>
                            </div>
                            <div class="setting-controls" style="gap:8px">
                                <label class="switch">
                                    <input type="checkbox" name="<?php echo $key; ?>" <?php echo $enabled ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <input type="number" min="0" max="365" step="1" style="width:90px;padding:10px;border:1px solid #e5e7eb;border-radius:8px" name="<?php echo $key; ?>_days" value="<?php echo (int)$days; ?>">
                                    <span style="color:#6b7280;font-size:12px">días</span>
                                </div>
                                <input class="recipients-input" type="text" name="<?php echo $key; ?>_recipients" placeholder="email1@dominio.com, email2@dominio.com" value="<?php echo Utils::escape($rec); ?>">
                            </div>
                        </div>
                        <?php endfor; ?>

                        <div class="form-actions">
                            <button type="button" id="saveSettingsBtn" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                            <div class="spacer"></div>
                            <div style="display:none">
                                <input type="email" id="testEmail" placeholder="Correo de prueba" value="<?php echo Utils::escape($user['email'] ?? ''); ?>">
                                <button type="button" id="sendTestBtn" class="btn btn-secondary"><i class="fas fa-paper-plane"></i> Enviar prueba</button>
                            </div>
                        </div>
                        <div id="settingsMsg" class="form-message" style="display:none"></div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</div>

<style>
.settings-form { display: flex; flex-direction: column; gap: 14px; }
.setting-item { display: flex; align-items: center; justify-content: space-between; padding: 14px; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; }
.setting-info h4 { margin: 0 0 6px 0; font-size: 15px; color: #111827; }
.setting-info p { margin: 0; color: #6b7280; font-size: 12px; }
.setting-controls { display: flex; align-items: center; gap: 12px; }
.recipients-input { padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; min-width: 320px; }
.form-actions { display: flex; align-items: center; gap: 12px; margin-top: 8px; }
.spacer { flex: 1; }
.form-message { margin-top: 10px; font-size: 14px; }
/* Switch */
.switch { position: relative; display: inline-block; width: 52px; height: 28px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #d1d5db; transition: .3s; border-radius: 9999px; }
.slider:before { position: absolute; content: ""; height: 22px; width: 22px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
.switch input:checked + .slider { background-color: #3b82f6; }
.switch input:checked + .slider:before { transform: translateX(24px); }
</style>

<script>
(function() {
    const msg = document.getElementById('settingsMsg');
    function post(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        }).then(r => r.json());
    }
    document.getElementById('saveSettingsBtn').addEventListener('click', async () => {
        const form = document.getElementById('notificationSettingsForm');
        const data = new FormData(form);
        const obj = {};
        for (const [k, v] of data.entries()) obj[k] = v;
        // checkboxes no marcados no vienen; agregamos claves en 0
        ['project_assigned_to_clan','task_overdue','task_due_soon_1','task_due_soon_2','task_due_soon_3'].forEach(k => { if (!obj[k]) obj[k] = ''; });
        const res = await post('?route=admin/update-notification-settings', obj);
        msg.style.display = 'block';
        msg.style.color = res.success ? '#10b981' : '#ef4444';
        msg.textContent = res.message || (res.success ? 'Guardado' : 'Error al guardar');
    });
    const sendBtn = document.getElementById('sendTestBtn');
    if (sendBtn) {
        sendBtn.addEventListener('click', async () => {
            const emailInput = document.getElementById('testEmail');
            const to = emailInput ? emailInput.value.trim() : '';
            if (!to) { return; }
            const res = await post('?route=admin/test-notification', { to });
            msg.style.display = 'block';
            msg.style.color = res.success ? '#10b981' : '#ef4444';
            msg.textContent = res.message || (res.success ? 'Enviado' : 'Error al enviar');
        });
    }
})();
</script>

<?php
$content = ob_get_clean();
$title = 'Notificaciones - ' . APP_NAME;
include __DIR__ . '/layout.php';
?>


