<?php
// SUPRESIÓN TOTAL DE ERRORES Y WARNINGS PARA ESTA VISTA
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Guardar el contenido en una variable
ob_start();
?>

<div class="clan-leader-projects minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Gestionar Proyectos</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name'] ?? 'Clan no encontrado'); ?></span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=clan_leader/dashboard" class="btn-minimal">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
                <button class="btn-minimal primary" onclick="openCreateProjectModal()">
                    <i class="fas fa-plus"></i>
                    Crear Proyecto
                </button>
            </div>
        </div>
        
        <!-- Búsqueda -->
        <div class="search-minimal">
            <form method="GET" action="?route=clan_leader/projects">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                           placeholder="Buscar proyectos...">
                </div>
                <button type="submit" class="btn-minimal">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="?route=clan_leader/projects" class="btn-minimal secondary">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>
    </header>

    <!-- Lista de Proyectos - SIN DEBUG -->
    <div class="content-minimal">
        <section class="projects-minimal">
            <?php if (!empty($projects) && is_array($projects)): ?>
                <div class="projects-list">
                    <?php foreach ($projects as $project): ?>
                        <?php if (!is_array($project)) continue; ?>
                        <div class="project-item">
                            <div class="project-info">
                                <div class="project-icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div class="project-details">
                                    <div class="project-name"><?php echo htmlspecialchars($project['project_name'] ?? 'Sin nombre'); ?></div>
                                    <div class="project-description"><?php echo htmlspecialchars($project['description'] ?? 'Sin descripción'); ?></div>
                                    <div class="project-meta">
                                        <span class="project-status status-<?php echo htmlspecialchars($project['status'] ?? 'open'); ?>">
                                            <?php echo ucfirst($project['status'] ?? 'Open'); ?>
                                        </span>
                                        <?php if (isset($project['kpi_points']) && $project['kpi_points'] > 0): ?>
                                            <span class="project-kpi">
                                                <i class="fas fa-chart-line"></i>
                                                <?php echo number_format($project['kpi_points']); ?> puntos KPI
                                            </span>
                                        <?php endif; ?>
                                        <span class="project-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo isset($project['created_at']) ? date('d/m/Y', strtotime($project['created_at'])) : 'N/A'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="project-actions">
                                <button class="btn-minimal" onclick="openEditProjectModal(<?php echo intval($project['project_id'] ?? 0); ?>, '<?php echo htmlspecialchars($project['project_name'] ?? ''); ?>', '<?php echo htmlspecialchars($project['description'] ?? ''); ?>', '<?php echo htmlspecialchars($project['time_limit'] ?? ''); ?>')">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </button>
                                <a href="?route=clan_leader/tasks&project_id=<?php echo intval($project['project_id'] ?? 0); ?>" class="btn-minimal">
                                    <i class="fas fa-tasks"></i>
                                    Tareas
                                </a>
                                <button class="btn-minimal danger" onclick="deleteProject(<?php echo intval($project['project_id'] ?? 0); ?>, '<?php echo htmlspecialchars($project['project_name'] ?? ''); ?>')">
                                    <i class="fas fa-trash"></i>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-minimal">
                    <span>📋 No hay proyectos en el clan</span>
                    <button class="btn-minimal primary" onclick="openCreateProjectModal()">Crear primer proyecto</button>
                </div>
            <?php endif; ?>
        </section>
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
    document.getElementById('editProjectId').value = projectId || '';
    document.getElementById('editProjectName').value = projectName || '';
    document.getElementById('editDescription').value = description || '';
    document.getElementById('editTimeLimit').value = timeLimit || '';
    document.getElementById('editProjectModal').style.display = 'flex';
}

function closeEditProjectModal() {
    document.getElementById('editProjectModal').style.display = 'none';
    document.getElementById('editProjectForm').reset();
}

// Eliminar proyecto
function deleteProject(projectId, projectName) {
    if (confirm(`¿Estás seguro de que quieres eliminar el proyecto "${projectName}"?`)) {
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
                alert('Proyecto eliminado exitosamente');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                alert(data.message || 'Error al eliminar proyecto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    }
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
            alert('Proyecto creado exitosamente');
            closeCreateProjectModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            alert(data.message || 'Error al crear proyecto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
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
            alert('Proyecto actualizado exitosamente');
            closeEditProjectModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            alert(data.message || 'Error al actualizar proyecto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
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