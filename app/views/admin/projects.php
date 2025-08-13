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
                    <button class="btn btn-primary" id="openCreateProjectBtnHeader" data-action="create-project">
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
                    <select id="statusFilter" data-action="filter-projects">
                        <option value="">Todos los estados</option>
                        <option value="open">Abiertos</option>
                        <option value="completed">Completados</option>
                        <option value="paused">Pausados</option>
                    </select>
                    <select id="clanFilter" data-action="filter-projects">
                        <option value="">Todos los clanes</option>
                        <?php foreach ($clans as $clan): ?>
                        <option value="<?php echo intval($clan['clan_id']); ?>">
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
                    <button class="btn btn-primary" id="openCreateProjectBtnEmpty" data-action="create-project">
                        <i class="fas fa-plus"></i>
                        Crear Primer Proyecto
                    </button>
                </div>
            </div>
            <?php else: ?>
            <div class="projects-grid">
                <?php foreach ($projects as $project): ?>
                <div class="project-card" data-status="<?php echo htmlspecialchars($project['status']); ?>" data-clan="<?php echo intval($project['clan_id']); ?>">
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
                                <button class="btn-icon" data-action="toggle-menu" data-project-id="<?php echo intval($project['project_id']); ?>">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="menu-dropdown" id="menu-<?php echo intval($project['project_id']); ?>">
                                     <button data-action="add-task" data-project-id="<?php echo intval($project['project_id']); ?>">
                                         <i class="fas fa-plus-circle"></i> Agregar Tarea
                                     </button>
                                    <button data-action="edit-project" data-project-id="<?php echo intval($project['project_id']); ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <a href="?route=admin/project-details&projectId=<?php echo intval($project['project_id']); ?>">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                    <button data-action="delete-project" data-project-id="<?php echo intval($project['project_id']); ?>" class="danger">
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
            <button class="modal-close" data-action="close-modal">
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
                    <option value="<?php echo intval($clan['clan_id']); ?>">
                        <?php echo Utils::escape($clan['clan_name']); ?>
                        (<?php echo intval($clan['member_count']); ?> miembros)
                    </option>
                    <?php endforeach; ?>
                </select>
                <span class="error-message" id="clanIdError"></span>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" data-action="close-modal">
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

<!-- Modal para crear tarea -->
<div id="taskModal" class="modal" style="display:none">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="taskModalTitle">Crear Tarea</h3>
            <button class="modal-close" data-action="close-task-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="taskForm" class="modal-form">
            <input type="hidden" id="taskProjectId" name="projectId">

            <div class="form-group">
                <label for="taskName">Nombre de la tarea *</label>
                <input type="text" id="taskName" name="taskName" required>
            </div>

            <div class="form-group">
                <label for="taskDescription">Descripción</label>
                <textarea id="taskDescription" name="description" rows="3" placeholder="Describe la tarea..."></textarea>
            </div>

            <div class="form-group">
                <label>Asignar a colaboradores del clan</label>
                <div id="taskMembers" class="checkbox-list" style="display:grid;grid-template-columns:1fr 1fr;gap:8px"></div>
                <span class="error-message" id="taskMembersError"></span>
            </div>

            <div class="form-group">
                <label for="taskDueDate">Fecha estimada de entrega</label>
                <input type="date" id="taskDueDate" name="dueDate">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" data-action="close-task-modal">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="taskSubmitBtn">
                    <span id="taskSubmitText">Crear Tarea</span>
                    <span id="taskSubmitLoader" class="btn-loader" style="display:none"><i class="fas fa-spinner fa-spin"></i></span>
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

/* Estilos consistentes para enlaces dentro del menú (evitar colores de visitado) */
.menu-dropdown a {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    text-decoration: none;
    background: transparent;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.menu-dropdown a:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.menu-dropdown a:visited,
.menu-dropdown a:active,
.menu-dropdown a:focus {
    color: var(--text-primary);
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

<!-- JavaScript Inline con todas las funciones necesarias -->
<script>
(function() {
    'use strict';
    
    // Sistema de gestión de proyectos con event delegation
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Inicializando gestión de proyectos...');
        
        // Referencias a elementos del DOM
        const modal = document.getElementById('projectModal');
        const form = document.getElementById('projectForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        const submitBtn = document.getElementById('submitBtn');
        const projectIdField = document.getElementById('projectId');
        
        // Función para abrir el modal de crear/editar
        function openProjectModal(isEdit = false, projectId = null) {
            if (!modal) {
                console.error('Modal no encontrado');
                return;
            }
            
            if (isEdit && projectId) {
                modalTitle.textContent = 'Editar Proyecto';
                submitText.textContent = 'Actualizar Proyecto';
                projectIdField.value = projectId;
                // TODO: Cargar datos del proyecto
            } else {
                modalTitle.textContent = 'Crear Proyecto';
                submitText.textContent = 'Crear Proyecto';
                if (form) form.reset();
                projectIdField.value = '';
            }
            
            modal.style.display = 'block';
            clearErrors();
        }
        
        // Función para cerrar el modal
        function closeProjectModal() {
            if (modal) {
                modal.style.display = 'none';
                clearErrors();
            }
        }

        // -------- Modal de Tarea --------
        const taskModal = document.getElementById('taskModal');
        const taskForm = document.getElementById('taskForm');
        const taskProjectId = document.getElementById('taskProjectId');
        const taskMembers = document.getElementById('taskMembers');
        const taskSubmitBtn = document.getElementById('taskSubmitBtn');
        const taskSubmitText = document.getElementById('taskSubmitText');
        const taskSubmitLoader = document.getElementById('taskSubmitLoader');

        function openTaskModal(projectId, clanId) {
            if (!taskModal) return;
            // reset form
            if (taskForm) taskForm.reset();
            if (taskMembers) taskMembers.innerHTML = '<div class="empty">Cargando miembros...</div>';
            taskProjectId.value = projectId || '';
            taskModal.style.display = 'block';
            // cargar miembros del clan por AJAX
            if (clanId) {
                fetch('?route=admin/clan-members&clanId=' + encodeURIComponent(clanId))
                    .then(r => r.json())
                    .then(data => {
                        if (!data || !data.success) {
                            taskMembers.innerHTML = '<div class="empty">No se pudieron cargar miembros</div>';
                            return;
                        }
                        const members = data.members || [];
                        if (!members.length) {
                            taskMembers.innerHTML = '<div class="empty">No hay miembros en el clan</div>';
                            return;
                        }
                        taskMembers.innerHTML = members.map(m => {
                            const full = (m.full_name || m.username || '').replace(/</g,'&lt;');
                            const role = (m.role_name || '').replace(/</g,'&lt;');
                            return `<label style="display:flex;align-items:center;gap:8px;border:1px solid var(--bg-accent);padding:8px;border-radius:10px;background:var(--bg-primary)">
                                <input type="checkbox" name="assignedUsers[]" value="${m.user_id}">
                                <span>${full} ${role?`(${role})`:''}</span>
                            </label>`;
                        }).join('');
                    })
                    .catch(() => { taskMembers.innerHTML = '<div class="empty">Error cargando miembros</div>'; });
            }
        }

        function closeTaskModal() { if (taskModal) taskModal.style.display = 'none'; }
        
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
        
        // Función para filtrar proyectos
        function filterProjects() {
            const statusFilter = document.getElementById('statusFilter');
            const clanFilter = document.getElementById('clanFilter');
            const projectCards = document.querySelectorAll('.project-card');
            
            const status = statusFilter ? statusFilter.value : '';
            const clan = clanFilter ? clanFilter.value : '';
            
            projectCards.forEach(function(card) {
                const projectStatus = card.dataset.status;
                const projectClan = card.dataset.clan;
                let showCard = true;
                
                if (status && projectStatus !== status) showCard = false;
                if (clan && projectClan !== clan) showCard = false;
                
                card.style.display = showCard ? 'block' : 'none';
            });
        }
        
        // Event delegation para manejar todos los clicks
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-action]');
            if (!target) return;
            
            const action = target.dataset.action;
            const projectId = target.dataset.projectId;
            
            switch(action) {
                case 'create-project':
                    e.preventDefault();
                    openProjectModal(false);
                    break;
                    
                case 'edit-project':
                    e.preventDefault();
                    openProjectModal(true, projectId);
                    break;

                case 'add-task':
                    e.preventDefault();
                    // Buscar clan desde la tarjeta del proyecto
                    const card = target.closest('.project-card');
                    const clanId = card ? card.dataset.clan : '';
                    openTaskModal(projectId, clanId);
                    break;
                    
                case 'view-project':
                    e.preventDefault();
                    alert('Función de ver detalles en desarrollo');
                    break;
                    
                case 'delete-project':
                    e.preventDefault();
                    if (!projectId) return;
                    if (!confirm('¿Estás seguro de que quieres eliminar este proyecto? Esta acción no se puede deshacer.')) return;
                    const fd = new FormData();
                    fd.append('projectId', projectId);
                    fetch('?route=admin/delete-project', {
                        method: 'POST',
                        body: fd
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success) {
                            alert('Proyecto eliminado exitosamente');
                            setTimeout(() => window.location.reload(), 800);
                        } else {
                            alert((data && data.message) ? data.message : 'Error al eliminar proyecto');
                        }
                    })
                    .catch(err => {
                        console.error('Error al eliminar proyecto:', err);
                        alert('Error de conexión al eliminar proyecto');
                    });
                    break;
                    
                case 'toggle-menu':
                    e.preventDefault();
                    e.stopPropagation();
                    const menu = document.getElementById('menu-' + projectId);
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
                    closeProjectModal();
                    break;
                case 'close-task-modal':
                    e.preventDefault();
                    closeTaskModal();
                    break;
            }
        });
        
        // Event listener para cambios en filtros
        document.addEventListener('change', function(e) {
            if (e.target.dataset.action === 'filter-projects') {
                filterProjects();
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
        
        // Cerrar modal al hacer clic fuera
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeProjectModal();
            }
            if (e.target === taskModal) {
                closeTaskModal();
            }
        });
        
        // Manejar envío del formulario
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Mostrar loader
                if (submitBtn) submitBtn.disabled = true;
                if (submitText) submitText.style.display = 'none';
                if (submitLoader) submitLoader.style.display = 'inline-block';
                
                // Preparar datos
                const formData = new FormData(form);
                
                // Enviar petición
                fetch('?route=admin/create-project', {
                    method: 'POST',
                    body: formData
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        closeProjectModal();
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        if (data.errors) {
                            showFormErrors(data.errors);
                        } else {
                            alert(data.message || 'Error al crear el proyecto');
                        }
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    alert('Error de conexión');
                })
                .finally(function() {
                    // Ocultar loader
                    if (submitBtn) submitBtn.disabled = false;
                    if (submitText) submitText.style.display = 'inline';
                    if (submitLoader) submitLoader.style.display = 'none';
                });
            });
        }
        
        // Envío del formulario de tarea
        if (taskForm) {
            taskForm.addEventListener('submit', function(e){
                e.preventDefault();
                if (taskSubmitBtn) taskSubmitBtn.disabled = true;
                if (taskSubmitText) taskSubmitText.style.display = 'none';
                if (taskSubmitLoader) taskSubmitLoader.style.display = 'inline-block';

                const formData = new FormData(taskForm);
                fetch('?route=admin/add-task', { method: 'POST', body: formData, headers: { 'Accept': 'application/json' } })
                    .then(async r=>{
                        const text = await r.text();
                        try { return { ok: r.ok, data: JSON.parse(text) }; }
                        catch(_) { throw new Error(text); }
                    })
                    .then(resp=>{
                        const data = resp.data || {};
                        if (resp.ok && data.success) {
                            // Cerrar inmediatamente y refrescar rápido
                            closeTaskModal();
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error al crear tarea');
                        }
                    })
                    .catch(err=>{ console.error('Error add-task:', err); alert('Error: ' + (err && err.message ? err.message.substring(0, 300) : 'conexión')); })
                    .finally(()=>{
                        if (taskSubmitBtn) taskSubmitBtn.disabled = false;
                        if (taskSubmitText) taskSubmitText.style.display = 'inline';
                        if (taskSubmitLoader) taskSubmitLoader.style.display = 'none';
                    });
            });
        }

        console.log('Gestión de proyectos inicializada correctamente');
    });
})();
</script>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// Configurar variables para el layout
$title = 'Gestión de Proyectos - ' . APP_NAME;

// Incluir el layout del admin (sin JavaScript adicional porque ya está inline)
include __DIR__ . '/layout.php';
?>