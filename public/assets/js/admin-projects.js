// JS de Gestión de Proyectos (Admin)
// Funciones globales disponibles inmediatamente

// Función para abrir el modal de crear proyecto
function openCreateProjectModal() {
  const modal = document.getElementById('projectModal');
  const title = document.getElementById('modalTitle');
  const submitText = document.getElementById('submitText');
  const form = document.getElementById('projectForm');
  const projectId = document.getElementById('projectId');
  
  if (title) title.textContent = 'Crear Proyecto';
  if (submitText) submitText.textContent = 'Crear Proyecto';
  if (form) form.reset();
  if (projectId) projectId.value = '';
  if (modal) modal.style.display = 'block';
  
  // Limpiar errores si existen
  clearErrors();
}

// Función para cerrar el modal
function closeProjectModal() {
  const modal = document.getElementById('projectModal');
  if (modal) modal.style.display = 'none';
  clearErrors();
}

// Función para editar proyecto
function editProject(projectId) {
  // Validar que el ID sea un número
  if (!projectId || isNaN(projectId)) {
    console.error('ID de proyecto inválido:', projectId);
    return;
  }
  
  const modal = document.getElementById('projectModal');
  const title = document.getElementById('modalTitle');
  const submitText = document.getElementById('submitText');
  const projectIdField = document.getElementById('projectId');
  
  if (title) title.textContent = 'Editar Proyecto';
  if (submitText) submitText.textContent = 'Actualizar Proyecto';
  if (projectIdField) projectIdField.value = projectId;
  if (modal) modal.style.display = 'block';
  
  // TODO: Cargar datos del proyecto para edición
}

// Función para ver detalles del proyecto
function viewProject(projectId) {
  // Validar que el ID sea un número
  if (!projectId || isNaN(projectId)) {
    console.error('ID de proyecto inválido:', projectId);
    return;
  }
  
  if (typeof showToast === 'function') {
    showToast('Función de ver detalles en desarrollo', 'info');
  } else {
    alert('Función de ver detalles en desarrollo');
  }
}

// Función para eliminar proyecto
function deleteProject(projectId) {
  // Validar que el ID sea un número
  if (!projectId || isNaN(projectId)) {
    console.error('ID de proyecto inválido:', projectId);
    return;
  }
  
  if (confirm('¿Estás seguro de que quieres eliminar este proyecto?')) {
    // TODO: Implementar eliminación
    if (typeof showToast === 'function') {
      showToast('Función de eliminar en desarrollo', 'warning');
    } else {
      alert('Función de eliminar en desarrollo');
    }
  }
}

// Función para mostrar/ocultar menú de acciones
function toggleProjectMenu(projectId) {
  // Validar que el ID sea un número
  if (!projectId || isNaN(projectId)) {
    console.error('ID de proyecto inválido:', projectId);
    return;
  }
  
  const menu = document.getElementById('menu-' + projectId);
  if (!menu) return;
  
  // Cerrar otros menús abiertos
  document.querySelectorAll('.menu-dropdown').forEach(m => {
    if (m !== menu) m.classList.remove('show');
  });
  
  // Toggle del menú actual
  menu.classList.toggle('show');
}

// Función para filtrar proyectos
function filterProjects() {
  const statusFilter = document.getElementById('statusFilter');
  const clanFilter = document.getElementById('clanFilter');
  const projectCards = document.querySelectorAll('.project-card');
  
  const status = statusFilter ? statusFilter.value : '';
  const clan = clanFilter ? clanFilter.value : '';
  
  projectCards.forEach(card => {
    const projectStatus = card.dataset.status;
    const projectClan = card.dataset.clan;
    let showCard = true;
    
    if (status && projectStatus !== status) showCard = false;
    if (clan && projectClan !== clan) showCard = false;
    
    card.style.display = showCard ? 'block' : 'none';
  });
}

// Función para mostrar errores en el formulario
function showFormErrors(errors) {
  clearErrors();
  Object.keys(errors).forEach(field => {
    const errorElement = document.getElementById(field + 'Error');
    if (errorElement) {
      errorElement.textContent = errors[field];
      errorElement.classList.add('show');
    }
  });
}

// Función para limpiar errores
function clearErrors() {
  document.querySelectorAll('.error-message').forEach(el => {
    el.classList.remove('show');
    el.textContent = '';
  });
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
  // Manejar envío del formulario
  const form = document.getElementById('projectForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const submitBtn = document.getElementById('submitBtn');
      const submitText = document.getElementById('submitText');
      const submitLoader = document.getElementById('submitLoader');
      
      // Mostrar loader
      if (submitBtn) submitBtn.disabled = true;
      if (submitText) submitText.style.display = 'none';
      if (submitLoader) submitLoader.style.display = 'inline-block';
      
      // Preparar datos del formulario
      const formData = new FormData(form);
      
      // Enviar petición
      fetch('?route=admin/create-project', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (typeof showToast === 'function') {
            showToast(data.message || 'Proyecto creado exitosamente', 'success');
          }
          closeProjectModal();
          setTimeout(() => window.location.reload(), 1500);
        } else {
          if (data.errors) {
            showFormErrors(data.errors);
          } else {
            if (typeof showToast === 'function') {
              showToast(data.message || 'Error al crear el proyecto', 'error');
            }
          }
        }
      })
      .catch(error => {
        console.error('Error:', error);
        if (typeof showToast === 'function') {
          showToast('Error de conexión', 'error');
        }
      })
      .finally(() => {
        // Ocultar loader
        if (submitBtn) submitBtn.disabled = false;
        if (submitText) submitText.style.display = 'inline';
        if (submitLoader) submitLoader.style.display = 'none';
      });
    });
  }
  
  // Cerrar menús desplegables al hacer clic fuera
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-menu')) {
      document.querySelectorAll('.menu-dropdown').forEach(menu => {
        menu.classList.remove('show');
      });
    }
  });
  
  // Cerrar modal al hacer clic fuera
  window.addEventListener('click', function(event) {
    const modal = document.getElementById('projectModal');
    if (event.target === modal) {
      closeProjectModal();
    }
  });
});