// Funciones para modales de KPI
function openAssignKPIModal(projectId, projectName) {
    document.getElementById('assign_project_id').value = projectId;
    document.getElementById('assign_project_name').textContent = projectName;
    document.getElementById('assignKPIModal').style.display = 'flex';
    updateAvailablePoints();
}

function closeAssignKPIModal() {
    document.getElementById('assignKPIModal').style.display = 'none';
}

function openEditKPIModal(projectId, projectName, currentPoints) {
    document.getElementById('edit_project_id').value = projectId;
    document.getElementById('edit_project_name').textContent = projectName;
    document.getElementById('edit_kpi_points').value = currentPoints;
    document.getElementById('current_points').textContent = currentPoints;
    document.getElementById('editKPIModal').style.display = 'flex';
}

function closeEditKPIModal() {
    document.getElementById('editKPIModal').style.display = 'none';
}

// Lógica para asignar KPI
function assignKPI(event) {
    event.preventDefault();
    const form = document.getElementById('assignKPIForm');
    const formData = new FormData(form);

    fetch(APP_URL + '?route=kpi/assign', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('KPI asignado exitosamente', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error al asignar KPI', 'error');
        }
    })
    .catch(error => {
        showToast('Error de red al asignar KPI', 'error');
    });
}

// Lógica para editar KPI (similar a asignar)
function editKPI(event) {
    event.preventDefault();
    const form = document.getElementById('editKPIForm');
    const formData = new FormData(form);

    fetch(APP_URL + '?route=kpi/assign', { // Reutilizamos la misma ruta del controlador
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('KPI actualizado exitosamente', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(data.message || 'Error al actualizar KPI', 'error');
        }
    })
    .catch(error => {
        showToast('Error de red al actualizar KPI', 'error');
    });
}


// Actualizar puntos disponibles
function updateAvailablePoints() {
    fetch(APP_URL + '?route=kpi/get-available-points')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('available_points').textContent = data.available_points;
        }
    });
}

// Cambiar modo de distribución
function toggleDistributionMode(projectId, currentMode) {
    const newMode = currentMode === 'automatic' ? 'percentage' : 'automatic';
            showConfirmationModal({
            title: 'Confirmar Cambio de Modo',
            message: `¿Quieres cambiar el modo de distribución a "${newMode}"?`,
            type: 'warning',
            confirmText: 'Cambiar',
            cancelText: 'Cancelar',
            onConfirm: () => {
        const formData = new FormData();
        formData.append('project_id', projectId);
        formData.append('mode', newMode);

        fetch(APP_URL + '?route=kpi/change-distribution', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Modo de distribución cambiado', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Error al cambiar modo', 'error');
            }
        });
    }
}

// --- NUEVAS FUNCIONES PARA GESTIÓN DE TAREAS ---

function openTasksModal(projectId, projectName) {
    console.log('openTasksModal called with:', projectId, projectName); // DEBUG
    document.getElementById('tasksModalTitle').textContent = `Tareas de: ${projectName}`;
    document.getElementById('tasksModal').style.display = 'flex';
    
    // Guardar el projectId en el modal para referencia
    document.getElementById('tasksModal').setAttribute('data-current-project-id', projectId);
    
    loadTasks(projectId);
}

function closeTasksModal() {
    document.getElementById('tasksModal').style.display = 'none';
    document.getElementById('tasksModalBody').innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
    
    // Mostrar indicador de actualización
    showRefreshIndicator();
    
    // Actualizar la lista de proyectos al cerrar el modal
    refreshProjectsList();
}

function loadTasks(projectId) {
    fetch(APP_URL + `?route=kpi/get-tasks&project_id=${projectId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTasks(data.tasks, data.project);
        } else {
            showToast(data.message || 'Error al cargar tareas', 'error');
            closeTasksModal();
        }
    })
    .catch(error => {
        showToast('Error de red al cargar tareas', 'error');
        closeTasksModal();
    });
}

function renderTasks(tasks, project) {
    const body = document.getElementById('tasksModalBody');
    const isPercentageMode = project.task_distribution_mode === 'percentage';
    
    let tasksHtml = tasks.map(task => `
        <div class="task-item">
            <input type="checkbox" class="task-checkbox" onchange="toggleTask(${task.task_id}, this.checked, ${project.project_id})" ${task.is_completed ? 'checked' : ''}>
            <span class="task-name ${task.is_completed ? 'completed' : ''}">${escapeHtml(task.task_name)}</span>
            <span class="task-points">
                ${isPercentageMode ? 
                    `${task.assigned_percentage}%` : 
                    `${parseFloat(task.automatic_points).toFixed(2)} pts`
                }
            </span>
        </div>
    `).join('');

    let totalPercentage = tasks.reduce((sum, task) => sum + parseFloat(task.assigned_percentage), 0);

    const addTaskForm = `
        <form id="addTaskForm" class="add-task-form" onsubmit="addTask(event, ${project.project_id})">
            <input type="text" name="task_name" placeholder="Nueva tarea..." required>
            ${isPercentageMode ? 
                `<input type="number" name="assigned_percentage" placeholder="%" min="0" max="100" step="0.1" required>` : 
                ''
            }
            <button type="submit" class="btn-minimal primary">Agregar</button>
        </form>
        ${isPercentageMode ? `<div class="percentage-total">Total: ${totalPercentage.toFixed(2)}%</div>` : ''}
    `;

    body.innerHTML = `
        <div class="tasks-list">${tasksHtml}</div>
        <div class="tasks-footer">${addTaskForm}</div>
    `;
}

function addTask(event, projectId) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('project_id', projectId);

    fetch(APP_URL + '?route=kpi/add-task', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTasks(projectId); // Recargar la lista de tareas
            updateProjectProgress(projectId); // Actualizar progreso en la página principal
        } else {
            showToast(data.message || 'Error al agregar tarea', 'error');
        }
    });
}

function toggleTask(taskId, isCompleted, projectId) {
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('is_completed', isCompleted);

    fetch(APP_URL + '?route=kpi/toggle-task-status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar progreso en la página principal inmediatamente
            updateProjectProgress(projectId);
        } else {
            showToast(data.message || 'Error al actualizar tarea', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión al actualizar tarea', 'error');
    });
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return text.toString()
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

// Función para actualizar el progreso del proyecto en tiempo real
function updateProjectProgress(projectId) {
    fetch(APP_URL + `?route=kpi/get-project-progress&project_id=${projectId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const projectItem = document.querySelector(`[data-project-id="${projectId}"]`);
            if (projectItem) {
                // Actualizar la barra de progreso
                const progressBar = projectItem.querySelector('.progress-fill-small');
                const progressText = projectItem.querySelector('.progress-text');
                
                if (progressBar && progressText) {
                    const newProgress = parseFloat(data.progress_percentage || 0);
                    
                    // Animar el cambio de progreso
                    progressBar.style.width = `${newProgress}%`;
                    progressText.textContent = `${newProgress.toFixed(1)}%`;
                    
                    // Añadir efecto visual temporal para indicar actualización
                    projectItem.classList.add('progress-updated');
                    setTimeout(() => {
                        projectItem.classList.remove('progress-updated');
                    }, 2000);
                }
            }
        }
    })
    .catch(error => {
        console.log('Error actualizando progreso:', error);
    });
}

// Función para refrescar la lista de proyectos
function refreshProjectsList() {
    fetch(APP_URL + '?route=kpi/get-projects-data')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateProjectsSection(data.projects, data.projectsWithoutKPI);
            hideRefreshIndicator();
            showToast('Lista de proyectos actualizada', 'success');
        } else {
            console.error('Error al actualizar proyectos:', data.message);
            hideRefreshIndicator();
            showToast('Error al actualizar proyectos', 'error');
        }
    })
    .catch(error => {
        console.error('Error al refrescar proyectos:', error);
        hideRefreshIndicator();
        showToast('Error de conexión al actualizar', 'error');
    });
}

// Función para mostrar indicador de actualización
function showRefreshIndicator() {
    // Crear overlay de actualización si no existe
    let indicator = document.getElementById('refresh-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'refresh-indicator';
        indicator.className = 'refresh-indicator';
        indicator.innerHTML = `
            <div class="refresh-content">
                <i class="fas fa-sync-alt fa-spin"></i>
                <span>Actualizando proyectos...</span>
            </div>
        `;
        document.body.appendChild(indicator);
    }
    
    // Mostrar el indicador
    indicator.style.display = 'flex';
    setTimeout(() => {
        indicator.classList.add('show');
    }, 10);
}

// Función para ocultar indicador de actualización
function hideRefreshIndicator() {
    const indicator = document.getElementById('refresh-indicator');
    if (indicator) {
        indicator.classList.remove('show');
        setTimeout(() => {
            indicator.style.display = 'none';
        }, 300);
    }
}

// Función para actualizar la sección de proyectos en el DOM
function updateProjectsSection(projects, projectsWithoutKPI) {
    // Actualizar proyectos con KPI
    const projectsWithKPISection = document.querySelector('[data-section="with-kpi"]');
    if (projectsWithKPISection && projects) {
        const projectsList = projectsWithKPISection.querySelector('.projects-list');
        const headerCount = projectsWithKPISection.querySelector('.section-header-minimal h3');
        
        if (projects.length > 0) {
            if (projectsList) {
                projectsList.innerHTML = generateProjectsHTML(projects, true);
            }
            if (headerCount) {
                headerCount.textContent = `Proyectos con KPI (${projects.length})`;
            }
            projectsWithKPISection.style.display = 'block';
        } else {
            projectsWithKPISection.style.display = 'none';
        }
    }
    
    // Actualizar proyectos pendientes
    let pendingSection = document.querySelector('[data-section="pending"]');
    
    if (projectsWithoutKPI && projectsWithoutKPI.length > 0) {
        if (!pendingSection) {
            // Crear la sección de proyectos pendientes si no existe
            const pendingHTML = `
                <section class="projects-minimal" data-section="pending">
                    <div class="section-header-minimal">
                        <h3>Proyectos Pendientes (${projectsWithoutKPI.length})</h3>
                    </div>
                    <div class="projects-list">
                        ${generateProjectsHTML(projectsWithoutKPI, false)}
                    </div>
                </section>
            `;
            
            // Insertar antes de la sección de proyectos con KPI
            const contentMain = document.querySelector('.content-minimal');
            if (contentMain) {
                contentMain.insertAdjacentHTML('afterbegin', pendingHTML);
            }
        } else {
            // Actualizar la sección existente
            const pendingList = pendingSection.querySelector('.projects-list');
            const pendingHeader = pendingSection.querySelector('.section-header-minimal h3');
            
            if (pendingList) {
                pendingList.innerHTML = generateProjectsHTML(projectsWithoutKPI, false);
            }
            if (pendingHeader) {
                pendingHeader.textContent = `Proyectos Pendientes (${projectsWithoutKPI.length})`;
            }
            pendingSection.style.display = 'block';
        }
    } else if (pendingSection) {
        // Ocultar la sección si no hay proyectos pendientes
        pendingSection.style.display = 'none';
    }
}

// Función para generar HTML de proyectos
function generateProjectsHTML(projects, withKPI) {
    return projects.map(project => {
        const progressPercentage = project.progress_percentage || 0;
        const itemClass = withKPI ? 'project-item assigned' : 'project-item pending';
        
        if (withKPI) {
            return `
                <div class="${itemClass}" data-project-id="${project.project_id}">
                    <div class="project-info">
                        <div class="project-name">${escapeHtml(project.project_name)}</div>
                        <div class="project-clan">${escapeHtml(project.clan_name || 'Sin asignar')}</div>
                    </div>
                    
                    <div class="project-kpi">
                        <span class="kpi-points">${formatNumber(project.kpi_points)} pts</span>
                        <span class="distribution-mode">
                            ${project.task_distribution_mode === 'automatic' ? '🔄 Auto' : '📊 %'}
                        </span>
                    </div>
                    
                    <div class="project-progress">
                        <div class="progress-bar-small">
                            <div class="progress-fill-small" style="width: ${progressPercentage}%"></div>
                        </div>
                        <span class="progress-text">${progressPercentage.toFixed(1)}%</span>
                    </div>
                    
                    <div class="project-actions">
                        <button class="btn-action" onclick='openTasksModal(${project.project_id}, ${JSON.stringify(project.project_name)})' title="Gestionar Tareas">
                            <i class="fas fa-tasks"></i>
                        </button>
                        <button class="btn-action" onclick='openEditKPIModal(${project.project_id}, ${JSON.stringify(project.project_name)}, ${project.kpi_points})' title="Editar KPI">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action" onclick='toggleDistributionMode(${project.project_id}, ${JSON.stringify(project.task_distribution_mode)})' title="Cambiar distribución">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="${itemClass}" data-project-id="${project.project_id}">
                    <div class="project-info">
                        <div class="project-name">${escapeHtml(project.project_name)}</div>
                        <div class="project-clan">${escapeHtml(project.clan_name || 'Sin asignar')}</div>
                    </div>
                    
                    <div class="project-meta">
                        <span class="project-date">${formatDate(project.created_at)}</span>
                        <span class="project-status">⏳ Pendiente</span>
                    </div>
                    
                    <div class="project-action">
                        <button class="btn-minimal primary" onclick="openAssignKPIModal(${project.project_id}, '${escapeHtml(project.project_name)}')">
                            <i class="fas fa-plus"></i>
                            Asignar
                        </button>
                    </div>
                </div>
            `;
        }
    }).join('');
}

// Función para obtener el ID del proyecto desde el contexto del modal
function getCurrentProjectId() {
    const modalTitle = document.getElementById('tasksModalTitle');
    if (modalTitle && modalTitle.textContent) {
        // Extraer el ID del proyecto del modal actual si es posible
        const projectItems = document.querySelectorAll('[data-project-id]');
        for (let item of projectItems) {
            const projectName = item.querySelector('.project-name')?.textContent;
            if (projectName && modalTitle.textContent.includes(projectName)) {
                return item.getAttribute('data-project-id');
            }
        }
    }
    return null;
}

// Funciones de utilidad para formateo
function formatNumber(number) {
    return new Intl.NumberFormat().format(number);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
