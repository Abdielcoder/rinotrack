// KPI Dashboard - JavaScript Interactivo

document.addEventListener('DOMContentLoaded', function() {
    initKPIDashboard();
});

function initKPIDashboard() {
    // Inicializar animaciones
    setTimeout(() => {
        animateNumbers();
        animateProgressBars();
    }, 300);

    // Configurar eventos
    setupEventListeners();
    
    // Auto-refresh cada 5 minutos
    setupAutoRefresh();
}

// Funciones para el dashboard KPI
function viewFullRanking() {
    // Implementar modal o redirección para ver ranking completo
    showToast('Funcionalidad de ranking completo en desarrollo', 'info');
}

function viewClanKPIDetails(clanId) {
    // Implementar modal o página de detalles KPI del clan
    window.location.href = `?route=kpi/clan-details&clanId=${clanId}`;
}

function assignKPItoClan(clanId) {
    // Redirigir a asignación de KPIs
    window.location.href = `?route=kpi/projects&clan=${clanId}`;
}

// Animaciones de números
function animateNumbers() {
    const statNumbers = document.querySelectorAll('.stat-number, .stat-value, .metric-value');
    
    statNumbers.forEach(element => {
        const finalNumber = parseInt(element.textContent.replace(/,/g, ''));
        if (isNaN(finalNumber)) return;
        
        const duration = 1500;
        const start = 0;
        const startTime = performance.now();
        
        function updateNumber(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            const currentNumber = Math.floor(easeOutCubic * finalNumber);
            
            element.textContent = new Intl.NumberFormat().format(currentNumber);
            
            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            } else {
                element.textContent = new Intl.NumberFormat().format(finalNumber);
            }
        }
        
        requestAnimationFrame(updateNumber);
    });
}

// Animar progreso de barras
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill, .progress-fill-mini, .progress-fill-project');
    
    progressBars.forEach((bar, index) => {
        const targetWidth = bar.style.width;
        const currentWidth = parseFloat(targetWidth) || 0;
        
        // Solo animar si hay progreso (width > 0)
        if (currentWidth > 0) {
            // Establecer width inicial solo si no está ya establecido
            if (!bar.dataset.animated) {
                bar.style.width = '0%';
                bar.dataset.animated = 'true';
                
                // Animar con delay escalonado para efecto más suave
                setTimeout(() => {
                    bar.style.transition = 'width 0.8s ease-out';
                    bar.style.width = targetWidth;
                }, 100 + (index * 50));
            }
        }
    });
}

// Configurar listeners de eventos
function setupEventListeners() {
    // Listener para tecla F5 personalizada
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
            e.preventDefault();
            refreshDashboard();
        }
    });

    // Listeners para botones de acción
    setupActionButtons();

    // Listener para tooltips
    setupTooltips();
}

function setupActionButtons() {
    // Botones de ranking
    const rankingButtons = document.querySelectorAll('.action-btn-small');
    rankingButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Botones principales
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Efecto de ripple
            createRippleEffect(this);
        });
    });
}

function createRippleEffect(element) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

function setupTooltips() {
    const elementsWithTooltips = document.querySelectorAll('[title]');
    
    elementsWithTooltips.forEach(element => {
        let tooltip;
        
        element.addEventListener('mouseenter', function() {
            const title = this.getAttribute('title');
            if (!title) return;
            
            // Crear tooltip
            tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = title;
            document.body.appendChild(tooltip);
            
            // Posicionar tooltip
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
            
            // Remover title para evitar tooltip nativo
            this.setAttribute('data-original-title', title);
            this.removeAttribute('title');
            
            // Animar entrada
            setTimeout(() => {
                tooltip.style.opacity = '1';
                tooltip.style.transform = 'translateY(0)';
            }, 10);
        });
        
        element.addEventListener('mouseleave', function() {
            if (tooltip) {
                tooltip.style.opacity = '0';
                tooltip.style.transform = 'translateY(-5px)';
                
                setTimeout(() => {
                    if (tooltip.parentNode) {
                        tooltip.parentNode.removeChild(tooltip);
                    }
                }, 200);
            }
            
            // Restaurar title
            const originalTitle = this.getAttribute('data-original-title');
            if (originalTitle) {
                this.setAttribute('title', originalTitle);
                this.removeAttribute('data-original-title');
            }
        });
    });
}

// Auto-refresh del dashboard
function setupAutoRefresh() {
    // Refresh automático cada 5 minutos
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            refreshDashboardData();
        }
    }, 5 * 60 * 1000);
}

// Función para refrescar datos sin recargar página
function refreshDashboardData() {
    showToast('Actualizando datos...', 'info');
    
    // Simular carga de datos
    setTimeout(() => {
        // En una implementación real, aquí harías fetch a las APIs
        animateNumbers();
        // No animar barras de progreso en auto-refresh para evitar confusión
        // animateProgressBars();
        showToast('Datos actualizados', 'success');
    }, 1000);
}

// Función para refrescar página completa
function refreshDashboard() {
    showToast('Actualizando dashboard...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Funciones de utilidad para KPIs
function formatKPINumber(number) {
    if (number >= 1000000) {
        return (number / 1000000).toFixed(1) + 'M';
    } else if (number >= 1000) {
        return (number / 1000).toFixed(1) + 'K';
    }
    return number.toLocaleString();
}

function calculateEfficiency(earned, assigned) {
    if (assigned === 0) return 0;
    return Math.round((earned / assigned) * 100);
}

function getProgressColor(percentage) {
    if (percentage >= 80) return 'var(--success)';
    if (percentage >= 60) return 'var(--warning)';
    if (percentage >= 40) return 'var(--info)';
    return 'var(--error)';
}

// Funciones para manejo de modales
function openKPIModal(title, content) {
    // Crear modal dinámico
    const modal = document.createElement('div');
    modal.className = 'kpi-modal-overlay';
    modal.innerHTML = `
        <div class="kpi-modal">
            <div class="kpi-modal-header">
                <h3>${title}</h3>
                <button class="kpi-modal-close">&times;</button>
            </div>
            <div class="kpi-modal-content">
                ${content}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Configurar eventos
    const closeBtn = modal.querySelector('.kpi-modal-close');
    closeBtn.addEventListener('click', () => closeKPIModal(modal));
    
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeKPIModal(modal);
        }
    });
    
    // Animar entrada
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
    
    return modal;
}

function closeKPIModal(modal) {
    modal.classList.remove('show');
    setTimeout(() => {
        if (modal.parentNode) {
            modal.parentNode.removeChild(modal);
        }
    }, 300);
}

// Funciones para gráficos dinámicos
function createMiniChart(data, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    // Crear SVG simple para mini gráfico
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '100');
    svg.setAttribute('height', '30');
    svg.setAttribute('viewBox', '0 0 100 30');
    
    // Crear línea de progreso
    const polyline = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
    
    const points = data.map((value, index) => {
        const x = (index / (data.length - 1)) * 100;
        const y = 30 - (value / Math.max(...data)) * 25;
        return `${x},${y}`;
    }).join(' ');
    
    polyline.setAttribute('points', points);
    polyline.setAttribute('fill', 'none');
    polyline.setAttribute('stroke', 'var(--primary-color)');
    polyline.setAttribute('stroke-width', '2');
    
    svg.appendChild(polyline);
    container.appendChild(svg);
}

// Funciones para manejo de filtros
function filterByPeriod(period) {
    // Implementar filtro por período
    showToast(`Filtrando por: ${period}`, 'info');
}

function filterByClan(clanId) {
    // Implementar filtro por clan
    showToast(`Filtrando por clan: ${clanId}`, 'info');
}

function resetFilters() {
    // Resetear todos los filtros
    showToast('Filtros reseteados', 'info');
    refreshDashboard();
}

// Funciones para exportar datos
function exportDashboardData(format = 'pdf') {
    showToast(`Exportando dashboard en formato ${format.toUpperCase()}...`, 'info');
    
    // En una implementación real, aquí se generaría el archivo
    setTimeout(() => {
        showToast(`Dashboard exportado exitosamente`, 'success');
    }, 2000);
}

// Inicializar cuando se carga el documento
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKPIDashboard);
} else {
    initKPIDashboard();
}