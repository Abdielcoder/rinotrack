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
                <div class="clan-icon-large"><?php echo $clanIcon ?? '⚡'; ?></div>
                <h1>Gestión de Acciones</h1>
                <span class="subtitle">Administra acciones y tareas de <?php echo htmlspecialchars($clan['clan_name'] ?? 'tu clan'); ?></span>
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
                
                <button class="btn-minimal primary" onclick="openCreateActionModal()">
                    <i class="fas fa-plus"></i>
                    Nueva Acción
                </button>
            </div>
        </div>
        
        <!-- Búsqueda consistente -->
        <div class="search-minimal">
            <form method="GET" action="?route=clan_leader/actions" class="search-form">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Buscar acciones...">
                </div>
                <button type="submit" class="btn-minimal">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="?route=clan_leader/actions" class="btn-minimal secondary">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="content-minimal">
        <?php if (!empty($actions)): ?>
            <!-- Vista de Cards (por defecto) -->
            <section class="projects-minimal animate-fade-in" id="cardsView">
                <div class="projects-grid-minimal">
                    <?php foreach ($actions as $action): ?>
                    <div class="project-item-enhanced">
                        <!-- Header de la Acción -->
                        <div class="project-info-header">
                            <div class="project-icon-minimal">
                                <i class="fas fa-bolt icon-gradient"></i>
                            </div>
                            <div class="project-details-minimal">
                                <div class="project-name-minimal"><?= htmlspecialchars($action['project_name']) ?></div>
                                <?php if (!empty($action['description'])): ?>
                                <div class="project-description-minimal"><?= htmlspecialchars($action['description']) ?></div>
                                <?php endif; ?>
                                <div class="project-meta-minimal">
                                    <span class="project-status-minimal status-<?= $action['status'] ?>">
                                        <?= ucfirst($action['status']) ?>
                                    </span>
                                    <?php if (isset($action['kpi_points']) && $action['kpi_points'] > 0): ?>
                                    <span class="project-kpi-minimal">
                                        <i class="fas fa-star"></i>
                                        <?= number_format($action['kpi_points']) ?> KPI
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="project-menu-minimal">
                                <button class="btn-menu-minimal" onclick="toggleActionMenu(<?= $action['project_id'] ?>)">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu-minimal" id="actionMenu<?= $action['project_id'] ?>">
                                    <button class="menu-item-minimal" onclick="openEditActionModal(<?= $action['project_id'] ?>, <?= json_encode($action['project_name']) ?>, <?= json_encode($action['description']) ?>, <?= json_encode($action['time_limit'] ?? '') ?>)">
                                        <i class="fas fa-edit"></i>
                                        Editar
                                    </button>
                                    <button class="menu-item-minimal" onclick="openCloneActionModal(<?= $action['project_id'] ?>)">
                                        <i class="fas fa-copy"></i>
                                        Clonar
                                    </button>
                                    <button class="menu-item-minimal danger" onclick="deleteAction(<?= $action['project_id'] ?>, <?= json_encode($action['project_name']) ?>)">
                                        <i class="fas fa-trash"></i>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($action['description'])): ?>
                        <div class="project-description">
                            <?= htmlspecialchars($action['description']) ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Estadísticas de la Acción -->
                        <div class="project-stats-minimal">
                            <div class="stat-item-minimal">
                                <div class="stat-icon-minimal">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="stat-content-minimal">
                                    <div class="stat-number-minimal"><?= $action['total_tasks'] ?? 0 ?></div>
                                    <div class="stat-label-minimal">Total</div>
                                </div>
                            </div>
                            <div class="stat-item-minimal completed">
                                <div class="stat-icon-minimal">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-content-minimal">
                                    <div class="stat-number-minimal"><?= $action['completed_tasks'] ?? 0 ?></div>
                                    <div class="stat-label-minimal">Completadas</div>
                                </div>
                            </div>
                            <div class="stat-item-minimal progress">
                                <div class="stat-icon-minimal">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-content-minimal">
                                    <div class="stat-number-minimal"><?= $action['progress_percentage'] ?? 0 ?>%</div>
                                    <div class="stat-label-minimal">Progreso</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Barra de Progreso -->
                        <div class="progress-bar-minimal">
                            <div class="progress-fill-minimal" style="width: <?= $action['progress_percentage'] ?? 0 ?>%"></div>
                        </div>
                        
                        <!-- Delegación -->
                        <div class="delegation-section-minimal">
                            <label class="checkbox-label-minimal">
                                <input type="checkbox" 
                                       class="delegation-toggle-hidden" 
                                       data-action-id="<?= $action['project_id'] ?>"
                                       <?= isset($action['allow_delegation']) && $action['allow_delegation'] ? 'checked' : '' ?>
                                       onchange="toggleActionDelegation(<?= $action['project_id'] ?>, this.checked)"
                                       style="display: none !important; position: absolute !important; left: -9999px !important;">
                                <span class="checkmark-custom"></span>
                                <span class="checkbox-text">
                                    <i class="fas fa-user-plus"></i>
                                    Permitir delegación de tareas
                                </span>
                            </label>
                        </div>
                        
                        <!-- Acciones de la Acción -->
                        <div class="project-actions-minimal">
                            <a href="?route=clan_leader/tasks&action_id=<?= $action['project_id'] ?>" class="btn-minimal primary">
                                <i class="fas fa-eye"></i>
                                Ver Tareas
                            </a>
                            <button class="btn-minimal secondary" onclick="openCreateTaskModal(<?= $action['project_id'] ?>)">
                                <i class="fas fa-plus"></i>
                                Nueva Tarea
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            
            <!-- Vista de Lista (oculta por defecto) -->
            <section class="projects-list-view animate-fade-in" id="listView" style="display: none;">
                <div class="projects-table-minimal">
                    <div class="table-header-minimal">
                        <div class="header-cell">Acción</div>
                        <div class="header-cell">Estado</div>
                        <div class="header-cell">Progreso</div>
                        <div class="header-cell">Tareas</div>
                        <div class="header-cell">Acciones</div>
                    </div>
                    
                    <?php foreach ($actions as $action): ?>
                    <div class="table-row-minimal">
                        <div class="cell-project">
                            <div class="project-icon-list">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="project-info-list">
                                <div class="project-name-list"><?= htmlspecialchars($action['project_name']) ?></div>
                                <?php if (!empty($action['description'])): ?>
                                <div class="project-description-list"><?= htmlspecialchars($action['description']) ?></div>
                                <?php endif; ?>
                                <?php if (isset($action['kpi_points']) && $action['kpi_points'] > 0): ?>
                                <div class="project-kpi-list">
                                    <i class="fas fa-star"></i>
                                    <?= number_format($action['kpi_points']) ?> KPI
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="cell-status">
                            <span class="status-badge-list status-<?= $action['status'] ?>">
                                <?= ucfirst($action['status']) ?>
                            </span>
                        </div>
                        
                        <div class="cell-progress">
                            <div class="progress-container-list">
                                <div class="progress-bar-list">
                                    <div class="progress-fill-list" style="width: <?= $action['progress_percentage'] ?? 0 ?>%"></div>
                                </div>
                                <span class="progress-text-list"><?= $action['progress_percentage'] ?? 0 ?>%</span>
                            </div>
                        </div>
                        
                        <div class="cell-tasks">
                            <div class="tasks-summary-list">
                                <span class="tasks-total"><?= $action['total_tasks'] ?? 0 ?></span>
                                <span class="tasks-separator">/</span>
                                <span class="tasks-completed"><?= $action['completed_tasks'] ?? 0 ?></span>
                            </div>
                        </div>
                        
                        <div class="cell-actions">
                            <div class="actions-list">
                                <a href="?route=clan_leader/tasks&action_id=<?= $action['project_id'] ?>" class="btn-list-action primary" title="Ver Tareas">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-list-action secondary" onclick="openCreateTaskModal(<?= $action['project_id'] ?>)" title="Nueva Tarea">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <div class="dropdown-container">
                                    <button class="btn-list-action menu" onclick="toggleListActionMenu(<?= $action['project_id'] ?>)" title="Más opciones">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu-list" id="listActionMenu<?= $action['project_id'] ?>">
                                        <button class="menu-item-list" onclick="openEditActionModal(<?= $action['project_id'] ?>, <?= json_encode($action['project_name']) ?>, <?= json_encode($action['description']) ?>, <?= json_encode($action['time_limit'] ?? '') ?>)">
                                            <i class="fas fa-edit"></i>
                                            Editar
                                        </button>
                                        <button class="menu-item-list" onclick="openCloneActionModal(<?= $action['project_id'] ?>)">
                                            <i class="fas fa-copy"></i>
                                            Clonar
                                        </button>
                                        <button class="menu-item-list danger" onclick="deleteAction(<?= $action['project_id'] ?>, <?= json_encode($action['project_name']) ?>)">
                                            <i class="fas fa-trash"></i>
                                            Eliminar
                                        </button>
                                    </div>
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
                    ⚡
                </div>
                <h3>No hay acciones en el clan</h3>
                <p>Crea tu primera acción para comenzar a organizar las tareas del equipo.</p>
                <button class="btn-minimal primary" onclick="openCreateActionModal()">
                    <i class="fas fa-plus"></i>
                    Crear primera acción
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para crear acción -->
<div id="createActionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Crear Nueva Acción</h3>
            <button class="modal-close" onclick="closeCreateActionModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="createActionForm" class="modal-form">
                <div class="form-group">
                    <label for="actionName">
                        <i class="fas fa-bolt"></i>
                        Nombre de la Acción
                    </label>
                    <input type="text" id="actionName" name="actionName" required 
                           placeholder="Ingrese el nombre de la acción">
                </div>
                
                <div class="form-group">
                    <label for="description">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </label>
                    <textarea id="description" name="description" required 
                              placeholder="Describa la acción" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="timeLimit">
                        <i class="fas fa-calendar-alt"></i>
                        Fecha Límite
                    </label>
                    <input type="date" id="timeLimit" name="timeLimit" 
                           placeholder="Seleccione la fecha límite de la acción">
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
            <button type="button" class="action-btn secondary" onclick="closeCreateActionModal()">
                <i class="fas fa-times"></i>
                <span>Cancelar</span>
            </button>
            <button type="submit" form="createActionForm" class="action-btn primary">
                <i class="fas fa-plus"></i>
                <span>Crear Acción</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal para editar acción -->
<div id="editActionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Acción</h3>
            <button class="modal-close" onclick="closeEditActionModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="editActionForm" class="modal-form">
                <input type="hidden" id="editActionId" name="actionId">
                
                <div class="form-group">
                    <label for="editActionName">
                        <i class="fas fa-bolt"></i>
                        Nombre de la Acción
                    </label>
                    <input type="text" id="editActionName" name="actionName" required 
                           placeholder="Ingrese el nombre de la acción">
                </div>
                
                <div class="form-group">
                    <label for="editDescription">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </label>
                    <textarea id="editDescription" name="description" required 
                              placeholder="Describa la acción" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="editTimeLimit">
                        <i class="fas fa-calendar-alt"></i>
                        Fecha Límite
                    </label>
                    <input type="date" id="editTimeLimit" name="timeLimit" 
                           placeholder="Seleccione la fecha límite de la acción">
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="action-btn secondary" onclick="closeEditActionModal()">
                <i class="fas fa-times"></i>
                <span>Cancelar</span>
            </button>
            <button type="submit" form="editActionForm" class="action-btn primary">
                <i class="fas fa-save"></i>
                <span>Guardar Cambios</span>
            </button>
        </div>
    </div>
</div>

<script>
// Funciones para el modal de crear acción
function openCreateActionModal() {
    document.getElementById('createActionModal').style.display = 'flex';
}

function closeCreateActionModal() {
    document.getElementById('createActionModal').style.display = 'none';
    document.getElementById('createActionForm').reset();
}

// Funciones para el modal de editar acción
function openEditActionModal(actionId, actionName, description, timeLimit) {
    document.getElementById('editActionId').value = actionId;
    document.getElementById('editActionName').value = actionName;
    document.getElementById('editDescription').value = description;
    document.getElementById('editTimeLimit').value = timeLimit || '';
    document.getElementById('editActionModal').style.display = 'flex';
}

function closeEditActionModal() {
    document.getElementById('editActionModal').style.display = 'none';
    document.getElementById('editActionForm').reset();
}

// Eliminar acción
function deleteAction(actionId, actionName) {
    confirmDelete(`¿Estás seguro de que quieres eliminar la acción "${actionName}"?`, () => {
        const formData = new FormData();
        formData.append('actionId', actionId);
        
        fetch('?route=clan_leader/delete-action', {
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

// Manejar envío del formulario de crear acción
document.getElementById('createActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('?route=clan_leader/create-action', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeCreateActionModal();
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

// Manejar envío del formulario de editar acción
document.getElementById('editActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('?route=clan_leader/update-action', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeEditActionModal();
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
document.getElementById('createActionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateActionModal();
    }
});

document.getElementById('editActionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditActionModal();
    }
});

// ========== FUNCIONALIDADES MEJORADAS PARA ACCIONES ==========

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
        localStorage.setItem('actionsViewType', 'cards');
    } else {
        cardsView.style.display = 'none';
        listView.style.display = 'block';
        listBtn.classList.add('active');
        
        // Guardar preferencia en localStorage
        localStorage.setItem('actionsViewType', 'list');
    }
}

// Cargar preferencia de vista al inicializar
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('actionsViewType') || 'cards';
    switchView(savedView);
});

// Abrir modal para crear tarea
function openCreateTaskModal(actionId) {
    // Redirigir a la página de creación de tareas con la acción preseleccionada
    window.location.href = `?route=clan_leader/tasks&action=create&action_id=${actionId}`;
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

// Función para clonar acción
function openCloneActionModal(actionId) {
    // Cargar datos de la acción para mostrar en el modal
    fetch('?route=clan_leader/get-action-data&action_id=' + actionId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCloneActionModal(data.action);
            } else {
                showToast('Error al cargar los datos de la acción: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error de conexión al cargar los datos de la acción', 'error');
        });
}

// Función para mostrar el modal de clonación de acciones
function showCloneActionModal(action) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content clone-project-modal">
            <div class="modal-header">
                <h3><i class="fas fa-copy"></i> Clonar Acción</h3>
                <button class="modal-close" onclick="closeCloneActionModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="cloneActionForm">
                    <input type="hidden" id="originalActionId" value="${action.project_id}">
                    
                    <div class="form-group">
                        <label for="cloneActionName">Nombre de la acción</label>
                        <input type="text" id="cloneActionName" name="action_name" value="${action.project_name} (Copia)" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cloneActionDescription">Descripción</label>
                        <textarea id="cloneActionDescription" name="description" rows="3">${action.description || ''}</textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cloneActionStartDate">Fecha de inicio</label>
                            <input type="date" id="cloneActionStartDate" name="start_date" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        
                        <div class="form-group">
                            <label for="cloneActionEndDate">Fecha de fin</label>
                            <input type="date" id="cloneActionEndDate" name="end_date" value="${action.time_limit || ''}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="cloneTasks" name="clone_tasks" checked>
                            Clonar también las tareas y subtareas de la acción
                        </label>
                    </div>
                    
                    <div class="form-group" id="adjustDatesGroup" style="display: none;">
                        <label>
                            <input type="checkbox" id="adjustDates" name="adjust_dates" checked>
                            Ajustar fechas de tareas proporcionalmente según la nueva duración de la acción
                        </label>
                        <small class="form-help">Las fechas de las tareas se ajustarán automáticamente para mantener la proporción original de la acción.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeCloneActionModal()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="cloneAction()">
                    <i class="fas fa-copy"></i> Clonar Acción
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

// Función para cerrar el modal de clonación de acciones
function closeCloneActionModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}

// Función para ejecutar la clonación de acciones
function cloneAction() {
    const form = document.getElementById('cloneActionForm');
    const formData = new FormData(form);
    
    // Agregar campos adicionales
    formData.append('originalActionId', document.getElementById('originalActionId').value);
    formData.append('clone_tasks', document.getElementById('cloneTasks').checked ? '1' : '0');
    formData.append('adjust_dates', document.getElementById('adjustDates').checked ? '1' : '0');
    
    // Mostrar loading
    const submitBtn = document.querySelector('.btn-primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Clonando...';
    submitBtn.disabled = true;
    
    fetch('?route=clan_leader/clone-action', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCloneActionModal();
            showToast('Acción clonada exitosamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('Error al clonar la acción: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión al clonar la acción', 'error');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Función específica para el menú de la vista lista
function toggleListActionMenu(actionId) {
    const menu = document.getElementById(`listActionMenu${actionId}`);
    
    if (!menu) {
        console.error(`No se encontró el menú con ID: listActionMenu${actionId}`);
        return;
    }
    
    // Cerrar todos los otros menús de lista
    const allListMenus = document.querySelectorAll('.dropdown-menu-list');
    allListMenus.forEach(m => {
        if (m !== menu) {
            m.style.display = 'none';
        }
    });
    
    // Toggle del menú actual
    if (menu.style.display === 'block') {
        menu.style.display = 'none';
    } else {
        menu.style.display = 'block';
    }
}

// Función para el menú de la vista cards (mantener compatibilidad)
function toggleActionMenu(actionId) {
    const menu = document.getElementById(`actionMenu${actionId}`);
    
    if (!menu) {
        console.error(`No se encontró el menú con ID: actionMenu${actionId}`);
        return;
    }
    
    // Cerrar todos los otros menús de cards
    const allCardMenus = document.querySelectorAll('.dropdown-menu-minimal');
    allCardMenus.forEach(m => {
        if (m !== menu) {
            m.style.display = 'none';
        }
    });
    
    // Toggle del menú actual
    if (menu.style.display === 'block') {
        menu.style.display = 'none';
    } else {
        menu.style.display = 'block';
    }
}

// Cerrar menús al hacer clic fuera
document.addEventListener('click', function(e) {
    // Solo cerrar si no se hizo clic en ningún botón de menú o contenedor
    if (!e.target.closest('.dropdown-container') && !e.target.closest('.btn-menu-minimal') && !e.target.closest('.btn-list-action.menu')) {
        document.querySelectorAll('.dropdown-menu-minimal, .dropdown-menu-list').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

// Toggle delegación de acción
function toggleActionDelegation(actionId, isAllowed) {
    const formData = new FormData();
    formData.append('action_id', actionId);
    formData.append('allow_delegation', isAllowed ? '1' : '0');
    
    fetch('?route=clan_leader/update-action-delegation', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Configuración actualizada', 'success');
            
            // Actualizar visualmente el checkbox
            const checkmark = document.querySelector(`[data-action-id="${actionId}"] + .checkmark-custom`);
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
            const checkbox = document.querySelector(`[data-action-id="${actionId}"]`);
            if (checkbox) {
                checkbox.checked = !isAllowed;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión', 'error');
        // Revertir checkbox si hay error
        const checkbox = document.querySelector(`[data-action-id="${actionId}"]`);
        if (checkbox) {
            checkbox.checked = !isAllowed;
        }
    });
}

// Función confirmDelete (reutilizada de proyectos)
function confirmDelete(message, callback) {
    if (confirm(message)) {
        callback();
    }
}
</script>

<?php
// Guardar el contenido generado
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
