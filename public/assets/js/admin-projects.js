// JS de Gestión de Proyectos (Admin)
// Garantiza funciones globales accesibles por handlers inline o listeners

(function() {
  let currentProjectId = null;
  let isEditMode = false;

  window.openCreateProjectModal = function() {
    isEditMode = false;
    currentProjectId = null;
    const title = document.getElementById('modalTitle');
    if (title) title.textContent = 'Crear Proyecto';
    const submitText = document.getElementById('submitText');
    if (submitText) submitText.textContent = 'Crear Proyecto';
    const form = document.getElementById('projectForm');
    if (form) form.reset();
    const pid = document.getElementById('projectId');
    if (pid) pid.value = '';
    const modal = document.getElementById('projectModal');
    if (modal) modal.style.display = 'block';
  };

  window.closeProjectModal = function() {
    const modal = document.getElementById('projectModal');
    if (modal) modal.style.display = 'none';
    if (typeof clearErrors === 'function') {
      clearErrors();
    }
  };

  window.editProject = function(projectId) {
    isEditMode = true;
    currentProjectId = projectId;
    const title = document.getElementById('modalTitle');
    if (title) title.textContent = 'Editar Proyecto';
    const submitText = document.getElementById('submitText');
    if (submitText) submitText.textContent = 'Actualizar Proyecto';
    const pid = document.getElementById('projectId');
    if (pid) pid.value = projectId;
    const modal = document.getElementById('projectModal');
    if (modal) modal.style.display = 'block';
  };

  window.viewProject = function(projectId) {
    if (typeof showToast === 'function') {
      showToast('Función de ver detalles en desarrollo', 'info');
    }
  };

  window.deleteProject = function(projectId) {
    if (typeof showConfirmationModal === 'function') {
      showConfirmationModal({
        title: 'Confirmar Eliminación',
        message: '¿Estás seguro de que quieres eliminar este proyecto?',
        type: 'warning',
        confirmText: 'Eliminar',
        cancelText: 'Cancelar',
        onConfirm: () => {
          if (typeof showToast === 'function') {
            showToast('Función de eliminar en desarrollo', 'warning');
          }
        }
      });
    }
  };

  window.toggleProjectMenu = function(projectId) {
    const menu = document.getElementById('menu-' + projectId);
    if (!menu) return;
    document.querySelectorAll('.menu-dropdown').forEach(m => {
      if (m !== menu) m.classList.remove('show');
    });
    menu.classList.toggle('show');
  };

  window.filterProjects = function() {
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
  };

  function showFormErrors(errors) {
    clearErrors();
    Object.keys(errors).forEach(field => {
      const el = document.getElementById(field + 'Error');
      if (el) {
        el.textContent = errors[field];
        el.classList.add('show');
      }
    });
  }

  function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => {
      el.classList.remove('show');
      el.textContent = '';
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    const btn1 = document.getElementById('openCreateProjectBtnHeader');
    if (btn1) btn1.addEventListener('click', openCreateProjectModal);
    const btn2 = document.getElementById('openCreateProjectBtnEmpty');
    if (btn2) btn2.addEventListener('click', openCreateProjectModal);

    const form = document.getElementById('projectForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const submitBtn = document.getElementById('submitBtn');
      const submitText = document.getElementById('submitText');
      const submitLoader = document.getElementById('submitLoader');
      if (submitBtn && submitText && submitLoader) {
        submitBtn.disabled = true;
        submitText.style.display = 'none';
        submitLoader.style.display = 'inline-block';
      }
      const formData = new FormData(form);
      fetch('?route=admin/create-project', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            if (typeof showToast === 'function') showToast(data.message, 'success');
            closeProjectModal();
            setTimeout(() => window.location.reload(), 1500);
          } else {
            if (data.errors) {
              showFormErrors(data.errors);
            } else {
              if (typeof showToast === 'function') showToast(data.message, 'error');
            }
          }
        })
        .catch(error => {
          console.error(error);
          if (typeof showToast === 'function') showToast('Error de conexión', 'error');
        })
        .finally(() => {
          if (submitBtn && submitText && submitLoader) {
            submitBtn.disabled = false;
            submitText.style.display = 'inline';
            submitLoader.style.display = 'none';
          }
        });
    });

    document.addEventListener('click', function(e) {
      if (!e.target.closest('.action-menu')) {
        document.querySelectorAll('.menu-dropdown').forEach(menu => menu.classList.remove('show'));
      }
    });

    window.addEventListener('click', function(event) {
      const modal = document.getElementById('projectModal');
      if (event.target === modal) {
        closeProjectModal();
      }
    });
  });
})();


