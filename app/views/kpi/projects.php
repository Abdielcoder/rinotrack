<?php
// Configurar archivos adicionales para layout
$additionalCSS = [
    APP_URL . 'assets/css/kpi.css'
];

$additionalJS = [
    APP_URL . 'assets/js/kpi-projects.js'
];

// Calcular puntos
$assignedPoints = 0;
foreach ($projects as $project) {
    $assignedPoints += $project['kpi_points'];
}
$remainingPoints = $currentKPI ? $currentKPI['total_points'] - $assignedPoints : 0;

// Iniciar captura de contenido
ob_start();
?>

<div class="kpi-projects-container minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Asignación de KPIs</h1>
                <span class="subtitle">Gestión de proyectos y puntos KPI</span>
            </div>
            
            <div class="actions-minimal">
                <a href="?route=admin" class="btn-minimal">
                    <i class="fas fa-arrow-left"></i>
                    Volver a Admin
                </a>
                <a href="?route=kpi/dashboard" class="btn-minimal">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>
                <a href="?route=kpi/quarters" class="btn-minimal">
                    <i class="fas fa-calendar-alt"></i>
                    Trimestres
                </a>
            </div>
        </div>
        
        <!-- Período Actual Minimalista -->
        <?php if ($currentKPI): ?>
            <div class="period-minimal">
                <div class="period-info">
                    <span class="period-name">
                        ✅ <?php echo htmlspecialchars($currentKPI['quarter'] . ' ' . $currentKPI['year']); ?> (Activo)
                    </span>
                    <div class="period-stats">
                        <span class="stat-item"><?php echo number_format($remainingPoints); ?> disponibles</span>
                        <span class="stat-separator">de</span>
                        <span class="stat-item"><?php echo number_format($currentKPI['total_points']); ?> totales</span>
                    </div>
                    <div class="period-progress">
                        <span class="progress-label"><?php echo $currentKPI['total_points'] > 0 ? round(($assignedPoints / $currentKPI['total_points']) * 100, 1) : 0; ?>% asignado</span>
                        <div class="progress-minimal">
                            <div class="progress-fill" style="width: <?php echo $currentKPI['total_points'] > 0 ? ($assignedPoints / $currentKPI['total_points']) * 100 : 0; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert-minimal">
                <span>⚠️ No hay período KPI activo</span>
                <a href="?route=kpi/quarters" class="link-minimal">Gestionar Trimestres</a>
            </div>
        <?php endif; ?>
    </header>

    <?php if ($currentKPI): ?>
        <!-- Contenido Principal Minimalista -->
        <main class="content-minimal">
            <!-- Proyectos Pendientes -->
            <?php if (!empty($projectsWithoutKPI)): ?>
                <section class="projects-minimal" data-section="pending">
                    <div class="section-header-minimal">
                        <h3>Proyectos Pendientes (<?php echo count($projectsWithoutKPI); ?>)</h3>
                    </div>
                
                    <div class="projects-list">
                        <?php foreach ($projectsWithoutKPI as $project): ?>
                            <div class="project-item pending" data-project-id="<?php echo $project['project_id']; ?>">
                                <div class="project-info">
                                    <div class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                    <div class="project-clan"><?php echo htmlspecialchars($project['clan_name'] ?? 'Sin asignar'); ?></div>
                                </div>
                                
                                <div class="project-meta">
                                    <span class="project-date"><?php echo date('d/m/Y', strtotime($project['created_at'])); ?></span>
                                    <span class="project-status">⏳ Pendiente</span>
                                </div>
                                
                                <div class="project-action">
                                    <button class="btn-minimal primary" onclick="openAssignKPIModal(<?php echo $project['project_id']; ?>, '<?php echo htmlspecialchars($project['project_name']); ?>')">
                                        <i class="fas fa-plus"></i>
                                        Asignar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Proyectos con KPI -->
            <?php if (!empty($projects)): ?>
                <section class="projects-minimal" data-section="with-kpi">
                    <div class="section-header-minimal">
                        <h3>Proyectos con KPI (<?php echo count($projects); ?>)</h3>
                    </div>
                
                    <div class="projects-list">
                        <?php foreach ($projects as $project): ?>
                            <?php 
                            // Calcular progreso simplificado
                            $progressPercentage = $project['progress_percentage'];
                            ?>
                            <div class="project-item assigned" data-project-id="<?php echo $project['project_id']; ?>">
                                <div class="project-info">
                                    <div class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></div>
                                    <div class="project-clan"><?php echo htmlspecialchars($project['clan_name'] ?? 'Sin asignar'); ?></div>
                                </div>
                                
                                <div class="project-kpi">
                                    <span class="kpi-points"><?php echo number_format($project['kpi_points']); ?> pts</span>
                                    <span class="distribution-mode">
                                        <?php echo $project['task_distribution_mode'] === 'automatic' ? '🔄 Auto' : '📊 %'; ?>
                                    </span>
                                </div>
                                
                                <div class="project-progress">
                                    <div class="progress-bar-small">
                                        <div class="progress-fill-small" style="width: <?php echo $progressPercentage; ?>%"></div>
                                    </div>
                                    <span class="progress-text"><?php echo round($progressPercentage, 1); ?>%</span>
                                </div>
                                
                                <div class="project-actions">
                                    <button class="btn-action" onclick="openTasksModal(<?php echo $project['project_id']; ?>, '<?php echo htmlspecialchars($project['project_name']); ?>')" title="Gestionar Tareas">
                                        <i class="fas fa-tasks"></i>
                                    </button>
                                    <button class="btn-action" onclick="openEditKPIModal(<?php echo $project['project_id']; ?>, '<?php echo htmlspecialchars($project['project_name']); ?>', <?php echo $project['kpi_points']; ?>)" title="Editar KPI">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action" onclick="toggleDistributionMode(<?php echo $project['project_id']; ?>, '<?php echo $project['task_distribution_mode']; ?>')" title="Cambiar distribución">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Estado Vacío -->
            <?php if (empty($projects) && empty($projectsWithoutKPI)): ?>
                <section class="empty-minimal">
                    <span>📋 No hay proyectos disponibles</span>
                    <a href="?route=admin/projects" class="btn-minimal primary">
                        <i class="fas fa-plus"></i>
                        Crear Proyecto
                    </a>
                </section>
            <?php endif; ?>
        </main>
    <?php endif; ?>
</div>

<!-- Modales -->

<!-- Modal Asignar KPI -->
<div id="assignKPIModal" class="modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content large">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-plus-circle"></i>
                <span>Asignar KPI al Proyecto</span>
            </div>
            <button class="modal-close" onclick="closeAssignKPIModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="assignKPIForm" onsubmit="assignKPI(event)">
            <input type="hidden" id="assign_project_id" name="project_id">
            
            <div class="modal-body">
                <div class="project-display-card">
                    <div class="project-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="project-info">
                        <h4 id="assign_project_name">Nombre del Proyecto</h4>
                        <p>Asigna puntos KPI a este proyecto para el período activo</p>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-group">
                        <label for="kpi_points" class="form-label">
                            <i class="fas fa-star"></i>
                            Puntos KPI a Asignar
                        </label>
                        <input type="number" 
                               id="kpi_points" 
                               name="kpi_points" 
                               class="form-input"
                               min="1" 
                               max="<?php echo $currentKPI['total_points'] ?? 1000; ?>" 
                               placeholder="Ingresa los puntos..."
                               required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Puntos disponibles: <span id="available_points" class="highlight"><?php echo number_format($remainingPoints); ?></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-cogs"></i>
                            Modalidad de Distribución
                        </label>
                        <div class="distribution-options">
                            <label class="distribution-option">
                                <input type="radio" name="distribution_mode" value="automatic" checked>
                                <div class="option-content">
                                    <div class="option-header">
                                        <span class="option-icon">🤖</span>
                                        <span class="option-title">Distribución Automática</span>
                                    </div>
                                    <p class="option-description">
                                        Los puntos se distribuyen equitativamente entre todas las tareas del proyecto
                                    </p>
                                </div>
                            </label>
                            
                            <label class="distribution-option">
                                <input type="radio" name="distribution_mode" value="percentage">
                                <div class="option-content">
                                    <div class="option-header">
                                        <span class="option-icon">📊</span>
                                        <span class="option-title">Por Porcentaje Manual</span>
                                    </div>
                                    <p class="option-description">
                                        Defines manualmente qué porcentaje de puntos vale cada tarea
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="action-btn secondary" onclick="closeAssignKPIModal()">
                    <i class="fas fa-times"></i>
                    <span>Cancelar</span>
                </button>
                <button type="submit" class="action-btn primary">
                    <i class="fas fa-check"></i>
                    <span>Asignar KPI</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar KPI -->
<div id="editKPIModal" class="modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-edit"></i>
                <span>Editar KPI del Proyecto</span>
            </div>
            <button class="modal-close" onclick="closeEditKPIModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editKPIForm" onsubmit="editKPI(event)">
            <input type="hidden" id="edit_project_id" name="project_id">
            
            <div class="modal-body">
                <div class="project-display-card">
                    <div class="project-icon edit">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="project-info">
                        <h4 id="edit_project_name">Nombre del Proyecto</h4>
                        <p>Modifica los puntos KPI asignados a este proyecto</p>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-group">
                        <label for="edit_kpi_points" class="form-label">
                            <i class="fas fa-star"></i>
                            Nuevos Puntos KPI
                        </label>
                        <input type="number" 
                               id="edit_kpi_points" 
                               name="kpi_points" 
                               class="form-input"
                               min="1" 
                               placeholder="Ingresa los nuevos puntos..."
                               required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Puntos actuales: <span id="current_points" class="highlight">0</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="action-btn secondary" onclick="closeEditKPIModal()">
                    <i class="fas fa-times"></i>
                    <span>Cancelar</span>
                </button>
                <button type="submit" class="action-btn primary">
                    <i class="fas fa-save"></i>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tareas -->
<div id="tasksModal" class="modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content large">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-tasks"></i>
                <span id="tasksModalTitle">Gestionar Tareas</span>
            </div>
            <button class="modal-close" onclick="closeTasksModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body" id="tasksModalBody">
            <!-- El contenido se cargará aquí vía AJAX -->
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i> Cargando...
            </div>
        </div>
    </div>
</div>

<script>
// Funciones JavaScript básicas para KPI Projects
window.openAssignKPIModal = function(projectId, projectName) {
    document.getElementById("assign_project_id").value = projectId;
    document.getElementById("assign_project_name").textContent = projectName;
    document.getElementById("assignKPIModal").style.display = "flex";
    updateAvailablePoints();
};

window.closeAssignKPIModal = function() {
    document.getElementById("assignKPIModal").style.display = "none";
};

window.assignKPI = function(event) {
    event.preventDefault();
    const form = document.getElementById("assignKPIForm");
    const formData = new FormData(form);

    fetch(APP_URL + "?route=kpi/assign", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            setTimeout(() => window.location.reload(), 1000);
        }
    })
    .catch(err => {
        console.error("Error al asignar KPI:", err);
    });
};

window.updateAvailablePoints = function() {
    fetch(APP_URL + "?route=kpi/get-available-points")
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById("available_points").textContent = data.available_points;
        }
    });
};

window.openTasksModal = function(projectId, projectName) {
    document.getElementById("tasksModalTitle").textContent = "Gestionar Tareas - " + projectName;
    document.getElementById("tasksModal").style.display = "flex";
    loadTasks(projectId);
};

window.closeTasksModal = function() {
    document.getElementById("tasksModal").style.display = "none";
};

window.openEditKPIModal = function(projectId, projectName, currentPoints) {
    document.getElementById("edit_project_id").value = projectId;
    document.getElementById("edit_project_name").textContent = projectName;
    document.getElementById("edit_kpi_points").value = currentPoints;
    document.getElementById("current_points").textContent = currentPoints;
    document.getElementById("editKPIModal").style.display = "flex";
};

window.closeEditKPIModal = function() {
    document.getElementById("editKPIModal").style.display = "none";
};

window.editKPI = function(event) {
    event.preventDefault();
    const form = document.getElementById("editKPIForm");
    const formData = new FormData(form);

    fetch(APP_URL + "?route=kpi/assign", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            setTimeout(() => window.location.reload(), 1000);
        }
    })
    .catch(err => {
        console.error("Error al editar KPI:", err);
    });
};

window.toggleDistributionMode = function(projectId, currentMode) {
    const newMode = currentMode === "automatic" ? "percentage" : "automatic";
    
    if (confirm("¿Quieres cambiar el modo de distribución a " + newMode + "?")) {
        const formData = new FormData();
        formData.append("project_id", projectId);
        formData.append("mode", newMode);

        fetch(APP_URL + "?route=kpi/change-distribution", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                setTimeout(() => window.location.reload(), 1000);
            }
        })
        .catch(err => {
            console.error("Error al cambiar modo de distribución:", err);
        });
    }
};

window.loadTasks = function(projectId) {
    fetch(APP_URL + "?route=kpi/get-tasks&project_id=" + projectId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTasks(data.tasks, data.project);
        } else {
            document.getElementById("tasksModalBody").innerHTML = 
                "<div class='error-message'>Problema al cargar las tareas: " + (data.message || "Problema desconocido") + "</div>";
        }
    })
    .catch(err => {
        document.getElementById("tasksModalBody").innerHTML = 
            "<div class='error-message'>Problema de red al cargar las tareas</div>";
    });
};

window.renderTasks = function(tasks, project) {
    const modalBody = document.getElementById("tasksModalBody");
    
    if (!tasks || tasks.length === 0) {
        modalBody.innerHTML = "<div class='empty-state'>No hay tareas para este proyecto</div>";
        return;
    }
    
    let html = `
        <div class="tasks-header">
            <h3>Proyecto: ${project.project_name}</h3>
            <button class="btn-minimal primary" onclick="addTask(event, ${project.project_id})">
                <i class="fas fa-plus"></i> Agregar Tarea
            </button>
        </div>
        <div class="tasks-list">
    `;
    
    tasks.forEach(task => {
        const statusText = task.is_completed ? "Completada" : "Pendiente";
        const statusClass = task.is_completed ? "completed" : "pending";
        const checkboxChecked = task.is_completed ? "checked" : "";
        
        html += `
            <div class="task-item ${statusClass}" data-task-id="${task.task_id}">
                <div class="task-checkbox">
                    <input type="checkbox" ${checkboxChecked} 
                           onchange="toggleTask(${task.task_id}, this.checked, ${project.project_id})">
                </div>
                <div class="task-content">
                    <div class="task-title">${task.task_name}</div>
                    <div class="task-description">${task.description || "Sin descripción"}</div>
                    <div class="task-meta">
                        <span class="task-status ${statusClass}">${statusText}</span>
                        <span class="task-points">${task.points || 0} pts</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += "</div>";
    modalBody.innerHTML = html;
};

window.addTask = function(event, projectId) {
    event.preventDefault();
    
    const taskName = prompt("Nombre de la tarea:");
    if (!taskName) return;
    
    const taskDescription = prompt("Descripción de la tarea (opcional):");
    const points = prompt("Puntos de la tarea:", "10");
    
    const formData = new FormData();
    formData.append("project_id", projectId);
    formData.append("task_name", taskName);
    formData.append("description", taskDescription || "");
    formData.append("points", points || "10");

    fetch(APP_URL + "?route=kpi/add-task", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTasks(projectId);
        }
    })
    .catch(err => {
        console.error("Error al agregar tarea:", err);
    });
};

window.toggleTask = function(taskId, isCompleted, projectId) {
    const formData = new FormData();
    formData.append("task_id", taskId);
    formData.append("is_completed", isCompleted ? "1" : "0");

    fetch(APP_URL + "?route=kpi/toggle-task-status", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskElement) {
                taskElement.className = `task-item ${isCompleted ? "completed" : "pending"}`;
                const statusElement = taskElement.querySelector(".task-status");
                if (statusElement) {
                    statusElement.textContent = isCompleted ? "Completada" : "Pendiente";
                    statusElement.className = `task-status ${isCompleted ? "completed" : "pending"}`;
                }
            }
        } else {
            const checkbox = document.querySelector(`[data-task-id="${taskId}"] input[type="checkbox"]`);
            if (checkbox) {
                checkbox.checked = !isCompleted;
            }
        }
    })
    .catch(err => {
        console.error("Error al actualizar tarea:", err);
        const checkbox = document.querySelector(`[data-task-id="${taskId}"] input[type="checkbox"]`);
        if (checkbox) {
            checkbox.checked = !isCompleted;
        }
    });
};
</script>

<?php
// Capturar contenido y pasarlo al layout
$content = ob_get_clean();

// Asegurar que las variables estén disponibles para el layout
if (!isset($additionalJS)) {
    $additionalJS = [
        APP_URL . 'assets/js/kpi-projects.js'
    ];
}

if (!isset($currentPage)) {
    $currentPage = 'kpi';
}

if (!isset($user)) {
    $user = (new Auth())->getCurrentUser();
}

require_once __DIR__ . '/../admin/layout.php';
?> 