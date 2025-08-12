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
                <a href="?route=clan_leader/dashboard" class="btn-minimal">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
                <button class="btn-minimal primary" onclick="openAddMemberModal()">
                    <i class="fas fa-plus"></i>
                    Agregar Miembro
                </button>
            </div>
        </div>
        
        <!-- Búsqueda -->
        <div class="search-minimal">
            <form method="GET" action="?route=clan_leader/members">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                           placeholder="Buscar miembros...">
                </div>
                <button type="submit" class="btn-minimal">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="?route=clan_leader/members" class="btn-minimal secondary">Limpiar</a>
                <?php endif; ?>
            </form>
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