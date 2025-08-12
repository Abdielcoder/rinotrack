<?php
// Verificar que tenemos los datos necesarios
if (!isset($task) || !isset($subtasks) || !isset($comments) || !isset($history) || !isset($assignedUsers) || !isset($labels)) {
    echo "<div class='error'>Error: Datos de tarea no disponibles</div>";
    return;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Tarea - <?= htmlspecialchars($task['task_name']) ?></title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="<?= APP_URL ?>assets/css/clan-leader.css">
    <link rel="stylesheet" href="<?= APP_URL ?>assets/css/styles.css">
    <style>
        .task-details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
        }
        
        .task-title-section {
            flex: 1;
        }
        
        .task-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .task-meta {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .task-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 14px;
        }
        
        .task-meta-item i {
            font-size: 16px;
        }
        
        .task-status-section {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-in_progress { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-overdue { background: #fee2e2; color: #dc2626; }
        
        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .priority-low { background: #f3f4f6; color: #374151; }
        .priority-medium { background: #fef3c7; color: #92400e; }
        .priority-high { background: #fed7d7; color: #c53030; }
        .priority-urgent { background: #fed7d7; color: #dc2626; }
        
        .task-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .section {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title i {
            color: #6b7280;
        }
        
        .description {
            color: #4b5563;
            line-height: 1.6;
            white-space: pre-wrap;
        }
        
        .subtasks-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .subtask-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .subtask-info {
            flex: 1;
        }
        
        .subtask-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .subtask-meta {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #6b7280;
        }
        
        .subtask-progress {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .progress-bar {
            width: 100px;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #10b981;
            transition: width 0.3s ease;
        }
        
        .assigned-users {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .user-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #fff;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #3b82f6;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }
        
        .user-role {
            font-size: 12px;
            color: #6b7280;
        }
        
        .user-percentage {
            font-weight: 600;
            color: #3b82f6;
            font-size: 14px;
        }
        
        .labels-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .label {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .comments-section {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .comment-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .comment-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .comment-author-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #3b82f6;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
        }
        
        .comment-author-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }
        
        .comment-time {
            font-size: 12px;
            color: #6b7280;
        }
        
        .comment-text {
            color: #4b5563;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        
        .comment-type {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 8px;
        }
        
        .comment-type-comment { background: #dbeafe; color: #1e40af; }
        .comment-type-status_change { background: #fef3c7; color: #92400e; }
        .comment-type-assignment { background: #d1fae5; color: #065f46; }
        
        .history-section {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .history-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border-left: 3px solid #e5e7eb;
            margin-bottom: 10px;
        }
        
        .history-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #6b7280;
        }
        
        .history-content {
            flex: 1;
        }
        
        .history-action {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }
        
        .history-details {
            font-size: 12px;
            color: #6b7280;
        }
        
        .history-time {
            font-size: 11px;
            color: #9ca3af;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: #fff;
        }
        
        .btn-primary:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        
        .btn-danger {
            background: #ef4444;
            color: #fff;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .overdue {
            border-left-color: #ef4444;
        }
        
        .overdue .task-title {
            color: #dc2626;
        }
        
        /* Estilos para subtareas interactivas */
        .subtask-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .subtask-actions {
            display: flex;
            gap: 5px;
        }
        
        .btn-icon-small {
            width: 24px;
            height: 24px;
            border: none;
            border-radius: 4px;
            background: #f3f4f6;
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.2s ease;
        }
        
        .btn-icon-small:hover {
            background: #e5e7eb;
            color: #374151;
        }
        
        .btn-icon-small.btn-danger:hover {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .subtask-controls {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .subtask-status-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .status-select {
            padding: 4px 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 12px;
            background: #fff;
        }
        
        .completion-slider {
            flex: 1;
            height: 6px;
            border-radius: 3px;
            background: #e5e7eb;
            outline: none;
            -webkit-appearance: none;
        }
        
        .completion-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
        }
        
        .completion-slider::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            border: none;
        }
        
        /* Estilos para comentarios */
        .add-comment-form {
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        
        .comment-input-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .comment-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            min-height: 80px;
        }
        
        .comment-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .comment-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-attachment {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .btn-attachment:hover {
            background: #e5e7eb;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 14px;
        }
        
        .attachment-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        
        .attachment-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-remove-attachment {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            padding: 2px;
        }
        
        .comments-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .comment-attachments {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .attachment-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            transition: all 0.2s ease;
        }
        
        .attachment-link:hover {
            background: #e5e7eb;
        }
        
        .no-comments {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }
        
        /* Estilos para colaboradores */
        .add-collaborator-section {
            margin-bottom: 15px;
        }
        
        .user-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-percentage {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .percentage-input {
            width: 50px;
            padding: 4px 6px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 12px;
            text-align: center;
        }
        
        .no-collaborators {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }
        
        /* Modal para agregar colaborador */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .close {
            color: #6b7280;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #374151;
        }
        
        .user-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        
        .user-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .user-option:hover {
            background-color: #f9fafb;
        }
        
        .user-option.selected {
            background-color: #dbeafe;
        }
        
        .user-option input[type="checkbox"] {
            margin: 0;
        }
    </style>
</head>
<body>
            <div class="task-details-container <?= ($task['due_date'] && strtotime($task['due_date']) < time() && $task['status'] !== 'completed') ? 'overdue' : '' ?>" data-task-id="<?= $task['task_id'] ?>">
        <!-- Header de la tarea -->
        <div class="task-header">
            <div class="task-title-section">
                <h1 class="task-title"><?= htmlspecialchars($task['task_name']) ?></h1>
                <div class="task-meta">
                    <?php if ($task['due_date']): ?>
                    <div class="task-meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Fecha límite: <?= date('d/m/Y', strtotime($task['due_date'])) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="task-meta-item">
                        <i class="fas fa-user"></i>
                        <span>Creado por: <?= htmlspecialchars($task['created_by_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="task-meta-item">
                        <i class="fas fa-project-diagram"></i>
                        <span>Proyecto: <?= htmlspecialchars($task['project_name'] ?? 'N/A') ?></span>
                    </div>
                    <?php if ($task['estimated_hours']): ?>
                    <div class="task-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>Estimado: <?= $task['estimated_hours'] ?>h</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="task-status-section">
                <div class="status-badge status-<?= $task['status'] ?>">
                    <?= ucfirst($task['status']) ?>
                </div>
                <div class="priority-badge priority-<?= $task['priority'] ?>">
                    <?= ucfirst($task['priority']) ?>
                </div>
                <?php if ($task['due_date'] && strtotime($task['due_date']) < time() && $task['status'] !== 'completed'): ?>
                <div class="status-badge status-overdue">
                    Vencida
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Contenido principal -->
        <div class="task-content">
            <div class="main-content">
                <!-- Descripción -->
                <?php if (!empty($task['description'])): ?>
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </h3>
                    <div class="description"><?= nl2br(htmlspecialchars($task['description'])) ?></div>
                </div>
                <?php endif; ?>
                
                <!-- Subtareas -->
                <?php if (!empty($subtasks)): ?>
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-tasks"></i>
                        Subtareas (<?= count($subtasks) ?>)
                    </h3>
                    <div class="subtasks-list">
                        <?php foreach ($subtasks as $subtask): ?>
                        <div class="subtask-item" data-subtask-id="<?= $subtask['subtask_id'] ?>">
                            <div class="subtask-info">
                                <div class="subtask-header">
                                    <div class="subtask-title"><?= htmlspecialchars($subtask['title']) ?></div>
                                    <div class="subtask-actions">
                                        <button class="btn-icon-small" onclick="editSubtask(<?= $subtask['subtask_id'] ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon-small" onclick="deleteSubtask(<?= $subtask['subtask_id'] ?>)" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="subtask-meta">
                                    <span>Estado: <?= ucfirst($subtask['status']) ?></span>
                                    <?php if (!empty($subtask['assigned_user_name'])): ?>
                                    <span>Asignado: <?= htmlspecialchars($subtask['assigned_user_name']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($subtask['due_date']): ?>
                                    <span>Vence: <?= date('d/m/Y', strtotime($subtask['due_date'])) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($subtask['description'])): ?>
                                <div class="subtask-description" style="margin-top: 8px; font-size: 13px; color: #6b7280;">
                                    <?= htmlspecialchars($subtask['description']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="subtask-controls">
                                <div class="subtask-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $subtask['completion_percentage'] ?>%"></div>
                                    </div>
                                    <span style="font-size: 12px; font-weight: 600; color: #374151;">
                                        <?= $subtask['completion_percentage'] ?>%
                                    </span>
                                </div>
                                <div class="subtask-status-controls">
                                    <select class="status-select" onchange="updateSubtaskStatus(<?= $subtask['subtask_id'] ?>, this.value)">
                                        <option value="pending" <?= $subtask['status'] === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                                        <option value="in_progress" <?= $subtask['status'] === 'in_progress' ? 'selected' : '' ?>>En Progreso</option>
                                        <option value="completed" <?= $subtask['status'] === 'completed' ? 'selected' : '' ?>>Completada</option>
                                    </select>
                                    <input type="range" min="0" max="100" value="<?= $subtask['completion_percentage'] ?>" 
                                           class="completion-slider" 
                                           onchange="updateSubtaskCompletion(<?= $subtask['subtask_id'] ?>, this.value)">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Comentarios -->
                <div class="section comments-section">
                    <h3 class="section-title">
                        <i class="fas fa-comments"></i>
                        Comentarios (<?= count($comments) ?>)
                    </h3>
                    
                    <!-- Formulario para agregar comentario -->
                    <div class="add-comment-form">
                        <div class="comment-input-group">
                            <textarea id="newComment" placeholder="Escribe un comentario..." rows="3" class="comment-textarea"></textarea>
                            <div class="comment-actions">
                                <label for="fileAttachment" class="btn-attachment">
                                    <i class="fas fa-paperclip"></i>
                                    Adjuntar archivo
                                </label>
                                <input type="file" id="fileAttachment" style="display: none;" onchange="handleFileAttachment(this)">
                                <button onclick="addComment()" class="btn btn-primary btn-sm">
                                    <i class="fas fa-paper-plane"></i>
                                    Enviar
                                </button>
                            </div>
                        </div>
                        <div id="attachmentPreview" class="attachment-preview" style="display: none;">
                            <div class="attachment-item">
                                <i class="fas fa-file"></i>
                                <span id="attachmentName"></span>
                                <button onclick="removeAttachment()" class="btn-remove-attachment">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de comentarios -->
                    <div class="comments-list">
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $comment): ?>
                            <div class="comment-item">
                            <div class="comment-header">
                                <div class="comment-author">
                                        <div class="comment-author-avatar">
                                            <?= strtoupper(substr($comment['full_name'] ?? $comment['username'], 0, 1)) ?>
                                        </div>
                                        <span class="comment-author-name"><?= htmlspecialchars($comment['full_name'] ?? $comment['username']) ?></span>
                                        <span class="comment-type comment-type-<?= $comment['comment_type'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $comment['comment_type'])) ?>
                                        </span>
                                    <?php 
                                        $attCount = (int)($comment['attachments_count'] ?? (is_array($comment['attachments'] ?? null) ? count($comment['attachments']) : 0));
                                    ?>
                                    <?php if ($attCount > 0): ?>
                                    <button class="btn-attachment" title="Ver adjuntos" onclick="toggleCommentAttachments(this)">
                                        <i class="fas fa-paperclip"></i>
                                        <?= $attCount ?> adjunto<?= $attCount>1?'s':'' ?>
                                    </button>
                                    <?php endif; ?>
                                    </div>
                                    <span class="comment-time"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></span>
                                </div>
                                <div class="comment-text"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></div>
                            <?php if ($attCount > 0): ?>
                            <div class="comment-attachments" style="display: none;">
                                <?php foreach ($comment['attachments'] as $attachment): ?>
                                <a href="<?= htmlspecialchars($attachment['file_path']) ?>" class="attachment-link" target="_blank" onclick="event.preventDefault(); openPreview('<?= htmlspecialchars($attachment['file_path']) ?>','<?= htmlspecialchars($attachment['file_name']) ?>')">
                                    <i class="fas fa-file"></i>
                                    <?= htmlspecialchars($attachment['file_name']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-comments">
                                <p>No hay comentarios aún. ¡Sé el primero en comentar!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="sidebar">
                <!-- Usuarios Asignados -->
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-users"></i>
                        Colaboradores (<?= count($assignedUsers) ?>)
                    </h3>
                    
                    <!-- Botón para agregar colaborador -->
                    <div class="add-collaborator-section">
                        <button onclick="showAddCollaboratorModal()" class="btn btn-secondary btn-sm">
                            <i class="fas fa-plus"></i>
                            Agregar Colaborador
                        </button>
                    </div>
                    
                    <!-- Lista de colaboradores -->
                    <div class="assigned-users">
                        <?php if (!empty($assignedUsers)): ?>
                            <?php foreach ($assignedUsers as $user): ?>
                            <div class="user-item" data-user-id="<?= $user['user_id'] ?>">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)) ?>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></div>
                                    <div class="user-role"><?= htmlspecialchars($user['username']) ?></div>
                                </div>
                                <div class="user-controls">
                                    <div class="user-percentage">
                                        <input type="number" min="0" max="100" value="<?= $user['assigned_percentage'] ?>" 
                                               class="percentage-input" 
                                               onchange="updateUserPercentage(<?= $user['user_id'] ?>, this.value)">
                                        <span>%</span>
                                    </div>
                                    <button onclick="removeCollaborator(<?= $user['user_id'] ?>)" class="btn-icon-small btn-danger" title="Remover">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-collaborators">
                                <p>No hay colaboradores asignados</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Etiquetas -->
                <?php if (!empty($labels)): ?>
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-tags"></i>
                        Etiquetas
                    </h3>
                    <div class="labels-container">
                        <?php foreach ($labels as $label): ?>
                        <span class="label" style="background: <?= $label['label_color'] ?>; color: #fff;">
                            <?= htmlspecialchars($label['label_name']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Historial -->
                <?php if (!empty($history)): ?>
                <div class="section history-section">
                    <h3 class="section-title">
                        <i class="fas fa-history"></i>
                        Historial
                    </h3>
                    <?php foreach ($history as $item): ?>
                    <div class="history-item">
                        <div class="history-icon">
                            <i class="fas fa-<?= $item['action_type'] === 'created' ? 'plus' : ($item['action_type'] === 'updated' ? 'edit' : 'info') ?>"></i>
                        </div>
                        <div class="history-content">
                            <div class="history-action"><?= htmlspecialchars($item['notes']) ?></div>
                            <div class="history-details">
                                Por: <?= htmlspecialchars($item['full_name'] ?? $item['username']) ?>
                            </div>
                        </div>
                        <div class="history-time">
                            <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Información adicional -->
                <div class="section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Información
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 14px;">
                        <div><strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($task['created_at'])) ?></div>
                        <?php if ($task['updated_at'] !== $task['created_at']): ?>
                        <div><strong>Actualizado:</strong> <?= date('d/m/Y H:i', strtotime($task['updated_at'])) ?></div>
                        <?php endif; ?>
                        <div><strong>Progreso:</strong> <?= $task['completion_percentage'] ?>%</div>
                        <?php if ($task['actual_hours']): ?>
                        <div><strong>Horas reales:</strong> <?= $task['actual_hours'] ?>h</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div class="action-buttons">
            <a href="?route=clan_leader/tasks" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver a Tareas
            </a>
            <a href="?route=clan_leader/tasks&action=edit&task_id=<?= $task['task_id'] ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Editar Tarea
            </a>
            <button onclick="deleteTask(<?= $task['task_id'] ?>)" class="btn btn-danger">
                <i class="fas fa-trash"></i>
                Eliminar
            </button>
        </div>
    </div>
    
    <!-- Modal para agregar colaborador -->
    <div id="addCollaboratorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Agregar Colaborador</h3>
                <span class="close" onclick="closeAddCollaboratorModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="user-list" id="availableUsersList">
                    <!-- Los usuarios se cargarán dinámicamente -->
                </div>
                <div class="modal-actions" style="margin-top: 20px; text-align: right;">
                    <button onclick="closeAddCollaboratorModal()" class="btn btn-secondary">Cancelar</button>
                    <button onclick="addSelectedCollaborators()" class="btn btn-primary">Agregar Seleccionados</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?= APP_URL ?>assets/js/clan-leader.js?v=<?= time() ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Fallback: si por alguna razón no se cargó deleteTask desde clan-leader.js
        if (typeof deleteTask !== 'function') {
            function deleteTask(taskId) {
                if (typeof showConfirmationModal !== 'function') {
                    // Fallback mínimo de confirmación si no existe el modal
                    if (!confirm('¿Estás seguro de que quieres eliminar esta tarea?')) return;
                    fetch('?route=clan_leader/delete-task', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'task_id=' + taskId
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            if (typeof showNotification === 'function') {
                                showNotification('Tarea eliminada exitosamente', 'success');
                            }
                            setTimeout(() => { window.location.href = '?route=clan_leader/tasks'; }, 800);
                        } else {
                            alert('Error al eliminar la tarea: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(() => alert('Error al eliminar la tarea'));
                    return;
                }

                // Flujo normal con modal bonito
                showConfirmationModal({
                    title: 'Confirmar Eliminación',
                    message: '¿Estás seguro de que quieres eliminar esta tarea?',
                    type: 'warning',
                    confirmText: 'Eliminar',
                    cancelText: 'Cancelar',
                    onConfirm: () => {
                        fetch('?route=clan_leader/delete-task', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'task_id=' + taskId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (typeof showNotification === 'function') {
                                    showNotification('Tarea eliminada exitosamente', 'success');
                                }
                                setTimeout(() => { window.location.href = '?route=clan_leader/tasks'; }, 800);
                            } else {
                                if (typeof showNotification === 'function') {
                                    showNotification('Error al eliminar la tarea: ' + data.message, 'error');
                                } else {
                                    alert('Error al eliminar la tarea: ' + (data.message || 'Error desconocido'));
                                }
                            }
                        })
                        .catch(error => {
                            if (typeof showNotification === 'function') {
                                showNotification('Error al eliminar la tarea', 'error');
                            } else {
                                alert('Error al eliminar la tarea');
                            }
                        });
                    }
                });
            }
        }

        // La función deleteTask ahora está definida (global o fallback)
        
        // Modal de previsualización
        function toggleCommentAttachments(button){
            const container = button.closest('.comment-item').querySelector('.comment-attachments');
            if (container) container.style.display = container.style.display==='none'?'block':'none';
        }
        function openPreview(url, name){
            let modal = document.getElementById('previewModal');
            if (!modal){
                const html = `
                <div id="previewModal" class="modal" style="display:none;">
                  <div class="modal-content" style="max-width:800px;">
                    <div class="modal-header">
                      <h3 class="modal-title" id="previewTitle">Vista previa</h3>
                      <span class="close" onclick="closePreview()">&times;</span>
                    </div>
                    <div id="previewBody" style="max-height:70vh;overflow:auto;"></div>
                    <div class="modal-actions" style="margin-top:12px;text-align:right;">
                      <a id="previewDownload" class="btn btn-primary" href="#" download>Descargar</a>
                    </div>
                  </div>
                </div>`;
                document.body.insertAdjacentHTML('beforeend', html);
                modal = document.getElementById('previewModal');
            }
            const body = document.getElementById('previewBody');
            const a = document.getElementById('previewDownload');
            body.innerHTML = '';
            a.href = url;
            a.setAttribute('download', name || 'archivo');
            const lower = (name||'').toLowerCase();
            if (lower.endsWith('.png')||lower.endsWith('.jpg')||lower.endsWith('.jpeg')||lower.endsWith('.gif')||lower.endsWith('.webp')){
                const img = document.createElement('img'); img.src = url; img.style.maxWidth='100%'; body.appendChild(img);
            } else if (lower.endsWith('.pdf')){
                const iframe = document.createElement('iframe'); iframe.src = url; iframe.style.width='100%'; iframe.style.height='70vh'; body.appendChild(iframe);
            } else {
                const p = document.createElement('p'); p.textContent = 'No hay vista previa para este formato. Usa Descargar.'; body.appendChild(p);
            }
            modal.style.display='block';
        }
        function closePreview(){ const modal = document.getElementById('previewModal'); if (modal) modal.style.display='none'; }
        // Variables globales ahora están en clan-leader.js
        
        // Funciones para subtareas
        function updateSubtaskStatus(subtaskId, status) {
            fetch('?route=clan_leader/update-subtask-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `subtask_id=${subtaskId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la UI
                    const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
                    if (subtaskItem) {
                        const statusSpan = subtaskItem.querySelector('.subtask-meta span:first-child');
                        if (statusSpan) {
                            statusSpan.textContent = `Estado: ${status.charAt(0).toUpperCase() + status.slice(1)}`;
                        }
                    }
                    showNotification('Estado de subtarea actualizado', 'success');
                } else {
                    showNotification('Error al actualizar estado: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al actualizar estado', 'error');
            });
        }
        
        function updateSubtaskCompletion(subtaskId, completion) {
            fetch('?route=clan_leader/update-subtask-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `subtask_id=${subtaskId}&completion_percentage=${completion}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar la UI
                    const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
                    if (subtaskItem) {
                        const progressFill = subtaskItem.querySelector('.progress-fill');
                        const percentageSpan = subtaskItem.querySelector('.subtask-progress span');
                        if (progressFill) progressFill.style.width = completion + '%';
                        if (percentageSpan) percentageSpan.textContent = completion + '%';
                    }
                    showNotification('Progreso de subtarea actualizado', 'success');
                } else {
                    showNotification('Error al actualizar progreso: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al actualizar progreso', 'error');
            });
        }
        
        function editSubtask(subtaskId) {
            // Implementar edición de subtarea
            showConfirmationModal({
                title: 'Función en Desarrollo',
                message: 'Función de edición de subtarea en desarrollo',
                type: 'info',
                confirmText: 'Entendido',
                onConfirm: () => {}
            });
        }
        
        function deleteSubtask(subtaskId) {
            showConfirmationModal({
                title: 'Confirmar Eliminación',
                message: '¿Estás seguro de que quieres eliminar esta subtarea?',
                type: 'warning',
                confirmText: 'Eliminar',
                cancelText: 'Cancelar',
                onConfirm: () => {
                    fetch('?route=clan_leader/delete-subtask', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `subtask_id=${subtaskId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
                            if (subtaskItem) subtaskItem.remove();
                            showNotification('Subtarea eliminada', 'success');
                        } else {
                            showNotification('Error al eliminar subtarea: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error al eliminar subtarea', 'error');
                    });
                }
            });
        }
        

        
        // Funciones para colaboradores
        function showAddCollaboratorModal() {
            // Cargar usuarios disponibles
            fetch('?route=clan_leader/get-available-users')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const userList = document.getElementById('availableUsersList');
                    userList.innerHTML = '';
                    
                    data.users.forEach(user => {
                        const userOption = document.createElement('div');
                        userOption.className = 'user-option';
                        userOption.innerHTML = `
                            <input type="checkbox" id="user_${user.user_id}" value="${user.user_id}">
                            <div class="user-avatar">
                                ${user.full_name ? user.full_name.charAt(0).toUpperCase() : user.username.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div style="font-weight: 600;">${user.full_name || user.username}</div>
                                <div style="font-size: 12px; color: #6b7280;">${user.username}</div>
                            </div>
                        `;
                        userList.appendChild(userOption);
                    });
                    
                    document.getElementById('addCollaboratorModal').style.display = 'block';
                } else {
                    showNotification('Error al cargar usuarios: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al cargar usuarios', 'error');
            });
        }
        
        function closeAddCollaboratorModal() {
            document.getElementById('addCollaboratorModal').style.display = 'none';
            selectedUsers = [];
        }
        
        function addSelectedCollaborators() {
            const checkboxes = document.querySelectorAll('#availableUsersList input[type="checkbox"]:checked');
            const userIds = Array.from(checkboxes).map(cb => cb.value);
            
            if (userIds.length === 0) {
                showNotification('Por favor selecciona al menos un usuario', 'error');
                return;
            }
            
            fetch('?route=clan_leader/add-collaborators', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `task_id=<?= $task['task_id'] ?>&user_ids=${userIds.join(',')}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddCollaboratorModal();
                    showNotification('Colaboradores agregados exitosamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error al agregar colaboradores: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al agregar colaboradores', 'error');
            });
        }
        
        function updateUserPercentage(userId, percentage) {
            fetch('?route=clan_leader/update-user-percentage', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `task_id=<?= $task['task_id'] ?>&user_id=${userId}&percentage=${percentage}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Porcentaje actualizado', 'success');
                } else {
                    showNotification('Error al actualizar porcentaje: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al actualizar porcentaje', 'error');
            });
        }
        
        function removeCollaborator(userId) {
            showConfirmationModal({
            title: 'Confirmar Remoción',
            message: '¿Estás seguro de que quieres remover este colaborador?',
            type: 'warning',
            confirmText: 'Remover',
            cancelText: 'Cancelar',
            onConfirm: () => {
                fetch('?route=clan_leader/remove-collaborator', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `task_id=<?= $task['task_id'] ?>&user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const userItem = document.querySelector(`[data-user-id="${userId}"]`);
                        if (userItem) userItem.remove();
                        showNotification('Colaborador removido', 'success');
                    } else {
                        showNotification('Error al remover colaborador: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al remover colaborador', 'error');
                });
            }
        });
        }
        
        // Función deleteTask ya está definida globalmente arriba
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('addCollaboratorModal');
            if (event.target === modal) {
                closeAddCollaboratorModal();
            }
        }
    </script>
</body>
</html> 