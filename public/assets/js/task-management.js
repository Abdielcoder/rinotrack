console.log('📜 Archivo task-management.js cargado correctamente - Versión:', Date.now());

let subtaskCounter = 0;

// Función de inicialización
function initializeTaskManagement() {
    console.log('🚀 === INICIALIZANDO GESTIÓN DE TAREAS ===');
    
    // Verificar que los elementos necesarios estén presentes
    const container = document.getElementById('subtasks-container');
    const template = document.getElementById('subtask-template');
    const addButton = document.querySelector('button[onclick="addSubtask()"]');
    
    console.log('🔍 Elementos encontrados:');
    console.log('  - Contenedor de subtareas:', container);
    console.log('  - Template de subtareas:', template);
    console.log('  - Botón agregar subtarea:', addButton);
    
    if (container && template && addButton) {
        console.log('✅ Todos los elementos están presentes');
    } else {
        console.error('❌ Faltan elementos necesarios');
    }
    
    // Verificar que el botón de guardar esté presente
    const saveButton = document.querySelector('button[onclick="saveTask()"]');
    console.log('💾 Botón de guardar:', saveButton);
    
    console.log('🎯 === INICIALIZACIÓN COMPLETADA ===');
}

// Ejecutar inicialización cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTaskManagement);
} else {
    initializeTaskManagement();
}

function addSubtask() {
    console.log('🚀 === AGREGANDO SUBTAREA ===');
    console.log('🔍 Buscando contenedor de subtareas...');
    
    const container = document.getElementById('subtasks-container');
    const template = document.getElementById('subtask-template');
    
    console.log('📦 Contenedor encontrado:', container);
    console.log('📋 Template encontrado:', template);
    
    if (!container || !template) {
        console.error('❌ No se encontró el contenedor o template de subtareas');
        console.error('❌ Container:', container);
        console.error('❌ Template:', template);
        return;
    }
    
    console.log('🔄 Reemplazando placeholders en template...');
    const subtaskHtml = template.innerHTML
        .replace(/{index}/g, subtaskCounter)
        .replace(/{number}/g, subtaskCounter + 1);
    
    console.log('📝 HTML generado:', subtaskHtml);
    
    const subtaskElement = document.createElement('div');
    subtaskElement.innerHTML = subtaskHtml;
    
    console.log('🔧 Elemento DOM creado:', subtaskElement);
    console.log('🔧 Primer hijo del elemento:', subtaskElement.firstElementChild);
    
    container.appendChild(subtaskElement.firstElementChild);
    
    console.log('✅ Subtarea agregada al contenedor');
    console.log('📊 Contador de subtareas actualizado a:', subtaskCounter + 1);
    
    subtaskCounter++;
    
    // Verificar que se agregó correctamente
    const allSubtasks = container.querySelectorAll('.subtask-item');
    console.log('📊 Total de subtareas en el contenedor:', allSubtasks.length);
}

function removeSubtask(index) {
    console.log('🗑️ === REMOVIENDO SUBTAREA ===');
    console.log('🔍 Buscando subtarea con índice:', index);
    
    const subtaskElement = document.querySelector(`[data-subtask-index="${index}"]`);
    console.log('📦 Elemento encontrado:', subtaskElement);
    
    if (subtaskElement) {
        subtaskElement.remove();
        console.log('✅ Subtarea removida exitosamente');
        
        // Verificar que se removió correctamente
        const allSubtasks = document.querySelectorAll('.subtask-item');
        console.log('📊 Total de subtareas restantes:', allSubtasks.length);
    } else {
        console.error('❌ No se encontró la subtarea con índice:', index);
    }
}

function saveTask() {
    // Validar formulario
    const taskTitle = document.getElementById('task_title').value;
    const taskDueDate = document.getElementById('task_due_date').value;
    const assignedMembers = document.querySelectorAll('input[name="assigned_members[]"]:checked');
    
    if (!taskTitle || !taskDueDate) {
        showToast('Por favor completa todos los campos requeridos', 'error');
        return;
    }
    
    if (assignedMembers.length === 0) {
        showToast('Debes asignar al menos un colaborador', 'error');
        return;
    }
    
    // Recopilar datos del formulario
    const formData = new FormData();
    formData.append('task_title', taskTitle);
    formData.append('task_due_date', taskDueDate);
    formData.append('task_project', document.getElementById('task_project').value);
    formData.append('task_description', document.getElementById('task_description').value);
    
    // Agregar miembros asignados
    assignedMembers.forEach(member => {
        formData.append('assigned_members[]', member.value);
    });
    
    // Agregar subtareas
    console.log('🔍 Buscando elementos de subtareas...');
    const subtaskElements = document.querySelectorAll('.subtask-item');
    console.log('📊 Elementos de subtareas encontrados:', subtaskElements.length);
    
    const subtasks = [];
    subtaskElements.forEach((subtask, index) => {
        console.log(`🔄 Procesando subtarea ${index + 1}:`, subtask);
        
        const titleInput = subtask.querySelector('input[name^="subtasks"][name$="[title]"]');
        const percentageInput = subtask.querySelector('input[name^="subtasks"][name$="[percentage]"]');
        const descriptionInput = subtask.querySelector('textarea[name^="subtasks"][name$="[description]"]');
        const dueDateInput = subtask.querySelector('input[name^="subtasks"][name$="[due_date]"]');
        const priorityInput = subtask.querySelector('select[name^="subtasks"][name$="[priority]"]');
        const assignedUserInput = subtask.querySelector('select[name^="subtasks"][name$="[assigned_to_user_id]"]');
        
        console.log(`📝 Inputs encontrados para subtarea ${index + 1}:`, {
            title: titleInput,
            percentage: percentageInput,
            description: descriptionInput,
            dueDate: dueDateInput,
            priority: priorityInput,
            assignedUser: assignedUserInput
        });
        
        if (titleInput && percentageInput) {
            const title = titleInput.value;
            const percentage = percentageInput.value;
            const description = descriptionInput ? descriptionInput.value : '';
            const dueDate = dueDateInput ? dueDateInput.value : null;
            const priority = priorityInput ? priorityInput.value : 'medium';
            const assignedUserId = assignedUserInput ? (assignedUserInput.value || null) : null;
            
            console.log(`📋 Valores de subtarea ${index + 1}:`, { 
                title, 
                percentage, 
                description, 
                dueDate, 
                priority, 
                assignedUserId 
            });
            
            if (title && percentage) {
                subtasks.push({
                    title: title,
                    percentage: percentage,
                    description: description,
                    due_date: dueDate,
                    priority: priority,
                    assigned_to_user_id: assignedUserId
                });
                console.log(`✅ Subtarea ${index + 1} agregada al array`);
            } else {
                console.log(`⚠️ Subtarea ${index + 1} no tiene título o porcentaje, saltando`);
            }
        } else {
            console.log(`❌ No se encontraron inputs para subtarea ${index + 1}`);
        }
    });
    
    console.log('📊 Array final de subtareas:', subtasks);
    
    if (subtasks.length > 0) {
        const subtasksJson = JSON.stringify(subtasks);
        formData.append('subtasks', subtasksJson);
        console.log('✅ Subtareas incluidas en FormData:', subtasksJson);
    } else {
        console.log('⚠️ No hay subtareas para incluir');
    }
    
    // Log para debug
    console.log('🚀 === ENVIANDO TAREA AL SERVIDOR ===');
    console.log('📋 FormData completo:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}:`, value);
    }
    
    // Log específico para subtareas
    const subtasksValue = formData.get('subtasks');
    if (subtasksValue) {
        console.log('🔍 Valor de subtareas en FormData:', subtasksValue);
        try {
            const parsed = JSON.parse(subtasksValue);
            console.log('✅ Subtareas parseadas correctamente:', parsed);
        } catch (e) {
            console.error('❌ Error parseando subtareas:', e);
        }
    } else {
        console.log('⚠️ No se encontró subtasks en FormData');
    }
    
    // Enviar datos al servidor
    fetch('?route=clan_leader/create-task', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', response.headers);
        return response.text(); // Primero obtener como texto
    })
    .then(text => {
        console.log('📄 Response body completo:', text);
        console.log('📄 Longitud del response:', text.length);
        
        try {
            const data = JSON.parse(text);
            console.log('✅ JSON parseado correctamente:', data);
            
            if (data.success) {
                console.log('🎉 Tarea creada exitosamente');
                showToast('Tarea creada exitosamente', 'success');
                setTimeout(() => {
                    window.location.href = '?route=clan_leader/tasks';
                }, 1500);
            } else {
                console.error('❌ Error del servidor:', data.message);
                showToast(data.message || 'Error al crear la tarea', 'error');
            }
        } catch (e) {
            console.error('❌ Error parseando JSON:', e);
            console.error('❌ Respuesta no JSON:', text);
            console.error('❌ Stack trace:', e.stack);
            showToast('Error del servidor. Ver consola para detalles.', 'error');
        }
    })
    .catch(error => {
        console.error('💥 === ERROR DE RED ===');
        console.error('Error completo:', error);
        console.error('Error name:', error.name);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        
        if (error.name === 'TypeError') {
            console.error('❌ Error de tipo - posible problema con fetch o FormData');
        } else if (error.name === 'NetworkError') {
            console.error('❌ Error de red - problema de conectividad');
        }
        
        showToast('Error al crear la tarea: ' + error.message, 'error');
    });
}

function closeTaskManagement() {
    showConfirmationModal({
        title: 'Confirmar Cierre',
        message: '¿Estás seguro de que quieres cerrar? Los cambios no guardados se perderán.',
        type: 'warning',
        confirmText: 'Cerrar',
        cancelText: 'Cancelar',
        onConfirm: () => {
            window.location.href = '?route=clan_leader/tasks';
        }
    });
}

/**
 * Inicializa la funcionalidad de seleccionar/deseleccionar todos los colaboradores
 */
function initializeSelectAllCollaborators() {
    console.log('🔍 Inicializando funcionalidad de seleccionar todos los colaboradores...');
    
    // Función simple para seleccionar/deseleccionar todos
    function selectAllMembers(selectAll) {
        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
        console.log('👥 Encontrados', memberCheckboxes.length, 'checkboxes de miembros');
        
        memberCheckboxes.forEach((checkbox, index) => {
            checkbox.checked = selectAll;
            console.log(`✅ Checkbox ${index + 1} establecido a:`, selectAll);
        });
    }
    
    // Buscar el checkbox principal
    const selectAllCheckbox = document.getElementById('select_all_members');
    console.log('📋 Checkbox principal encontrado:', selectAllCheckbox);
    
    if (selectAllCheckbox) {
        // Agregar evento click simple
        selectAllCheckbox.addEventListener('click', function() {
            console.log('🖱️ Checkbox principal clickeado');
            const isChecked = this.checked;
            console.log('🔄 Estado del checkbox:', isChecked);
            
            // Seleccionar/deseleccionar todos
            selectAllMembers(isChecked);
        });
        
        console.log('✅ Evento click agregado al checkbox principal');
    } else {
        console.error('❌ No se encontró el checkbox principal');
    }
}





function getMemberColor(userId) {
    const colors = ['#667eea', '#48bb78', '#ed8936', '#e53e3e', '#9f7aea', '#38b2ac', '#a0aec0', '#f6e05e'];
    return colors[userId % colors.length];
}

function getActiveTasksCount(userId) {
    // Esta función debería obtener el conteo real de tareas activas del usuario
    // Por ahora retornamos un número aleatorio para demostración
    return Math.floor(Math.random() * 20) + 1;
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 350px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    `;
    
    if (type === 'success') {
        toast.style.background = '#10b981';
    } else if (type === 'error') {
        toast.style.background = '#ef4444';
    } else if (type === 'warning') {
        toast.style.background = '#f59e0b';
    } else {
        toast.style.background = '#3b82f6';
    }
    
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Función para mostrar modales de confirmación personalizados
function showConfirmationModal(options) {
    const {
        title = 'Confirmar Acción',
        message = '¿Estás seguro de que quieres realizar esta acción?',
        icon = 'warning',
        confirmText = 'Confirmar',
        cancelText = 'Cancelar',
        onConfirm = null,
        onCancel = null,
        type = 'warning' // warning, info, success
    } = options;

    // Crear el HTML del modal
    const modalHTML = `
        <div class="confirmation-modal-overlay" id="confirmationModalOverlay">
            <div class="confirmation-modal" id="confirmationModal">
                <div class="confirmation-modal-header">
                    <h3 class="confirmation-modal-title">${title}</h3>
                </div>
                <div class="confirmation-modal-body">
                    <i class="fas fa-${getIconForType(type)} confirmation-modal-icon ${type}"></i>
                    <p class="confirmation-modal-message">${message}</p>
                    <div class="confirmation-modal-actions">
                        <button class="confirmation-modal-btn cancel" id="confirmationCancelBtn">
                            ${cancelText}
                        </button>
                        <button class="confirmation-modal-btn confirm" id="confirmationConfirmBtn">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Agregar el modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const overlay = document.getElementById('confirmationModalOverlay');
    const modal = document.getElementById('confirmationModal');
    const confirmBtn = document.getElementById('confirmationConfirmBtn');
    const cancelBtn = document.getElementById('confirmationCancelBtn');

    // Mostrar el modal con animación
    setTimeout(() => {
        overlay.classList.add('show');
    }, 10);

    // Función para cerrar el modal
    const closeModal = (result) => {
        overlay.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(overlay);
            if (result && onConfirm) {
                onConfirm();
            } else if (!result && onCancel) {
                onCancel();
            }
        }, 300);
    };

    // Event listeners
    confirmBtn.addEventListener('click', () => closeModal(true));
    cancelBtn.addEventListener('click', () => closeModal(false));
    
    // Cerrar al hacer clic en el overlay
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeModal(false);
        }
    });

    // Cerrar con Escape
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            closeModal(false);
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);

    // Enfocar el botón de cancelar por defecto
    cancelBtn.focus();
}

// Función para obtener el icono según el tipo
function getIconForType(type) {
    const icons = {
        warning: 'exclamation-triangle',
        info: 'info-circle',
        success: 'check-circle',
        danger: 'times-circle',
        question: 'question-circle'
    };
    return icons[type] || 'exclamation-triangle';
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado, inicializando funcionalidades...');
    
    // Establecer fecha mínima como hoy
    const today = new Date().toISOString().split('T')[0];
    const dueDateInput = document.getElementById('task_due_date');
    if (dueDateInput) {
        dueDateInput.min = today;
        console.log('📅 Fecha mínima establecida:', today);
    }
    
    // Inicializar funcionalidad de seleccionar todos los colaboradores
    console.log('🔧 Llamando a initializeSelectAllCollaborators...');
    initializeSelectAllCollaborators();
    
    console.log('✅ Todas las funcionalidades inicializadas');
});

// Estilos para animaciones de toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Estilos CSS para modales de confirmación
const modalStyles = document.createElement('style');
modalStyles.textContent = `
    .confirmation-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .confirmation-modal-overlay.show {
        opacity: 1;
    }

    .confirmation-modal {
        background: white;
        border-radius: 12px;
        padding: 0;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    .confirmation-modal-overlay.show .confirmation-modal {
        transform: scale(1);
    }

    .confirmation-modal-header {
        padding: 20px 24px 0;
    }

    .confirmation-modal-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
    }

    .confirmation-modal-body {
        padding: 20px 24px 24px;
        text-align: center;
    }

    .confirmation-modal-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .confirmation-modal-icon.warning {
        color: #f59e0b;
    }

    .confirmation-modal-icon.info {
        color: #3b82f6;
    }

    .confirmation-modal-icon.success {
        color: #10b981;
    }

    .confirmation-modal-icon.danger {
        color: #ef4444;
    }

    .confirmation-modal-message {
        margin: 0 0 1.5rem 0;
        color: #6b7280;
        line-height: 1.5;
        white-space: pre-line;
    }

    .confirmation-modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .confirmation-modal-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 100px;
    }

    .confirmation-modal-btn.cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .confirmation-modal-btn.cancel:hover {
        background: #e5e7eb;
    }

    .confirmation-modal-btn.confirm {
        background: #ef4444;
        color: white;
    }

    .confirmation-modal-btn.confirm:hover {
        background: #dc2626;
    }

    .confirmation-modal-btn.confirm.warning {
        background: #f59e0b;
    }

    .confirmation-modal-btn.confirm.warning:hover {
        background: #d97706;
    }

    .confirmation-modal-btn.confirm.success {
        background: #10b981;
    }

    .confirmation-modal-btn.confirm.success:hover {
        background: #059669;
    }

    .confirmation-modal-btn.confirm.info {
        background: #3b82f6;
    }

    .confirmation-modal-btn.confirm.info:hover {
        background: #2563eb;
    }
`;
document.head.appendChild(modalStyles); 