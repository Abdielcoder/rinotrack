<?php

class Subtask {
    private $db;
    
    // Constantes de prioridad
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';
    
    // Constantes de estado
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Obtener subtarea por ID
     */
    public function findById($subtaskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    s.*,
                    t.task_name as parent_task_name,
                    u_assigned.full_name as assigned_to_fullname,
                    u_assigned.username as assigned_to_username,
                    u_created.full_name as created_by_fullname,
                    u_created.username as created_by_username
                FROM Subtasks s
                LEFT JOIN Tasks t ON s.task_id = t.task_id
                LEFT JOIN Users u_assigned ON s.assigned_to_user_id = u_assigned.user_id
                LEFT JOIN Users u_created ON s.created_by_user_id = u_created.user_id
                WHERE s.subtask_id = ?
            ");
            $stmt->execute([$subtaskId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error al buscar subtarea: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener conteos de comentarios y adjuntos para una subtarea
     */
    public function getSubtaskCounts($subtaskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    (SELECT COUNT(*) FROM Subtask_Comments WHERE subtask_id = ?) as comment_count,
                    (SELECT COUNT(*) FROM Subtask_Attachments WHERE subtask_id = ?) as attachment_count
            ");
            $stmt->execute([$subtaskId, $subtaskId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error al obtener conteos de subtarea: " . $e->getMessage());
            return ['comment_count' => 0, 'attachment_count' => 0];
        }
    }
    
    /**
     * Obtener comentarios de una subtarea
     */
    public function getComments($subtaskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT sc.*, u.full_name, u.username
                FROM Subtask_Comments sc
                JOIN Users u ON sc.user_id = u.user_id
                WHERE sc.subtask_id = ?
                ORDER BY sc.created_at DESC
            ");
            $stmt->execute([$subtaskId]);
            $comments = $stmt->fetchAll();

            // Enriquecer con adjuntos por comentario
            if (!empty($comments)) {
                $commentIds = array_column($comments, 'comment_id');
                if (!empty($commentIds)) {
                    $in = implode(',', array_fill(0, count($commentIds), '?'));
                    $stmtA = $this->db->prepare("
                        SELECT comment_id, file_name, file_path, file_type, uploaded_at, attachment_id 
                        FROM Subtask_Attachments 
                        WHERE comment_id IN ($in) AND comment_id IS NOT NULL
                    ");
                    $stmtA->execute($commentIds);
                    $rows = $stmtA->fetchAll();
                    
                    error_log("DEBUG: Adjuntos encontrados para comentarios: " . print_r($rows, true));
                    
                    $byComment = [];
                    foreach ($rows as $r) {
                        $byComment[$r['comment_id']][] = $r;
                    }
                    
                    foreach ($comments as &$c) {
                        $c['attachments'] = $byComment[$c['comment_id']] ?? [];
                        $c['attachments_count'] = count($c['attachments']);
                        error_log("DEBUG: Comentario {$c['comment_id']} tiene " . count($c['attachments']) . " adjuntos");
                    }
                }
                
                // También buscar adjuntos independientes (sin comment_id) para esta subtarea
                $stmtB = $this->db->prepare("
                    SELECT 
                        sa.*,
                        u.full_name as uploaded_by_name,
                        u.username as uploaded_by_username
                    FROM Subtask_Attachments sa
                    JOIN Users u ON sa.user_id = u.user_id
                    WHERE sa.subtask_id = ? AND (sa.comment_id IS NULL OR sa.comment_id = 0)
                    ORDER BY sa.uploaded_at DESC
                ");
                $stmtB->execute([$subtaskId]);
                $independentAttachments = $stmtB->fetchAll();
                
                // Agregar adjuntos independientes como un "comentario" especial
                if (!empty($independentAttachments)) {
                    $fakeComment = [
                        'comment_id' => 0,
                        'subtask_id' => $subtaskId,
                        'user_id' => 0,
                        'comment_text' => '',
                        'comment_type' => 'attachment_only',
                        'full_name' => 'Archivos adjuntos',
                        'username' => '',
                        'created_at' => '',
                        'attachments' => $independentAttachments,
                        'attachments_count' => count($independentAttachments),
                        'is_attachment_only' => true
                    ];
                    array_unshift($comments, $fakeComment);
                }
            }

            return $comments;
        } catch (Exception $e) {
            error_log("Error al obtener comentarios de subtarea: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Agregar comentario a una subtarea
     */
    public function addComment($subtaskId, $userId, $commentText, $commentType = 'comment', $relatedUserId = null, $oldValue = null, $newValue = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Subtask_Comments (subtask_id, user_id, comment_text, comment_type, related_user_id, old_value, new_value) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$subtaskId, $userId, $commentText, $commentType, $relatedUserId, $oldValue, $newValue]);
            
            if ($result) {
                $commentId = $this->db->lastInsertId();
                
                // Registrar en el historial de la tarea padre
                $subtask = $this->findById($subtaskId);
                if ($subtask) {
                    $this->logSubtaskAction($subtask['task_id'], $userId, 'commented', 'subtask_comment', null, $commentText, 'Comentario en subtarea: ' . $subtask['title']);
                }
                
                return $commentId;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error al agregar comentario a subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener documentos adjuntos de una subtarea
     */
    public function getAttachments($subtaskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    sa.*,
                    u.full_name as uploaded_by_name,
                    u.username as uploaded_by_username,
                    sc.comment_text
                FROM Subtask_Attachments sa
                JOIN Users u ON sa.user_id = u.user_id
                LEFT JOIN Subtask_Comments sc ON sa.comment_id = sc.comment_id
                WHERE sa.subtask_id = ?
                ORDER BY sa.uploaded_at DESC
            ");
            $stmt->execute([$subtaskId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener adjuntos de subtarea: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener un adjunto específico por ID
     */
    public function getAttachmentById($attachmentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    sa.*,
                    u.full_name as uploaded_by_name,
                    u.username as uploaded_by_username,
                    sc.comment_text
                FROM Subtask_Attachments sa
                JOIN Users u ON sa.user_id = u.user_id
                LEFT JOIN Subtask_Comments sc ON sa.comment_id = sc.comment_id
                WHERE sa.attachment_id = ?
            ");
            $stmt->execute([$attachmentId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error al obtener adjunto por ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Agregar documento adjunto a una subtarea
     */
    public function addAttachment($subtaskId, $userId, $fileName, $filePath, $fileSize = null, $fileType = null, $description = null, $commentId = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Subtask_Attachments (subtask_id, user_id, comment_id, file_name, file_path, file_size, file_type, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$subtaskId, $userId, $commentId, $fileName, $filePath, $fileSize, $fileType, $description]);
            
            if ($result) {
                $attachmentId = (int)$this->db->lastInsertId();
                
                // Registrar en el historial de la tarea padre
                $subtask = $this->findById($subtaskId);
                if ($subtask) {
                    $this->logSubtaskAction($subtask['task_id'], $userId, 'attached', 'subtask_attachment', null, $fileName, 'Documento adjuntado en subtarea: ' . $subtask['title']);
                }
                
                return $attachmentId > 0 ? $attachmentId : true;
            }

            return false;
            
        } catch (Exception $e) {
            error_log("Error al agregar adjunto a subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vincular un archivo adjunto a un comentario
     */
    public function linkAttachmentToComment($attachmentId, $commentId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Subtask_Attachments 
                SET comment_id = ? 
                WHERE attachment_id = ?
            ");
            
            return $stmt->execute([$commentId, $attachmentId]);
            
        } catch (Exception $e) {
            error_log("Error al vincular adjunto a comentario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Corregir rutas de archivos adjuntos existentes
     */
    public function fixAttachmentPaths() {
        try {
            // Obtener todos los archivos adjuntos con rutas incorrectas
            $stmt = $this->db->prepare("
                SELECT attachment_id, file_path 
                FROM Subtask_Attachments 
                WHERE file_path NOT LIKE 'uploads/%' 
                AND file_path NOT LIKE 'task_attachments/%'
            ");
            $stmt->execute();
            $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $updated = 0;
            foreach ($attachments as $attachment) {
                $oldPath = $attachment['file_path'];
                $newPath = 'uploads/' . $oldPath;
                
                // Verificar si el archivo existe en el directorio correcto
                $fullPath = dirname(__DIR__, 2) . '/public/' . $newPath;
                if (file_exists($fullPath)) {
                    // Actualizar la ruta en la base de datos
                    $updateStmt = $this->db->prepare("
                        UPDATE Subtask_Attachments 
                        SET file_path = ? 
                        WHERE attachment_id = ?
                    ");
                    if ($updateStmt->execute([$newPath, $attachment['attachment_id']])) {
                        $updated++;
                        error_log("Ruta corregida: {$oldPath} -> {$newPath}");
                    }
                } else {
                    error_log("Archivo no encontrado: {$fullPath}");
                }
            }
            
            return $updated;
            
        } catch (Exception $e) {
            error_log("Error al corregir rutas de archivos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar subtarea
     */
    public function update($subtaskId, $title, $description = null, $assignedUserId = null, $priority = null, $dueDate = null, $completionPercentage = null, $status = null) {
        try {
            // Construir query dinámicamente
            $updates = [];
            $params = [];
            
            if ($title !== null) {
                $updates[] = 'title = ?';
                $params[] = $title;
            }
            
            if ($description !== null) {
                $updates[] = 'description = ?';
                $params[] = $description;
            }
            
            if ($assignedUserId !== null) {
                $updates[] = 'assigned_to_user_id = ?';
                $params[] = $assignedUserId;
            }
            
            if ($priority !== null) {
                $updates[] = 'priority = ?';
                $params[] = $priority;
            }
            
            if ($dueDate !== null) {
                $updates[] = 'due_date = ?';
                $params[] = $dueDate;
            }
            
            if ($completionPercentage !== null) {
                $updates[] = 'completion_percentage = ?';
                $params[] = $completionPercentage;
            }
            
            if ($status !== null) {
                $updates[] = 'status = ?';
                $params[] = $status;
            }
            
            if (empty($updates)) {
                return false;
            }
            
            $updates[] = 'updated_at = NOW()';
            $params[] = $subtaskId;
            
            $sql = "UPDATE Subtasks SET " . implode(', ', $updates) . " WHERE subtask_id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);
            
        } catch (Exception $e) {
            error_log("Error al actualizar subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar permisos de usuario en una subtarea
     */
    public function checkUserPermissions($subtaskId, $userId) {
        try {
            // Obtener información de la subtarea y su tarea padre
            $stmt = $this->db->prepare("
                SELECT 
                    s.*,
                    t.project_id,
                    t.assigned_to_user_id as task_assigned_to,
                    t.created_by_user_id as task_created_by,
                    p.clan_id,
                    p.project_manager_id
                FROM Subtasks s
                JOIN Tasks t ON s.task_id = t.task_id
                LEFT JOIN Projects p ON t.project_id = p.project_id
                WHERE s.subtask_id = ?
            ");
            $stmt->execute([$subtaskId]);
            $result = $stmt->fetch();
            
            if (!$result) {
                return ['can_view' => false, 'can_comment' => false, 'can_attach' => false];
            }
            
            // Permitir todos los permisos a cualquier usuario autenticado
            return [
                'can_view' => true,
                'can_comment' => true,
                'can_attach' => true,
                'subtask' => $result
            ];
            
        } catch (Exception $e) {
            error_log("Error al verificar permisos de subtarea: " . $e->getMessage());
            return ['can_view' => false, 'can_comment' => false, 'can_attach' => false];
        }
    }
    
    /**
     * SISTEMA ULTRA SIMPLE DE GUARDAR ARCHIVOS
     */
    public function saveUploadedFile($file, $subtaskId, $userId) {
        try {
            // Directorio donde se guardan los archivos
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';
            
            // Crear directorio si no existe
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $uniqueName = 'file_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $targetPath = $uploadDir . $uniqueName;
            
            // Mover archivo
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return [
                    'original_name' => $file['name'],
                    'saved_name' => $uniqueName,
                    'file_path' => 'uploads/' . $uniqueName,
                    'file_size' => $file['size'],
                    'file_type' => $file['type']
                ];
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error saving file: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registrar acción en el historial de la tarea padre
     */
    private function logSubtaskAction($taskId, $userId, $actionType, $fieldName = null, $oldValue = null, $newValue = null, $notes = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Task_History (task_id, user_id, action_type, field_name, old_value, new_value, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$taskId, $userId, $actionType, $fieldName, $oldValue, $newValue, $notes]);
        } catch (Exception $e) {
            error_log("Error al registrar acción en historial: " . $e->getMessage());
            return false;
        }
    }
}
?>