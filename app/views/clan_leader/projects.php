<?php
// Guardar el contenido en una variable
ob_start();
?>

<!-- Cargar CSS de rediseño -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/clan-leader-redesign.css">

<div class="clan-leader-projects minimal">
    <!-- Header Minimalista Consistente -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <div class="clan-icon-large"><?php echo $clanIcon ?? '🏢'; ?></div>
                <h1>Gestión de Proyectos</h1>
                <span class="subtitle">Administra proyectos y tareas de <?php echo htmlspecialchars($clan['clan_name'] ?? 'tu clan'); ?></span>
            </div>
            
            <div class="actions-minimal">
                <!-- Toggle de vista -->
                <div class="view-toggle-minimal">
                    <button class="view-btn active" onclick="switchView('cards')" id="cardsViewBtn">
                        <i class="fas fa-th-large"></i>
                        Cards
                    </button>
                    <button class="view-btn" onclick="switchView('list')" id="listViewBtn">
                        <i class="fas fa-list"></i>
                        Lista
                    </button>
                </div>
                
                <button class="btn-minimal primary" onclick="openCreateProjectModal()">
                    <i class="fas fa-plus"></i>
                    Nuevo Proyecto
                </button>
            </div>
        </div>
        
        <!-- Búsqueda consistente -->
        <div class="search-minimal">
            <form method="GET" action="?route=clan_leader/projects" class="search-form">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Buscar proyectos...">
                </div>
                <button type="submit" class="btn-minimal">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="?route=clan_leader/projects" class="btn-minimal secondary">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="content-minimal">
        <?php if (!empty($projects)): ?>
            <!-- Vista de Cards (por defecto) -->
            <section class="projects-minimal animate-fade-in" id="cardsView">
                <div class="projects-grid-minimal">
                    <?php foreach ($projects as $project): ?>
                    <div class="project-item-enhanced">
                        <!-- Header del Proyecto -->
                        <div class="project-info-header">
                            <div class="project-icon-minimal">
                                <i class="fas fa-project-diagram icon-gradient"></i>
                            </div>
                            <div class="project-details-minimal">
                                <div class="project-name-minimal"><?= htmlspecialchars($project['project_name']) ?></div>
                                <?php if (!empty($project['description'])): ?>
                                <div class="project-description-minimal"><?= htmlspecialchars($project['description']) ?></div>
                                <?php endif; ?>
                                <div class="project-meta-minimal">
                                    <span class="project-status-minimal status-<?= $project['status'] ?>">
                                        <?= ucfirst($project['status']) ?>
                                    </span>
                                    <?php if (isset($project['kpi_points']) && $project['kpi_points'] > 0): ?>
                                    <span class="project-kpi-minimal">
                                        <i class="fas fa-star"></i>
                                        <?= number_format($project['kpi_points']) ?> KPI
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="project-menu-minimal">
                                <button class="btn-menu-minimal" onclick="toggleProjectMenu(<?= $project['project_id'] ?>)">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu-minimal" id="projectMenu<?= $project['project_id'] ?>">
                                    <button class="menu-item-minimal" onclick="openEditProjectModal(<?= $project['project_id'] ?>, '<?= htmlspecialchars($project['project_name']) ?>', '<?= htmlspecialchars($project['description']) ?>', '<?= $project['time_limit'] ?? '' ?>')">
                                        <i class="fas fa-edit"></i>
                                        Editar
                                    </button>
                                    <button class="menu-item-minimal" onclick="openCloneProjectModal(<?= $project['project_id'] ?>)">
                                        <i class="fas fa-copy"></i>
                                        Clonar
                                    </button>
                                    <button class="menu-item-minimal danger" onclick="deleteProject(<?= $project['project_id'] ?>, '<?= htmlspecialchars($project['project_name']) ?>')">
                                        <i class="fas fa-trash"></i>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($project['description'])): ?>
                        <div class="project-description">
                            <?= htmlspecialchars($project['description']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Estadísticas del Proyecto -->
                        <div class="project-stats-minimal">
                            <div class="stat-item-minimal">
                                <div class="stat-icon-minimal">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="stat-content-minimal">
                                    <div class="stat-number-minimal"><?= $project['total_tasks'] ?? 0 ?></div>
                                    <div class="stat-label-minimal">Total</div>
                                </div>
                            </div>
                            <div class="stat-item-minimal completed">
                                <div class="stat-icon-minimal">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-content-minimal">
                                    <div class="stat-number-minimal"><?= $project['completed_tasks'] ?? 0 ?></div>
                                    <div class="stat-label-minimal">Completadas</div>
                                </div>
                            </div>
                            <div class="stat-item-minimal progress">
                                <div class="stat-icon-minimal">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-content-minimal">
                                    <div class="stat-number-minimal"><?= $project['progress_percentage'] ?? 0 ?>%</div>
                                    <div class="stat-label-minimal">Progreso</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Barra de Progreso -->
                        <div class="progress-bar-minimal">
                            <div class="progress-fill-minimal" style="width: <?= $project['progress_percentage'] ?? 0 ?>%"></div>
                        </div>
                        
                        <!-- Delegación -->
                        <div class="delegation-section-minimal">
                            <label class="checkbox-label-minimal">
                                <input type="checkbox" 
                                       class="delegation-toggle-hidden" 
                                       data-project-id="<?= $project['project_id'] ?>"
                                       <?= isset($project['allow_delegation']) && $project['allow_delegation'] ? 'checked' : '' ?>
                                       onchange="toggleProjectDelegation(<?= $project['project_id'] ?>, this.checked)"
                                       style="display: none !important; position: absolute !important; left: -9999px !important;">
                                <span class="checkmark-custom"></span>
                                <span class="checkbox-text">
                                    <i class="fas fa-user-plus"></i>
                                    Permitir delegación de tareas
                                </span>
                            </label>
                        </div>
                        
                        <!-- Acciones del Proyecto -->
                        <div class="project-actions-minimal">
                            <a href="?route=clan_leader/tasks&project_id=<?= $project['project_id'] ?>" class="btn-minimal primary">
                                <i class="fas fa-eye"></i>
                                Ver Tareas
                            </a>
                            <button class="btn-minimal secondary" onclick="openCreateTaskModal(<?= $project['project_id'] ?>)">
                                <i class="fas fa-plus"></i>
                                Nueva Tarea
                            </button>
                        </div>
                        
                        <!-- Nota: La sección colapsable se ha removido - ahora se usa navegación directa -->
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            
            <!-- Vista de Lista (oculta por defecto) -->
            <section class="projects-list-view animate-fade-in" id="listView" style="display: none;">
                <div class="projects-table-minimal">
                    <div class="table-header-minimal">
                        <div class="header-cell">Proyecto</div>
                        <div class="header-cell">Estado</div>
                        <div class="header-cell">Progreso</div>
                        <div class="header-cell">Tareas</div>
                        <div class="header-cell">Acciones</div>
                    </div>
                    
                    <?php foreach ($projects as $project): ?>
                    <div class="table-row-minimal">
                        <div class="cell-project">
                            <div class="project-icon-list">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="project-info-list">
                                <div class="project-name-list"><?= htmlspecialchars($project['project_name']) ?></div>
                                <?php if (!empty($project['description'])): ?>
                                <div class="project-description-list"><?= htmlspecialchars($project['description']) ?></div>
                                <?php endif; ?>
                                <?php if (isset($project['kpi_points']) && $project['kpi_points'] > 0): ?>
                                <div class="project-kpi-list">
                                    <i class="fas fa-star"></i>
                                    <?= number_format($project['kpi_points']) ?> KPI
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="cell-status">
                            <span class="status-badge-list status-<?= $project['status'] ?>">
                                <?= ucfirst($project['status']) ?>
                            </span>
                        </div>
                        
                        <div class="cell-progress">
                            <div class="progress-container-list">
                                <div class="progress-bar-list">
                                    <div class="progress-fill-list" style="width: <?= $project['progress_percentage'] ?? 0 ?>%"></div>
                                </div>
                                <span class="progress-text-list"><?= $project['progress_percentage'] ?? 0 ?>%</span>
                            </div>
                        </div>
                        
                        <div class="cell-tasks">
                            <div class="tasks-summary-list">
                                <span class="tasks-total"><?= $project['total_tasks'] ?? 0 ?></span>
                                <span class="tasks-separator">/</span>
                                <span class="tasks-completed"><?= $project['completed_tasks'] ?? 0 ?></span>
                            </div>
                        </div>
                        
                        <div class="cell-actions">
                            <div class="actions-list">
                                <a href="?route=clan_leader/tasks&project_id=<?= $project['project_id'] ?>" class="btn-list-action primary" title="Ver Tareas">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-list-action secondary" onclick="openCreateTaskModal(<?= $project['project_id'] ?>)" title="Nueva Tarea">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button class="btn-list-action menu" onclick="toggleProjectMenu(<?= $project['project_id'] ?>)" title="Más opciones">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <!-- Menú contextual -->
                                <div class="dropdown-menu-list" id="projectMenu<?= $project['project_id'] ?>">
                                    <button class="menu-item-list" onclick="openEditProjectModal(<?= $project['project_id'] ?>, '<?= htmlspecialchars($project['project_name']) ?>', '<?= htmlspecialchars($project['description']) ?>', '<?= $project['time_limit'] ?? '' ?>')">
                                        <i class="fas fa-edit"></i>
                                        Editar
                                    </button>
                                    <button class="menu-item-list" onclick="openCloneProjectModal(<?= $project['project_id'] ?>)">
                                        <i class="fas fa-copy"></i>
                                        Clonar
                                    </button>
                                    <button class="menu-item-list danger" onclick="deleteProject(<?= $project['project_id'] ?>, '<?= htmlspecialchars($project['project_name']) ?>')">
                                        <i class="fas fa-trash"></i>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php else: ?>
            <div class="empty-minimal">
                <div class="empty-icon-minimal">
                    📋
                </div>
                <h3>No hay proyectos en el clan</h3>
                <p>Crea tu primer proyecto para comenzar a organizar las tareas del equipo.</p>
                <button class="btn-minimal primary" onclick="openCreateProjectModal()">
                    <i class="fas fa-plus"></i>
                    Crear primer proyecto
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para crear proyecto -->
<div id="createProjectModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Crear Nuevo Proyecto</h3>
            <button class="modal-close" onclick="closeCreateProjectModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="createProjectForm" class="modal-form">
                <div class="form-group">
                    <label for="projectName">
                        <i class="fas fa-project-diagram"></i>
                        Nombre del Proyecto
                    </label>
                    <input type="text" id="projectName" name="projectName" required 
                           placeholder="Ingrese el nombre del proyecto">
                </div>
                
                <div class="form-group">
                    <label for="description">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </label>
                    <textarea id="description" name="description" required 
                              placeholder="Describa el proyecto" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="timeLimit">
                        <i class="fas fa-calendar-alt"></i>
                        Fecha Límite
                    </label>
                    <input type="date" id="timeLimit" name="timeLimit" 
                           placeholder="Seleccione la fecha límite del proyecto">
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="isEditable" name="isEditable" value="1">
                        <span class="checkmark"></span>
                        <i class="fas fa-edit"></i>
                        Permitir edición por miembros del clan
                    </label>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="action-btn secondary" onclick="closeCreateProjectModal()">
                <i class="fas fa-times"></i>
                <span>Cancelar</span>
            </button>
            <button type="submit" form="createProjectForm" class="action-btn primary">
                <i class="fas fa-plus"></i>
                <span>Crear Proyecto</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal para editar proyecto -->
<div id="editProjectModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Proyecto</h3>
            <button class="modal-close" onclick="closeEditProjectModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="editProjectForm" class="modal-form">
                <input type="hidden" id="editProjectId" name="projectId">
                
                <div class="form-group">
                    <label for="editProjectName">
                        <i class="fas fa-project-diagram"></i>
                        Nombre del Proyecto
                    </label>
                    <input type="text" id="editProjectName" name="projectName" required 
                           placeholder="Ingrese el nombre del proyecto">
                </div>
                
                <div class="form-group">
                    <label for="editDescription">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </label>
                    <textarea id="editDescription" name="description" required 
                              placeholder="Describa el proyecto" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="editTimeLimit">
                        <i class="fas fa-calendar-alt"></i>
                        Fecha Límite
                    </label>
                    <input type="date" id="editTimeLimit" name="timeLimit" 
                           placeholder="Seleccione la fecha límite del proyecto">
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="action-btn secondary" onclick="closeEditProjectModal()">
                <i class="fas fa-times"></i>
                <span>Cancelar</span>
            </button>
            <button type="submit" form="editProjectForm" class="action-btn primary">
                <i class="fas fa-save"></i>
                <span>Guardar Cambios</span>
            </button>
        </div>
    </div>
</div>

<script>
// Funciones para el modal de crear proyecto
function openCreateProjectModal() {
    document.getElementById('createProjectModal').style.display = 'flex';
}

function closeCreateProjectModal() {
    document.getElementById('createProjectModal').style.display = 'none';
    document.getElementById('createProjectForm').reset();
}

// Funciones para el modal de editar proyecto
function openEditProjectModal(projectId, projectName, description, timeLimit) {
    document.getElementById('editProjectId').value = projectId;
    document.getElementById('editProjectName').value = projectName;
    document.getElementById('editDescription').value = description;
    document.getElementById('editTimeLimit').value = timeLimit || '';
    document.getElementById('editProjectModal').style.display = 'flex';
}

function closeEditProjectModal() {
    document.getElementById('editProjectModal').style.display = 'none';
    document.getElementById('editProjectForm').reset();
}

// Eliminar proyecto
function deleteProject(projectId, projectName) {
            confirmDelete(`¿Estás seguro de que quieres eliminar el proyecto "${projectName}"?`, () => {
        const formData = new FormData();
        formData.append('projectId', projectId);
        
        fetch('?route=clan_leader/delete-project', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        });
    });
}

// Manejar envío del formulario de crear proyecto
document.getElementById('createProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('?route=clan_leader/create-project', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeCreateProjectModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
    });
});

// Manejar envío del formulario de editar proyecto
document.getElementById('editProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('?route=clan_leader/update-project', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeEditProjectModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
    });
});

// Cerrar modales al hacer clic fuera
document.getElementById('createProjectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateProjectModal();
    }
});

document.getElementById('editProjectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditProjectModal();
    }
});

// ========== NUEVAS FUNCIONALIDADES MEJORADAS ==========

// Función para intercambiar entre vista de cards y lista
function switchView(viewType) {
    const cardsView = document.getElementById('cardsView');
    const listView = document.getElementById('listView');
    const cardsBtn = document.getElementById('cardsViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    // Remover clases activas
    cardsBtn.classList.remove('active');
    listBtn.classList.remove('active');
    
    if (viewType === 'cards') {
        cardsView.style.display = 'block';
        listView.style.display = 'none';
        cardsBtn.classList.add('active');
        
        // Guardar preferencia en localStorage
        localStorage.setItem('projectsViewType', 'cards');
    } else {
        cardsView.style.display = 'none';
        listView.style.display = 'block';
        listBtn.classList.add('active');
        
        // Guardar preferencia en localStorage
        localStorage.setItem('projectsViewType', 'list');
    }
}

// Cargar preferencia de vista al inicializar
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('projectsViewType') || 'cards';
    switchView(savedView);
});

// Abrir modal para crear tarea
function openCreateTaskModal(projectId) {
    // Redirigir a la página de creación de tareas con el proyecto preseleccionado
    window.location.href = `?route=clan_leader/tasks&action=create&project_id=${projectId}`;
}

// Función para mostrar mensajes toast
function showToast(message, type = 'info') {
    // Crear elemento toast si no existe
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    
    // Configurar mensaje y tipo
    toast.textContent = message;
    toast.className = `toast toast-${type} show`;
    
    // Ocultar después de 3 segundos
    setTimeout(() => {
        toast.classList.remove('show');
    }, 5000);
}

// Función para clonar proyecto
function openCloneProjectModal(projectId) {
    // Cargar datos del proyecto para mostrar en el modal
    fetch('?route=clan_leader/get-project-data&project_id=' + projectId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCloneProjectModal(data.project);
            } else {
                showToast('Error al cargar los datos del proyecto: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión al cargar los datos del proyecto', 'error');
        });
}

// Función para mostrar el modal de clonación de proyectos
function showCloneProjectModal(project) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content clone-project-modal">
            <div class="modal-header">
                <h3><i class="fas fa-copy"></i> Clonar Proyecto</h3>
                <button class="modal-close" onclick="closeCloneProjectModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="cloneProjectForm">
                    <input type="hidden" id="originalProjectId" value="${project.project_id}">
                    
                    <div class="form-group">
                        <label for="cloneProjectName">Nombre del proyecto</label>
                        <input type="text" id="cloneProjectName" name="project_name" value="${project.project_name} (Copia)" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cloneProjectDescription">Descripción</label>
                        <textarea id="cloneProjectDescription" name="description" rows="3">${project.description || ''}</textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cloneProjectStartDate">Fecha de inicio</label>
                            <input type="date" id="cloneProjectStartDate" name="start_date" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        
                        <div class="form-group">
                            <label for="cloneProjectEndDate">Fecha de fin</label>
                            <input type="date" id="cloneProjectEndDate" name="end_date" value="${project.time_limit || ''}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="cloneTasks" name="clone_tasks" checked>
                            Clonar también las tareas y subtareas del proyecto
                        </label>
                    </div>
                    
                    <div class="form-group" id="adjustDatesGroup" style="display: none;">
                        <label>
                            <input type="checkbox" id="adjustDates" name="adjust_dates" checked>
                            Ajustar fechas de tareas proporcionalmente según la nueva duración del proyecto
                        </label>
                        <small class="form-help">Las fechas de las tareas se ajustarán automáticamente para mantener la proporción original del proyecto.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeCloneProjectModal()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="cloneProject()">
                    <i class="fas fa-copy"></i> Clonar Proyecto
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'flex';
    
    // Mostrar/ocultar opción de ajuste de fechas según si se clonan tareas
    const cloneTasksCheckbox = document.getElementById('cloneTasks');
    const adjustDatesGroup = document.getElementById('adjustDatesGroup');
    
    cloneTasksCheckbox.addEventListener('change', function() {
        adjustDatesGroup.style.display = this.checked ? 'block' : 'none';
    });
}

// Función para cerrar el modal de clonación de proyectos
function closeCloneProjectModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}

// Función para ejecutar la clonación de proyectos
function cloneProject() {
    const form = document.getElementById('cloneProjectForm');
    const formData = new FormData(form);
    
    // Agregar campos adicionales
    formData.append('originalProjectId', document.getElementById('originalProjectId').value);
    formData.append('clone_tasks', document.getElementById('cloneTasks').checked ? '1' : '0');
    formData.append('adjust_dates', document.getElementById('adjustDates').checked ? '1' : '0');
    
    // Mostrar loading
    const submitBtn = document.querySelector('.btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Clonando...';
    submitBtn.disabled = true;
    
    fetch('?route=clan_leader/clone-project', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCloneProjectModal();
            showToast('Proyecto clonado exitosamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('Error al clonar el proyecto: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión al clonar el proyecto', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Toggle del menú del proyecto
function toggleProjectMenu(projectId) {
    const menu = document.getElementById(`projectMenu${projectId}`);
    
    if (!menu) {
        console.error(`No se encontró el menú con ID: projectMenu${projectId}`);
        return;
    }
    
    // Cerrar todos los otros menús
    const allMenus = document.querySelectorAll('.dropdown-menu-minimal, .dropdown-menu-list');
    allMenus.forEach(m => {
        if (m !== menu) {
            m.classList.remove('show');
        }
    });
    
    // Toggle del menú actual
    menu.classList.toggle('show');
}

// Cerrar menús al hacer clic fuera
document.addEventListener('click', function(e) {
    // Solo cerrar si no se hizo clic en ningún botón de menú
    if (!e.target.closest('.btn-menu-minimal') && !e.target.closest('.btn-list-action.menu')) {
        document.querySelectorAll('.dropdown-menu-minimal, .dropdown-menu-list').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Toggle delegación de proyecto
function toggleProjectDelegation(projectId, isAllowed) {
    const formData = new FormData();
    formData.append('project_id', projectId);
    formData.append('allow_delegation', isAllowed ? '1' : '0');
    
    fetch('?route=clan_leader/update-project-delegation', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Configuración actualizada', 'success');
            
            // Actualizar visualmente el checkbox
            const checkmark = document.querySelector(`[data-project-id="${projectId}"] + .checkmark-custom`);
            if (checkmark) {
                if (isAllowed) {
                    checkmark.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                    checkmark.style.borderColor = '#667eea';
                } else {
                    checkmark.style.background = '#fff';
                    checkmark.style.borderColor = '#d1d5db';
                }
            }
        } else {
            showToast(data.message || 'Error al actualizar configuración', 'error');
            // Revertir checkbox si hay error
            const checkbox = document.querySelector(`[data-project-id="${projectId}"]`);
            if (checkbox) {
                checkbox.checked = !isAllowed;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
        // Revertir checkbox si hay error
        const checkbox = document.querySelector(`[data-project-id="${projectId}"]`);
        if (checkbox) {
            checkbox.checked = !isAllowed;
        }
    });
}
</script>

<style>
/* Estilos para checkbox personalizado */
.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    gap: 10px;
    padding: 10px 0;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    background-color: #fff;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-label:hover .checkmark {
    border-color: #3b82f6;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 6px;
    height: 12px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.checkbox-label i {
    color: #6b7280;
    margin-right: 5px;
}

.checkbox-label input[type="checkbox"]:checked ~ i {
    color: #3b82f6;
}

/* ========== ESTILOS CONSISTENTES PARA PROYECTOS ========== */

/* Toggle de vista */
.view-toggle-minimal {
    display: flex;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    padding: 4px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-right: 16px;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.view-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: #7f8c8d;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 13px;
    font-weight: 600;
}

.view-btn:hover {
    color: #2c3e50;
    background: rgba(116, 75, 162, 0.1);
}

.view-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.view-btn.active i {
    color: white;
}

/* Grid de proyectos minimalista */
.projects-grid-minimal {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

/* Cards de proyecto consistentes */
.project-item-enhanced {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    padding: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
}

.project-item-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.01) 100%);
    pointer-events: none;
}

.project-item-enhanced:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

/* Header del proyecto minimalista */
.project-info-header {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}

.project-icon-minimal {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white !important;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.project-icon-minimal i {
    color: white !important;
}

.project-details-minimal {
    flex: 1;
    min-width: 0;
}

.project-name-minimal {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 8px 0;
    line-height: 1.3;
}

.project-description-minimal {
    font-size: 14px;
    color: #7f8c8d;
    line-height: 1.4;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.project-meta-minimal {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.project-status-minimal {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.project-status-minimal.status-active { 
    background: rgba(39, 174, 96, 0.1); 
    color: #27ae60; 
    border: 1px solid rgba(39, 174, 96, 0.2);
}

.project-status-minimal.status-completed { 
    background: rgba(52, 152, 219, 0.1); 
    color: #3498db; 
    border: 1px solid rgba(52, 152, 219, 0.2);
}

.project-status-minimal.status-pending { 
    background: rgba(243, 156, 18, 0.1); 
    color: #f39c12; 
    border: 1px solid rgba(243, 156, 18, 0.2);
}

.project-kpi-minimal {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #f39c12;
    font-weight: 600;
}

/* Menú minimalista */
.project-menu-minimal {
    position: relative;
}

.btn-menu-minimal {
    background: rgba(116, 75, 162, 0.1);
    border: none;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    color: #764ba2;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-menu-minimal:hover {
    background: rgba(116, 75, 162, 0.2);
    transform: scale(1.05);
}

.dropdown-menu-minimal {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    padding: 8px 0;
    min-width: 160px;
    z-index: 1000;
    border: 1px solid rgba(0, 0, 0, 0.05);
    display: none;
}

.dropdown-menu-minimal.show {
    display: block !important;
}

.menu-item-minimal {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
    color: #2c3e50;
    transition: all 0.2s ease;
    font-weight: 500;
}

.menu-item-minimal:hover {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
}

.menu-item-minimal.danger {
    color: #e74c3c;
}

.menu-item-minimal.danger:hover {
    background: rgba(231, 76, 60, 0.05);
}

/* Estadísticas minimalistas */
.project-stats-minimal {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    justify-content: space-between;
}

.stat-item-minimal {
    text-align: center;
    padding: 12px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    flex: 1;
    transition: all 0.2s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.stat-item-minimal:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.stat-item-minimal.completed {
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1) 0%, rgba(39, 174, 96, 0.05) 100%);
    border-color: rgba(39, 174, 96, 0.2);
}

.stat-item-minimal.progress {
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.05) 100%);
    border-color: rgba(52, 152, 219, 0.2);
}

.stat-icon-minimal {
    font-size: 18px;
    margin-bottom: 6px;
    color: #7f8c8d;
    transition: all 0.2s ease;
}

.stat-item-minimal.completed .stat-icon-minimal {
    color: #27ae60;
}

.stat-item-minimal.progress .stat-icon-minimal {
    color: #3498db;
}

.stat-number-minimal {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label-minimal {
    font-size: 11px;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

/* Barra de progreso minimalista */
.progress-bar-minimal {
    height: 8px;
    background: rgba(236, 240, 241, 0.8);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 16px;
    position: relative;
}

.progress-fill-minimal {
    height: 100%;
    background: linear-gradient(90deg, #27ae60 0%, #2ecc71 100%);
    border-radius: 4px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.progress-fill-minimal::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Sección de delegación minimalista */
.delegation-section-minimal {
    margin-bottom: 16px;
}

.checkbox-label-minimal {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 8px 0;
    transition: all 0.2s ease;
}

.checkbox-label-minimal input[type="checkbox"] {
    display: none !important;
    position: absolute !important;
    left: -9999px !important;
    opacity: 0 !important;
    pointer-events: none !important;
}

/* Asegurar que no aparezcan checkboxes nativos */
.delegation-toggle-hidden {
    display: none !important;
    position: absolute !important;
    left: -9999px !important;
    opacity: 0 !important;
    pointer-events: none !important;
}

.checkbox-label-minimal:hover {
    opacity: 0.8;
}

.checkbox-label-minimal:hover .checkmark-custom {
    border-color: #764ba2;
    transform: scale(1.05);
}

.checkbox-label-minimal .checkmark-custom {
    width: 24px;
    height: 24px;
    background-color: #fff;
    border: 2px solid #d1d5db;
    border-radius: 6px;
    position: relative;
    transition: all 0.3s ease;
    flex-shrink: 0;
    cursor: pointer;
}

.checkbox-label-minimal input[type="checkbox"]:checked + .checkmark-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.checkbox-label-minimal input[type="checkbox"]:checked + .checkmark-custom::after {
    content: '';
    position: absolute;
    left: 8px;
    top: 3px;
    width: 6px;
    height: 12px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.checkbox-text {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #5a6c7d;
    font-weight: 500;
}

.checkbox-text i {
    color: #764ba2;
    font-size: 16px;
}

/* Acciones minimalistas - 2 botones */
.project-actions-minimal {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.project-actions-minimal .btn-minimal {
    flex: 1;
    min-width: 0;
    justify-content: center;
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 600;
}

/* Estado vacío minimalista */
.empty-minimal {
    text-align: center;
    padding: 80px 20px;
    color: #7f8c8d;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 20px;
    margin: 40px 0;
    border: 2px dashed rgba(127, 140, 141, 0.2);
}

.empty-icon-minimal {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.6;
}

.empty-minimal h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-weight: 600;
}

.empty-minimal p {
    margin: 0 0 30px 0;
    font-size: 16px;
    line-height: 1.5;
}

/* ========== ESTILOS PARA VISTA DE LISTA ========== */

/* Tabla minimalista */
.projects-table-minimal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.table-header-minimal {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1.2fr;
    gap: 16px;
    padding: 16px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.header-cell {
    display: flex;
    align-items: center;
}

.table-row-minimal {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1.2fr;
    gap: 16px;
    padding: 16px 20px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    align-items: center;
}

.table-row-minimal:hover {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
}

.table-row-minimal:last-child {
    border-bottom: none;
}

/* Celda de proyecto */
.cell-project {
    display: flex;
    align-items: center;
    gap: 12px;
}

.project-icon-list {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.project-info-list {
    flex: 1;
    min-width: 0;
}

.project-name-list {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
    line-height: 1.3;
}

.project-description-list {
    font-size: 12px;
    color: #7f8c8d;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 4px;
}

.project-kpi-list {
    font-size: 11px;
    color: #f39c12;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Celda de estado */
.cell-status {
    display: flex;
    justify-content: center;
}

.status-badge-list {
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-badge-list.status-active { 
    background: rgba(39, 174, 96, 0.1); 
    color: #27ae60; 
    border: 1px solid rgba(39, 174, 96, 0.2);
}

.status-badge-list.status-completed { 
    background: rgba(52, 152, 219, 0.1); 
    color: #3498db; 
    border: 1px solid rgba(52, 152, 219, 0.2);
}

.status-badge-list.status-pending { 
    background: rgba(243, 156, 18, 0.1); 
    color: #f39c12; 
    border: 1px solid rgba(243, 156, 18, 0.2);
}

/* Celda de progreso */
.cell-progress {
    display: flex;
    justify-content: center;
}

.progress-container-list {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}

.progress-bar-list {
    flex: 1;
    height: 6px;
    background: #ecf0f1;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill-list {
    height: 100%;
    background: linear-gradient(90deg, #27ae60 0%, #2ecc71 100%);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.progress-text-list {
    font-size: 12px;
    font-weight: 600;
    color: #2c3e50;
    min-width: 35px;
}

/* Celda de tareas */
.cell-tasks {
    display: flex;
    justify-content: center;
}

.tasks-summary-list {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 14px;
    font-weight: 600;
}

.tasks-total {
    color: #2c3e50;
}

.tasks-separator {
    color: #bdc3c7;
}

.tasks-completed {
    color: #27ae60;
}

/* Celda de acciones */
.cell-actions {
    display: flex;
    justify-content: center;
    position: relative;
}

.actions-list {
    display: flex;
    gap: 6px;
    position: relative;
}

.btn-list-action {
    padding: 8px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.btn-list-action.primary {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.btn-list-action.primary:hover {
    background: rgba(52, 152, 219, 0.2);
    transform: scale(1.05);
}

.btn-list-action.secondary {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

.btn-list-action.secondary:hover {
    background: rgba(108, 117, 125, 0.2);
    transform: scale(1.05);
}

.btn-list-action.menu {
    background: rgba(116, 75, 162, 0.1);
    color: #764ba2;
}

.btn-list-action.menu:hover {
    background: rgba(116, 75, 162, 0.2);
    transform: scale(1.05);
}

/* Menú contextual para lista */
.dropdown-menu-list {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    padding: 6px 0;
    min-width: 140px;
    z-index: 9999;
    border: 1px solid rgba(0, 0, 0, 0.05);
    display: none;
    margin-top: 4px;
}

.dropdown-menu-list.show {
    display: block !important;
}

.menu-item-list {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 12px;
    color: #2c3e50;
    transition: background 0.2s ease;
    font-weight: 500;
}

.menu-item-list:hover {
    background: rgba(102, 126, 234, 0.05);
}

.menu-item-list.danger {
    color: #e74c3c;
}

.menu-item-list.danger:hover {
    background: rgba(231, 76, 60, 0.05);
}

/* Responsive design */
@media (max-width: 768px) {
    .projects-grid-minimal {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 16px 0;
    }
    
    .project-stats-minimal {
        flex-direction: column;
        gap: 8px;
    }
    
    .stat-item-minimal {
        padding: 8px;
    }
    
    .project-actions-minimal {
        flex-direction: column;
    }
    
    .project-actions-minimal .btn-minimal {
        flex: none;
    }
    
    /* Vista de lista responsive */
    .table-header-minimal,
    .table-row-minimal {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .table-header-minimal {
        display: none; /* Ocultar header en móvil */
    }
    
    .table-row-minimal {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 12px;
        background: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .cell-project,
    .cell-status,
    .cell-progress,
    .cell-tasks,
    .cell-actions {
        justify-content: flex-start;
    }
    
    .view-toggle-minimal {
        margin-bottom: 12px;
        margin-right: 0;
    }
}

.project-menu-btn {
    background: none;
    border: none;
    padding: 8px;
    border-radius: 6px;
    cursor: pointer;
    color: #666;
    transition: all 0.2s ease;
}

.project-menu-btn:hover {
    background: #f5f5f5;
    color: #333;
}

.project-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    padding: 8px 0;
    min-width: 180px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

.project-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
    color: #333;
    transition: background 0.2s ease;
}

.menu-item:hover {
    background: #f8f9fa;
}

.menu-item.danger {
    color: #dc3545;
}

.menu-item.danger:hover {
    background: #fff5f5;
}

/* Acciones del proyecto */
.project-actions-enhanced {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    flex: 1;
    justify-content: center;
    min-width: 0;
}

.btn-action.primary {
    background: #007bff;
    color: white;
}

.btn-action.primary:hover {
    background: #0056b3;
    transform: translateY(-1px);
}

.btn-action.secondary {
    background: #6c757d;
    color: white;
}

.btn-action.secondary:hover {
    background: #545b62;
    transform: translateY(-1px);
}

.btn-action.info {
    background: #17a2b8;
    color: white;
}

.btn-action.info:hover {
    background: #138496;
    transform: translateY(-1px);
}

/* Sección de tareas del proyecto */
.project-tasks-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #f1f3f4;
    background: #fafbfc;
    border-radius: 8px;
    padding: 15px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tasks-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.tasks-header h4 {
    margin: 0;
    color: #2c3e50;
    font-size: 16px;
}

.btn-mini {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    background: #e9ecef;
    color: #495057;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
}

.btn-mini:hover {
    background: #dee2e6;
}

.btn-mini.primary {
    background: #007bff;
    color: white;
}

.btn-mini.primary:hover {
    background: #0056b3;
}

/* Lista de tareas */
.tasks-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.task-item-mini {
    background: white;
    border-radius: 8px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-left: 4px solid #dee2e6;
}

.task-item-mini:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.task-item-mini.completed {
    border-left-color: #27ae60;
    opacity: 0.8;
}

.task-item-mini.in-progress {
    border-left-color: #3498db;
}

.task-item-mini.pending {
    border-left-color: #f39c12;
}

.task-info {
    flex: 1;
}

.task-header-inline {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.task-title {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
}

.task-badges {
    display: flex;
    gap: 6px;
}

.task-status, .task-priority {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.task-status.status-completed { background: #d4edda; color: #155724; }
.task-status.status-in-progress { background: #cce7ff; color: #004085; }
.task-status.status-pending { background: #fff3cd; color: #856404; }

.task-priority.priority-high { background: #f8d7da; color: #721c24; }
.task-priority.priority-medium { background: #fff3cd; color: #856404; }
.task-priority.priority-low { background: #d1ecf1; color: #0c5460; }

.task-description {
    font-size: 12px;
    color: #666;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.task-meta {
    display: flex;
    gap: 12px;
    font-size: 11px;
    color: #666;
}

.task-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Acciones de tarea */
.task-actions-mini {
    display: flex;
    gap: 4px;
    flex-shrink: 0;
}

.btn-task-action {
    padding: 6px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-task-action.edit {
    background: #fff3cd;
    color: #856404;
}

.btn-task-action.edit:hover {
    background: #ffeaa7;
}

.btn-task-action.delete {
    background: #f8d7da;
    color: #721c24;
}

.btn-task-action.delete:hover {
    background: #f5c6cb;
}

.btn-task-action.view {
    background: #cce7ff;
    color: #004085;
}

.btn-task-action.view:hover {
    background: #b3d7ff;
}

/* Estados especiales */
.loading-tasks, .no-tasks, .error-tasks {
    text-align: center;
    padding: 30px;
    color: #666;
}

.loading-tasks i {
    font-size: 24px;
    color: #007bff;
    margin-bottom: 10px;
}

.no-tasks i, .error-tasks i {
    font-size: 32px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* Toast notifications */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 6px;
    color: white;
    font-weight: 600;
    z-index: 10000;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast-success { background: #27ae60; }
.toast-error { background: #e74c3c; }
.toast-info { background: #3498db; }

/* Estilos para el modal de clonación de proyectos */
.clone-project-modal {
    max-width: 700px;
    width: 90%;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group input[type="checkbox"] {
    width: auto;
    margin-right: 0.5rem;
}

.form-help {
    display: block;
    margin-top: 0.25rem;
    font-size: 12px;
    color: #6b7280;
    font-style: italic;
}

.modal-footer {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.btn-primary {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.btn-primary:hover:not(:disabled) {
    background: #2563eb;
}

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

@media (max-width: 768px) {
    .clone-project-modal {
        width: 95%;
        margin: 1rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
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