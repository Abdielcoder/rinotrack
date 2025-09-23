<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="clan-leader-members minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Gestionar Miembros</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name'] ?? ''); ?></span>
            </div>
            
            <div class="actions-minimal">
                <button class="btn-minimal primary" onclick="openAddMemberModal()">
                    <i class="fas fa-plus"></i>
                    Agregar Miembro
                </button>
            </div>
        </div>
        
        <!-- Búsqueda en tiempo real -->
        <div class="search-minimal">
            <div class="search-container">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" id="memberSearch" 
                           value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                           placeholder="Buscar miembros...">
                </div>
                <button type="button" id="clearSearch" class="btn-minimal secondary clear-btn" 
                        style="<?php echo empty($search) ? 'display: none;' : ''; ?>">
                    <i class="fas fa-times"></i>
                    Limpiar
                </button>
            </div>
        </div>
    </header>

    <!-- Lista de Miembros -->
    <div class="content-minimal">
        <section class="members-minimal">
            <?php if (!empty($members)): ?>
                <div class="members-list">
                    <?php foreach ($members as $member): ?>
                        <div class="member-item">
                            <div class="member-info">
                                <div class="member-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="member-details">
                                    <div class="member-name"><?php echo htmlspecialchars($member['full_name']); ?></div>
                                    <div class="member-username">@<?php echo htmlspecialchars($member['username']); ?></div>
                                    <div class="member-email"><?php echo htmlspecialchars($member['email']); ?></div>
                                    <div class="member-role">
                                        <span class="role-badge"><?php echo htmlspecialchars($member['role_name'] ?? 'Sin rol'); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="member-status">
                                <?php if ($member['is_active']): ?>
                                    <span class="status-active">Activo</span>
                                <?php else: ?>
                                    <span class="status-inactive">Inactivo</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="member-actions">
                                <?php if ($member['user_id'] != $user['user_id']): ?>
                                    <button class="btn-minimal danger" 
                                            onclick="removeMember(<?php echo $member['user_id']; ?>, '<?php echo htmlspecialchars($member['full_name']); ?>')">
                                        <i class="fas fa-user-minus"></i>
                                        Remover
                                    </button>
                                <?php else: ?>
                                    <span class="leader-badge">Líder del Clan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-minimal">
                    <span>👥 No hay miembros en el clan</span>
                    <button class="btn-minimal primary" onclick="openAddMemberModal()">Agregar primer miembro</button>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<!-- Modal para agregar miembro -->
<div id="addMemberModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-user-plus"></i>
                Agregar Miembro al Clan
            </h3>
            <button class="modal-close" onclick="closeAddMemberModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="addMemberForm" class="modal-form">
                <div class="form-group">
                    <label for="userId">
                        <i class="fas fa-user"></i>
                        Seleccionar Usuario *
                    </label>
                    <div class="select-search-container">
                        <input type="text" id="userSearch" placeholder="Buscar usuario..." class="select-search-input">
                        <select id="userId" name="userId" required style="display: none;">
                            <option value="">Seleccionar usuario...</option>
                            <!-- Se llenará dinámicamente -->
                        </select>
                        <div id="userDropdown" class="select-dropdown">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                    <small class="form-help">Escribe para buscar y selecciona un usuario para agregar al clan</small>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeAddMemberModal()">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <button type="submit" form="addMemberForm" class="btn-primary">
                <i class="fas fa-plus"></i>
                Agregar Miembro
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('memberSearch');
    const clearButton = document.getElementById('clearSearch');
    const memberItems = document.querySelectorAll('.member-item');
    const membersList = document.querySelector('.members-list');
    const emptyState = document.querySelector('.empty-minimal');
    
    // Función para filtrar miembros
    function filterMembers(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        
        memberItems.forEach(function(item) {
            const name = item.querySelector('.member-name').textContent.toLowerCase();
            const username = item.querySelector('.member-username').textContent.toLowerCase();
            const email = item.querySelector('.member-email').textContent.toLowerCase();
            
            if (term === '' || 
                name.includes(term) || 
                username.includes(term) || 
                email.includes(term)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Mostrar/ocultar estado vacío si no hay resultados
        if (visibleCount === 0 && term !== '') {
            if (emptyState) {
                emptyState.style.display = 'block';
                emptyState.innerHTML = `
                    <span>🔍 No se encontraron miembros que coincidan con "${searchTerm}"</span>
                    <button class="btn-minimal primary" onclick="clearSearch()">Limpiar búsqueda</button>
                `;
            }
            if (membersList) membersList.style.display = 'none';
        } else {
            if (emptyState) emptyState.style.display = 'none';
            if (membersList) membersList.style.display = 'block';
        }
        
        // Mostrar/ocultar botón limpiar
        if (clearButton) {
            clearButton.style.display = term !== '' ? 'block' : 'none';
        }
    }
    
    // Función para limpiar búsqueda
    function clearSearch() {
        searchInput.value = '';
        filterMembers('');
        searchInput.focus();
    }
    
    // Event listener para búsqueda en tiempo real
    searchInput.addEventListener('input', function() {
        filterMembers(this.value);
    });
    
    // Event listener para botón limpiar
    if (clearButton) {
        clearButton.addEventListener('click', clearSearch);
    }
    
    // Hacer la función clearSearch global para el botón del estado vacío
    window.clearSearch = clearSearch;
    
    // Aplicar filtro inicial si hay búsqueda previa
    if (searchInput.value) {
        filterMembers(searchInput.value);
    }
    
    // Funcionalidad del buscador de usuarios en el modal
    const userSearchInput = document.getElementById('userSearch');
    const userDropdown = document.getElementById('userDropdown');
    const userIdSelect = document.getElementById('userId');
    let allUsers = [];
    let selectedUserId = null;
    
    // Función para cargar usuarios disponibles
    function loadAvailableUsers() {
        fetch('?route=clan_leader/getAvailableUsers')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allUsers = data.users;
                    renderUserDropdown(allUsers);
                } else {
                    console.error('Error al cargar usuarios:', data.message);
                }
            })
            .catch(error => {
                console.error('Error de red:', error);
            });
    }
    
    // Función para renderizar el dropdown de usuarios
    function renderUserDropdown(users) {
        userDropdown.innerHTML = '';
        
        if (users.length === 0) {
            userDropdown.innerHTML = '<div class="dropdown-item" style="color: #6b7280; cursor: default;">No hay usuarios disponibles</div>';
            return;
        }
        
        users.forEach(user => {
            const item = document.createElement('div');
            item.className = 'dropdown-item';
            item.dataset.userId = user.user_id;
            item.innerHTML = `
                <div class="user-info">
                    <div class="user-name">${user.full_name}</div>
                    <div class="user-email">${user.email}</div>
                </div>
            `;
            
            item.addEventListener('click', function() {
                selectUser(user);
            });
            
            userDropdown.appendChild(item);
        });
    }
    
    // Función para seleccionar un usuario
    function selectUser(user) {
        selectedUserId = user.user_id;
        userSearchInput.value = `${user.full_name} (${user.email})`;
        userDropdown.classList.remove('show');
        
        // Actualizar el select oculto
        userIdSelect.value = user.user_id;
        
        // Marcar como seleccionado en el dropdown
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.remove('selected');
            if (item.dataset.userId == user.user_id) {
                item.classList.add('selected');
            }
        });
    }
    
    // Event listeners para el buscador de usuarios
    userSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        if (searchTerm.length === 0) {
            renderUserDropdown(allUsers);
            selectedUserId = null;
            userIdSelect.value = '';
        } else if (selectedUserId && this.value.includes('(')) {
            // Si el usuario ya está seleccionado, no filtrar
            return;
        } else {
            // Filtrar usuarios
            const filteredUsers = allUsers.filter(user => 
                user.full_name.toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm) ||
                user.username.toLowerCase().includes(searchTerm)
            );
            renderUserDropdown(filteredUsers);
        }
        
        userDropdown.classList.add('show');
    });
    
    userSearchInput.addEventListener('focus', function() {
        if (allUsers.length > 0) {
            userDropdown.classList.add('show');
        }
    });
    
    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.select-search-container')) {
            userDropdown.classList.remove('show');
        }
    });
    
    // Cargar usuarios cuando se abre el modal
    window.openAddMemberModal = function() {
        document.getElementById('addMemberModal').style.display = 'flex';
        loadAvailableUsers();
    };
    
    window.closeAddMemberModal = function() {
        document.getElementById('addMemberModal').style.display = 'none';
        userSearchInput.value = '';
        userIdSelect.value = '';
        selectedUserId = null;
        userDropdown.classList.remove('show');
    };
});
</script>

<style>
/* Estilos para la búsqueda en tiempo real */
.search-container {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
}

.search-input {
    flex: 1;
    position: relative;
}

.clear-btn {
    white-space: nowrap;
    flex-shrink: 0;
}

/* Asegurar que el input ocupe el espacio disponible */
.search-input input {
    width: 100%;
    box-sizing: border-box;
}

/* Responsive: en pantallas pequeñas, apilar verticalmente */
@media (max-width: 768px) {
    .search-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .clear-btn {
        align-self: flex-start;
    }
}

/* Estilos del Modal - Copiados del dashboard principal */
.modal-overlay {
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
    backdrop-filter: blur(4px);
}

.modal-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow: hidden;
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    color: #374151;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-close {
    background: none;
    border: none;
    color: #6b7280;
    font-size: 18px;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #374151;
    transform: scale(1.1);
}

.modal-body {
    padding: 24px;
    max-height: 60vh;
    overflow-y: auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-group select,
.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: white;
    box-sizing: border-box;
}

.form-group select:focus,
.form-group input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-help {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #6b7280;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    background: #f9fafb;
}

/* Estilos de botones iguales al dashboard principal */
.btn-primary,
.btn-secondary {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #1e3a8a;
    color: white;
}

.btn-primary:hover {
    background: #1e3a8a;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(30, 58, 138, 0.3);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

/* Estilos para el buscador de usuarios */
.select-search-container {
    position: relative;
    width: 100%;
}

.select-search-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: white;
    box-sizing: border-box;
}

.select-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.select-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #e5e7eb;
    border-top: none;
    border-radius: 0 0 8px 8px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.select-dropdown.show {
    display: block;
}

.dropdown-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background-color 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8fafc;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-item.selected {
    background-color: #3b82f6;
    color: white;
}

.user-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.user-name {
    font-weight: 500;
    font-size: 14px;
}

.user-email {
    font-size: 12px;
    color: #6b7280;
}

.dropdown-item.selected .user-email {
    color: #e5e7eb;
}

/* Responsive para Modal */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .modal-header, .modal-body, .modal-footer {
        padding: 16px 20px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .action-btn {
        justify-content: center;
    }
}
</style>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para Clan Leader
$additionalCSS = [
    APP_URL . 'assets/css/clan-leader.css'
];

// JavaScript adicional para Clan Leader  
$additionalJS = [
    APP_URL . 'assets/js/clan-leader.js'
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?> 