<?php
// Capturar el contenido de la vista
ob_start();
?>

<div class="modern-dashboard" data-theme="default">
    <!-- Menú de navegación moderno -->
    <nav class="modern-nav glass">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-icon gradient-bg">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <span class="brand-text">Gestión de Proyectos</span>
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
                <li class="nav-item active">
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
        <!-- Header -->
        <header class="page-header animate-fade-in">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-project-diagram"></i>
                    Gestión de Proyectos
                </h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openCreateProjectModal()">
                        <i class="fas fa-plus"></i>
                        Crear Proyecto
                    </button>
                </div>
            </div>
        </header>

        <!-- Grid de proyectos -->
        <section class="content-section animate-fade-in">
            <div class="projects-header">
                <h3>Proyectos Activos (<?php echo count($projects); ?>)</h3>
                <div class="filter-options">
                    <select id="statusFilter" onchange="filterProjects()">
                        <option value="">Todos los estados</option>
                        <option value="open">Abiertos</option>
                        <option value="completed">Completados</option>
                        <option value="paused">Pausados</option>
                    </select>
                    <select id="clanFilter" onchange="filterProjects()">
                        <option value="">Todos los clanes</option>
                        <?php foreach ($clans as $clan): ?>
                        <option value="<?php echo $clan['clan_id']; ?>">
                            <?php echo Utils::escape($clan['clan_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if (empty($projects)): ?>
            <div class="empty-state">
                <div class="empty-content">
                    <i class="fas fa-project-diagram"></i>
                    <h3>No hay proyectos</h3>
                    <p>Comienza creando tu primer proyecto</p>
                    <button class="btn btn-primary" onclick="openCreateProjectModal()">
                        <i class="fas fa-plus"></i>
                        Crear Primer Proyecto
                    </button>
                </div>
            </div>
            <?php else: ?>
            <div class="projects-grid">
                <?php foreach ($projects as $project): ?>
                <div class="project-card" data-status="<?php echo $project['status']; ?>" data-clan="<?php echo $project['clan_id']; ?>">
                    <div class="project-header">
                        <div class="project-info">
                            <h4 class="project-name"><?php echo Utils::escape($project['project_name']); ?></h4>
                            <span class="project-clan"><?php echo Utils::escape($project['clan_name']); ?></span>
                        </div>
                        <div class="project-actions">
                            <span class="status-badge status-<?php echo $project['status']; ?>">
                                <?php 
                                $statusLabels = [
                                    'open' => 'Abierto',
                                    'completed' => 'Completado',
                                    'paused' => 'Pausado',
                                    'cancelled' => 'Cancelado'
                                ];
                                echo $statusLabels[$project['status']] ?? $project['status'];
                                ?>
                            </span>
                            <div class="action-menu">
                                <button class="btn-icon" onclick="toggleProjectMenu(<?php echo $project['project_id']; ?>)">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="menu-dropdown" id="menu-<?php echo $project['project_id']; ?>">
                                    <button onclick="editProject(<?php echo $project['project_id']; ?>)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button onclick="viewProject(<?php echo $project['project_id']; ?>)">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </button>
                                    <button onclick="deleteProject(<?php echo $project['project_id']; ?>)" class="danger">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($project['description']): ?>
                    <div class="project-description">
                        <p><?php echo Utils::escape(substr($project['description'], 0, 100)) . (strlen($project['description']) > 100 ? '...' : ''); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="project-progress">
                        <div class="progress-info">
                            <span>Progreso</span>
                            <span class="progress-value"><?php echo number_format($project['progress_percentage'], 1); ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $project['progress_percentage']; ?>%"></div>
                        </div>
                    </div>

                    <div class="project-stats">
                        <div class="stat-item">
                            <i class="fas fa-tasks"></i>
                            <span><?php echo $project['total_tasks']; ?> tareas</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $project['completed_tasks']; ?> completadas</span>
                        </div>
                    </div>

                    <div class="project-footer">
                        <div class="project-meta">
                            <span class="created-by">Por: <?php echo Utils::escape($project['created_by_name'] ?: $project['created_by_username']); ?></span>
                            <span class="created-date"><?php echo date('d/m/Y', strtotime($project['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<!-- Modal para crear/editar proyecto -->
<div id="projectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Crear Proyecto</h3>
            <button class="modal-close" onclick="closeProjectModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="projectForm" class="modal-form">
            <input type="hidden" id="projectId" name="projectId">
            
            <div class="form-group">
                <label for="projectName">Nombre del Proyecto *</label>
                <input type="text" id="projectName" name="projectName" required>
                <span class="error-message" id="projectNameError"></span>
            </div>
            
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description" name="description" rows="4" placeholder="Describe el proyecto..."></textarea>
                <span class="error-message" id="descriptionError"></span>
            </div>
            
            <div class="form-group">
                <label for="clanId">Asignar a Clan *</label>
                <select id="clanId" name="clanId" required>
                    <option value="">Seleccionar clan</option>
                    <?php foreach ($clans as $clan): ?>
                    <option value="<?php echo $clan['clan_id']; ?>">
                        <?php echo Utils::escape($clan['clan_name']); ?>
                        (<?php echo $clan['member_count']; ?> miembros)
                    </option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message" id="clanIdError"></span>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeProjectModal()">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span id="submitText">Crear Proyecto</span>
                    <span id="submitLoader" class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Estilos específicos para gestión de proyectos */
.projects-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-xl);
    padding: var(--spacing-lg);
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    border: 1px solid var(--bg-accent);
}

.projects-header h3 {
    font-size: 1.3rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.filter-options {
    display: flex;
    gap: var(--spacing-md);
}

.filter-options select {
    padding: var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--bg-accent);
    border-radius: var(--radius-md);
    background: var(--bg-tertiary);
    color: var(--text-primary);
    cursor: pointer;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--spacing-xl);
}

.project-card {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--bg-accent);
    transition: all var(--transition-normal);
    position: relative;
}

.project-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--spacing-lg);
}

.project-info {
    flex: 1;
}

.project-name {
    font-size: 1.2rem;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.project-clan {
    font-size: 0.9rem;
    color: var(--text-muted);
    background: var(--bg-tertiary);
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
}

.project-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.status-badge {
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 0.8rem;
    font-weight: var(--font-weight-medium);
    text-transform: uppercase;
}

.status-open {
    background: var(--info);
    color: white;
}

.status-completed {
    background: var(--success);
    color: white;
}

.status-paused {
    background: var(--warning);
    color: white;
}

.status-cancelled {
    background: var(--error);
    color: white;
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

.project-description {
    margin-bottom: var(--spacing-lg);
}

.project-description p {
    color: var(--text-secondary);
    line-height: 1.5;
}

.project-progress {
    margin-bottom: var(--spacing-lg);
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-sm);
}

.progress-info span:first-child {
    font-weight: var(--font-weight-medium);
    color: var(--text-primary);
}

.progress-value {
    font-weight: var(--font-weight-semibold);
    color: var(--primary-color);
}

.progress-bar {
    height: 8px;
    background: var(--bg-accent);
    border-radius: var(--radius-full);
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--primary-gradient);
    border-radius: var(--radius-full);
    transition: width var(--transition-normal);
}

.project-stats {
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

.project-footer {
    border-top: 1px solid var(--bg-accent);
    padding-top: var(--spacing-md);
}

.project-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.created-by {
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

textarea {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

@media (max-width: 768px) {
    .projects-header {
        flex-direction: column;
        align-items: stretch;
        gap: var(--spacing-md);
    }
    
    .filter-options {
        flex-direction: column;
    }
    
    .projects-grid {
        grid-template-columns: 1fr;
    }
    
    .project-header {
        flex-direction: column;
        gap: var(--spacing-md);
    }
    
    .project-actions {
        align-self: flex-end;
    }
    
    .project-stats {
        flex-direction: column;
        gap: var(--spacing-sm);
    }
}
</style>

<script>
// JavaScript para gestión de proyectos
let currentProjectId = null;
let isEditMode = false;

function openCreateProjectModal() {
    isEditMode = false;
    currentProjectId = null;
    document.getElementById('modalTitle').textContent = 'Crear Proyecto';
    document.getElementById('submitText').textContent = 'Crear Proyecto';
    document.getElementById('projectForm').reset();
    document.getElementById('projectId').value = '';
    document.getElementById('projectModal').style.display = 'block';
}

function closeProjectModal() {
    document.getElementById('projectModal').style.display = 'none';
    clearErrors();
}

function editProject(projectId) {
    // Placeholder para edición
    isEditMode = true;
    currentProjectId = projectId;
    document.getElementById('modalTitle').textContent = 'Editar Proyecto';
    document.getElementById('submitText').textContent = 'Actualizar Proyecto';
    document.getElementById('projectId').value = projectId;
    document.getElementById('projectModal').style.display = 'block';
}

function viewProject(projectId) {
    // Placeholder para ver detalles
    showToast('Función de ver detalles en desarrollo', 'info');
}

function deleteProject(projectId) {
            showConfirmationModal({
            title: 'Confirmar Eliminación',
            message: '¿Estás seguro de que quieres eliminar este proyecto?',
            type: 'warning',
            confirmText: 'Eliminar',
            cancelText: 'Cancelar',
            onConfirm: () => {
        showToast('Función de eliminar en desarrollo', 'warning');
    }
}

function toggleProjectMenu(projectId) {
    const menu = document.getElementById('menu-' + projectId);
    const allMenus = document.querySelectorAll('.menu-dropdown');
    
    // Cerrar otros menús
    allMenus.forEach(m => {
        if (m !== menu) {
            m.classList.remove('show');
        }
    });
    
    menu.classList.toggle('show');
}

function filterProjects() {
    const statusFilter = document.getElementById('statusFilter').value;
    const clanFilter = document.getElementById('clanFilter').value;
    const projectCards = document.querySelectorAll('.project-card');
    
    projectCards.forEach(card => {
        const projectStatus = card.dataset.status;
        const projectClan = card.dataset.clan;
        
        let showCard = true;
        
        if (statusFilter && projectStatus !== statusFilter) {
            showCard = false;
        }
        
        if (clanFilter && projectClan !== clanFilter) {
            showCard = false;
        }
        
        card.style.display = showCard ? 'block' : 'none';
    });
}

// Manejar envío del formulario
document.getElementById('projectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoader = document.getElementById('submitLoader');
    
    // Mostrar loader
    submitBtn.disabled = true;
    submitText.style.display = 'none';
    submitLoader.style.display = 'inline-block';
    
    const formData = new FormData(this);
    
    fetch('?route=admin/create-project', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeProjectModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            if (data.errors) {
                showFormErrors(data.errors);
            } else {
                showToast(data.message, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitText.style.display = 'inline';
        submitLoader.style.display = 'none';
    });
});

function showFormErrors(errors) {
    clearErrors();
    Object.keys(errors).forEach(field => {
        const errorElement = document.getElementById(field + 'Error');
        if (errorElement) {
            errorElement.textContent = errors[field];
            errorElement.classList.add('show');
        }
    });
}

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.classList.remove('show');
        element.textContent = '';
    });
}

// Cerrar menús al hacer clic fuera
document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-menu')) {
        document.querySelectorAll('.menu-dropdown').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('projectModal');
    if (event.target === modal) {
        closeProjectModal();
    }
}
</script>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// Configurar variables para el layout
$title = 'Gestión de Proyectos - ' . APP_NAME;

// Incluir el layout del admin
include __DIR__ . '/layout.php';
?>