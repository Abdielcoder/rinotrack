<?php
// Iniciar buffer de salida
ob_start();

// Configurar variables para el layout
$title = 'Gestión de Usuarios - ' . APP_NAME;

// JavaScript que debe estar disponible inmediatamente
$additionalJS = [];
$additionalCSS = [];

// Agregar JavaScript inline al head
$inlineJS = '<script>
// JavaScript para gestión de usuarios
let currentUserId = null;
let isEditMode = false;

window.openCreateUserModal = function() {
    isEditMode = false;
    currentUserId = null;
    document.getElementById("modalTitle").textContent = "Crear Usuario";
    document.getElementById("submitText").textContent = "Crear Usuario";
    document.getElementById("userForm").reset();
    document.getElementById("userId").value = "";
    document.getElementById("passwordGroup").style.display = "block";
    document.getElementById("password").required = true;
    document.getElementById("userModal").style.display = "block";
};

window.closeUserModal = function() {
    document.getElementById("userModal").style.display = "none";
    clearErrors();
};

window.editUser = function(userId) {
    isEditMode = true;
    currentUserId = userId;
    document.getElementById("modalTitle").textContent = "Editar Usuario";
    document.getElementById("submitText").textContent = "Actualizar Usuario";
    document.getElementById("userId").value = userId;
    document.getElementById("passwordGroup").style.display = "none";
    document.getElementById("password").required = false;
    
    // Cargar datos del usuario desde la tabla
    const userRow = document.querySelector("tr:has(button[onclick=\"editUser(" + userId + ")\"])");
    if (userRow) {
        const cells = userRow.querySelectorAll("td");
        
        // Extraer datos de la fila
        const username = cells[0].querySelector(".username").textContent;
        const email = cells[1].textContent;
        const fullName = cells[2].textContent !== "-" ? cells[2].textContent : "";
        const roleBadge = cells[3].querySelector(".role-badge");
        const isActive = cells[4].querySelector(".status-badge").classList.contains("active");
        
        // Llenar el formulario
        document.getElementById("username").value = username;
        document.getElementById("email").value = email;
        document.getElementById("fullName").value = fullName;
        document.getElementById("isActive").checked = isActive;
        
        // Buscar el rol en el select
        const roleText = roleBadge.textContent.trim();
        const roleSelect = document.getElementById("roleId");
        for (let option of roleSelect.options) {
            if (option.textContent.trim() === roleText) {
                roleSelect.value = option.value;
                break;
            }
        }
    }
    
    document.getElementById("userModal").style.display = "block";
};

window.searchUsers = function() {
    const searchTerm = document.getElementById("searchInput").value;
    const url = new URL(window.location);
    if (searchTerm) {
        url.searchParams.set("search", searchTerm);
    } else {
        url.searchParams.delete("search");
    }
    window.location.href = url.toString();
};

window.toggleUserStatus = function(userId) {
    if (confirm("¿Estás seguro de que quieres cambiar el estado de este usuario?")) {
        const formData = new FormData();
        formData.append("userId", userId);
        
        fetch("?route=admin/toggle-user-status", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Estado del usuario actualizado exitosamente");
                location.reload();
            } else {
                alert(data.message || "Error al actualizar el estado del usuario");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error de conexión al actualizar el estado del usuario");
        });
    }
};

function clearErrors() {
    const errorElements = document.querySelectorAll(".error-message");
    errorElements.forEach(element => {
        element.classList.remove("show");
        element.textContent = "";
    });
}

// Event listeners cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function() {
    // Buscar al presionar Enter
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                searchUsers();
            }
        });
    }

    // Manejar envío del formulario
    const userForm = document.getElementById("userForm");
    if (userForm) {
        userForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById("submitBtn");
            const submitText = document.getElementById("submitText");
            const submitLoader = document.getElementById("submitLoader");
            
            // Mostrar loader
            submitBtn.disabled = true;
            submitText.style.display = "none";
            submitLoader.style.display = "inline-block";
            
            const formData = new FormData(this);
            const url = isEditMode ? "?route=admin/update-user" : "?route=admin/create-user";
            
            fetch(url, {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeUserModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    if (data.errors) {
                        // Mostrar errores específicos del formulario
                        let errorMessage = "Errores de validación:\n";
                        Object.keys(data.errors).forEach(field => {
                            errorMessage += `- ${data.errors[field]}\n`;
                        });
                        alert(errorMessage);
                    } else {
                        alert(data.message || "Error al procesar la solicitud");
                    }
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error de conexión: " + error.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitText.style.display = "inline";
                submitLoader.style.display = "none";
            });
        });
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
        const userModal = document.getElementById("userModal");
        if (event.target === userModal) {
            closeUserModal();
        }
    };

    window.deleteUser = function(userId) {
        if (!confirm('¿Deseas eliminar definitivamente este usuario? Esta acción no se puede deshacer.')) return;
        const formData = new FormData();
        formData.append('userId', userId);
        fetch('?route=admin/delete-user', { method: 'POST', body: formData })
            .then(r=>r.json())
            .then(data=>{
                if (data && data.success) {
                    alert('Usuario eliminado');
                    location.reload();
                } else {
                    alert((data && data.message) ? data.message : 'Error al eliminar');
                }
            })
            .catch(err=>{ console.error('deleteUser error:', err); alert('Error de conexión'); });
    };
});
</script>';

// Agregar el JavaScript al head
$additionalJS[] = $inlineJS;
?>

<div class="modern-dashboard" data-theme="default">
    <!-- Menú de navegación moderno -->
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-users"></i>
                </div>
                <span class="brand-text">Gestión de Usuarios</span>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="?route=admin" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item active">
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
                <li class="nav-item">
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
        <!-- Header con búsqueda -->
        <header class="page-header animate-fade-in">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-users"></i>
                    Gestión de Usuarios
                </h1>
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Buscar usuarios..." 
                               value="<?php echo Utils::escape($search); ?>" class="search-input">
                        <button class="search-btn" onclick="searchUsers()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <button class="btn btn-primary" onclick="openCreateUserModal()">
                        <i class="fas fa-user-plus"></i>
                        Crear Usuario
                    </button>
                </div>
            </div>
        </header>

        <!-- Tabla de usuarios -->
        <section class="content-section animate-fade-in">
            <div class="table-container">
                <div class="table-header">
                    <h3>Lista de Usuarios (<?php echo count($users ?? []); ?>)</h3>
                </div>
                
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Nombre Completo</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Último Login</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users ?? [])): ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-content">
                                        <i class="fas fa-users"></i>
                                        <p>No se encontraron usuarios</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($users as $userItem): ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-sm">
                                            <?php echo strtoupper(substr($userItem['full_name'] ?: $userItem['username'], 0, 1)); ?>
                                        </div>
                                        <span class="username"><?php echo Utils::escape($userItem['username']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo Utils::escape($userItem['email']); ?></td>
                                <td><?php echo Utils::escape($userItem['full_name'] ?: '-'); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $userItem['role_name'] ?? 'none'; ?>">
                                        <?php 
                                        $roleModel = new Role();
                                        echo $roleModel->getDisplayName($userItem['role_name'] ?? 'Sin rol'); 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $userItem['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $userItem['is_active'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if ($userItem['last_login']) {
                                        echo date('d/m/Y H:i', strtotime($userItem['last_login']));
                                    } else {
                                        echo 'Nunca';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon btn-edit" 
                                                onclick="editUser(<?php echo $userItem['user_id']; ?>)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-toggle" 
                                                onclick="toggleUserStatus(<?php echo $userItem['user_id']; ?>)"
                                                title="<?php echo $userItem['is_active'] ? 'Desactivar' : 'Activar'; ?>">
                                            <i class="fas fa-<?php echo $userItem['is_active'] ? 'ban' : 'check'; ?>"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" 
                                                onclick="deleteUser(<?php echo $userItem['user_id']; ?>)"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Modal para crear/editar usuario -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Crear Usuario</h3>
            <button class="modal-close" onclick="closeUserModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="userForm" class="modal-form">
            <input type="hidden" id="userId" name="userId">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="username">Nombre de Usuario *</label>
                    <input type="text" id="username" name="username" required>
                    <span class="error-message" id="usernameError"></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-message" id="emailError"></span>
                </div>
                
                <div class="form-group">
                    <label for="fullName">Nombre Completo *</label>
                    <input type="text" id="fullName" name="fullName" required>
                    <span class="error-message" id="fullNameError"></span>
                </div>
                
                <div class="form-group">
                    <label for="roleId">Rol *</label>
                    <select id="roleId" name="roleId" required>
                        <option value="">Seleccionar rol</option>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['role_id']; ?>">
                            <?php 
                            $roleModel = new Role();
                            echo $roleModel->getDisplayName($role['role_name']); 
                            ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error-message" id="roleIdError"></span>
                </div>
                
                <div class="form-group" id="passwordGroup">
                    <label for="password">Contraseña *</label>
                    <input type="password" id="password" name="password">
                    <span class="error-message" id="passwordError"></span>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="isActive" name="isActive" checked>
                        <span class="checkmark"></span>
                        Usuario activo
                    </label>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeUserModal()">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span id="submitText">Crear Usuario</span>
                    <span id="submitLoader" class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Estilos específicos para gestión de usuarios */
.page-header {
    margin-bottom: var(--spacing-2xl);
    padding: var(--spacing-xl);
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--bg-accent);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-lg);
}

.page-title {
    font-size: 2rem;
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.search-box {
    display: flex;
    align-items: center;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    border: 1px solid var(--bg-accent);
}

.search-input {
    border: none;
    background: transparent;
    padding: var(--spacing-sm);
    font-size: 0.9rem;
    color: var(--text-primary);
    width: 250px;
    outline: none;
}

.search-btn {
    background: var(--primary-color);
    border: none;
    padding: var(--spacing-sm);
    border-radius: var(--radius-sm);
    color: var(--text-white);
    cursor: pointer;
    transition: all var(--transition-normal);
}

.search-btn:hover {
    background: var(--primary-dark);
}

.table-container {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--bg-accent);
    overflow: hidden;
}

.table-header {
    padding: var(--spacing-xl);
    border-bottom: 2px solid var(--bg-tertiary);
}

.table-header h3 {
    font-size: 1.3rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: var(--bg-tertiary);
    padding: var(--spacing-lg);
    text-align: left;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    border-bottom: 1px solid var(--bg-accent);
}

.data-table td {
    padding: var(--spacing-lg);
    border-bottom: 1px solid var(--bg-accent);
    color: var(--text-secondary);
}

.data-table tr:hover {
    background: var(--bg-tertiary);
}

.user-cell {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.user-avatar-sm {
    width: 35px;
    height: 35px;
    background: var(--primary-gradient);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-white);
    font-weight: var(--font-weight-semibold);
    font-size: 0.9rem;
}

.username {
    font-weight: var(--font-weight-medium);
    color: var(--text-primary);
}

.role-badge {
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    font-weight: var(--font-weight-medium);
    text-transform: uppercase;
}

.role-super_admin {
    background: #dc3545;
    color: white;
}

.role-admin {
    background: #fd7e14;
    color: white;
}

.role-lider_clan {
    background: #20c997;
    color: white;
}

.role-usuario_normal {
    background: #6c757d;
    color: white;
}

.role-none {
    background: var(--bg-accent);
    color: var(--text-muted);
}

.status-badge {
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    font-weight: var(--font-weight-medium);
}

.status-badge.active {
    background: var(--success);
    color: white;
}

.status-badge.inactive {
    background: var(--error);
    color: white;
}

.action-buttons {
    display: flex;
    gap: var(--spacing-xs);
}

.btn-icon {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-normal);
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-edit {
    background: var(--info);
    color: white;
}

.btn-edit:hover {
    background: #0056b3;
    transform: translateY(-1px);
}

.btn-toggle {
    background: var(--warning);
    color: white;
}

.btn-toggle:hover {
    background: #e0a800;
    transform: translateY(-1px);
}

.btn-delete {
    background: var(--error);
    color: white;
}

.btn-delete:hover {
    filter: brightness(0.9);
    transform: translateY(-1px);
}

.empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
}

.empty-content i {
    font-size: 3rem;
    color: var(--text-muted);
    margin-bottom: var(--spacing-md);
}

.empty-content p {
    color: var(--text-muted);
    font-size: 1.1rem;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: var(--bg-primary);
    margin: 5% auto;
    padding: 0;
    border-radius: var(--radius-xl);
    width: 90%;
    max-width: 600px;
    box-shadow: var(--shadow-xl);
    border: 1px solid var(--bg-accent);
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: var(--spacing-xl);
    border-bottom: 2px solid var(--bg-tertiary);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    font-size: 1.5rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-muted);
    cursor: pointer;
    transition: color var(--transition-normal);
}

.modal-close:hover {
    color: var(--text-primary);
}

.modal-form {
    padding: var(--spacing-xl);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.form-group label {
    font-weight: var(--font-weight-medium);
    color: var(--text-primary);
}

.form-group input,
.form-group select {
    padding: var(--spacing-md);
    border: 1px solid var(--bg-accent);
    border-radius: var(--radius-md);
    background: var(--bg-tertiary);
    color: var(--text-primary);
    font-size: 0.95rem;
    transition: all var(--transition-normal);
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    cursor: pointer;
}

.checkmark {
    width: 20px;
    height: 20px;
    background: var(--bg-tertiary);
    border: 1px solid var(--bg-accent);
    border-radius: var(--radius-sm);
    position: relative;
}

.checkbox-label input:checked + .checkmark {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.checkbox-label input:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 0.8rem;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-md);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--bg-accent);
}

.error-message {
    color: var(--error);
    font-size: 0.8rem;
    display: none;
}

.error-message.show {
    display: block;
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: stretch;
        gap: var(--spacing-md);
    }
    
    .header-actions {
        flex-direction: column;
    }
    
    .search-input {
        width: 100%;
    }
    
    .data-table {
        font-size: 0.9rem;
    }
    
    .data-table th,
    .data-table td {
        padding: var(--spacing-md);
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// Incluir el layout del admin
include __DIR__ . '/layout.php';
?>