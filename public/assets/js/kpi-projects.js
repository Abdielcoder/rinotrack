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
    if (confirm(`¿Quieres cambiar el modo de distribución a "${newMode}"?`)) {
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
    loadTasks(projectId);
}

function closeTasksModal() {
    document.getElementById('tasksModal').style.display = 'none';
    document.getElementById('tasksModalBody').innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
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
            <input type="checkbox" class="task-checkbox" onchange="toggleTask(${task.task_id}, this.checked)" ${task.is_completed ? 'checked' : ''}>
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
        } else {
            showToast(data.message || 'Error al agregar tarea', 'error');
        }
    });
}

function toggleTask(taskId, isCompleted) {
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('is_completed', isCompleted);

    fetch(APP_URL + '?route=kpi/toggle-task-status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            showToast(data.message || 'Error al actualizar tarea', 'error');
        }
        // Podríamos recargar las tareas, pero por ahora la UI se actualiza instantáneamente
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
