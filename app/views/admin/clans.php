<?php
// Iniciar buffer de salida
ob_start();

// Configurar variables para el layout
$title = 'Gestión de Clanes - ' . APP_NAME;

// JavaScript que debe estar disponible inmediatamente
$additionalJS = [];
$additionalCSS = [];
?>

<div class="modern-dashboard" data-theme="default">
    <!-- Menú de navegación moderno -->
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-users-cog"></i>
                </div>
                <span class="brand-text">Gestión de Clanes</span>
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

            <!-- Información del usuario -->
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

    <!-- Contenido principal -->
    <main class="main-content">
        <!-- Header -->
        <header class="page-header animate-fade-in">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-users-cog"></i>
                    Gestión de Clanes
                </h1>
                <div class="header-actions">
                    <button class="btn btn-primary" data-action="create-clan">
                        <i class="fas fa-plus"></i>
                        Crear Clan
                    </button>
                </div>
            </div>
        </header>

        <!-- Grid de clanes -->
        <section class="content-section animate-fade-in">
            <div class="clans-header">
                <h3>Clanes Activos (<?php echo count($clans); ?>)</h3>
            </div>

            <?php if (empty($clans)): ?>
            <div class="empty-state">
                <div class="empty-content">
                    <i class="fas fa-users-cog"></i>
                    <h3>No hay clanes</h3>
                    <p>Comienza creando tu primer clan</p>
                    <button class="btn btn-primary" data-action="create-clan">
                        <i class="fas fa-plus"></i>
                        Crear Primer Clan
                    </button>
                </div>
            </div>
            <?php else: ?>
            <div class="clans-grid">
                <?php foreach ($clans as $clan): ?>
                <div class="clan-card">
                    <div class="clan-header">
                        <div class="clan-info">
                            <h4 class="clan-name"><?php echo Utils::escape($clan['clan_name']); ?></h4>
                            <span class="clan-departamento"><?php echo Utils::escape($clan['clan_departamento']); ?></span>
                        </div>
                        <div class="clan-actions">
                            <div class="action-menu">
                                <button class="btn-icon" data-action="toggle-clan-menu" data-clan-id="<?php echo intval($clan['clan_id']); ?>">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="menu-dropdown" id="clan-menu-<?php echo intval($clan['clan_id']); ?>">
                                    <button data-action="edit-clan" data-clan-id="<?php echo intval($clan['clan_id']); ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button data-action="manage-members" data-clan-id="<?php echo intval($clan['clan_id']); ?>">
                                        <i class="fas fa-users"></i> Gestionar Miembros
                                    </button>
                                    <button data-action="view-details" data-clan-id="<?php echo intval($clan['clan_id']); ?>">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </button>
                                    <button data-action="delete-clan" data-clan-id="<?php echo intval($clan['clan_id']); ?>" class="danger">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clan-stats">
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span><?php echo $clan['member_count']; ?> miembros</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-project-diagram"></i>
                            <span><?php echo $clan['project_count']; ?> proyectos</span>
                        </div>
                    </div>

                    <div class="clan-footer">
                        <div class="clan-meta">
                            <span class="created-date">Creado: <?php echo date('d/m/Y', strtotime($clan['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<!-- Modal para crear/editar clan -->
<div id="clanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Crear Clan</h3>
            <button class="modal-close" data-action="close-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="clanForm" class="modal-form">
            <input type="hidden" id="clanId" name="clanId">
            
            <div class="form-group">
                <label for="clanName">Nombre del Clan *</label>
                <input type="text" id="clanName" name="clanName" required>
                <span class="error-message" id="clanNameError"></span>
            </div>
            
            <div class="form-group">
                <label for="clanDepartamento">Departamento</label>
                <input type="text" id="clanDepartamento" name="clanDepartamento" placeholder="Ej: Desarrollo, Marketing, etc.">
                <span class="error-message" id="clanDepartamentoError"></span>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" data-action="close-modal">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span id="submitText">Crear Clan</span>
                    <span id="submitLoader" class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para gestionar miembros -->
<div id="membersModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="membersModalTitle">Gestionar Miembros del Clan</h3>
            <button class="modal-close" data-action="close-members-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body" style="width: 100%;">
            <div class="form-group">
                <label for="userSelect">Agregar Usuario</label>
                <div class="user-picker">
                    <input type="text" id="userSearch" placeholder="Buscar por nombre, usuario o email" />
                    <select id="userSelect" style="height: 50px;">
                        <option value="">Seleccionar usuario</option>
                    </select>
                </div>
                <div id="selectedUserLabel" class="hint-text" style="margin-top:6px;"></div>
                <div style="margin-top:10px;">
                    <button type="button" class="btn btn-primary" data-action="add-member" id="addMemberBtn" disabled>
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
                <small id="availableCount" class="hint-text"></small>
            </div>
            
            <div id="membersList">
                <!-- Lista de miembros se cargará dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles del clan -->
<div id="clanDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="detailsModalTitle">Detalles del Clan</h3>
            <button class="modal-close" data-action="close-details-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body" id="clanDetailsContent">
            <!-- Contenido se cargará dinámicamente -->
        </div>
    </div>
</div>

<style>
/* Estilos específicos para gestión de clanes */
.clans-header {
    margin-bottom: var(--spacing-xl);
    padding: var(--spacing-lg);
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    border: 1px solid var(--bg-accent);
}

.clans-header h3 {
    font-size: 1.3rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.clans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: var(--spacing-xl);
}

.clan-card {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--bg-accent);
    transition: all var(--transition-normal);
    position: relative;
}

.clan-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.clan-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-lg);
}

.clan-info {
    flex: 1;
}

.clan-name {
    font-size: 1.2rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.clan-departamento {
    font-size: 0.9rem;
    color: var(--text-muted);
    background: var(--bg-tertiary);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
}

.clan-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.action-menu {
    position: relative;
}

.menu-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--bg-primary);
    border: 1px solid var(--bg-accent);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    min-width: 150px;
    z-index: 100;
    display: none;
}

.menu-dropdown.show {
    display: block;
}

.menu-dropdown button {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    border: none;
    background: transparent;
    color: var(--text-primary);
    text-align: left;
    cursor: pointer;
    transition: background var(--transition-normal);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.menu-dropdown button:hover {
    background: var(--bg-tertiary);
}

.menu-dropdown button.danger {
    color: var(--error);
}

.menu-dropdown button.danger:hover {
    background: var(--error);
    color: white;
}

.clan-stats {
    display: flex;
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
}

.stat-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.stat-item i {
    color: var(--primary-color);
}

.clan-footer {
    border-top: 1px solid var(--bg-accent);
    padding-top: var(--spacing-md);
}

.clan-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.created-date {
    font-weight: var(--font-weight-medium);
}

.empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    border: 1px solid var(--bg-accent);
}

.empty-content i {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: var(--spacing-lg);
}

.empty-content h3 {
    font-size: 1.5rem;
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
}

.empty-content p {
    color: var(--text-muted);
    margin-bottom: var(--spacing-xl);
}

@media (max-width: 768px) {
    .clans-grid {
        grid-template-columns: 1fr;
    }
    
    .clan-header {
        flex-direction: column;
        gap: var(--spacing-md);
    }
    
    .clan-actions {
        align-self: flex-end;
    }
    
    .clan-stats {
        flex-direction: column;
        gap: var(--spacing-sm);
    }
}
</style>

<style>
/* Mejora modal miembros - diseño minimalista y amplio */
#membersModal .modal-content {
    max-width: 820px;
    width: 92vw;
    padding-bottom: 0;
}

#membersModal .modal-header {
    padding: var(--spacing-lg) var(--spacing-xl);
}

#membersModal .modal-body {
    padding: var(--spacing-xl);
}

.user-picker {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: var(--spacing-md);
    align-items: center;
}

.user-picker input,
.user-picker select {
    height: 44px;
}

.user-picker button {
    height: 44px;
    padding: 0 var(--spacing-lg);
}

#availableCount.hint-text {
    display: block;
    margin-top: var(--spacing-sm);
    color: var(--text-muted);
}

#membersList .table-wrapper {
    margin-top: var(--spacing-lg);
}

#membersList table.data-table th,
#membersList table.data-table td {
    padding: 14px 18px;
}

/* Mejor espaciado entre filas */
#membersList .data-table tr td {
    border-bottom: 1px solid var(--bg-accent);
}

/* Botón remover más sutil */
#membersList .btn.btn-secondary {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .user-picker {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- JavaScript Inline con todas las funciones necesarias -->
<script>
(function() {
    'use strict';
    
    // Variables globales
    let currentClanId = null;
    let isEditMode = false;
    const ALL_USERS = <?php echo json_encode(array_map(function($u){
        return [
            'user_id' => (int)$u['user_id'],
            'username' => $u['username'],
            'full_name' => $u['full_name'],
            'email' => $u['email'],
            'is_active' => (int)$u['is_active'],
            'role_name' => $u['role_name']
        ];
    }, $users ?? [])); ?>;
    let availableUsersCache = [];
    
    // Sistema de gestión de clanes con event delegation
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Inicializando gestión de clanes...');
        
        // Referencias a elementos del DOM
        const clanModal = document.getElementById('clanModal');
        const membersModal = document.getElementById('membersModal');
        const detailsModal = document.getElementById('clanDetailsModal');
        const clanForm = document.getElementById('clanForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        const submitBtn = document.getElementById('submitBtn');
        const clanIdField = document.getElementById('clanId');
        
        // Función para abrir modal de crear/editar clan
        function openClanModal(isEdit = false, clanId = null) {
            if (!clanModal) {
                console.error('Modal de clan no encontrado');
                return;
            }
            
            isEditMode = isEdit;
            currentClanId = clanId;
            
            if (isEdit && clanId) {
                modalTitle.textContent = 'Editar Clan';
                submitText.textContent = 'Actualizar Clan';
                clanIdField.value = clanId;
                // TODO: Cargar datos del clan
            } else {
                modalTitle.textContent = 'Crear Clan';
                submitText.textContent = 'Crear Clan';
                if (clanForm) clanForm.reset();
                clanIdField.value = '';
            }
            
            clanModal.style.display = 'block';
            clearErrors();
        }
        
        // Función para cerrar modal de clan
        function closeClanModal() {
            if (clanModal) {
                clanModal.style.display = 'none';
                clearErrors();
                if (clanForm) clanForm.reset();
                isEditMode = false;
                currentClanId = null;
            }
        }
        
        // Función para cerrar modal de miembros
        function closeMembersModal() {
            if (membersModal) {
                membersModal.style.display = 'none';
                currentClanId = null;
            }
        }
        
        // Función para cerrar modal de detalles
        function closeDetailsModal() {
            if (detailsModal) {
                detailsModal.style.display = 'none';
            }
        }
        
        // Función para limpiar errores
        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(function(el) {
                el.classList.remove('show');
                el.textContent = '';
            });
        }
        
        // Función para mostrar errores
        function showFormErrors(errors) {
            clearErrors();
            Object.keys(errors).forEach(function(field) {
                const errorElement = document.getElementById(field + 'Error');
                if (errorElement) {
                    errorElement.textContent = errors[field];
                    errorElement.classList.add('show');
                }
            });
        }
        
        // Función para renderizar la lista de miembros
        function renderMembersList(members) {
            const membersList = document.getElementById('membersList');
            if (!membersList) return;
            
            if (!Array.isArray(members) || members.length === 0) {
                membersList.innerHTML = '<p>No hay miembros en este clan.</p>';
                return;
            }
            
            const rows = members.map(m => `
                <tr>
                    <td>${m.full_name ? m.full_name : (m.username || '-')}</td>
                    <td>${m.email || '-'}</td>
                    <td>${m.role_name || 'Sin rol'}</td>
                    <td>${m.is_active ? 'Activo' : 'Inactivo'}</td>
                    <td style="text-align:right;">
                        <button class="btn btn-secondary" data-action="remove-member" data-user-id="${m.user_id}">
                            <i class="fas fa-user-minus"></i> Remover
                        </button>
                    </td>
                </tr>
            `).join('');
            
            membersList.innerHTML = `
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            `;
        }

        // Función para cargar miembros del clan
        function loadClanMembers(clanId) {
            const membersList = document.getElementById('membersList');
            if (!membersList) return;
            membersList.innerHTML = '<p>Cargando miembros...</p>';
            
            const url = `?route=admin/clan-members&clanId=${encodeURIComponent(clanId)}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data && data.success) {
                        renderMembersList(data.members || []);
                        updateAvailableUserSelect(data.members || []);
                    } else {
                        membersList.innerHTML = `<p>${(data && data.message) ? data.message : 'Error al cargar miembros'}</p>`;
                        updateAvailableUserSelect([]);
                    }
                })
                .catch(err => {
                    console.error('Error al cargar miembros:', err);
                    membersList.innerHTML = '<p>Error de conexión al cargar miembros.</p>';
                    updateAvailableUserSelect([]);
                });
        }

        // Calcular usuarios disponibles y poblar el select
        function updateAvailableUserSelect(currentMembers) {
            const userSelect = document.getElementById('userSelect');
            const userSearch = document.getElementById('userSearch');
            const addBtn = document.getElementById('addMemberBtn');
            const availableCount = document.getElementById('availableCount');
            if (!userSelect) return;
            
            const memberIds = new Set((currentMembers || []).map(m => parseInt(m.user_id)));
            // Filtrar activos y que no estén en el clan
            availableUsersCache = (ALL_USERS || []).filter(u => u.is_active === 1 && !memberIds.has(parseInt(u.user_id)));
            
            function populate(filterText = '') {
                const text = (filterText || '').toLowerCase();
                userSelect.innerHTML = '<option value="">Seleccionar usuario</option>';
                let count = 0;
                availableUsersCache.forEach(u => {
                    const label = `${u.full_name || u.username} (@${u.username}) - ${u.email || ''}`;
                    if (!text || label.toLowerCase().includes(text)) {
                        const opt = document.createElement('option');
                        opt.value = u.user_id;
                        opt.textContent = `${label} [${u.role_name || 'sin rol'}]`;
                        opt.dataset.display = `${u.full_name || u.username}`;
                        userSelect.appendChild(opt);
                        count++;
                    }
                });
                if (availableCount) availableCount.textContent = count > 0 ? `${count} usuarios disponibles` : 'Sin usuarios disponibles';
                if (addBtn) addBtn.disabled = (userSelect.value === '');
            }
            
            populate('');
            if (userSearch) {
                userSearch.value = '';
                userSearch.oninput = () => populate(userSearch.value);
            }
            if (userSelect) {
                userSelect.onchange = () => {
                    if (addBtn) addBtn.disabled = (userSelect.value === '');
                    const selLabel = document.getElementById('selectedUserLabel');
                    const opt = userSelect.options[userSelect.selectedIndex];
                    selLabel.textContent = opt && opt.value ? `Seleccionado: ${opt.dataset.display}` : '';
                };
            }
        }
        
        // Event delegation para manejar todos los clicks
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-action]');
            if (!target) return;
            
            const action = target.dataset.action;
            const clanId = target.dataset.clanId;
            
            switch(action) {
                case 'create-clan':
                    e.preventDefault();
                    openClanModal(false);
                    break;
                    
                case 'edit-clan':
                    e.preventDefault();
                    openClanModal(true, clanId);
                    break;
                    
                case 'manage-members':
                    e.preventDefault();
                    if (clanId) {
                        currentClanId = clanId;
                        document.getElementById('membersModalTitle').textContent = 'Gestionar Miembros del Clan';
                        membersModal.style.display = 'block';
                        loadClanMembers(clanId);
                    }
                    break;
                    
                case 'view-details':
                    e.preventDefault();
                    if (clanId) {
                        // TODO: Implementar vista de detalles
                        alert('Función de ver detalles en desarrollo');
                    }
                    break;
                    
                case 'delete-clan':
                    e.preventDefault();
                    if (!clanId) return;
                    if (!confirm('¿Eliminar este clan? Esta acción no se puede deshacer.')) return;
                    const fdDel = new FormData();
                    fdDel.append('clanId', clanId);
                    fetch('?route=admin/delete-clan', { method: 'POST', body: fdDel })
                      .then(r => r.json())
                      .then(data => {
                          if (data && data.success) {
                              alert('Clan eliminado exitosamente');
                              setTimeout(() => window.location.reload(), 800);
                          } else {
                              alert((data && data.message) ? data.message : 'No se pudo eliminar el clan');
                          }
                      })
                      .catch(err => {
                          console.error('Error al eliminar clan:', err);
                          alert('Error de conexión al eliminar clan');
                      });
                    break;
                    
                case 'toggle-clan-menu':
                    e.preventDefault();
                    e.stopPropagation();
                    const menu = document.getElementById('clan-menu-' + clanId);
                    if (menu) {
                        // Cerrar otros menús
                        document.querySelectorAll('.menu-dropdown').forEach(function(m) {
                            if (m !== menu) m.classList.remove('show');
                        });
                        menu.classList.toggle('show');
                    }
                    break;
                    
                case 'close-modal':
                    e.preventDefault();
                    closeClanModal();
                    break;
                    
                case 'close-members-modal':
                    e.preventDefault();
                    closeMembersModal();
                    break;
                    
                case 'close-details-modal':
                    e.preventDefault();
                    closeDetailsModal();
                    break;
                    
                case 'add-member':
                    e.preventDefault();
                    const userSelect = document.getElementById('userSelect');
                    const userId = userSelect ? userSelect.value : '';
                    
                    if (!userId) {
                        alert('Por favor selecciona un usuario');
                        return;
                    }
                    
                    if (!currentClanId) {
                        alert('Error: Clan no seleccionado');
                        return;
                    }
                    // Ejecutar alta de miembro
                    const fd = new FormData();
                    fd.append('clanId', currentClanId);
                    fd.append('userId', userId);
                    
                    fetch('?route=admin/add-clan-member', {
                        method: 'POST',
                        body: fd
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success) {
                            // Recargar lista y limpiar selección
                            if (userSelect) userSelect.value = '';
                            loadClanMembers(currentClanId);
                        } else {
                            alert((data && data.message) ? data.message : 'Error al agregar miembro');
                        }
                    })
                    .catch(err => {
                        console.error('Error al agregar miembro:', err);
                        alert('Error de conexión al agregar miembro');
                    });
                    break;

                case 'remove-member':
                    e.preventDefault();
                    const userIdToRemove = target.dataset.userId;
                    if (!userIdToRemove || !currentClanId) return;
                    if (!confirm('¿Remover este usuario del clan?')) return;
                    
                    const fdRemove = new FormData();
                    fdRemove.append('clanId', currentClanId);
                    fdRemove.append('userId', userIdToRemove);
                    
                    fetch('?route=admin/remove-clan-member', {
                        method: 'POST',
                        body: fdRemove
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success) {
                            loadClanMembers(currentClanId);
                        } else {
                            alert((data && data.message) ? data.message : 'Error al remover miembro');
                        }
                    })
                    .catch(err => {
                        console.error('Error al remover miembro:', err);
                        alert('Error de conexión al remover miembro');
                    });
                    break;
            }
        });
        
        // Cerrar menús al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.action-menu')) {
                document.querySelectorAll('.menu-dropdown').forEach(function(menu) {
                    menu.classList.remove('show');
                });
            }
        });
        
        // Cerrar modales al hacer clic fuera
        window.addEventListener('click', function(e) {
            if (e.target === clanModal) {
                closeClanModal();
            }
            if (e.target === membersModal) {
                closeMembersModal();
            }
            if (e.target === detailsModal) {
                closeDetailsModal();
            }
        });
        
        // Función para construir la URL correcta del archivo
        function buildFileUrl(filename) {
            const currentUrl = window.location.href;
            const urlObj = new URL(currentUrl);
            
            // Extraer solo la parte del pathname sin query string
            let basePath = urlObj.pathname;
            
            // Remover la parte de query string del path
            if (basePath.includes('?')) {
                basePath = basePath.split('?')[0];
            }
            
            // Si el path termina con un archivo (como index.php), removerlo
            if (basePath.includes('.php')) {
                basePath = basePath.substring(0, basePath.lastIndexOf('/') + 1);
            }
            
            // Detectar la ruta base correcta
            let correctBasePath = '';
            
                // Si estamos en /desarrollo/rinotrack/public/, usar esa ruta
                if (basePath.includes('/desarrollo/rinotrack/public/')) {
                    correctBasePath = '/desarrollo/rinotrack/public/';
                }
                // Si estamos en /rinotrack/public/, usar esa ruta
                else if (basePath.includes('/rinotrack/public/')) {
                    correctBasePath = '/rinotrack/public/';
            }
            // Si estamos en /public/, usar esa ruta
            else if (basePath.includes('/public/')) {
                correctBasePath = '/public/';
            }
            // Si estamos en la raíz, usar /
            else if (basePath === '/' || basePath === '') {
                correctBasePath = '/';
            }
            // Por defecto, usar la ruta actual pero limpiada
            else {
                correctBasePath = basePath.endsWith('/') ? basePath : basePath + '/';
            }
            
            // Construir la URL completa
            const fileUrl = urlObj.origin + correctBasePath + filename;
            console.log('URL construida para', filename, ':', fileUrl);
            console.log('Path original:', basePath);
            console.log('Path corregido:', correctBasePath);
            
            return fileUrl;
        }
        
        // Manejar envío del formulario de clan
        if (clanForm) {
            clanForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Mostrar loader
                if (submitBtn) submitBtn.disabled = true;
                if (submitText) submitText.style.display = 'none';
                if (submitLoader) submitLoader.style.display = 'inline-block';
                
                // Preparar datos
                const formData = new FormData(clanForm);
                
                // Determinar la ruta según el modo
                const route = isEditMode ? 'admin/update-clan' : 'admin/create-clan';
                
                // Detectar la URL base correcta para los archivos
                const currentPath = window.location.pathname;
                let baseUrl = '';
                
                // Si estamos en /desarrollo/rinotrack/public/, usar /desarrollo/rinotrack/public/
                if (currentPath.includes('/desarrollo/rinotrack/public/')) {
                    baseUrl = '/desarrollo/rinotrack/public/';
                }
                // Si estamos en /rinotrack/public/, usar /rinotrack/public/
                else if (currentPath.includes('/rinotrack/public/')) {
                    baseUrl = '/rinotrack/public/';
                }
                // Si estamos en /public/, usar /public/
                else if (currentPath.includes('/public/')) {
                    baseUrl = '/public/';
                }
                // Si estamos en la raíz, usar /
                else if (currentPath === '/' || currentPath === '') {
                    baseUrl = '/';
                }
                // Por defecto, usar la ruta actual
                else {
                    baseUrl = currentPath.endsWith('/') ? currentPath : currentPath + '/';
                }
                
                // Para crear clanes, usar siempre la ruta del router (más confiable)
                let fullUrl;
                if (isEditMode) {
                    // Para editar, usar la ruta del router
                    fullUrl = baseUrl + '?route=' + route;
                } else {
                    // Para crear, usar también la ruta del router (más confiable)
                    fullUrl = baseUrl + '?route=' + route;
                }
                
                console.log('Enviando formulario a:', fullUrl);
                console.log('Datos del formulario:', Object.fromEntries(formData));
                console.log('URL base detectada:', baseUrl);
                console.log('Modo:', isEditMode ? 'edición' : 'creación');
                console.log('Ruta del archivo actual:', window.location.pathname);
                console.log('URL completa del navegador:', window.location.href);
                console.log('Usando ruta del router:', route);
                
                // Enviar petición
                fetch(fullUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(function(response) {
                    console.log('Respuesta del servidor:', response);
                    if (!response.ok) {
                        throw new Error('Error HTTP: ' + response.status);
                    }
                    return response.json();
                })
                .then(function(data) {
                    console.log('Datos recibidos:', data);
                    if (data.success) {
                        alert(data.message || 'Clan procesado exitosamente');
                        closeClanModal();
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        if (data.errors) {
                            showFormErrors(data.errors);
                        } else {
                            alert(data.message || 'Error al procesar la solicitud');
                        }
                    }
                })
                .catch(function(error) {
                    console.error('Error completo:', error);
                    alert('Error de conexión: ' + error.message);
                })
                .finally(function() {
                    // Ocultar loader
                    if (submitBtn) submitBtn.disabled = false;
                    if (submitText) submitText.style.display = 'inline';
                    if (submitLoader) submitLoader.style.display = 'none';
                });
            });
        }
        
        console.log('Gestión de clanes inicializada correctamente');
    });
})();
</script>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// Incluir el layout del admin
include __DIR__ . '/layout.php';
?> 