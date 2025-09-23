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
                                    <?php if (!empty($member['avatar_path']) && $member['avatar_path'] !== ''): ?>
                                        <img src="<?php echo APP_URL . htmlspecialchars($member['avatar_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($member['full_name']); ?>" 
                                             class="avatar-image"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="avatar-initials" style="display: none;" data-user-id="<?php echo $member['user_id']; ?>">
                                            <?php echo strtoupper(substr($member['full_name'], 0, 1)); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="avatar-initials" data-user-id="<?php echo $member['user_id']; ?>">
                                            <?php echo strtoupper(substr($member['full_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
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
                        <select id="userId" name="userId" style="display: none;">
                        <option value="">Seleccionar usuario...</option>
                        <!-- Se llenará dinámicamente -->
                    </select>
                        <div id="userDropdown" class="select-dropdown">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                    <small class="form-help">Escribe para buscar, haz clic en un usuario o presiona Enter para seleccionar</small>
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

<!-- Modal de Notificación Personalizado -->
<div id="customNotificationModal" class="custom-notification-overlay" style="display: none;">
    <div class="custom-notification-content">
        <div class="custom-notification-header">
            <span id="notificationTitle">Notificación</span>
        </div>
        <div class="custom-notification-body">
            <div class="custom-notification-icon">
                <i id="notificationIcon" class="fas fa-info-circle"></i>
            </div>
            <div class="custom-notification-message">
                <span id="notificationMessage">Mensaje</span>
            </div>
        </div>
        <div class="custom-notification-footer">
            <button type="button" class="custom-notification-btn custom-notification-btn-primary" onclick="closeCustomNotification()">
                Aceptar
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variable global para la URL de la aplicación
    const APP_URL = '<?php echo APP_URL; ?>';
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
        console.log('Cargando usuarios disponibles...');
        
        // Mostrar estado de carga
        userDropdown.innerHTML = '<div class="dropdown-item" style="color: #6b7280; cursor: default;"><i class="fas fa-spinner fa-spin"></i> Cargando usuarios...</div>';
        userDropdown.classList.add('show');
        
        fetch('?route=clan_leader/get-available-users')
            .then(response => {
                console.log('Respuesta del servidor:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                if (data.success) {
                    allUsers = data.users;
                    console.log('Usuarios cargados:', allUsers.length);
                    renderUserDropdown(allUsers);
                } else {
                    console.error('Error al cargar usuarios:', data.message);
                    userDropdown.innerHTML = '<div class="dropdown-item" style="color: #ef4444; cursor: default;"><i class="fas fa-exclamation-triangle"></i> ' + (data.message || 'Error al cargar usuarios') + '</div>';
                    userDropdown.classList.add('show');
                }
            })
            .catch(error => {
                console.error('Error de red:', error);
                userDropdown.innerHTML = '<div class="dropdown-item" style="color: #ef4444; cursor: default;"><i class="fas fa-wifi"></i> Error de conexión</div>';
                userDropdown.classList.add('show');
            });
    }
    
    // Función para renderizar el dropdown de usuarios
    function renderUserDropdown(users) {
        console.log('Renderizando dropdown con', users.length, 'usuarios');
        userDropdown.innerHTML = '';
        
        if (!users || users.length === 0) {
            userDropdown.innerHTML = '<div class="dropdown-item" style="color: #6b7280; cursor: default;">No hay usuarios disponibles</div>';
            userDropdown.classList.add('show');
            return;
        }
        
        users.forEach(user => {
            const item = document.createElement('div');
            item.className = 'dropdown-item';
            item.dataset.userId = user.user_id;
            
            // Generar inicial (solo primera letra)
            const fullName = user.full_name || 'Sin nombre';
            const initial = fullName.charAt(0).toUpperCase();
            
            // Crear avatar
            let avatarHtml = '';
            if (user.avatar_path && user.avatar_path !== '') {
                avatarHtml = `
                    <img src="${APP_URL}${user.avatar_path}"
                         alt="${fullName}"
                         class="dropdown-avatar-image"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="dropdown-avatar-initials" style="display: none;" data-user-id="${user.user_id}">
                        ${initial}
                    </div>
                `;
            } else {
                avatarHtml = `
                    <div class="dropdown-avatar-initials" data-user-id="${user.user_id}">
                        ${initial}
                    </div>
                `;
            }
            
            item.innerHTML = `
                <div class="dropdown-user-avatar">
                    ${avatarHtml}
                </div>
                <div class="user-info">
                    <div class="user-name">${fullName}</div>
                    <div class="user-email">${user.email || 'Sin email'}</div>
                </div>
            `;
            
            item.addEventListener('click', function() {
                selectUser(user);
            });
            
            userDropdown.appendChild(item);
        });
        
        userDropdown.classList.add('show');
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
        const searchTerm = this.value.toLowerCase().trim();
        console.log('Buscando:', searchTerm);
        
        if (searchTerm.length === 0) {
            renderUserDropdown(allUsers);
            selectedUserId = null;
            userIdSelect.value = '';
        } else if (selectedUserId && this.value.includes('(')) {
            // Si el usuario ya está seleccionado, no filtrar
            return;
        } else {
            // Filtrar usuarios
            const filteredUsers = allUsers.filter(user => {
                const fullName = (user.full_name || '').toLowerCase();
                const email = (user.email || '').toLowerCase();
                const username = (user.username || '').toLowerCase();
                
                return fullName.includes(searchTerm) ||
                       email.includes(searchTerm) ||
                       username.includes(searchTerm);
            });
            
            console.log('Usuarios filtrados:', filteredUsers.length);
            
            // Si hay exactamente un usuario que coincide perfectamente, seleccionarlo automáticamente
            if (filteredUsers.length === 1) {
                const user = filteredUsers[0];
                const fullName = (user.full_name || '').toLowerCase();
                const email = (user.email || '').toLowerCase();
                
                // Verificar si el término de búsqueda coincide exactamente con el nombre o email
                if (fullName === searchTerm || email === searchTerm || 
                    fullName.includes(searchTerm) && searchTerm.length >= 3) {
                    console.log('Selección automática de usuario:', user.full_name);
                    selectUser(user);
                    return;
                }
            }
            
            renderUserDropdown(filteredUsers);
        }
    });
    
    userSearchInput.addEventListener('focus', function() {
        if (allUsers.length > 0) {
            userDropdown.classList.add('show');
        } else {
            // Mostrar estado de carga si aún no se han cargado los usuarios
            userDropdown.innerHTML = '<div class="dropdown-item" style="color: #6b7280; cursor: default;">Cargando usuarios...</div>';
            userDropdown.classList.add('show');
        }
    });
    
    // Manejar la tecla Enter para seleccionar el primer usuario disponible
    userSearchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            
            // Buscar usuarios que coincidan con el término actual
            const searchTerm = this.value.toLowerCase().trim();
            const filteredUsers = allUsers.filter(user => {
                const fullName = (user.full_name || '').toLowerCase();
                const email = (user.email || '').toLowerCase();
                const username = (user.username || '').toLowerCase();
                
                return fullName.includes(searchTerm) ||
                       email.includes(searchTerm) ||
                       username.includes(searchTerm);
            });
            
            // Si hay usuarios filtrados, seleccionar el primero
            if (filteredUsers.length > 0) {
                selectUser(filteredUsers[0]);
            } else if (searchTerm.length >= 3) {
                // Si no hay coincidencias pero el término es largo, buscar coincidencias parciales
                const partialMatches = allUsers.filter(user => {
                    const fullName = (user.full_name || '').toLowerCase();
                    const email = (user.email || '').toLowerCase();
                    
                    return fullName.startsWith(searchTerm) || email.startsWith(searchTerm);
                });
                
                if (partialMatches.length > 0) {
                    selectUser(partialMatches[0]);
                } else {
                    showCustomNotification('No se encontró un usuario que coincida con "' + searchTerm + '"', 'warning', 'Usuario no encontrado');
                }
            } else {
                showCustomNotification('Escribe al menos 3 caracteres para buscar usuarios', 'info', 'Búsqueda requerida');
            }
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
        console.log('Abriendo modal de agregar miembro...');
        document.getElementById('addMemberModal').style.display = 'flex';
        
        // Resetear el estado
        userSearchInput.value = '';
        userIdSelect.value = '';
        selectedUserId = null;
        allUsers = [];
        userDropdown.innerHTML = '';
        userDropdown.classList.remove('show');
        
        // Cargar usuarios
        loadAvailableUsers();
    };
    
    window.closeAddMemberModal = function() {
        document.getElementById('addMemberModal').style.display = 'none';
        userSearchInput.value = '';
        userIdSelect.value = '';
        selectedUserId = null;
        userDropdown.classList.remove('show');
    };
    
    // Funciones para el modal de notificación personalizado
    window.showCustomNotification = function(message, type = 'info', title = 'Notificación') {
        const modal = document.getElementById('customNotificationModal');
        const titleElement = document.getElementById('notificationTitle');
        const messageElement = document.getElementById('notificationMessage');
        const iconElement = document.getElementById('notificationIcon');
        
        // Configurar el título
        titleElement.textContent = title;
        
        // Configurar el mensaje
        messageElement.textContent = message;
        
        // Configurar el icono según el tipo
        const iconContainer = iconElement.parentElement;
        iconContainer.className = 'custom-notification-icon ' + type;
        
        switch(type) {
            case 'success':
                iconElement.className = 'fas fa-check-circle';
                titleElement.textContent = '¡Éxito!';
                break;
            case 'error':
                iconElement.className = 'fas fa-exclamation-circle';
                titleElement.textContent = 'Error';
                break;
            case 'warning':
                iconElement.className = 'fas fa-exclamation-triangle';
                titleElement.textContent = 'Advertencia';
                break;
            case 'info':
            default:
                iconElement.className = 'fas fa-info-circle';
                titleElement.textContent = 'Información';
                break;
        }
        
        // Mostrar el modal
        modal.style.display = 'flex';
    };
    
    window.closeCustomNotification = function() {
        const modal = document.getElementById('customNotificationModal');
        const title = document.getElementById('notificationTitle').textContent;
        
        // Si es un mensaje de éxito, cerrar también el modal de agregar miembro y recargar
        if (title === '¡Éxito!') {
            closeAddMemberModal();
            // Recargar la página para mostrar el nuevo miembro
            window.location.reload();
        }
        
        modal.style.display = 'none';
    };
    
    // Cerrar modal de notificación con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const notificationModal = document.getElementById('customNotificationModal');
            if (notificationModal.style.display === 'flex') {
                closeCustomNotification();
            }
        }
    });
    
    // Validación personalizada del formulario
    document.getElementById('addMemberForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Validando formulario de agregar miembro...');
        console.log('selectedUserId:', selectedUserId);
        console.log('userIdSelect.value:', userIdSelect.value);
        console.log('userSearchInput.value:', userSearchInput.value);
        
        // Validar que se haya seleccionado un usuario
        if (!selectedUserId || userIdSelect.value === '') {
            // Intentar encontrar el usuario basado en el texto del input
            const searchTerm = userSearchInput.value.trim();
            if (searchTerm.length >= 3) {
                const foundUser = allUsers.find(user => {
                    const fullName = (user.full_name || '').toLowerCase();
                    const email = (user.email || '').toLowerCase();
                    const displayText = `${user.full_name} (${user.email})`.toLowerCase();
                    
                    return fullName === searchTerm.toLowerCase() ||
                           email === searchTerm.toLowerCase() ||
                           displayText === searchTerm.toLowerCase() ||
                           fullName.includes(searchTerm.toLowerCase());
                });
                
                if (foundUser) {
                    console.log('Usuario encontrado automáticamente:', foundUser.full_name);
                    selectUser(foundUser);
                    // Continuar con el envío del formulario
                } else {
                    showCustomNotification('Por favor selecciona un usuario válido de la lista o escribe al menos 3 caracteres y presiona Enter', 'warning', 'Usuario requerido');
                    userSearchInput.focus();
                    return false;
                }
            } else {
                showCustomNotification('Por favor selecciona un usuario para agregar al clan o escribe al menos 3 caracteres y presiona Enter', 'warning', 'Usuario requerido');
                userSearchInput.focus();
                return false;
            }
        }
        
        console.log('Enviando formulario con userId:', selectedUserId);
        console.log('userIdSelect.value:', userIdSelect.value);
        
        // Preparar los datos del formulario
        const formData = new FormData();
        formData.append('userId', selectedUserId);
        
        // También asegurar que el select oculto tenga el valor correcto
        userIdSelect.value = selectedUserId;
        
        console.log('Datos a enviar:', {
            userId: selectedUserId,
            userIdSelectValue: userIdSelect.value,
            url: '?route=clan_leader/add-member'
        });
        
        // Verificar que los datos se están enviando correctamente
        for (let pair of formData.entries()) {
            console.log('FormData:', pair[0] + ': ' + pair[1]);
        }
        
        fetch('?route=clan_leader/add-member', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Respuesta del servidor:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (data.success) {
                showCustomNotification('Usuario agregado al clan exitosamente', 'success', '¡Éxito!');
                // El modal se cerrará manualmente cuando el usuario haga clic en Aceptar
            } else {
                showCustomNotification('Error al agregar usuario: ' + (data.message || 'Error desconocido'), 'error', 'Error');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            showCustomNotification('Error de conexión al agregar usuario: ' + error.message, 'error', 'Error de conexión');
        });
    });
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
    display: flex;
    align-items: center;
    gap: 12px;
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

/* Estilos para avatares en el dropdown */
.dropdown-user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    position: relative;
    flex-shrink: 0;
}

.dropdown-avatar-image {
    width: 110%;
    height: 110%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
    margin: -5%;
}

.dropdown-avatar-initials {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    font-weight: 700;
    font-size: 18px;
    border-radius: 50%;
    text-transform: uppercase;
    letter-spacing: 0;
}

/* Colores específicos por user_id para las iniciales del dropdown (mismos que los avatares principales) */
.dropdown-avatar-initials[data-user-id="1"] { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.dropdown-avatar-initials[data-user-id="2"] { background: linear-gradient(135deg, #10b981, #059669); }
.dropdown-avatar-initials[data-user-id="4"] { background: linear-gradient(135deg, #f59e0b, #d97706); }
.dropdown-avatar-initials[data-user-id="5"] { background: linear-gradient(135deg, #ef4444, #dc2626); }
.dropdown-avatar-initials[data-user-id="6"] { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.dropdown-avatar-initials[data-user-id="9"] { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.dropdown-avatar-initials[data-user-id="10"] { background: linear-gradient(135deg, #84cc16, #65a30d); }
.dropdown-avatar-initials[data-user-id="11"] { background: linear-gradient(135deg, #f97316, #ea580c); }
.dropdown-avatar-initials[data-user-id="12"] { background: linear-gradient(135deg, #ec4899, #db2777); }
.dropdown-avatar-initials[data-user-id="13"] { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.dropdown-avatar-initials[data-user-id="14"] { background: linear-gradient(135deg, #a855f7, #9333ea); }
.dropdown-avatar-initials[data-user-id="15"] { background: linear-gradient(135deg, #f59e0b, #d97706); }
.dropdown-avatar-initials[data-user-id="16"] { background: linear-gradient(135deg, #ef4444, #dc2626); }
.dropdown-avatar-initials[data-user-id="17"] { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.dropdown-avatar-initials[data-user-id="18"] { background: linear-gradient(135deg, #10b981, #059669); }
.dropdown-avatar-initials[data-user-id="19"] { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.dropdown-avatar-initials[data-user-id="20"] { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.dropdown-avatar-initials[data-user-id="21"] { background: linear-gradient(135deg, #84cc16, #65a30d); }
.dropdown-avatar-initials[data-user-id="22"] { background: linear-gradient(135deg, #f97316, #ea580c); }
.dropdown-avatar-initials[data-user-id="23"] { background: linear-gradient(135deg, #ec4899, #db2777); }
.dropdown-avatar-initials[data-user-id="24"] { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.dropdown-avatar-initials[data-user-id="25"] { background: linear-gradient(135deg, #a855f7, #9333ea); }
.dropdown-avatar-initials[data-user-id="26"] { background: linear-gradient(135deg, #f59e0b, #d97706); }
.dropdown-avatar-initials[data-user-id="27"] { background: linear-gradient(135deg, #ef4444, #dc2626); }
.dropdown-avatar-initials[data-user-id="28"] { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.dropdown-avatar-initials[data-user-id="29"] { background: linear-gradient(135deg, #10b981, #059669); }
.dropdown-avatar-initials[data-user-id="30"] { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

/* Estilos para el spinner de carga */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Estilos para el Modal de Notificación Personalizado */
.custom-notification-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(4px);
}

.custom-notification-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 400px;
    overflow: hidden;
    animation: notificationFadeIn 0.3s ease-out;
}

@keyframes notificationFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.custom-notification-header {
    padding: 20px 24px 0;
    text-align: center;
}

.custom-notification-header span {
    font-size: 18px;
    font-weight: 600;
    color: #374151;
}

.custom-notification-body {
    padding: 20px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.custom-notification-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.custom-notification-icon.success {
    background: linear-gradient(135deg, #10b981, #059669);
}

.custom-notification-icon.error {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.custom-notification-icon.warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.custom-notification-icon.info {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.custom-notification-message {
    color: #374151;
    font-size: 16px;
    line-height: 1.5;
    font-weight: 500;
}

.custom-notification-footer {
    padding: 0 24px 24px;
    display: flex;
    justify-content: center;
}

.custom-notification-btn {
    padding: 12px 32px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    min-width: 120px;
}

.custom-notification-btn-primary {
    background: #1e3a8a;
    color: white;
}

.custom-notification-btn-primary:hover {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(30, 58, 138, 0.3);
}

.custom-notification-btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.custom-notification-btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

/* Estilos para avatares */
.member-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    position: relative;
}

.avatar-image {
    width: 110%;
    height: 110%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
    margin: -5%;
}

.avatar-initials {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    font-weight: 700;
    font-size: 20px;
    border-radius: 50%;
    text-transform: uppercase;
    letter-spacing: 0;
}

/* Colores específicos por user_id para las iniciales */
.avatar-initials[data-user-id="1"] { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.avatar-initials[data-user-id="2"] { background: linear-gradient(135deg, #10b981, #059669); }
.avatar-initials[data-user-id="4"] { background: linear-gradient(135deg, #f59e0b, #d97706); }
.avatar-initials[data-user-id="5"] { background: linear-gradient(135deg, #ef4444, #dc2626); }
.avatar-initials[data-user-id="6"] { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.avatar-initials[data-user-id="9"] { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.avatar-initials[data-user-id="10"] { background: linear-gradient(135deg, #84cc16, #65a30d); }
.avatar-initials[data-user-id="11"] { background: linear-gradient(135deg, #f97316, #ea580c); }
.avatar-initials[data-user-id="12"] { background: linear-gradient(135deg, #ec4899, #db2777); }
.avatar-initials[data-user-id="13"] { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.avatar-initials[data-user-id="14"] { background: linear-gradient(135deg, #a855f7, #9333ea); }
.avatar-initials[data-user-id="15"] { background: linear-gradient(135deg, #f59e0b, #d97706); }
.avatar-initials[data-user-id="16"] { background: linear-gradient(135deg, #ef4444, #dc2626); }
.avatar-initials[data-user-id="17"] { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.avatar-initials[data-user-id="18"] { background: linear-gradient(135deg, #10b981, #059669); }
.avatar-initials[data-user-id="19"] { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.avatar-initials[data-user-id="20"] { background: linear-gradient(135deg, #06b6d4, #0891b2); }

/* Colores adicionales para más usuarios */
.avatar-initials[data-user-id="21"] { background: linear-gradient(135deg, #84cc16, #65a30d); }
.avatar-initials[data-user-id="22"] { background: linear-gradient(135deg, #f97316, #ea580c); }
.avatar-initials[data-user-id="23"] { background: linear-gradient(135deg, #ec4899, #db2777); }
.avatar-initials[data-user-id="24"] { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.avatar-initials[data-user-id="25"] { background: linear-gradient(135deg, #a855f7, #9333ea); }
.avatar-initials[data-user-id="26"] { background: linear-gradient(135deg, #f59e0b, #d97706); }
.avatar-initials[data-user-id="27"] { background: linear-gradient(135deg, #ef4444, #dc2626); }
.avatar-initials[data-user-id="28"] { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.avatar-initials[data-user-id="29"] { background: linear-gradient(135deg, #10b981, #059669); }
.avatar-initials[data-user-id="30"] { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

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
    
    .member-avatar {
        width: 50px;
        height: 50px;
    }
    
    .avatar-initials {
        font-size: 18px;
    }
    
    .dropdown-user-avatar {
        width: 40px;
        height: 40px;
    }
    
    .dropdown-avatar-initials {
        font-size: 16px;
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