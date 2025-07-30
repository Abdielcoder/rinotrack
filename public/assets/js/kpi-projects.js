/**
 * JavaScript para la gestión de asignación de KPIs a proyectos
 * Incluye funcionalidades para asignar, editar y cambiar distribución de puntos
 */

// Variables globales
let currentAvailablePoints = 0;
let isLoading = false;

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    initKPIProjectsPage();
});

/**
 * Inicializar la página de proyectos KPI
 */
function initKPIProjectsPage() {
    console.log('🚀 Iniciando página de proyectos KPI...');
    
    // Obtener puntos disponibles desde el DOM
    const availablePointsElement = document.getElementById('available_points');
    if (availablePointsElement) {
        currentAvailablePoints = parseInt(availablePointsElement.textContent.replace(/,/g, '')) || 0;
    }
    
    // Animar las tarjetas de proyecto
    animateProjectCards();
    
    // Configurar eventos de teclado para modales
    setupModalKeyboardEvents();
    
    // Configurar validación en tiempo real
    setupRealTimeValidation();
    
    console.log('✅ Página de proyectos KPI inicializada');
}

/**
 * Animar tarjetas de proyecto con efecto slideInUp
 */
function animateProjectCards() {
    const projectCards = document.querySelectorAll('.project-card');
    projectCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.animation = `slideInUp 0.6s ease forwards ${index * 0.1}s`;
    });
}

/**
 * Configurar eventos de teclado para modales
 */
function setupModalKeyboardEvents() {
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAssignKPIModal();
            closeEditKPIModal();
        }
    });
}

/**
 * Configurar validación en tiempo real
 */
function setupRealTimeValidation() {
    const kpiPointsInput = document.getElementById('kpi_points');
    const editKpiPointsInput = document.getElementById('edit_kpi_points');
    
    if (kpiPointsInput) {
        kpiPointsInput.addEventListener('input', function() {
            validateKPIPoints(this, currentAvailablePoints);
        });
    }
    
    if (editKpiPointsInput) {
        editKpiPointsInput.addEventListener('input', function() {
            validateKPIPoints(this, 999999); // Sin límite para edición
        });
    }
}

/**
 * Validar puntos KPI en tiempo real
 */
function validateKPIPoints(input, maxPoints) {
    const value = parseInt(input.value) || 0;
    const isValid = value > 0 && value <= maxPoints;
    
    // Remover clases previas
    input.classList.remove('valid', 'invalid');
    
    // Agregar clase correspondiente
    if (input.value) {
        input.classList.add(isValid ? 'valid' : 'invalid');
    }
    
    // Actualizar hint si existe
    const hint = input.parentElement.querySelector('.form-hint');
    if (hint && !isValid && input.value) {
        const originalHint = hint.innerHTML;
        hint.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Valor inválido (máximo ${maxPoints.toLocaleString()})`;
        hint.style.color = 'var(--error)';
        
        setTimeout(() => {
            hint.innerHTML = originalHint;
            hint.style.color = '';
        }, 3000);
    }
}

/**
 * Abrir modal para asignar KPI
 */
function openAssignKPIModal(projectId, projectName) {
    if (isLoading) return;
    
    console.log(`📝 Abriendo modal para asignar KPI al proyecto: ${projectName}`);
    
    // Configurar datos del modal
    document.getElementById('assign_project_id').value = projectId;
    document.getElementById('assign_project_name').textContent = projectName;
    
    // Obtener puntos disponibles actualizados desde el servidor
    fetch(`?route=kpi/get-available-points`, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentAvailablePoints = data.available_points;
            
            // Actualizar puntos disponibles en el modal
            const availablePointsSpan = document.getElementById('available_points');
            if (availablePointsSpan) {
                availablePointsSpan.textContent = currentAvailablePoints.toLocaleString();
            }
            
            // Configurar límite máximo del input
            const kpiPointsInput = document.getElementById('kpi_points');
            if (kpiPointsInput) {
                kpiPointsInput.max = currentAvailablePoints;
                kpiPointsInput.value = '';
                kpiPointsInput.classList.remove('valid', 'invalid');
            }
        }
    })
    .catch(error => {
        console.error('Error al obtener puntos disponibles:', error);
        // Usar valor local como fallback
        const availablePointsSpan = document.getElementById('available_points');
        if (availablePointsSpan) {
            availablePointsSpan.textContent = currentAvailablePoints.toLocaleString();
        }
    });
    
    // Resetear formulario y mostrar modal
    const form = document.getElementById('assignKPIForm');
    if (form) {
        form.reset();
        // Marcar automática como seleccionada por defecto
        const automaticRadio = form.querySelector('input[value="automatic"]');
        if (automaticRadio) {
            automaticRadio.checked = true;
        }
    }
    
    // Mostrar modal con animación
    const modal = document.getElementById('assignKPIModal');
    if (modal) {
        modal.style.display = 'flex';
        requestAnimationFrame(() => {
            modal.classList.add('show');
        });
        
        // Asegurar que el modal-content tenga el z-index correcto
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.position = 'relative';
            modalContent.style.zIndex = '1';
        }
        
        // Focus en el input de puntos
        setTimeout(() => {
            if (kpiPointsInput) {
                kpiPointsInput.focus();
            }
        }, 300);
    }
}

/**
 * Cerrar modal de asignación
 */
function closeAssignKPIModal() {
    const modal = document.getElementById('assignKPIModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            const form = document.getElementById('assignKPIForm');
            if (form) {
                form.reset();
            }
        }, 300);
    }
}

/**
 * Asignar KPI al proyecto
 */
function assignKPI(event) {
    event.preventDefault();
    
    if (isLoading) return;
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validaciones antes de enviar
    const kpiPoints = parseInt(formData.get('kpi_points')) || 0;
    
    if (kpiPoints <= 0) {
        showToast('Los puntos KPI deben ser mayor a 0', 'error');
        return;
    }
    
    if (kpiPoints > currentAvailablePoints) {
        showToast(`Solo hay ${currentAvailablePoints.toLocaleString()} puntos disponibles`, 'error');
        return;
    }
    
    // Mostrar estado de carga
    setLoadingState(true, 'Asignando KPI...');
    
    fetch('?route=kpi/assign', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeAssignKPIModal();
            
            // Actualizar puntos disponibles sin recargar
            updateAvailablePoints();
            
            // Recargar página después de un breve delay para mostrar el proyecto en la lista correcta
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error al asignar KPI:', error);
        showToast('Error de conexión al asignar KPI', 'error');
    })
    .finally(() => {
        setLoadingState(false);
    });
}

/**
 * Abrir modal para editar KPI
 */
function openEditKPIModal(projectId, projectName, currentPoints) {
    if (isLoading) return;
    
    console.log(`✏️ Abriendo modal para editar KPI del proyecto: ${projectName}`);
    
    // Configurar datos del modal
    document.getElementById('edit_project_id').value = projectId;
    document.getElementById('edit_project_name').textContent = projectName;
    document.getElementById('edit_kpi_points').value = currentPoints;
    
    // Actualizar texto de puntos actuales
    const currentPointsSpan = document.getElementById('current_points');
    if (currentPointsSpan) {
        currentPointsSpan.textContent = currentPoints.toLocaleString();
    }
    
    // Mostrar modal con animación
    const modal = document.getElementById('editKPIModal');
    if (modal) {
        modal.style.display = 'flex';
        requestAnimationFrame(() => {
            modal.classList.add('show');
        });
        
        // Asegurar que el modal-content tenga el z-index correcto
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.position = 'relative';
            modalContent.style.zIndex = '1';
        }
        
        // Focus en el input de puntos
        setTimeout(() => {
            const editInput = document.getElementById('edit_kpi_points');
            if (editInput) {
                editInput.focus();
                editInput.select(); // Seleccionar todo el texto
            }
        }, 300);
    }
}

/**
 * Cerrar modal de edición
 */
function closeEditKPIModal() {
    const modal = document.getElementById('editKPIModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            const form = document.getElementById('editKPIForm');
            if (form) {
                form.reset();
            }
        }, 300);
    }
}

/**
 * Editar KPI del proyecto
 */
function editKPI(event) {
    event.preventDefault();
    
    if (isLoading) return;
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validaciones antes de enviar
    const kpiPoints = parseInt(formData.get('kpi_points')) || 0;
    
    if (kpiPoints <= 0) {
        showToast('Los puntos KPI deben ser mayor a 0', 'error');
        return;
    }
    
    // Mostrar estado de carga
    setLoadingState(true, 'Actualizando KPI...');
    
    fetch('?route=kpi/assign', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            closeEditKPIModal();
            
            // Actualizar puntos disponibles sin recargar
            updateAvailablePoints();
            
            // Recargar página después de un breve delay para mostrar el proyecto en la lista correcta
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error al editar KPI:', error);
        showToast('Error de conexión al editar KPI', 'error');
    })
    .finally(() => {
        setLoadingState(false);
    });
}

/**
 * Cambiar modalidad de distribución
 */
function toggleDistributionMode(projectId, currentMode) {
    if (isLoading) return;
    
    const newMode = currentMode === 'automatic' ? 'percentage' : 'automatic';
    const modeText = newMode === 'automatic' ? 'automática' : 'por porcentaje';
    
    console.log(`🔄 Cambiando distribución del proyecto ${projectId} a ${modeText}`);
    
    // Mostrar estado de carga
    setLoadingState(true, `Cambiando a distribución ${modeText}...`);
    
    const formData = new FormData();
    formData.append('project_id', projectId);
    formData.append('mode', newMode);
    
    fetch('?route=kpi/change-distribution', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            
            // Actualizar toggle visual inmediatamente
            updateDistributionToggle(projectId, newMode);
            
            // Recargar página después de un breve delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('❌ Error al cambiar distribución:', error);
        showToast('Error de conexión al cambiar distribución', 'error');
    })
    .finally(() => {
        setLoadingState(false);
    });
}

/**
 * Actualizar toggle de distribución visualmente
 */
function updateDistributionToggle(projectId, newMode) {
    const projectCard = document.querySelector(`[data-project-id="${projectId}"]`);
    if (!projectCard) return;
    
    const toggle = projectCard.querySelector('.toggle-switch');
    if (!toggle) return;
    
    const thumb = toggle.querySelector('.switch-thumb');
    const leftLabel = toggle.querySelector('.switch-label.left');
    const rightLabel = toggle.querySelector('.switch-label.right');
    const badge = projectCard.querySelector('.distribution-badge');
    
    if (thumb) {
        thumb.classList.remove('left', 'right');
        thumb.classList.add(newMode === 'automatic' ? 'left' : 'right');
    }
    
    if (leftLabel && rightLabel) {
        leftLabel.classList.toggle('active', newMode === 'automatic');
        rightLabel.classList.toggle('active', newMode === 'percentage');
    }
    
    if (badge) {
        badge.className = `distribution-badge ${newMode}`;
        badge.textContent = newMode === 'automatic' ? 'Automática' : 'Por Porcentaje';
    }
}

/**
 * Establecer estado de carga
 */
function setLoadingState(loading, message = 'Cargando...') {
    isLoading = loading;
    
    // Deshabilitar todos los botones de acción
    const actionButtons = document.querySelectorAll('.action-btn, .toggle-switch');
    actionButtons.forEach(btn => {
        btn.style.pointerEvents = loading ? 'none' : '';
        btn.style.opacity = loading ? '0.6' : '';
    });
    
    if (loading) {
        showToast(message, 'info');
    }
}

/**
 * Actualizar puntos disponibles en tiempo real
 */
function updateAvailablePoints() {
    // Obtener el valor de puntos asignados del formulario activo
    let assignedPoints = 0;
    let form = document.getElementById('assignKPIForm');
    
    if (form && form.style.display !== 'none') {
        // Formulario de asignación
        const formData = new FormData(form);
        assignedPoints = parseInt(formData.get('kpi_points')) || 0;
    } else {
        // Formulario de edición
        form = document.getElementById('editKPIForm');
        if (form && form.style.display !== 'none') {
            const formData = new FormData(form);
            assignedPoints = parseInt(formData.get('kpi_points')) || 0;
        }
    }
    
    if (assignedPoints > 0) {
        // Actualizar la variable global
        currentAvailablePoints = Math.max(0, currentAvailablePoints - assignedPoints);
        
        // Actualizar el elemento en el DOM del modal
        const availablePointsElement = document.getElementById('available_points');
        if (availablePointsElement) {
            availablePointsElement.textContent = currentAvailablePoints.toLocaleString();
        }
        
        // Actualizar también el progreso en el header si existe
        updateProgressInHeader(assignedPoints);
    }
}

/**
 * Actualizar el progreso en el header
 */
function updateProgressInHeader(assignedPoints) {
    // Buscar elementos en el header principal
    const progressLabel = document.querySelector('.period-progress .progress-label');
    const progressFill = document.querySelector('.period-progress .progress-fill');
    const availablePointsText = document.querySelector('.period-stats .stat-item:first-child');
    
    if (progressLabel && progressFill && availablePointsText) {
        // Obtener el total de puntos del trimestre
        const totalPointsText = document.querySelector('.period-stats .stat-item:last-child');
        if (totalPointsText) {
            const totalPoints = parseInt(totalPointsText.textContent.replace(/,/g, '')) || 0;
            
            // Calcular el nuevo porcentaje
            const currentAssigned = totalPoints - currentAvailablePoints;
            const percentage = totalPoints > 0 ? (currentAssigned / totalPoints) * 100 : 0;
            
            // Actualizar el label y la barra de progreso
            progressLabel.textContent = `${percentage.toFixed(1)}% asignado`;
            progressFill.style.width = `${percentage}%`;
            
            // Actualizar también el texto de puntos disponibles
            availablePointsText.textContent = `${currentAvailablePoints.toLocaleString()} disponibles`;
        }
    }
}

/**
 * Función toast mejorada
 */
function showToast(message, type = 'info') {
    // Remover toasts existentes
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Crear estructura del toast
    toast.innerHTML = `
        <div class="toast-content">
            <i class="toast-icon fas ${getToastIcon(type)}"></i>
            <span class="toast-message">${message}</span>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Estilos del toast
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 500px;
        padding: 0;
        border-radius: var(--radius-lg);
        color: white;
        font-weight: var(--font-weight-medium);
        z-index: 10000;
        box-shadow: var(--shadow-lg);
        animation: slideInRight 0.4s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        overflow: hidden;
    `;
    
    // Color según tipo
    const colors = {
        success: 'var(--success)',
        error: 'var(--error)', 
        warning: 'var(--warning)',
        info: 'var(--info)'
    };
    
    toast.style.background = colors[type] || colors.info;
    
    // Estilos internos
    const style = document.createElement('style');
    style.textContent = `
        .toast-content {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-md) var(--spacing-lg);
            flex: 1;
        }
        .toast-icon {
            font-size: 1.1rem;
        }
        .toast-message {
            font-size: 0.9rem;
            line-height: 1.4;
        }
        .toast-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: var(--spacing-md);
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        .toast-close:hover {
            opacity: 1;
        }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(toast);
    
    // Auto-remover después de 4 segundos
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.animation = 'slideOutRight 0.4s ease';
            setTimeout(() => {
                toast.remove();
                style.remove();
            }, 400);
        }
    }, 4000);
}

/**
 * Obtener icono para toast según tipo
 */
function getToastIcon(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    return icons[type] || icons.info;
}

/**
 * Cerrar modales al hacer clic en overlay - Versión mejorada
 */
document.addEventListener('click', function(event) {
    // Solo cerrar si se hace clic exactamente en el overlay (no en sus hijos)
    if (event.target.classList.contains('modal-overlay')) {
        const assignModal = document.getElementById('assignKPIModal');
        const editModal = document.getElementById('editKPIModal');
        
        if (assignModal && assignModal.contains(event.target)) {
            closeAssignKPIModal();
        } else if (editModal && editModal.contains(event.target)) {
            closeEditKPIModal();
        }
    }
    
    // También manejar clics en el modal container (fondo oscuro)
    if (event.target.classList.contains('modal') && event.target.style.display !== 'none') {
        const modalId = event.target.id;
        if (modalId === 'assignKPIModal') {
            closeAssignKPIModal();
        } else if (modalId === 'editKPIModal') {
            closeEditKPIModal();
        }
    }
});

/**
 * Prevenir cierre del modal al hacer clic dentro del contenido
 */
document.addEventListener('click', function(event) {
    // Si el clic es dentro del modal-content, no cerrar el modal
    if (event.target.closest('.modal-content')) {
        event.stopPropagation();
    }
});

// Animaciones CSS adicionales
const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .modal.show {
        animation: fadeIn 0.3s ease;
    }
    
    .modal.show .modal-content {
        animation: slideInUp 0.3s ease;
    }
    
    .form-input.valid {
        border-color: var(--success);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .form-input.invalid {
        border-color: var(--error);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
`;

document.head.appendChild(additionalStyles);

console.log('📦 kpi-projects.js cargado correctamente');