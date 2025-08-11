console.log('📜 Archivo task-management.js cargado correctamente');

let subtaskCounter = 0;

// function addSubtask() {
//     const container = document.getElementById('subtasks-container');
//     const template = document.getElementById('subtask-template');
//     const subtaskHtml = template.innerHTML
//         .replace(/{index}/g, subtaskCounter)
//         .replace(/{number}/g, subtaskCounter + 1);
    
//     const subtaskElement = document.createElement('div');
//     subtaskElement.innerHTML = subtaskHtml;
//     container.appendChild(subtaskElement.firstElementChild);
    
//     subtaskCounter++;
// }

// function removeSubtask(index) {
//     const subtaskElement = document.querySelector(`[data-subtask-index="${index}"]`);
//     if (subtaskElement) {
//         subtaskElement.remove();
//     }
// }

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
    
    // Agregar subtareas - OCULTO
    // const subtasks = [];
    // document.querySelectorAll('.subtask-item').forEach((subtask, index) => {
    //     const title = subtask.querySelector('input[name^="subtasks"][name$="[title]"]').value;
    //     const percentage = subtask.querySelector('input[name^="subtasks"][name$="[percentage]"]').value;
    //     const description = subtask.querySelector('textarea[name^="subtasks"][name$="[description]"]').value;
        
    //     if (title && percentage) {
    //         subtasks.push({
    //             title: title,
    //             percentage: percentage,
    //             description: description
    //         });
    //     }
    // });
    
    // formData.append('subtasks', JSON.stringify(subtasks));
    
    // Log para debug
    console.log('Enviando tarea con datos:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}:`, value);
    }
    
    // Enviar datos al servidor
    fetch('?route=clan_leader/create-task', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text(); // Primero obtener como texto
    })
    .then(text => {
        console.log('Response body:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showToast('Tarea creada exitosamente', 'success');
                setTimeout(() => {
                    window.location.href = '?route=clan_leader/tasks';
                }, 1500);
            } else {
                showToast(data.message || 'Error al crear la tarea', 'error');
                console.error('Error del servidor:', data.message);
            }
        } catch (e) {
            console.error('Error parseando JSON:', e);
            console.error('Respuesta no JSON:', text);
            showToast('Error del servidor. Ver consola para detalles.', 'error');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        showToast('Error al crear la tarea', 'error');
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
    
    // Esperar un poco más para asegurar que el DOM esté completamente cargado
    setTimeout(() => {
        const selectAllCheckbox = document.getElementById('select_all_members');
        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
        
        console.log('📋 Checkbox principal encontrado:', selectAllCheckbox);
        console.log('👥 Checkboxes de miembros encontrados:', memberCheckboxes.length);
        
        if (!selectAllCheckbox) {
            console.error('❌ No se encontró el checkbox principal "select_all_members"');
            console.log('🔍 Buscando elementos en el DOM...');
            console.log('🔍 Elementos con ID "select_all_members":', document.querySelectorAll('#select_all_members'));
            console.log('🔍 Elementos con clase "select-all-checkbox":', document.querySelectorAll('.select-all-checkbox'));
            return;
        }
        
        if (memberCheckboxes.length === 0) {
            console.error('❌ No se encontraron checkboxes de miembros con clase "member-checkbox"');
            console.log('🔍 Buscando elementos en el DOM...');
            console.log('🔍 Elementos con clase "member-checkbox":', document.querySelectorAll('.member-checkbox'));
            console.log('🔍 Elementos con clase "collaborator-checkbox":', document.querySelectorAll('.collaborator-checkbox'));
            return;
        }
        
        // Evento para el checkbox "Seleccionar todos"
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            console.log('🔄 Checkbox principal cambiado a:', isChecked);
            
            // Seleccionar/deseleccionar todos los checkboxes de miembros
            memberCheckboxes.forEach((checkbox, index) => {
                checkbox.checked = isChecked;
                console.log(`✅ Checkbox ${index + 1} establecido a:`, isChecked);
            });
            
            // Actualizar el estado visual
            updateSelectAllState();
        });
        
        // Eventos para los checkboxes individuales
        memberCheckboxes.forEach((checkbox, index) => {
            checkbox.addEventListener('change', function() {
                console.log(`👤 Checkbox de miembro ${index + 1} cambiado a:`, this.checked);
                updateSelectAllState();
            });
        });
        
        // Función para actualizar el estado del checkbox "Seleccionar todos"
        function updateSelectAllState() {
            const checkedCount = document.querySelectorAll('.member-checkbox:checked').length;
            const totalCount = memberCheckboxes.length;
            
            console.log(`📊 Estado actual: ${checkedCount}/${totalCount} seleccionados`);
            
            if (checkedCount === 0) {
                // Ninguno seleccionado
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
                console.log('🔘 Estado: Ninguno seleccionado');
            } else if (checkedCount === totalCount) {
                // Todos seleccionados
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
                console.log('✅ Estado: Todos seleccionados');
            } else {
                // Algunos seleccionados (estado indeterminado)
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
                console.log('⚠️ Estado: Algunos seleccionados (indeterminado)');
            }
        }
        
        // Estado inicial
        updateSelectAllState();
        console.log('🎉 Funcionalidad de seleccionar todos inicializada correctamente');
        
        // Verificación final
        console.log('🔍 Verificación final:');
        console.log('📍 Checkbox principal posición:', selectAllCheckbox.getBoundingClientRect());
        console.log('📍 Checkbox principal visible:', selectAllCheckbox.offsetParent !== null);
        console.log('📍 Checkbox principal clickeable:', selectAllCheckbox.style.pointerEvents !== 'none');
        
    }, 100); // Esperar 100ms para asegurar que el DOM esté listo
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
    
    // Agregar primera subtarea por defecto - OCULTO
    // addSubtask();
    
    console.log('✅ Todas las funcionalidades inicializadas');
    
    // Verificación adicional después de un breve delay
    setTimeout(() => {
        console.log('🔍 Verificación adicional después de 500ms...');
        const selectAllCheckbox = document.getElementById('select_all_members');
        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
        
        console.log('📋 Checkbox principal (verificación tardía):', selectAllCheckbox);
        console.log('👥 Checkboxes de miembros (verificación tardía):', memberCheckboxes.length);
        
        if (selectAllCheckbox) {
            console.log('✅ Checkbox principal encontrado correctamente');
            console.log('📍 Posición:', selectAllCheckbox.getBoundingClientRect());
            console.log('🎨 Estilos computados:', window.getComputedStyle(selectAllCheckbox));
        }
        
        if (memberCheckboxes.length > 0) {
            console.log('✅ Checkboxes de miembros encontrados correctamente');
            memberCheckboxes.forEach((checkbox, index) => {
                console.log(`📍 Checkbox ${index + 1} posición:`, checkbox.getBoundingClientRect());
            });
        }
    }, 500);
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