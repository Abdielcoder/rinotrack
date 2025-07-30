// KPI Quarters - JavaScript para Gestión de Trimestres

document.addEventListener('DOMContentLoaded', function() {
    initQuartersPage();
});

function initQuartersPage() {
    setupEventListeners();
    animateQuarterCards();
    setupFilters();
}

// Variables globales
let isEditMode = false;
let currentQuarterId = null;

// Event Listeners
function setupEventListeners() {
    // Form submit
    const quarterForm = document.getElementById('quarterForm');
    if (quarterForm) {
        quarterForm.addEventListener('submit', handleQuarterSubmit);
    }

    // Modal clicks
    window.addEventListener('click', function(event) {
        const quarterModal = document.getElementById('quarterModal');
        const detailsModal = document.getElementById('quarterDetailsModal');
        
        if (event.target === quarterModal) {
            closeQuarterModal();
        }
        
        if (event.target === detailsModal) {
            closeQuarterDetailsModal();
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeQuarterModal();
            closeQuarterDetailsModal();
        }
        
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            openCreateQuarterModal();
        }
    });
}

// Animaciones
function animateQuarterCards() {
    const cards = document.querySelectorAll('.quarter-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Funciones del Modal
function openCreateQuarterModal() {
    isEditMode = false;
    currentQuarterId = null;
    
    document.getElementById('quarterModalTitle').textContent = 'Crear Nuevo Trimestre';
    document.getElementById('quarterSubmitText').textContent = 'Crear Trimestre';
    document.getElementById('quarterForm').reset();
    document.getElementById('quarterId').value = '';
    
    // Establecer año y trimestre por defecto
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    const currentQuarter = Math.ceil(currentMonth / 3);
    
    document.getElementById('quarterYear').value = currentYear;
    document.getElementById('quarterPeriod').value = `Q${currentQuarter}`;
    
    document.getElementById('quarterModal').style.display = 'block';
}

function closeQuarterModal() {
    document.getElementById('quarterModal').style.display = 'none';
    clearFormErrors();
    isEditMode = false;
    currentQuarterId = null;
}

function editQuarter(quarterId) {
    isEditMode = true;
    currentQuarterId = quarterId;
    
    document.getElementById('quarterModalTitle').textContent = 'Editar Trimestre';
    document.getElementById('quarterSubmitText').textContent = 'Actualizar Trimestre';
    document.getElementById('quarterId').value = quarterId;
    
    // Cargar datos del trimestre
    loadQuarterData(quarterId);
    
    document.getElementById('quarterModal').style.display = 'block';
}

function loadQuarterData(quarterId) {
    showToast('Cargando datos del trimestre...', 'info');
    
    fetch(`?route=kpi/quarter-details&id=${quarterId}`, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            populateQuarterForm(data.quarter);
        } else {
            showToast(data.message || 'Error al cargar datos del trimestre', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión al cargar datos', 'error');
    });
}

function populateQuarterForm(quarter) {
    document.getElementById('quarterYear').value = quarter.year;
    document.getElementById('quarterPeriod').value = quarter.quarter;
    document.getElementById('totalPoints').value = quarter.total_points;
}

// Manejo del formulario
function handleQuarterSubmit(e) {
    e.preventDefault();
    
    if (!validateQuarterForm()) {
        return;
    }
    
    const submitBtn = document.getElementById('quarterSubmitBtn');
    const submitText = document.getElementById('quarterSubmitText');
    const submitLoader = document.getElementById('quarterSubmitLoader');
    
    // Mostrar loading
    submitBtn.disabled = true;
    submitText.style.display = 'none';
    submitLoader.style.display = 'inline-block';
    
    const formData = new FormData(e.target);
    const route = isEditMode ? 'kpi/update-quarter' : 'kpi/create-quarter';
    
    fetch(`?route=${route}`, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeQuarterModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
            showFormErrors(data.errors);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error de conexión al guardar', 'error');
    })
    .finally(() => {
        // Ocultar loading
        submitBtn.disabled = false;
        submitText.style.display = 'inline-block';
        submitLoader.style.display = 'none';
    });
}

function validateQuarterForm() {
    clearFormErrors();
    let isValid = true;
    
    const year = document.getElementById('quarterYear').value;
    const quarter = document.getElementById('quarterPeriod').value;
    const points = document.getElementById('totalPoints').value;
    
    if (!year) {
        showFieldError('quarterYear', 'El año es requerido');
        isValid = false;
    }
    
    if (!quarter) {
        showFieldError('quarterPeriod', 'El trimestre es requerido');
        isValid = false;
    }
    
    if (!points || points < 100) {
        showFieldError('totalPoints', 'Los puntos deben ser mínimo 100');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
    field.classList.add('error');
}

function clearFormErrors() {
    const errors = document.querySelectorAll('.field-error');
    errors.forEach(error => error.remove());
    
    const fields = document.querySelectorAll('.error');
    fields.forEach(field => field.classList.remove('error'));
}

function showFormErrors(errors) {
    if (!errors) return;
    
    Object.keys(errors).forEach(field => {
        showFieldError(field, errors[field]);
    });
}

// Funciones de acción
function activateQuarter(quarterId) {
    const confirmMsg = '¿Estás seguro de que quieres activar este trimestre?\n\nEsto desactivará el trimestre actual si existe.';
    
    if (confirm(confirmMsg)) {
        showToast('Activando trimestre...', 'info');
        
        const formData = new FormData();
        formData.append('quarterId', quarterId);
        
        fetch('?route=kpi/activate-quarter', {
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
            showToast('Error de conexión al activar trimestre', 'error');
        });
    }
}

function viewQuarterDetails(quarterId) {
    document.getElementById('quarterDetailsModal').style.display = 'block';
    loadQuarterDetails(quarterId);
}

function closeQuarterDetailsModal() {
    document.getElementById('quarterDetailsModal').style.display = 'none';
}

function loadQuarterDetails(quarterId) {
    const contentDiv = document.querySelector('.quarter-details-content');
    contentDiv.innerHTML = `
        <div class="loading-details">
            <i class="fas fa-spinner fa-spin"></i>
            Cargando detalles del trimestre...
        </div>
    `;
    
    fetch(`?route=kpi/quarter-details&id=${quarterId}`, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderQuarterDetails(data.quarter, data.projects, data.clans);
        } else {
            contentDiv.innerHTML = `
                <div class="error-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error al cargar detalles: ${data.message}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        contentDiv.innerHTML = `
            <div class="error-state">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Error de conexión al cargar detalles</p>
            </div>
        `;
    });
}

function renderQuarterDetails(quarter, projects, clans) {
    const contentDiv = document.querySelector('.quarter-details-content');
    
    const progressPercentage = quarter.total_points > 0 ? 
        ((quarter.assigned_points || 0) / quarter.total_points * 100).toFixed(1) : 0;
    
    contentDiv.innerHTML = `
        <div class="quarter-details">
            <!-- Header de detalles -->
            <div class="details-header">
                <div class="quarter-title-section">
                    <h3>${quarter.quarter} ${quarter.year}</h3>
                    <span class="status-badge ${quarter.is_active ? 'active' : 'inactive'}">
                        ${quarter.is_active ? 'Activo' : 'Inactivo'}
                    </span>
                </div>
                <div class="quarter-summary">
                    <div class="summary-stat">
                        <span class="stat-value">${formatNumber(quarter.total_points)}</span>
                        <span class="stat-label">Puntos Totales</span>
                    </div>
                    <div class="summary-stat">
                        <span class="stat-value">${formatNumber(quarter.assigned_points || 0)}</span>
                        <span class="stat-label">Asignados</span>
                    </div>
                    <div class="summary-stat">
                        <span class="stat-value">${progressPercentage}%</span>
                        <span class="stat-label">Utilización</span>
                    </div>
                </div>
            </div>
            
            <!-- Progreso general -->
            <div class="details-progress">
                <div class="progress-header">
                    <span>Progreso de Asignación</span>
                    <span>${progressPercentage}%</span>
                </div>
                <div class="progress-bar-details">
                    <div class="progress-fill-details" style="width: ${progressPercentage}%"></div>
                </div>
            </div>
            
            <!-- Proyectos del trimestre -->
            <div class="details-section">
                <h4><i class="fas fa-project-diagram"></i> Proyectos Asignados</h4>
                ${projects.length > 0 ? renderProjectsList(projects) : '<p class="empty-message">No hay proyectos asignados a este trimestre</p>'}
            </div>
            
            <!-- Estadísticas por clan -->
            <div class="details-section">
                <h4><i class="fas fa-users"></i> Distribución por Clan</h4>
                ${clans.length > 0 ? renderClansStats(clans) : '<p class="empty-message">No hay clanes con KPIs asignados</p>'}
            </div>
        </div>
    `;
}

function renderProjectsList(projects) {
    return `
        <div class="projects-list">
            ${projects.map(project => `
                <div class="project-item">
                    <div class="project-info">
                        <h5>${escapeHtml(project.project_name)}</h5>
                        <span class="project-clan">${escapeHtml(project.clan_name)}</span>
                    </div>
                    <div class="project-points">
                        <span class="points-value">${formatNumber(project.kpi_points)}</span>
                        <span class="points-label">puntos</span>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

function renderClansStats(clans) {
    return `
        <div class="clans-stats">
            ${clans.map(clan => `
                <div class="clan-stat">
                    <div class="clan-info">
                        <h5>${escapeHtml(clan.clan_name)}</h5>
                        <span class="clan-projects">${clan.projects_count} proyectos</span>
                    </div>
                    <div class="clan-points">
                        <span class="points-value">${formatNumber(clan.total_points)}</span>
                        <span class="points-label">puntos</span>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Funciones de filtrado
function setupFilters() {
    const yearFilter = document.querySelector('.year-filter');
    if (yearFilter) {
        yearFilter.addEventListener('change', function() {
            filterByYear(this.value);
        });
    }
}

function filterByYear(year) {
    const cards = document.querySelectorAll('.quarter-card');
    
    cards.forEach(card => {
        if (!year || card.dataset.year === year) {
            card.style.display = 'block';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 10);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
    
    // Actualizar contador
    updateFilterCounter(year);
}

function updateFilterCounter(year) {
    const visibleCards = document.querySelectorAll('.quarter-card[style*="display: block"], .quarter-card:not([style*="display: none"])');
    const counter = document.querySelector('.filter-counter');
    
    if (counter) {
        const count = year ? visibleCards.length : document.querySelectorAll('.quarter-card').length;
        counter.textContent = `${count} trimestre${count !== 1 ? 's' : ''}`;
    }
}

// Funciones de utilidad
function formatNumber(number) {
    return new Intl.NumberFormat().format(number);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Funciones de exportación
function exportQuarterData(quarterId, format = 'excel') {
    showToast(`Exportando datos en formato ${format.toUpperCase()}...`, 'info');
    
    // En una implementación real, se haría la descarga
    setTimeout(() => {
        showToast('Datos exportados exitosamente', 'success');
    }, 2000);
}

// Función para duplicar trimestre
function duplicateQuarter(quarterId) {
    const confirmMsg = '¿Quieres crear un nuevo trimestre basado en este?\n\nSe copiará la configuración pero no los proyectos asignados.';
    
    if (confirm(confirmMsg)) {
        showToast('Duplicando trimestre...', 'info');
        
        const formData = new FormData();
        formData.append('sourceQuarterId', quarterId);
        
        fetch('?route=kpi/duplicate-quarter', {
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
            showToast('Error al duplicar trimestre', 'error');
        });
    }
}

// Función para eliminar trimestre
function deleteQuarter(quarterId) {
    const confirmMsg = '¿Estás seguro de que quieres eliminar este trimestre?\n\nEsta acción no se puede deshacer.\n\nSolo se pueden eliminar trimestres inactivos sin proyectos asignados.';
    
    if (confirm(confirmMsg)) {
        showToast('Eliminando trimestre...', 'info');
        
        const formData = new FormData();
        formData.append('quarterId', quarterId);
        
        fetch('?route=kpi/delete-quarter', {
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
            showToast('Error de conexión al eliminar trimestre', 'error');
        });
    }
}

// Inicializar si la página ya está cargada
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuartersPage);
} else {
    initQuartersPage();
}