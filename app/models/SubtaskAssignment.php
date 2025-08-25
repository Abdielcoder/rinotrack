<?php

class SubtaskAssignment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Obtener usuarios asignados a una subtarea
     */
    public function getAssignedUsers($subtaskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    sa.assignment_id,
                    sa.subtask_id,
                    sa.user_id,
                    sa.assigned_percentage,
                    sa.assigned_at,
                    u.full_name,
                    u.username,
                    u.email
                FROM Subtask_Assignments sa
                JOIN Users u ON sa.user_id = u.user_id
                WHERE sa.subtask_id = ?
                ORDER BY sa.assigned_at ASC
            ");
            $stmt->execute([$subtaskId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error al obtener usuarios asignados a subtarea: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Asignar múltiples usuarios a una subtarea
     */
    public function assignUsers($subtaskId, $userIds, $assignedByUserId, $defaultPercentage = 100.00) {
        try {
            $this->db->beginTransaction();
            
            // Eliminar asignaciones existentes
            $stmt = $this->db->prepare("DELETE FROM Subtask_Assignments WHERE subtask_id = ?");
            $stmt->execute([$subtaskId]);
            
            // Asignar nuevos usuarios
            $stmt = $this->db->prepare("
                INSERT INTO Subtask_Assignments (subtask_id, user_id, assigned_percentage, assigned_by_user_id) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($userIds as $userId) {
                $stmt->execute([$subtaskId, $userId, $defaultPercentage, $assignedByUserId]);
            }
            
            // Actualizar assigned_to_user_id en Subtasks para compatibilidad (primer usuario asignado)
            if (!empty($userIds)) {
                $updateStmt = $this->db->prepare("
                    UPDATE Subtasks 
                    SET assigned_to_user_id = ?, updated_at = NOW() 
                    WHERE subtask_id = ?
                ");
                $updateStmt->execute([$userIds[0], $subtaskId]);
            } else {
                $updateStmt = $this->db->prepare("
                    UPDATE Subtasks 
                    SET assigned_to_user_id = NULL, updated_at = NOW() 
                    WHERE subtask_id = ?
                ");
                $updateStmt->execute([$subtaskId]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            error_log("Error al asignar usuarios a subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Desasignar todos los usuarios de una subtarea
     */
    public function unassignAllUsers($subtaskId) {
        try {
            $this->db->beginTransaction();
            
            // Eliminar todas las asignaciones
            $stmt = $this->db->prepare("DELETE FROM Subtask_Assignments WHERE subtask_id = ?");
            $stmt->execute([$subtaskId]);
            
            // Limpiar assigned_to_user_id en Subtasks
            $updateStmt = $this->db->prepare("
                UPDATE Subtasks 
                SET assigned_to_user_id = NULL, updated_at = NOW() 
                WHERE subtask_id = ?
            ");
            $updateStmt->execute([$subtaskId]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            error_log("Error al desasignar usuarios de subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remover un usuario específico de una subtarea
     */
    public function removeUser($subtaskId, $userId) {
        try {
            $this->db->beginTransaction();
            
            // Eliminar la asignación específica
            $stmt = $this->db->prepare("DELETE FROM Subtask_Assignments WHERE subtask_id = ? AND user_id = ?");
            $stmt->execute([$subtaskId, $userId]);
            
            // Verificar si quedan usuarios asignados
            $checkStmt = $this->db->prepare("SELECT user_id FROM Subtask_Assignments WHERE subtask_id = ? ORDER BY assigned_at ASC LIMIT 1");
            $checkStmt->execute([$subtaskId]);
            $firstUser = $checkStmt->fetch();
            
            // Actualizar assigned_to_user_id en Subtasks
            if ($firstUser) {
                $updateStmt = $this->db->prepare("
                    UPDATE Subtasks 
                    SET assigned_to_user_id = ?, updated_at = NOW() 
                    WHERE subtask_id = ?
                ");
                $updateStmt->execute([$firstUser['user_id'], $subtaskId]);
            } else {
                $updateStmt = $this->db->prepare("
                    UPDATE Subtasks 
                    SET assigned_to_user_id = NULL, updated_at = NOW() 
                    WHERE subtask_id = ?
                ");
                $updateStmt->execute([$subtaskId]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            error_log("Error al remover usuario de subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar porcentaje de asignación de un usuario
     */
    public function updateUserPercentage($subtaskId, $userId, $percentage) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Subtask_Assignments 
                SET assigned_percentage = ?, updated_at = NOW() 
                WHERE subtask_id = ? AND user_id = ?
            ");
            return $stmt->execute([$percentage, $subtaskId, $userId]);
            
        } catch (Exception $e) {
            error_log("Error al actualizar porcentaje de usuario en subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si un usuario está asignado a una subtarea
     */
    public function isUserAssigned($subtaskId, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM Subtask_Assignments 
                WHERE subtask_id = ? AND user_id = ?
            ");
            $stmt->execute([$subtaskId, $userId]);
            $result = $stmt->fetch();
            return (int)$result['count'] > 0;
            
        } catch (Exception $e) {
            error_log("Error al verificar asignación de usuario en subtarea: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener estadísticas de asignaciones de una subtarea
     */
    public function getAssignmentStats($subtaskId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_users,
                    SUM(assigned_percentage) as total_percentage,
                    AVG(assigned_percentage) as avg_percentage
                FROM Subtask_Assignments 
                WHERE subtask_id = ?
            ");
            $stmt->execute([$subtaskId]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas de asignación de subtarea: " . $e->getMessage());
            return ['total_users' => 0, 'total_percentage' => 0, 'avg_percentage' => 0];
        }
    }
}

?>
