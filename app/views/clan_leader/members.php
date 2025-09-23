<?php
// Guardar el contenido en una variable
ob_start();
?>

<div class="clan-leader-members minimal">
    <!-- Header Minimalista -->
    <header class="minimal-header">
        <div class="header-row">
            <div class="title-minimal">
                <h1>Gestionar Miembros</h1>
                <span class="subtitle"><?php echo htmlspecialchars($clan['clan_name'] ?? ''); ?></span>
            </div>
            
            <div class="actions-minimal">
                <button class="btn-minimal primary" onclick="openAddMemberModal()">
                    <i class="fas fa-plus"></i>
                    Agregar Miembro
                </button>
            </div>
        </div>
        
        <!-- Búsqueda en tiempo real -->
        <div class="search-minimal">
            <div class="search-container">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" id="memberSearch" 
                           value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                           placeholder="Buscar miembros...">
                </div>
                <button type="button" id="clearSearch" class="btn-minimal secondary clear-btn" 
                        style="<?php echo empty($search) ? 'display: none;' : ''; ?>">
                    <i class="fas fa-times"></i>
                    Limpiar
                </button>
            </div>
        </div>
    </header>

    <!-- Lista de Miembros -->
    <div class="content-minimal">
        <section class="members-minimal">
            <?php if (!empty($members)): ?>
                <div class="members-list">
                    <?php foreach ($members as $member): ?>
                        <div class="member-item">
                            <div class="member-info">
                                <div class="member-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="member-details">
                                    <div class="member-name"><?php echo htmlspecialchars($member['full_name']); ?></div>
                                    <div class="member-username">@<?php echo htmlspecialchars($member['username']); ?></div>
                                    <div class="member-email"><?php echo htmlspecialchars($member['email']); ?></div>
                                    <div class="member-role">
                                        <span class="role-badge"><?php echo htmlspecialchars($member['role_name'] ?? 'Sin rol'); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="member-status">
                                <?php if ($member['is_active']): ?>
                                    <span class="status-active">Activo</span>
                                <?php else: ?>
                                    <span class="status-inactive">Inactivo</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="member-actions">
                                <?php if ($member['user_id'] != $user['user_id']): ?>
                                    <button class="btn-minimal danger" 
                                            onclick="removeMember(<?php echo $member['user_id']; ?>, '<?php echo htmlspecialchars($member['full_name']); ?>')">
                                        <i class="fas fa-user-minus"></i>
                                        Remover
                                    </button>
                                <?php else: ?>
                                    <span class="leader-badge">Líder del Clan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-minimal">
                    <span>👥 No hay miembros en el clan</span>
                    <button class="btn-minimal primary" onclick="openAddMemberModal()">Agregar primer miembro</button>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<!-- Modal para agregar miembro -->
<div id="addMemberModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar Miembro al Clan</h3>
            <button class="modal-close" onclick="closeAddMemberModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <form id="addMemberForm" class="modal-form">
                <div class="form-group">
                    <label for="userId">
                        <i class="fas fa-user"></i>
                        Seleccionar Usuario
                    </label>
                    <select id="userId" name="userId" required>
                        <option value="">Seleccionar usuario...</option>
                        <!-- Se llenará dinámicamente -->
                    </select>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="action-btn secondary" onclick="closeAddMemberModal()">
                <i class="fas fa-times"></i>
                <span>Cancelar</span>
            </button>
            <button type="submit" form="addMemberForm" class="action-btn primary">
                <i class="fas fa-plus"></i>
                <span>Agregar Miembro</span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('memberSearch');
    const clearButton = document.getElementById('clearSearch');
    const memberItems = document.querySelectorAll('.member-item');
    const membersList = document.querySelector('.members-list');
    const emptyState = document.querySelector('.empty-minimal');
    
    // Función para filtrar miembros
    function filterMembers(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        
        memberItems.forEach(function(item) {
            const name = item.querySelector('.member-name').textContent.toLowerCase();
            const username = item.querySelector('.member-username').textContent.toLowerCase();
            const email = item.querySelector('.member-email').textContent.toLowerCase();
            
            if (term === '' || 
                name.includes(term) || 
                username.includes(term) || 
                email.includes(term)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Mostrar/ocultar estado vacío si no hay resultados
        if (visibleCount === 0 && term !== '') {
            if (emptyState) {
                emptyState.style.display = 'block';
                emptyState.innerHTML = `
                    <span>🔍 No se encontraron miembros que coincidan con "${searchTerm}"</span>
                    <button class="btn-minimal primary" onclick="clearSearch()">Limpiar búsqueda</button>
                `;
            }
            if (membersList) membersList.style.display = 'none';
        } else {
            if (emptyState) emptyState.style.display = 'none';
            if (membersList) membersList.style.display = 'block';
        }
        
        // Mostrar/ocultar botón limpiar
        if (clearButton) {
            clearButton.style.display = term !== '' ? 'block' : 'none';
        }
    }
    
    // Función para limpiar búsqueda
    function clearSearch() {
        searchInput.value = '';
        filterMembers('');
        searchInput.focus();
    }
    
    // Event listener para búsqueda en tiempo real
    searchInput.addEventListener('input', function() {
        filterMembers(this.value);
    });
    
    // Event listener para botón limpiar
    if (clearButton) {
        clearButton.addEventListener('click', clearSearch);
    }
    
    // Hacer la función clearSearch global para el botón del estado vacío
    window.clearSearch = clearSearch;
    
    // Aplicar filtro inicial si hay búsqueda previa
    if (searchInput.value) {
        filterMembers(searchInput.value);
    }
});
</script>

<style>
/* Estilos para la búsqueda en tiempo real */
.search-container {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
}

.search-input {
    flex: 1;
    position: relative;
}

.clear-btn {
    white-space: nowrap;
    flex-shrink: 0;
}

/* Asegurar que el input ocupe el espacio disponible */
.search-input input {
    width: 100%;
    box-sizing: border-box;
}

/* Responsive: en pantallas pequeñas, apilar verticalmente */
@media (max-width: 768px) {
    .search-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .clear-btn {
        align-self: flex-start;
    }
}
</style>

<?php
// Guardar el contenido en una variable
$content = ob_get_clean();

// CSS adicional para Clan Leader
$additionalCSS = [
    APP_URL . 'assets/css/clan-leader.css'
];

// JavaScript adicional para Clan Leader  
$additionalJS = [
    APP_URL . 'assets/js/clan-leader.js'
];

// Incluir el layout
require_once __DIR__ . '/../admin/layout.php';
?> 