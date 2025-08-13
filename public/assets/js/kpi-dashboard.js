// KPI Dashboard - JavaScript Interactivo

document.addEventListener('DOMContentLoaded', function() {
    initKPIDashboard();
    // Inicializar el camino tipo serpiente
    if (window.snakePathData) {
        initializeSnakePath();
    }
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

// ============================================================================
// CAMINO TIPO SERPIENTE - FUNCIONES
// ============================================================================

/**
 * Inicializar el camino tipo serpiente
 */
function initializeSnakePath() {
    console.log('Inicializando snake path...');
    console.log('window.snakePathData:', window.snakePathData);
    
    if (!window.snakePathData) {
        console.log('No hay datos disponibles para el snake path');
        return;
    }
    
    if (!window.snakePathData.clans_data) {
        console.log('No hay datos de clanes en snakePathData');
        console.log('Estructura de snakePathData:', Object.keys(window.snakePathData));
        return;
    }
    
    console.log('Inicializando snake path con datos:', window.snakePathData);
    console.log('Clanes disponibles:', window.snakePathData.clans_data);
    
    // Generar el camino
    generateSnakePath(window.snakePathData);
    
    // Generar marcadores de clanes
    generateClanMarkers(window.snakePathData);
    
    // Generar leyenda
    generateClanLegend(window.snakePathData);
}

/**
 * Generar el camino tipo serpiente
 */
function generateSnakePath(data) {
    const pathGrid = document.getElementById('snakePathGrid');
    if (!pathGrid) return;
    
    // Definir el camino tipo serpiente con estilo elegante y flujo suave
    const snakePath = [
        // Fila 1: Izquierda a derecha (INICIO -> 250)
        ['INICIO', '50', '100', '150', '200', '250', null, null, null, null, null, null, null],
        
        // Fila 2: Izquierda a derecha (275 -> 500)
        ['275', '300', '325', '350', '375', '400', '425', '450', '475', '500', null, null, null],
        
        // Fila 3: Derecha a izquierda (525 -> 750)
        [null, null, null, '525', '550', '575', '600', '625', '650', '675', '700', '725', '750'],
        
        // Fila 4: Izquierda a derecha (775 -> 1000 FIN)
        ['775', '800', '825', '850', '875', '900', '925', '950', '975', '1000', 'FIN', null, null]
    ];
    
    // Limpiar el grid
    pathGrid.innerHTML = '';
    
    // Generar las celdas del camino
    snakePath.forEach((row, rowIndex) => {
        row.forEach((value, colIndex) => {
            if (value !== null) {
                const cell = createPathCell(value, rowIndex, colIndex, data);
                pathGrid.appendChild(cell);
            }
        });
    });
    
    // Agregar flechas de conexión
    // addConnectionArrows(); // Eliminado según la solicitud
}



/**
 * Crear una celda del camino
 */
function createPathCell(value, rowIndex, colIndex, data) {
    const cell = document.createElement('div');
    cell.className = 'path-cell';
    
    // Determinar el tipo de celda
    if (value === 'INICIO') {
        cell.classList.add('start');
        cell.textContent = 'INICIO';
    } else if (value === 'META') {
        cell.classList.add('goal');
        cell.textContent = 'META';
    } else {
        const numValue = parseInt(value);
        if (isNaN(numValue)) {
            cell.classList.add('regular');
            cell.textContent = value;
        } else {
            // Verificar si es un hito importante
            if (numValue % 100 === 0 && numValue > 0) {
                cell.classList.add('milestone');
            } else {
                cell.classList.add('regular');
            }
            
            // Verificar si es la posición actual del trimestre
            if (isQuarterProgressPosition(numValue, data.quarter_progress)) {
                cell.classList.add('quarter-progress');
            }
            
            cell.textContent = numValue;
        }
    }
    
    // Posicionar la celda en el grid
    cell.style.gridRow = rowIndex + 1;
    cell.style.gridColumn = colIndex + 1;
    
    // Agregar tooltip con información
    if (value !== 'INICIO' && value !== 'META') {
        const numValue = parseInt(value);
        if (!isNaN(numValue)) {
            cell.title = `${numValue} puntos`;
        }
    }
    
    return cell;
}

/**
 * Verificar si una posición corresponde al progreso actual del trimestre
 */
function isQuarterProgressPosition(position, quarterProgress) {
    const targetPosition = Math.round((quarterProgress / 100) * 1000);
    return Math.abs(position - targetPosition) <= 25; // Margen de 25 puntos
}

/**
 * Generar los marcadores de clanes
 */
function generateClanMarkers(data) {
    const markersContainer = document.getElementById('clanMarkers');
    if (!markersContainer) {
        console.error('No se encontró el contenedor de marcadores');
        return;
    }
    
    // Limpiar marcadores existentes
    markersContainer.innerHTML = '';
    
    console.log('Generando marcadores para clanes:', data.clans_data);
    
    // Agrupar clanes por posición
    const clansByPosition = {};
    
    data.clans_data.forEach((clan, index) => {
        console.log(`Clan ${index + 1}:`, clan.clan_name, 'Puntos:', clan.earned_points);
        
        // Calcular posición base usando earned_points directamente
        const basePosition = calculateMarkerPosition(clan.earned_points || 0);
        const positionKey = `${basePosition.x}-${basePosition.y}`;
        
        if (!clansByPosition[positionKey]) {
            clansByPosition[positionKey] = [];
        }
        clansByPosition[positionKey].push(clan);
    });
    
    // Crear marcadores con padding para múltiples clanes
    Object.keys(clansByPosition).forEach(positionKey => {
        const clans = clansByPosition[positionKey];
        const basePosition = calculateMarkerPosition(clans[0].earned_points || 0);
        
        // Calcular padding basado en el número de clanes
        const totalClans = clans.length;
        const markerWidth = 32; // Ancho del marcador en px
        const padding = 8; // Espacio entre marcadores en px
        const totalWidth = (totalClans * markerWidth) + ((totalClans - 1) * padding);
        
        // Convertir porcentaje a píxeles (asumiendo que el contenedor tiene un ancho fijo)
        const containerWidth = 1200; // Ancho aproximado del contenedor en px
        const baseXInPx = (basePosition.x / 100) * containerWidth;
        const startX = baseXInPx - (totalWidth / 2); // Centrar el grupo
        
        clans.forEach((clan, index) => {
            const marker = createClanMarker(clan, data, {
                x: startX + (index * (markerWidth + padding)),
                y: Math.max(2, basePosition.y - 8) // 8% arriba de la caja, mínimo 2%
            });
            markersContainer.appendChild(marker);
        });
    });
    
    console.log('Marcadores generados:', markersContainer.children.length);
}

/**
 * Crear un marcador de clan
 */
function createClanMarker(clan, data, customPosition = null) {
    const marker = document.createElement('div');
    marker.className = 'clan-marker';
    marker.style.backgroundColor = clan.clan_color || '#3b82f6';
    marker.style.width = '32px';
    marker.style.height = '32px';
    marker.style.zIndex = '10';
    
    // Calcular posición
    let position;
    if (customPosition) {
        // Usar posición personalizada (para múltiples clanes)
        position = customPosition;
        marker.style.left = position.x + 'px';
        marker.style.top = position.y + '%';
    } else {
        // Calcular posición normal usando earned_points directamente
        position = calculateMarkerPosition(clan.earned_points || 0);
        
        // Posicionar ligeramente arriba de la caja para mejor visibilidad
        const adjustedY = Math.max(2, position.y - 8); // 8% arriba de la caja, mínimo 2%
        
        marker.style.left = position.x + '%';
        marker.style.top = adjustedY + '%';
    }
    
    // Agregar icono del clan
    const icon = document.createElement('i');
    icon.className = clan.clan_icon || 'fas fa-users';
    icon.style.fontSize = '14px';
    marker.appendChild(icon);
    
    // Agregar tooltip con información del clan
    marker.title = `${clan.clan_name}\n${clan.earned_points || 0} puntos (${clan.progress_percentage || 0}%)`;
    
    // Agregar evento click para mostrar detalles
    marker.addEventListener('click', () => {
        showClanDetails(clan);
    });
    
    // Agregar borde blanco para mejor visibilidad
    marker.style.border = '3px solid white';
    marker.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.3)';
    
    // Si el clan tiene 0 puntos, hacer el marcador más sutil
    if (clan.earned_points === 0) {
        marker.style.opacity = '0.7';
        marker.style.filter = 'grayscale(30%)';
    }
    
    console.log(`Marcador creado para ${clan.clan_name} en posición:`, position);
    
    return marker;
}

/**
 * Calcular la posición de un marcador en el camino
 */
function calculateMarkerPosition(points) {
    console.log(`Calculando posición para ${points} puntos`);
    
    // Mapear puntos a posiciones específicas en las cajas
    const pointPositions = {
        // Fila 1: Izquierda a derecha (INICIO -> 250)
        0: {x: 7.7, y: 12.5},    // INICIO
        50: {x: 15.4, y: 12.5},
        100: {x: 23.1, y: 12.5},
        150: {x: 30.8, y: 12.5},
        200: {x: 38.5, y: 12.5},
        250: {x: 46.2, y: 12.5},
        
        // Fila 2: Izquierda a derecha (275 -> 500)
        275: {x: 7.7, y: 37.5},
        300: {x: 15.4, y: 37.5},
        325: {x: 23.1, y: 37.5},
        350: {x: 30.8, y: 37.5},
        375: {x: 38.5, y: 37.5},
        400: {x: 46.2, y: 37.5},
        425: {x: 53.8, y: 37.5},
        450: {x: 61.5, y: 37.5},
        475: {x: 69.2, y: 37.5},
        500: {x: 76.9, y: 37.5},
        
        // Fila 3: Derecha a izquierda (525 -> 750)
        525: {x: 84.6, y: 62.5},
        550: {x: 76.9, y: 62.5},
        575: {x: 69.2, y: 62.5},
        600: {x: 61.5, y: 62.5},
        625: {x: 53.8, y: 62.5},
        650: {x: 46.2, y: 62.5},
        675: {x: 38.5, y: 62.5},
        700: {x: 30.8, y: 62.5},
        725: {x: 23.1, y: 62.5},
        750: {x: 15.4, y: 62.5},
        
        // Fila 4: Izquierda a derecha (775 -> 1000 FIN)
        775: {x: 7.7, y: 87.5},
        800: {x: 15.4, y: 87.5},
        825: {x: 23.1, y: 87.5},
        850: {x: 30.8, y: 87.5},
        875: {x: 38.5, y: 87.5},
        900: {x: 46.2, y: 87.5},
        925: {x: 53.8, y: 87.5},
        950: {x: 61.5, y: 87.5},
        975: {x: 69.2, y: 87.5},
        1000: {x: 76.9, y: 87.5},  // Caja 1000
        1001: {x: 84.6, y: 87.5}   // FIN
    };
    
    // Para puntos mayores o iguales a 1000, usar la posición FIN
    if (points >= 1000) {
        console.log(`${points} puntos >= 1000, usando posición FIN`);
        return pointPositions[1001]; // Posición FIN
    }
    
    // Para puntos exactos, usar la posición correspondiente
    if (pointPositions[points]) {
        console.log(`${points} puntos exactos, usando posición ${points}`);
        return pointPositions[points];
    }
    
    // Para puntos intermedios, encontrar la caja más cercana
    const availablePoints = Object.keys(pointPositions).map(Number).sort((a, b) => a - b);
    let closestPoint = availablePoints[0];
    let minDifference = Math.abs(points - closestPoint);
    
    for (const point of availablePoints) {
        const difference = Math.abs(points - point);
        if (difference < minDifference) {
            minDifference = difference;
            closestPoint = point;
        }
    }
    
    console.log(`${points} puntos intermedios, usando posición más cercana: ${closestPoint}`);
    return pointPositions[closestPoint];
}

/**
 * Generar la leyenda de clanes
 */
function generateClanLegend(data) {
    const legendContainer = document.getElementById('clanLegend');
    if (!legendContainer) return;
    
    // Limpiar leyenda existente
    legendContainer.innerHTML = '';
    
    data.clans_data.forEach(clan => {
        const legendItem = createLegendItem(clan);
        legendContainer.appendChild(legendItem);
    });
}

/**
 * Crear un elemento de la leyenda
 */
function createLegendItem(clan) {
    const item = document.createElement('div');
    item.className = 'legend-item';
    
    // Marcador de color
    const marker = document.createElement('div');
    marker.className = 'legend-marker';
    marker.style.backgroundColor = clan.clan_color;
    
    const icon = document.createElement('i');
    icon.className = clan.clan_icon;
    marker.appendChild(icon);
    
    // Información del clan
    const info = document.createElement('div');
    info.className = 'legend-info';
    
    const name = document.createElement('div');
    name.className = 'legend-clan-name';
    name.textContent = clan.clan_name;
    
    const dept = document.createElement('div');
    dept.className = 'legend-clan-dept';
    dept.textContent = clan.clan_departamento || 'Sin departamento';
    
    info.appendChild(name);
    info.appendChild(dept);
    
    // Puntos
    const points = document.createElement('div');
    points.className = 'legend-points';
    
    const pointsValue = document.createElement('div');
    pointsValue.className = 'legend-points-value';
    pointsValue.textContent = clan.earned_points.toLocaleString();
    
    const pointsLabel = document.createElement('div');
    pointsLabel.className = 'legend-points-label';
    pointsLabel.textContent = 'puntos';
    
    points.appendChild(pointsValue);
    points.appendChild(pointsLabel);
    
    // Ensamblar el elemento
    item.appendChild(marker);
    item.appendChild(info);
    item.appendChild(points);
    
    return item;
}

/**
 * Mostrar detalles de un clan
 */
function showClanDetails(clan) {
    // Crear modal con detalles del clan
    const modal = document.createElement('div');
    modal.className = 'clan-details-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>${clan.clan_name}</h3>
                <button class="modal-close" onclick="this.parentElement.parentElement.parentElement.remove()">×</button>
            </div>
            <div class="modal-body">
                <div class="clan-detail-item">
                    <strong>Departamento:</strong> ${clan.clan_departamento || 'Sin departamento'}
                </div>
                <div class="clan-detail-item">
                    <strong>Puntos Ganados:</strong> ${clan.earned_points.toLocaleString()}
                </div>
                <div class="clan-detail-item">
                    <strong>Puntos Asignados:</strong> ${clan.total_assigned.toLocaleString()}
                </div>
                <div class="clan-detail-item">
                    <strong>Progreso:</strong> ${clan.progress_percentage}%
                </div>
                <div id="clan-users-points" class="clan-users-points" style="margin-top:14px">
                    <div style="font-weight:700;margin-bottom:8px">Resumen por usuario</div>
                    <div class="loading">Cargando...</div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar estilos al modal
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    `;
    
    const modalContent = modal.querySelector('.modal-content');
    modalContent.style.cssText = `
        background: white;
        border-radius: 12px;
        padding: 2rem;
        max-width: 520px;
        width: 90%;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    `;
    
    const modalHeader = modal.querySelector('.modal-header');
    modalHeader.style.cssText = `
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    `;
    
    const modalClose = modal.querySelector('.modal-close');
    modalClose.style.cssText = `
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6b7280;
        padding: 0.25rem;
    `;
    
    const clanDetailItems = modal.querySelectorAll('.clan-detail-item');
    clanDetailItems.forEach(item => {
        item.style.cssText = `
            margin-bottom: 0.75rem;
            padding: 0.5rem;
            background: #f9fafb;
            border-radius: 6px;
        `;
    });
    
    document.body.appendChild(modal);
    // Cargar resumen por usuario
    fetch(`?route=kpi/get-projects-data`)
        .then(r => r.json())
        .then(data => {
            const cont = modal.querySelector('#clan-users-points');
            if (!cont) return;
            if (!data || !data.success || !Array.isArray(data.projects)) { cont.innerHTML = '<div class="empty">Sin datos</div>'; return; }
            const projects = data.projects.filter(p => Number(p.clan_id) === Number(clan.clan_id));
            const userMap = new Map();
            projects.forEach(p => {
                (p.tasks || []).forEach(t => {
                    const assigned = (t.all_assigned_user_ids || '').split(',').filter(Boolean).map(n => Number(n));
                    const names = (t.all_assigned_users || '').split(',').map(s => s.trim());
                    const completed = String(t.status) === 'completed';
                    if (!completed) return;
                    const points = Number(t.automatic_points || 0) || Number((t.assigned_percentage || 0) * (p.kpi_points || 0) / 100) || 0;
                    assigned.forEach((uid, idx) => {
                        const name = names[idx] || `Usuario ${uid}`;
                        const prev = userMap.get(uid) || { name, earned: 0 };
                        prev.earned += points / (assigned.length || 1);
                        userMap.set(uid, prev);
                    });
                });
            });
            if (userMap.size === 0) { cont.innerHTML = '<div class="empty">Sin puntos aún</div>'; return; }
            const items = Array.from(userMap.values()).sort((a,b)=>b.earned-a.earned).slice(0,20)
                .map(u => `<div class="user-point-item" style="display:flex;justify-content:space-between;padding:8px;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:6px">
                    <span style="font-weight:600">${u.name}</span>
                    <span>${u.earned.toFixed(1)} pts</span>
                </div>`).join('');
            cont.innerHTML = items;
        })
        .catch(()=>{
            const cont = modal.querySelector('#clan-users-points');
            if (cont) cont.innerHTML = '<div class="empty">Error al cargar</div>';
        });
    
    // Cerrar modal al hacer click fuera
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Inicializar cuando se carga el documento
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKPIDashboard);
} else {
    initKPIDashboard();
}