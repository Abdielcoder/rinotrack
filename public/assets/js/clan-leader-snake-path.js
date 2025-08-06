/**
 * Snake Path para Miembros del Clan
 * Adaptado del KPI Dashboard para mostrar el progreso de miembros
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el snake path si hay datos disponibles
    if (window.snakePathData && window.snakePathData.members_data) {
        initializeSnakePath();
    }
});

/**
 * Inicializar el snake path para miembros
 */
function initializeSnakePath() {
    console.log('Inicializando snake path para miembros...');
    console.log('window.snakePathData:', window.snakePathData);
    
    if (!window.snakePathData) {
        console.log('No hay datos disponibles para el snake path');
        return;
    }
    
    if (!window.snakePathData.members_data) {
        console.log('No hay datos de miembros en snakePathData');
        console.log('Estructura de snakePathData:', Object.keys(window.snakePathData));
        return;
    }
    
    console.log('Inicializando snake path con datos:', window.snakePathData);
    console.log('Miembros disponibles:', window.snakePathData.members_data);
    
    // Generar el camino
    generateSnakePath(window.snakePathData);
    
    // Generar marcadores de miembros
    generateMemberMarkers(window.snakePathData);
    
    // Generar leyenda
    generateMemberLegend(window.snakePathData);
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
    } else if (value === 'FIN') {
        cell.classList.add('goal');
        cell.textContent = 'FIN';
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
    if (value !== 'INICIO' && value !== 'FIN') {
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
 * Generar los marcadores de miembros
 */
function generateMemberMarkers(data) {
    const markersContainer = document.getElementById('memberMarkers');
    if (!markersContainer) { 
        console.error('No se encontró el contenedor de marcadores'); 
        return; 
    }
    markersContainer.innerHTML = '';
    console.log('Generando marcadores para miembros:', data.members_data);

    // Obtener las posiciones reales de las celdas
    const cellPositions = getCellPositions();
    console.log('Posiciones de celdas obtenidas:', cellPositions);

    // Agrupar miembros por posición
    const membersByPosition = {};
    data.members_data.forEach(member => {
        const position = calculateMarkerPositionFromCells(member.earned_points || 0, cellPositions);
        const positionKey = `${position.x}-${position.y}`;
        
        if (!membersByPosition[positionKey]) {
            membersByPosition[positionKey] = {
                position: position,
                members: []
            };
        }
        membersByPosition[positionKey].members.push(member);
    });

    // Crear marcadores agrupados
    Object.keys(membersByPosition).forEach(positionKey => {
        const group = membersByPosition[positionKey];
        
        // Log específico para Abdiel (500 puntos)
        const abdielInGroup = group.members.find(m => m.full_name && m.full_name.includes('Abdiel'));
        if (abdielInGroup) {
            console.log('=== ABDIEL ENCONTRADO ===');
            console.log('Puntos de Abdiel:', abdielInGroup.earned_points);
            console.log('Posición calculada:', group.position);
            console.log('Posición clave:', positionKey);
            console.log('=======================');
        }
        
        const marker = createGroupMarker(group, data);
        markersContainer.appendChild(marker);
    });

    console.log('Marcadores agrupados generados:', Object.keys(membersByPosition).length);
}

function createGroupMarker(group, data) {
    // Usar porcentajes como en el dashboard de admin
    const x = group.position.x;
    const y = group.position.y;

    // Posicionar exactamente encima del número dentro del cuadro usando porcentajes
    let markerX = x;
    let markerY = Math.max(2, y - 8); // 8% arriba de la caja, mínimo 2%

    // Ajustes especiales para posiciones en los bordes
    if (x <= 10) {
        markerX = 12;
    } else if (x >= 90) {
        markerX = 88;
    }

    const marker = document.createElement('div');
    marker.className = 'member-marker group-marker';
    marker.style.position = 'absolute';
    marker.style.left = markerX + '%';
    marker.style.top = markerY + '%';
    marker.style.width = '32px';
    marker.style.height = '32px';
    marker.style.zIndex = '10';
    marker.style.cursor = 'pointer';
    marker.style.transition = 'all 0.3s ease';

    // Crear el indicador de grupo
    const indicator = document.createElement('div');
    indicator.className = 'group-indicator';
    indicator.style.width = '100%';
    indicator.style.height = '100%';
    indicator.style.borderRadius = '50%';
    indicator.style.backgroundColor = '#3b82f6';
    indicator.style.border = '3px solid #1e40af';
    indicator.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.3)';
    indicator.style.display = 'flex';
    indicator.style.alignItems = 'center';
    indicator.style.justifyContent = 'center';
    indicator.style.color = 'white';
    indicator.style.fontWeight = 'bold';
    indicator.style.fontSize = '12px';
    indicator.style.fontFamily = 'Arial, sans-serif';

    // Mostrar el número de miembros
    const memberCount = group.members.length;
    indicator.textContent = memberCount;

    // Tooltip con información básica
    const memberNames = group.members.map(m => m.full_name).join(', ');
    const totalPoints = group.members.reduce((sum, m) => sum + (m.earned_points || 0), 0);
    marker.title = `${memberCount} miembro${memberCount > 1 ? 's' : ''}\n${memberNames}\nTotal: ${totalPoints} puntos`;

    marker.appendChild(indicator);

    // Agregar evento de clic para expandir
    marker.addEventListener('click', () => {
        showGroupMembers(group, data);
    });

    // Efectos hover
    marker.addEventListener('mouseenter', () => {
        marker.style.transform = 'scale(1.1)';
        marker.style.boxShadow = '0 6px 20px rgba(0, 0, 0, 0.4)';
    });

    marker.addEventListener('mouseleave', () => {
        marker.style.transform = 'scale(1)';
        marker.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.3)';
    });

    return marker;
}

function showGroupMembers(group, data) {
    // Crear modal para mostrar todos los miembros del grupo
    const modal = document.createElement('div');
    modal.className = 'modal group-modal';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.zIndex = '1000';

    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content group-modal-content';
    modalContent.style.backgroundColor = 'white';
    modalContent.style.borderRadius = '12px';
    modalContent.style.padding = '24px';
    modalContent.style.maxWidth = '600px';
    modalContent.style.width = '90%';
    modalContent.style.maxHeight = '80vh';
    modalContent.style.overflowY = 'auto';
    modalContent.style.boxShadow = '0 20px 60px rgba(0, 0, 0, 0.3)';
    modalContent.style.animation = 'modalSlideIn 0.3s ease-out';

    // Calcular posición en el snake path
    const positionText = getPositionText(group.position);
    const totalPoints = group.members.reduce((sum, m) => sum + (m.earned_points || 0), 0);

    modalContent.innerHTML = `
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e5e7eb;">
            <h3 style="margin: 0; color: #1f2937; font-size: 1.5rem; font-weight: bold;">Miembros en ${positionText}</h3>
            <button class="modal-close" onclick="this.closest('.modal').remove()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280; padding: 5px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom: 20px; padding: 15px; background: linear-gradient(135deg, #3b82f6, #1e40af); color: white; border-radius: 8px;">
                <div style="font-size: 1.1rem; font-weight: bold; margin-bottom: 5px;">Resumen de la Posición</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.9rem;">
                    <div><strong>Total de Miembros:</strong> ${group.members.length}</div>
                    <div><strong>Puntos Totales:</strong> ${totalPoints.toLocaleString()}</div>
                    <div><strong>Puntos Promedio:</strong> ${Math.round(totalPoints / group.members.length).toLocaleString()}</div>
                    <div><strong>Posición:</strong> ${positionText}</div>
                </div>
            </div>
            <div class="members-grid" style="display: grid; gap: 15px;">
                ${group.members.map(member => `
                    <div class="member-card" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px; background: white; transition: all 0.3s ease; cursor: pointer;" 
                         onmouseover="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.2)'" 
                         onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                         onclick="showMemberDetails(${JSON.stringify(member).replace(/"/g, '&quot;')})">
                        <div class="member-avatar" style="width: 50px; height: 50px; border-radius: 50%; background: white; border: 3px solid ${member.member_color || '#3b82f6'}; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0;">
                            <span style="color: ${member.member_color || '#3b82f6'}; font-size: 1.5rem; font-weight: bold; font-family: Arial, sans-serif;">${(member.full_name || '').charAt(0).toUpperCase()}</span>
                        </div>
                        <div class="member-info" style="flex: 1;">
                            <div style="font-weight: bold; color: #1f2937; margin-bottom: 5px;">${member.full_name}</div>
                            <div style="font-size: 0.9rem; color: #6b7280; margin-bottom: 3px;">${member.email || 'No disponible'}</div>
                            <div style="display: flex; gap: 15px; font-size: 0.85rem;">
                                <span style="color: #059669;"><strong>Puntos:</strong> ${member.earned_points.toLocaleString()}</span>
                                <span style="color: #7c3aed;"><strong>Progreso:</strong> ${member.progress_percentage}%</span>
                                <span style="color: #dc2626;"><strong>Proyectos:</strong> ${member.total_projects}</span>
                            </div>
                        </div>
                        <div style="color: #6b7280; font-size: 1.2rem;">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;

    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
}

function getPositionText(position) {
    // Determinar el texto de la posición basado en las coordenadas
    const x = position.x;
    const y = position.y;
    
    // Mapear coordenadas a números específicos del snake path
    if (x <= 10 && y <= 20) return 'INICIO (0 puntos)';
    if (x >= 85 && y >= 85) return 'FIN (1000+ puntos)';
    
    // Mapeo más preciso basado en las coordenadas del snake path
    const positionMap = {
        // Fila 1: Izquierda a derecha (0 -> 250)
        '8-15': '0 puntos',
        '16-15': '50 puntos', 
        '24-15': '100 puntos',
        '32-15': '150 puntos',
        '40-15': '200 puntos',
        '48-15': '250 puntos',
        
        // Fila 2: Izquierda a derecha (275 -> 500)
        '8-40': '275 puntos',
        '16-40': '300 puntos',
        '24-40': '325 puntos',
        '32-40': '350 puntos',
        '40-40': '375 puntos',
        '48-40': '400 puntos',
        '56-40': '425 puntos',
        '64-40': '450 puntos',
        '72-40': '475 puntos',
        '80-40': '500 puntos',
        
        // Fila 3: Derecha a izquierda (525 -> 750)
        '88-65': '525 puntos',
        '80-65': '550 puntos',
        '72-65': '575 puntos',
        '64-65': '600 puntos',
        '56-65': '625 puntos',
        '48-65': '650 puntos',
        '40-65': '675 puntos',
        '32-65': '700 puntos',
        '24-65': '725 puntos',
        '16-65': '750 puntos',
        
        // Fila 4: Izquierda a derecha (775 -> 1000)
        '8-90': '775 puntos',
        '16-90': '800 puntos',
        '24-90': '825 puntos',
        '32-90': '850 puntos',
        '40-90': '875 puntos',
        '48-90': '900 puntos',
        '56-90': '925 puntos',
        '64-90': '950 puntos',
        '72-90': '975 puntos',
        '80-90': '1000 puntos'
    };
    
    // Buscar la posición más cercana
    const positionKey = `${Math.round(x)}-${Math.round(y)}`;
    const exactMatch = positionMap[positionKey];
    
    if (exactMatch) {
        return exactMatch;
    }
    
    // Si no hay coincidencia exacta, buscar la más cercana
    let closestKey = null;
    let minDistance = Infinity;
    
    Object.keys(positionMap).forEach(key => {
        const [keyX, keyY] = key.split('-').map(Number);
        const distance = Math.sqrt(Math.pow(x - keyX, 2) + Math.pow(y - keyY, 2));
        if (distance < minDistance) {
            minDistance = distance;
            closestKey = key;
        }
    });
    
    if (closestKey && minDistance < 10) { // Solo si está relativamente cerca
        return positionMap[closestKey];
    }
    
    // Fallback: determinar posición general
    if (y <= 25) return 'Fila 1 (0-250 puntos)';
    else if (y <= 50) return 'Fila 2 (275-500 puntos)';
    else if (y <= 75) return 'Fila 3 (525-750 puntos)';
    else return 'Fila 4 (775-1000 puntos)';
}

/**
 * Generar la leyenda de miembros
 */
function generateMemberLegend(data) {
    const legendContainer = document.getElementById('memberLegend');
    if (!legendContainer) return;
    
    // Limpiar leyenda existente
    legendContainer.innerHTML = '';
    
    data.members_data.forEach(member => {
        const legendItem = createLegendItem(member);
        legendContainer.appendChild(legendItem);
    });
}

/**
 * Crear un elemento de la leyenda
 */
function createLegendItem(member) {
    const item = document.createElement('div');
    item.className = 'legend-item';
    
    // Marcador de color
    const marker = document.createElement('div');
    marker.className = 'legend-marker';
    marker.style.backgroundColor = 'white'; // Fondo blanco
    
    // Obtener la inicial del nombre
    const fullName = member.full_name || '';
    const initial = fullName.charAt(0).toUpperCase();
    
    // Crear elemento de texto para la inicial
    const initialText = document.createElement('span');
    initialText.textContent = initial;
    initialText.style.color = member.member_color || '#3b82f6'; // Color del texto usando el color del miembro
    initialText.style.fontSize = '1.2rem';
    initialText.style.fontWeight = 'bold';
    initialText.style.fontFamily = 'Arial, sans-serif';
    marker.appendChild(initialText);
    
    // Agregar borde del color del miembro
    marker.style.border = `2px solid ${member.member_color || '#3b82f6'}`;
    
    // Información del miembro
    const info = document.createElement('div');
    info.className = 'legend-info';
    
    const name = document.createElement('div');
    name.className = 'legend-member-name';
    name.textContent = member.full_name;
    
    const email = document.createElement('div');
    email.className = 'legend-member-email';
    email.textContent = member.email || 'Sin email';
    
    info.appendChild(name);
    info.appendChild(email);
    
    // Puntos
    const points = document.createElement('div');
    points.className = 'legend-points';
    
    const pointsValue = document.createElement('div');
    pointsValue.className = 'legend-points-value';
    pointsValue.textContent = member.earned_points.toLocaleString();
    
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
 * Mostrar detalles del miembro
 */
function showMemberDetails(member) {
    // Obtener la inicial del nombre
    const fullName = member.full_name || '';
    const initial = fullName.charAt(0).toUpperCase();
    
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalles del Miembro</h3>
                <button class="modal-close" onclick="this.closest('.modal').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="member-details">
                    <div class="member-avatar" style="background: white; border: 3px solid ${member.member_color || '#3b82f6'}; display: flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%;">
                        <span style="color: ${member.member_color || '#3b82f6'}; font-size: 2.5rem; font-weight: bold; font-family: Arial, sans-serif;">${initial}</span>
                    </div>
                    <div class="member-info">
                        <h4>${member.full_name}</h4>
                        <p><strong>Email:</strong> ${member.email || 'No disponible'}</p>
                        <p><strong>Puntos Ganados:</strong> ${member.earned_points.toLocaleString()}</p>
                        <p><strong>Puntos Asignados:</strong> ${member.total_assigned.toLocaleString()}</p>
                        <p><strong>Progreso:</strong> ${member.progress_percentage}%</p>
                        <p><strong>Proyectos Participando:</strong> ${member.total_projects}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.remove();
        }
    });
} 

/**
 * Leer dinámicamente las coordenadas de los cuadros del snake path
 */
function getCellPositions() {
    const pathGrid = document.getElementById('snakePathGrid');
    if (!pathGrid) { 
        console.error('No se encontró el grid del snake path'); 
        return {}; 
    }
    const cells = pathGrid.querySelectorAll('.path-cell');
    const cellPositions = {};
    
    console.log('=== LECTURA DE COORDENADAS DE CELDAS ===');
    cells.forEach(cell => {
        const text = cell.textContent.trim();
        const rect = cell.getBoundingClientRect();
        const gridRect = pathGrid.getBoundingClientRect();
        const relativeX = ((rect.left + rect.width / 2) - gridRect.left) / gridRect.width * 100;
        const relativeY = ((rect.top + rect.height / 2) - gridRect.top) / gridRect.height * 100;
        
        let value;
        if (text === 'INICIO') { 
            value = 0; 
        } else if (text === 'FIN') { 
            value = 1001; 
        } else { 
            value = parseInt(text); 
        }
        
        if (!isNaN(value)) {
            cellPositions[value] = {
                x: relativeX,
                y: relativeY,
                width: (rect.width / gridRect.width) * 100,
                height: (rect.height / gridRect.height) * 100
            };
            console.log(`Celda "${text}" (${value}): x=${relativeX.toFixed(1)}%, y=${relativeY.toFixed(1)}%`);
        }
    });
    console.log('=== FIN DE LECTURA ===');
    return cellPositions;
}

/**
 * Calcular la posición de un marcador basada en las celdas reales
 */
function calculateMarkerPositionFromCells(points, cellPositions) {
    console.log(`Calculando posición para ${points} puntos usando celdas reales`);
    
    // Para puntos >= 1000, usar posición FIN
    if (points >= 1000) {
        console.log(`${points} puntos >= 1000, usando posición FIN`);
        return cellPositions[1001] || {x: 88, y: 90};
    }
    
    // Buscar coincidencia exacta
    if (cellPositions[points]) {
        console.log(`${points} puntos exactos, usando posición ${points}`);
        return cellPositions[points];
    }
    
    // Buscar la posición más cercana
    const availablePoints = Object.keys(cellPositions).map(Number).sort((a, b) => a - b);
    let closestPoint = availablePoints[0];
    let minDifference = Math.abs(points - closestPoint);
    
    for (const point of availablePoints) {
        const difference = Math.abs(points - point);
        if (difference < minDifference) {
            minDifference = difference;
            closestPoint = point;
        }
    }
    
    console.log(`${points} puntos intermedios, usando posición más cercana: ${closestPoint} (diferencia: ${minDifference})`);
    return cellPositions[closestPoint] || {x: 8, y: 15};
} 