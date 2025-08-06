// KPI Quarters - JavaScript para Gestión de Trimestres

(function() {
    'use strict';
    
    // Variables globales
    let isEditMode = false;
    let currentQuarterId = null;

    // Funciones del Modal - Expuestas globalmente
    window.openCreateQuarterModal = function() {
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
        
        document.getElementById('quarterModal').style.display = 'flex';
    };

    window.closeQuarterModal = function() {
        document.getElementById('quarterModal').style.display = 'none';
        clearFormErrors();
        isEditMode = false;
        currentQuarterId = null;
    };

    window.editQuarter = function(quarterId) {
        isEditMode = true;
        currentQuarterId = quarterId;
        
        document.getElementById('quarterModalTitle').textContent = 'Editar Trimestre';
        document.getElementById('quarterSubmitText').textContent = 'Actualizar Trimestre';
        document.getElementById('quarterId').value = quarterId;
        
        // Cargar datos del trimestre
        loadQuarterData(quarterId);
        
        document.getElementById('quarterModal').style.display = 'flex';
    };

    window.activateQuarter = function(quarterId) {
        const confirmMsg = '¿Estás seguro de que quieres activar este trimestre?\n\nEsto desactivará el trimestre actual si existe.';
        
        confirmDelete(confirmMsg, () => {
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
        });
    };

    window.viewQuarterDetails = function(quarterId) {
        document.getElementById('quarterDetailsModal').style.display = 'flex';
        loadQuarterDetails(quarterId);
    };

    window.closeQuarterDetailsModal = function() {
        document.getElementById('quarterDetailsModal').style.display = 'none';
    };

    window.filterByYear = function(year) {
        const cards = document.querySelectorAll('.quarter-item');
        
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
    };

    window.deleteQuarter = function(quarterId) {
        const confirmMsg = '¿Estás seguro de que quieres eliminar este trimestre?\n\nEsta acción no se puede deshacer.\n\nSolo se pueden eliminar trimestres inactivos sin proyectos asignados.';
        
        confirmDelete(confirmMsg, () => {
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
        });
    };

    // Funciones internas
    function initQuartersPage() {
        setupEventListeners();
        animateQuarterCards();
    }

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
                window.closeQuarterModal();
            }
            
            if (event.target === detailsModal) {
                window.closeQuarterDetailsModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeQuarterModal();
                window.closeQuarterDetailsModal();
            }
            
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.openCreateQuarterModal();
            }
        });
    }

    function animateQuarterCards() {
        const cards = document.querySelectorAll('.quarter-item');
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
        document.getElementById('quarterId').value = quarter.kpi_quarter_id;
        document.getElementById('quarterYear').value = quarter.year;
        document.getElementById('quarterPeriod').value = quarter.quarter;
        document.getElementById('totalPoints').value = quarter.total_points;
    }

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
        
        // Debug: Log los datos que se están enviando
        console.log('Enviando datos:', {
            route: route,
            isEditMode: isEditMode,
            formData: Object.fromEntries(formData)
        });
        
        fetch(`?route=${route}`, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                // Intentar obtener el mensaje de error del servidor
                let errorMessage = `HTTP error! status: ${response.status}`;
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {
                    // Si no se puede parsear JSON, usar el mensaje por defecto
                }
                throw new Error(errorMessage);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                window.closeQuarterModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(data.message, 'error');
                if (data.errors) {
                    showFormErrors(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al guardar: ' + error.message, 'error');
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
        
        // Debug: Log los valores del formulario
        console.log('Validando formulario:', { year, quarter, points, isEditMode });
        
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
        
        if (points > 100000) {
            showFieldError('totalPoints', 'Los puntos deben ser máximo 100,000');
            isValid = false;
        }
        
        console.log('Formulario válido:', isValid);
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
                
                <div class="details-progress">
                    <div class="progress-header">
                        <span>Progreso de Asignación</span>
                        <span>${progressPercentage}%</span>
                    </div>
                    <div class="progress-bar-details">
                        <div class="progress-fill-details" style="width: ${progressPercentage}%"></div>
                    </div>
                </div>
                
                <div class="details-section">
                    <h4><i class="fas fa-project-diagram"></i> Proyectos Asignados</h4>
                    ${projects.length > 0 ? renderProjectsList(projects) : '<p class="empty-message">No hay proyectos asignados a este trimestre</p>'}
                </div>
                
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

    function formatNumber(number) {
        return new Intl.NumberFormat().format(number);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showToast(message, type = 'info') {
        // Usar función global si existe
        if (typeof window.showToast === 'function' && window.showToast !== showToast) {
            window.showToast(message, type);
            return;
        }
        
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
                if (toast.parentNode) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuartersPage);
    } else {
        initQuartersPage();
    }
})();