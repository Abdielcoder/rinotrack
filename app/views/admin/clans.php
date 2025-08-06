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
                    <button class="btn btn-primary" onclick="openCreateClanModal()">
                        <i class="fas fa-plus"></i>
                        Crear Clan
                    </button>
                </div>
            </div>
        </header>

        <!-- Grid de clanes -->
        <section class="content-section animate-fade-in">
            <div class="clans-header">
                <h3>Clanes Registrados (<?php echo count($clans); ?>)</h3>
            </div>

            <?php if (empty($clans)): ?>
            <div class="empty-state">
                <div class="empty-content">
                    <i class="fas fa-users-cog"></i>
                    <h3>No hay clanes</h3>
                    <p>Comienza creando tu primer clan</p>
                    <button class="btn btn-primary" onclick="openCreateClanModal()">
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
                            <span class="clan-id" style="display: none;">ID: <?php echo $clan['clan_id']; ?></span>
                        </div>
                        <div class="clan-actions">
                            <div class="action-buttons-group">
                                <button class="action-btn action-btn--edit" 
                                        onclick="editClan(<?php echo $clan['clan_id']; ?>)"
                                        title="Editar clan"
                                        data-tooltip="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn action-btn--members" 
                                        onclick="manageClanMembers(<?php echo $clan['clan_id']; ?>)"
                                        title="Gestionar miembros"
                                        data-tooltip="Miembros">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button class="action-btn action-btn--delete" 
                                        onclick="deleteClan(<?php echo $clan['clan_id']; ?>)"
                                        title="Eliminar clan"
                                        data-tooltip="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="clan-stats">
                        <div class="stat-box">
                            <div class="stat-icon members">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-number"><?php echo $clan['member_count']; ?></span>
                                <span class="stat-label">Miembros</span>
                            </div>
                        </div>

                        <div class="stat-box">
                            <div class="stat-icon projects">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-number"><?php echo $clan['project_count']; ?></span>
                                <span class="stat-label">Proyectos</span>
                            </div>
                        </div>
                    </div>

                    <div class="clan-footer">
                        <div class="clan-meta">
                            <span class="created-date">
                                <i class="fas fa-calendar"></i>
                                Creado: <?php echo date('d/m/Y', strtotime($clan['created_at'])); ?>
                            </span>
                            <?php if (!empty($clan['clan_departamento'])): ?>
                            <span class="clan-department">
                                <i class="fas fa-building"></i>
                                <?php echo Utils::escape($clan['clan_departamento']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="clan-actions-footer">
                            <button class="btn btn-secondary btn-sm" onclick="viewClanDetails(<?php echo $clan['clan_id']; ?>)">
                                <i class="fas fa-eye"></i>
                                Ver Detalles
                            </button>
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
            <button class="modal-close" onclick="closeClanModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="clanForm" class="modal-form">
            <input type="hidden" id="clanId" name="clanId">
            
            <div class="form-group">
                <label for="clanName">Nombre del Clan *</label>
                <input type="text" id="clanName" name="clanName" required placeholder="Ej: Desarrollo, Marketing, Diseño...">
                <span class="error-message" id="clanNameError"></span>
                <small class="form-help">El nombre debe ser único y descriptivo</small>
            </div>
            
            <div class="form-group">
                <label for="clanDepartamento">
                    <i class="fas fa-building"></i>
                    Departamento
                </label>
                <input type="text" id="clanDepartamento" name="clanDepartamento" placeholder="Ej: Tecnología, Recursos Humanos, Finanzas...">
                <span class="error-message" id="clanDepartamentoError"></span>
                <small class="form-help">Departamento al que pertenece el clan (opcional)</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeClanModal()">
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

<!-- Modal para ver detalles del clan -->
<div id="clanDetailsModal" class="modal">
    <div class="modal-content extra-large">
        <div class="modal-header">
            <h3 id="detailsModalTitle">
                <i class="fas fa-info-circle"></i>
                Detalles del Clan
            </h3>
            <button class="modal-close" onclick="closeClanDetailsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-form">
            <div class="details-container">
                <!-- Información General -->
                <div class="details-section">
                    <div class="section-header">
                        <h4><i class="fas fa-info"></i> Información General</h4>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Nombre del Clan:</label>
                            <span id="detailClanName" class="value"></span>
                        </div>
                        <div class="info-item">
                            <label>ID del Clan:</label>
                            <span id="detailClanId" class="value"></span>
                        </div>
                        <div class="info-item">
                            <label>Fecha de Creación:</label>
                            <span id="detailCreatedAt" class="value"></span>
                        </div>
                        <div class="info-item" style="display: none;">
                            <label>Departamento:</label>
                            <span id="detailClanDepartamento" class="value"></span>
                        </div>
                        <div class="info-item">
                            <label>Total de Miembros:</label>
                            <span id="detailMemberCount" class="value badge-success"></span>
                        </div>
                        <div class="info-item">
                            <label>Total de Proyectos:</label>
                            <span id="detailProjectCount" class="value badge-info"></span>
                        </div>
                    </div>
                </div>

                <!-- Miembros del Clan -->
                <div class="details-section">
                    <div class="section-header">
                        <h4><i class="fas fa-users"></i> Miembros del Clan</h4>
                        <button class="btn btn-primary btn-sm" onclick="manageClanMembers(currentDetailsClanId)">
                            <i class="fas fa-cog"></i> Gestionar
                        </button>
                    </div>
                    <div id="detailsMembers" class="members-grid">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>

                <!-- Proyectos del Clan -->
                <div class="details-section">
                    <div class="section-header">
                        <h4><i class="fas fa-project-diagram"></i> Proyectos del Clan</h4>
                    </div>
                    <div id="detailsProjects" class="projects-grid">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="details-section">
                    <div class="section-header">
                        <h4><i class="fas fa-chart-bar"></i> Estadísticas</h4>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-title">Miembros Activos</span>
                                <span id="statActiveMembersCount" class="stat-value">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-title">Proyectos Activos</span>
                                <span id="statActiveProjectsCount" class="stat-value">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="stat-content">
                                <span class="stat-title">Días desde Creación</span>
                                <span id="statDaysOld" class="stat-value">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeClanDetailsModal()">
                    <i class="fas fa-times"></i>
                    Cerrar
                </button>
                <button class="btn btn-primary" onclick="editClan(currentDetailsClanId)">
                    <i class="fas fa-edit"></i>
                    Editar Clan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para gestionar miembros del clan -->
<div id="membersModal" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3 id="membersModalTitle">Gestionar Miembros del Clan</h3>
            <button class="modal-close" onclick="closeMembersModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-form">
            <div class="members-section">
                <div class="section-header">
                    <h4>Miembros Actuales</h4>
                    <span id="memberCount" class="count-badge">0 miembros</span>
                </div>
                <div id="membersList" class="members-list">
                    <!-- Se llena dinámicamente -->
                </div>
            </div>
            
            <div class="add-member-section">
                <div class="section-header">
                    <h4>Agregar Nuevo Miembro</h4>
                </div>
                <div class="add-member-form">
                    <select id="userSelect" class="user-select">
                        <option value="">Seleccionar usuario</option>
                        <?php foreach ($users as $userItem): ?>
                        <option value="<?php echo $userItem['user_id']; ?>">
                            <?php echo Utils::escape($userItem['full_name'] ?: $userItem['username']); ?>
                            (<?php echo Utils::escape($userItem['email']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-primary" onclick="addMemberToClan()">
                        <i class="fas fa-plus"></i>
                        Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript necesario para la funcionalidad de clanes -->
<script>
// Variables globales
let currentClanId = null;
let isEditMode = false;
let currentDetailsClanId = null;
let currentDetailsClanName = null;

// Función para eliminar clan - debe estar disponible inmediatamente
function deleteClan(clanId) {
    if (confirm("¿Estás seguro de que quieres eliminar este clan? Esta acción no se puede deshacer.")) {
        const formData = new FormData();
        formData.append("clanId", clanId);
        
        fetch("?route=admin/delete-clan", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Clan eliminado exitosamente");
                location.reload();
            } else {
                alert(data.message || "Error al eliminar el clan");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error de conexión al eliminar el clan");
        });
    }
}

// Asegurar que la función esté disponible globalmente
window.deleteClan = deleteClan;

// Definir todas las funciones globalmente inmediatamente
window.viewClanDetails = function(clanId) {
    currentDetailsClanId = clanId;
    
    const modal = document.getElementById("clanDetailsModal");
    if (!modal) {
        alert("Error: Modal no disponible");
        return;
    }
    modal.style.display = "block";
    
    // Cargar datos del clan
    fetch("?route=admin/clan-details&clanId=" + clanId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateClanDetails(data.clan);
            } else {
                alert(data.message || "Error al cargar detalles del clan");
                closeClanDetailsModal();
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error de conexión al cargar detalles");
            closeClanDetailsModal();
        });
};

window.closeClanDetailsModal = function() {
    const modal = document.getElementById("clanDetailsModal");
    if (modal) modal.style.display = "none";
    currentDetailsClanId = null;
    currentDetailsClanName = null;
};

window.openCreateClanModal = function() {
    isEditMode = false;
    currentClanId = null;
    document.getElementById("modalTitle").textContent = "Crear Clan";
    document.getElementById("submitText").textContent = "Crear Clan";
    document.getElementById("clanForm").reset();
    document.getElementById("clanId").value = "";
    document.getElementById("clanModal").style.display = "block";
};

window.closeClanModal = function() {
    document.getElementById("clanModal").style.display = "none";
    clearErrors();
    document.getElementById("clanForm").reset();
    document.getElementById("modalTitle").textContent = "Crear Clan";
    document.getElementById("submitText").textContent = "Crear Clan";
    isEditMode = false;
    currentClanId = null;
};

window.editClan = function(clanId) {
    isEditMode = true;
    currentClanId = clanId;
    document.getElementById("modalTitle").textContent = "Editar Clan";
    document.getElementById("submitText").textContent = "Actualizar Clan";
    document.getElementById("clanId").value = clanId;
    
    fetch("?route=admin/clan-details&clanId=" + clanId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const clan = data.clan;
                document.getElementById("clanName").value = clan.clan_name;
                document.getElementById("clanDepartamento").value = clan.clan_departamento || "";
                document.getElementById("clanModal").style.display = "block";
            } else {
                alert(data.message || "Error al cargar datos del clan");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error de conexión al cargar datos del clan");
        });
};

window.manageClanMembers = function(clanId) {
    currentClanId = clanId;
    document.getElementById("membersModalTitle").textContent = "Gestionar Miembros del Clan";
    document.getElementById("membersModal").style.display = "block";
    loadClanMembers(clanId);
};

window.closeMembersModal = function() {
    document.getElementById("membersModal").style.display = "none";
    currentClanId = null;
};

window.addMemberToClan = function() {
    const userSelect = document.getElementById("userSelect");
    const userId = userSelect.value;
    
    if (!userId) {
        alert("Por favor selecciona un usuario");
        return;
    }
    
    const formData = new FormData();
    formData.append("clanId", currentClanId);
    formData.append("userId", userId);
    
    fetch("?route=admin/add-clan-member", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Miembro agregado exitosamente");
            loadClanMembers(currentClanId);
            userSelect.value = "";
        } else {
            alert(data.message || "Error al agregar miembro");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error de conexión al agregar miembro");
    });
};

window.removeMemberFromClan = function(userId) {
    if (confirm("¿Quieres quitar este miembro del clan?")) {
        const formData = new FormData();
        formData.append("clanId", currentClanId);
        formData.append("userId", userId);
        
        fetch("?route=admin/remove-clan-member", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Miembro removido exitosamente");
                loadClanMembers(currentClanId);
            } else {
                alert(data.message || "Error al remover miembro");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error de conexión al remover miembro");
        });
    }
};

// Funciones auxiliares
function clearErrors() {
    const errorElements = document.querySelectorAll(".error-message");
    errorElements.forEach(element => {
        element.classList.remove("show");
        element.textContent = "";
    });
}

function populateClanDetails(clanData) {
    currentDetailsClanName = clanData.clan_name;
    
    document.getElementById("detailClanName").textContent = clanData.clan_name;
    document.getElementById("detailClanId").textContent = clanData.clan_id;
    document.getElementById("detailCreatedAt").textContent = new Date(clanData.created_at).toLocaleDateString();
    document.getElementById("detailMemberCount").textContent = clanData.members.length + " miembros";
    document.getElementById("detailProjectCount").textContent = clanData.projects.length + " proyectos";
    
    // Renderizar miembros
    const membersContainer = document.getElementById("detailsMembers");
    if (membersContainer) {
        let membersHTML = "";
        clanData.members.forEach(member => {
            membersHTML += "<div class=\"member-card\">";
            membersHTML += "<div class=\"member-avatar\"><i class=\"fas fa-user\"></i></div>";
            membersHTML += "<div class=\"member-info\">";
            membersHTML += "<div class=\"member-name\">" + member.full_name + "</div>";
            membersHTML += "<div class=\"member-email\">" + member.email + "</div>";
            membersHTML += "</div></div>";
        });
        membersContainer.innerHTML = membersHTML;
    }
    
    // Renderizar proyectos
    const projectsContainer = document.getElementById("detailsProjects");
    if (projectsContainer) {
        let projectsHTML = "";
        clanData.projects.forEach(project => {
            projectsHTML += "<div class=\"project-card\">";
            projectsHTML += "<div class=\"project-name\">" + project.project_name + "</div>";
            projectsHTML += "<div class=\"project-description\">" + (project.description || "Sin descripción") + "</div>";
            projectsHTML += "</div>";
        });
        projectsContainer.innerHTML = projectsHTML;
    }
}

function loadClanMembers(clanId) {
    fetch("?route=admin/clan-members&clanId=" + clanId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const membersContainer = document.getElementById("membersList");
                if (membersContainer) {
                    let membersHTML = "";
                    data.members.forEach(member => {
                        membersHTML += "<div class=\"member-item\">";
                        membersHTML += "<div class=\"member-info\">";
                        membersHTML += "<div class=\"member-name\">" + member.full_name + "</div>";
                        membersHTML += "<div class=\"member-email\">" + member.email + "</div>";
                        membersHTML += "</div>";
                        membersHTML += "<button class=\"btn-remove\" onclick=\"removeMemberFromClan(" + member.user_id + ")\">";
                        membersHTML += "<i class=\"fas fa-times\"></i>";
                        membersHTML += "</button>";
                        membersHTML += "</div>";
                    });
                    membersContainer.innerHTML = membersHTML;
                }
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error al cargar miembros del clan");
        });
}

// Event listeners cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function() {
    // Manejar envío del formulario de clan
    const clanForm = document.getElementById("clanForm");
    if (clanForm) {
        clanForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById("submitBtn");
            const submitText = document.getElementById("submitText");
            const submitLoader = document.getElementById("submitLoader");
            
            // Mostrar loader
            submitBtn.disabled = true;
            submitText.style.display = "none";
            submitLoader.style.display = "inline-block";
            
            const formData = new FormData(this);
            
            // Determinar la ruta según el modo
            const route = isEditMode ? "admin/update-clan" : "admin/create-clan";
            
            fetch("?route=" + route, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeClanModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert(data.message || "Error al procesar la solicitud");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error de conexión");
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitText.style.display = "inline";
                submitLoader.style.display = "none";
            });
        });
    }

    // Cerrar modales al hacer clic fuera
    window.onclick = function(event) {
        const clanModal = document.getElementById("clanModal");
        const membersModal = document.getElementById("membersModal");
        const detailsModal = document.getElementById("clanDetailsModal");
        
        if (event.target === clanModal) {
            closeClanModal();
        }
        
        if (event.target === membersModal) {
            closeMembersModal();
        }
        
        if (event.target === detailsModal) {
            closeClanDetailsModal();
        }
    };
});
</script>

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
    font-size: 1.3rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.clan-id {
    font-size: 0.8rem;
    color: var(--text-muted);
    background: var(--bg-tertiary);
    padding: 2px 6px;
    border-radius: var(--radius-sm);
}

.clan-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
}

.stat-box {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    border: 1px solid var(--bg-accent);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.stat-icon.members {
    background: var(--success);
}

.stat-icon.projects {
    background: var(--info);
}

.stat-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.stat-number {
    font-size: 1.2rem;
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
}

.stat-label {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.clan-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--bg-accent);
    padding-top: var(--spacing-md);
}

.clan-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    flex-wrap: wrap;
}

.created-date, .clan-department {
    font-size: 0.8rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

.clan-department {
    color: var(--primary-color);
    font-weight: var(--font-weight-medium);
}

.btn-sm {
    padding: var(--spacing-sm) var(--spacing-md);
    font-size: 0.85rem;
}

.form-help {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: var(--spacing-xs);
}

/* Modal de miembros */
.modal-content.large {
    max-width: 700px;
}

.modal-content.extra-large {
    max-width: 900px;
    max-height: 90vh;
}

/* Estilos para el modal de detalles del clan */
.details-container {
    position: relative;
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 5px;
}

.details-container::-webkit-scrollbar {
    width: 6px;
}

.details-container::-webkit-scrollbar-track {
    background: var(--bg-tertiary);
    border-radius: 3px;
}

.details-container::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 3px;
}

.details-section {
    margin-bottom: var(--spacing-2xl);
    padding: var(--spacing-lg);
    background: var(--bg-tertiary);
    border-radius: var(--radius-lg);
    border: 1px solid var(--bg-accent);
}

.details-section .section-header {
    border-bottom: 2px solid var(--primary-color);
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-md);
}

.details-section .section-header h4 {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--text-primary);
    font-size: 1.1rem;
    font-weight: var(--font-weight-semibold);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-lg);
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.info-item label {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: var(--font-weight-medium);
}

.info-item .value {
    font-size: 1rem;
    color: var(--text-primary);
    font-weight: var(--font-weight-semibold);
    padding: var(--spacing-sm) 0;
}

.badge-success {
    background: var(--success);
    color: white;
    padding: var(--spacing-xs) var(--spacing-sm) !important;
    border-radius: var(--radius-sm);
    font-size: 0.85rem !important;
}

.badge-info {
    background: var(--info);
    color: white;
    padding: var(--spacing-xs) var(--spacing-sm) !important;
    border-radius: var(--radius-sm);
    font-size: 0.85rem !important;
}

.members-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-md);
}

.member-card {
    background: var(--bg-primary);
    border: 1px solid var(--bg-accent);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    transition: all var(--transition-normal);
}

.member-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.member-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-sm);
}

.member-avatar-details {
    width: 45px;
    height: 45px;
    background: var(--primary-gradient);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: var(--font-weight-bold);
    font-size: 1.1rem;
}

.member-info-details {
    flex: 1;
}

.member-name-details {
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin-bottom: 2px;
}

.member-email-details {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.member-role-details {
    font-size: 0.8rem;
    padding: 2px 8px;
    border-radius: var(--radius-sm);
    background: var(--primary-color);
    color: white;
    margin-top: var(--spacing-xs);
    display: inline-block;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: var(--spacing-md);
}

.project-card {
    background: var(--bg-primary);
    border: 1px solid var(--bg-accent);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.project-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--info);
}

.project-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.project-name {
    font-size: 1.1rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
}

.project-description {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: var(--spacing-md);
    line-height: 1.4;
}

.project-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-lg);
}

.stat-card {
    background: var(--bg-primary);
    border: 1px solid var(--bg-accent);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    transition: all var(--transition-normal);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.stat-card .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: white;
    background: var(--primary-gradient);
}

.stat-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.stat-title {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: var(--font-weight-medium);
}

.stat-value {
    font-size: 1.8rem;
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
}

.empty-data {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--text-muted);
}

.empty-data i {
    font-size: 2rem;
    margin-bottom: var(--spacing-md);
    opacity: 0.5;
}

.loading-details {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--text-muted);
}

.loading-details i {
    font-size: 1.5rem;
    margin-right: var(--spacing-sm);
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Overlay de loading */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: var(--radius-lg);
}

.loading-overlay .loading-details {
    background: var(--bg-primary);
    padding: var(--spacing-xl);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    text-align: center;
    color: var(--text-primary);
}

.loading-overlay .loading-details i {
    font-size: 2rem;
    margin-bottom: var(--spacing-md);
    color: var(--primary-color);
    animation: spin 1s linear infinite;
}

.members-section {
    margin-bottom: var(--spacing-xl);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-md);
    border-bottom: 2px solid var(--bg-tertiary);
}

.section-header h4 {
    font-size: 1.1rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.count-badge {
    background: var(--primary-color);
    color: white;
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    font-weight: var(--font-weight-medium);
}

.members-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
    max-height: 300px;
    overflow-y: auto;
}

.member-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-md);
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    border: 1px solid var(--bg-accent);
}

.member-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.member-avatar {
    width: 35px;
    height: 35px;
    background: var(--primary-gradient);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: var(--font-weight-semibold);
    font-size: 0.9rem;
}

.member-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.member-name {
    font-weight: var(--font-weight-medium);
    color: var(--text-primary);
}

.member-email {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.add-member-form {
    display: flex;
    gap: var(--spacing-md);
    align-items: flex-start;
}

.user-select {
    flex: 1;
    padding: var(--spacing-md);
    border: 1px solid var(--bg-accent);
    border-radius: var(--radius-md);
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.empty-members {
    text-align: center;
    padding: var(--spacing-xl);
    color: var(--text-muted);
}

.loading-state {
    text-align: center;
    padding: var(--spacing-xl);
    color: var(--text-muted);
}

.loading-state i {
    margin-right: var(--spacing-sm);
}

.error-state {
    text-align: center;
    padding: var(--spacing-xl);
    color: var(--error);
}

.member-role {
    font-size: 0.75rem;
    color: var(--primary-color);
    background: rgba(99, 102, 241, 0.1);
    padding: 2px 6px;
    border-radius: var(--radius-sm);
    margin-top: 2px;
    display: inline-block;
}

.btn-remove {
    background: var(--error);
    color: white;
    border: none;
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    cursor: pointer;
    font-size: 0.8rem;
    transition: all var(--transition-normal);
}

.btn-remove:hover {
    background: #c82333;
    transform: translateY(-1px);
}

/* Nuevos estilos minimalistas para acciones de clan */
.clan-actions {
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateX(15px) scale(0.9);
    pointer-events: none;
}

.clan-card:hover .clan-actions {
    opacity: 1;
    transform: translateX(0) scale(1);
    pointer-events: auto;
}

/* Animación staggered para los botones individuales */
.action-btn:nth-child(1) {
    transition-delay: 0.1s;
}

.action-btn:nth-child(2) {
    transition-delay: 0.15s;
}

.action-btn:nth-child(3) {
    transition-delay: 0.2s;
}

.action-buttons-group {
    display: flex;
    gap: 6px;
    align-items: center;
}

.action-btn {
    position: relative;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--text-muted);
    font-size: 14px;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    opacity: 0;
    transition: opacity 0.2s ease;
}

.action-btn:hover::before {
    opacity: 1;
}

.action-btn:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: rgba(255, 255, 255, 0.2);
}

.action-btn:active {
    transform: translateY(0) scale(0.95);
    transition: all 0.1s ease;
}

/* Colores específicos para cada acción */
.action-btn--edit:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.action-btn--members:hover {
    background: linear-gradient(135deg, #48bb78, #38a169);
    color: white;
    box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4);
}

.action-btn--delete:hover {
    background: linear-gradient(135deg, #f56565, #e53e3e);
    color: white;
    box-shadow: 0 8px 25px rgba(245, 101, 101, 0.4);
}

/* Tooltips minimalistas */
.action-btn[data-tooltip] {
    position: relative;
}

.action-btn[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
    z-index: 1000;
    animation: tooltipFadeIn 0.2s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.action-btn[data-tooltip]:hover::before {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-bottom: 6px solid rgba(0, 0, 0, 0.9);
    z-index: 1001;
    animation: tooltipFadeIn 0.2s ease;
}

@keyframes tooltipFadeIn {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

/* Efecto de glassmorphism mejorado */
.clan-card {
    background: rgba(255, 255, 255, 0.02) !important;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    position: relative;
    overflow: hidden;
}

.clan-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.05),
        transparent
    );
    transition: left 0.5s ease;
}

.clan-card:hover::before {
    left: 100%;
}

/* Animación sutil al cargar */
.clan-card {
    animation: cardSlideIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    opacity: 0;
    transform: translateY(20px);
}

.clan-card:nth-child(1) { animation-delay: 0.1s; }
.clan-card:nth-child(2) { animation-delay: 0.2s; }
.clan-card:nth-child(3) { animation-delay: 0.3s; }
.clan-card:nth-child(4) { animation-delay: 0.4s; }
.clan-card:nth-child(5) { animation-delay: 0.5s; }

@keyframes cardSlideIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Indicador visual sutil para hover */
.clan-card {
    border-left: 3px solid transparent;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.clan-card:hover {
    border-left-color: rgba(102, 126, 234, 0.6);
}

@media (max-width: 768px) {
    .clans-grid {
        grid-template-columns: 1fr;
    }
    
    .clan-header {
        flex-direction: column;
        gap: var(--spacing-md);
    }
    
    .clan-footer {
        flex-direction: column;
        gap: var(--spacing-md);
        align-items: stretch;
    }
    
    .add-member-form {
        flex-direction: column;
    }
    
    .stat-box {
        flex-direction: column;
        text-align: center;
        gap: var(--spacing-sm);
    }
    
    /* Ajustes responsivos para las acciones */
    .clan-actions {
        opacity: 1;
        transform: translateX(0);
        margin-top: var(--spacing-md);
    }
    
    .action-buttons-group {
        justify-content: center;
        gap: 12px;
    }
    
    .action-btn {
        width: 42px;
        height: 42px;
        font-size: 16px;
    }
    
    .action-btn[data-tooltip]:hover::after {
        bottom: -40px;
        font-size: 13px;
        padding: 8px 12px;
    }
    
    /* Ajustes responsivos para el modal de detalles */
    .modal-content.extra-large {
        max-width: 95%;
        margin: 2% auto;
    }
    
    .details-container {
        max-height: 75vh;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .members-grid {
        grid-template-columns: 1fr;
    }
    
    .projects-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
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