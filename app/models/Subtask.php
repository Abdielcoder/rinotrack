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
            // Contar comentarios
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Subtask_Comments WHERE subtask_id = ?");
            $stmt->execute([$subtaskId]);
            $commentsCount = $stmt->fetchColumn();
            
            // Contar adjuntos
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Subtask_Attachments WHERE subtask_id = ?");
            $stmt->execute([$subtaskId]);
            $attachmentsCount = $stmt->fetchColumn();
            
            return [
                'comments_count' => (int)$commentsCount,
                'attachments_count' => (int)$attachmentsCount
            ];
        } catch (Exception $e) {
            error_log("Error al obtener conteos de subtarea: " . $e->getMessage());
            return ['comments_count' => 0, 'attachments_count' => 0];
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
                    SELECT sa.*, u.full_name as uploaded_by_name
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
                $commentId = (int)$this->db->lastInsertId();
                
                // Registrar en el historial de la tarea padre
                $subtask = $this->findById($subtaskId);
                if ($subtask) {
                    $this->logSubtaskAction($subtask['task_id'], $userId, 'commented', 'subtask_comment', null, $commentText, 'Comentario agregado en subtarea: ' . $subtask['title']);
                }
                
                return $commentId > 0 ? $commentId : true;
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
     * Eliminar comentario de subtarea
     */
    public function deleteComment($commentId, $userId) {
        try {
            // Verificar que el usuario puede eliminar el comentario (autor o admin)
            $stmt = $this->db->prepare("
                SELECT sc.*, s.task_id, s.title 
                FROM Subtask_Comments sc
                JOIN Subtasks s ON sc.subtask_id = s.subtask_id
                WHERE sc.comment_id = ?
            ");
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch();
            
            if (!$comment) {
                return false;
            }
            
            // Solo el autor del comentario puede eliminarlo (o se puede agregar lógica para admin)
            if ($comment['user_id'] != $userId) {
                return false;
            }
            
            $this->db->beginTransaction();
            
            // Eliminar adjuntos asociados al comentario
            $stmt = $this->db->prepare("DELETE FROM Subtask_Attachments WHERE comment_id = ?");
            $stmt->execute([$commentId]);
            
            // Eliminar el comentario
            $stmt = $this->db->prepare("DELETE FROM Subtask_Comments WHERE comment_id = ?");
            $result = $stmt->execute([$commentId]);
            
            if ($result) {
                // Registrar en el historial
                $this->logSubtaskAction($comment['task_id'], $userId, 'deleted', 'subtask_comment', $comment['comment_text'], null, 'Comentario eliminado de subtarea: ' . $comment['title']);
                
                $this->db->commit();
                return true;
            }
            
            $this->db->rollback();
            return false;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            error_log("Error al eliminar comentario de subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar adjunto de subtarea
     */
    public function deleteAttachment($attachmentId, $userId) {
        try {
            // Verificar que el usuario puede eliminar el adjunto
            $stmt = $this->db->prepare("
                SELECT sa.*, s.task_id, s.title 
                FROM Subtask_Attachments sa
                JOIN Subtasks s ON sa.subtask_id = s.subtask_id
                WHERE sa.attachment_id = ?
            ");
            $stmt->execute([$attachmentId]);
            $attachment = $stmt->fetch();
            
            if (!$attachment) {
                return false;
            }
            
            // Solo el autor del adjunto puede eliminarlo (o se puede agregar lógica para admin)
            if ($attachment['user_id'] != $userId) {
                return false;
            }
            
            // Eliminar el archivo físico si existe
            $filePath = $attachment['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Eliminar el registro de la base de datos
            $stmt = $this->db->prepare("DELETE FROM Subtask_Attachments WHERE attachment_id = ?");
            $result = $stmt->execute([$attachmentId]);
            
            if ($result) {
                // Registrar en el historial
                $this->logSubtaskAction($attachment['task_id'], $userId, 'deleted', 'subtask_attachment', $attachment['file_name'], null, 'Adjunto eliminado de subtarea: ' . $attachment['title']);
                
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error al eliminar adjunto de subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar permisos de usuario en una subtarea (sin restricciones)
     */
    public function checkUserPermissions($subtaskId, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    s.*,
                    t.project_id,
                    p.clan_id
                FROM Subtasks s
                JOIN Tasks t ON s.task_id = t.task_id
                JOIN Projects p ON t.project_id = p.project_id
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
     * Guardar archivo adjunto y retornar información del archivo
     */
    public function saveUploadedFile($file, $subtaskId, $userId) {
        try {
            $uploadsDir = __DIR__ . '/../../public/uploads/';
            
            // Crear directorio si no existe
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            
            // Generar nombre único para el archivo
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('subtask_' . $subtaskId . '_') . '.' . $extension;
            $filepath = $uploadsDir . $filename;
            
            // Mover archivo subido
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return [
                    'original_name' => $file['name'],
                    'saved_name' => $filename,
                    'file_path' => $filepath,
                    'public_path' => '/desarrollo/rinotrack/public/uploads/' . $filename,
                    'file_size' => $file['size'],
                    'file_type' => $file['type']
                ];
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error al guardar archivo de subtarea: " . $e->getMessage());
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
            error_log("Error al registrar acción de subtarea en historial: " . $e->getMessage());
            return false;
        }
    }
}

?>
