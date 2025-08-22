<?php

class CheckboxState {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Guardar o actualizar el estado de un checkbox
     */
    public function saveCheckboxState($commentId, $commentType, $checkboxIndex, $checkboxText, $isChecked, $userId) {
        try {
            $sql = "
                INSERT INTO Comment_Checkbox_States 
                (comment_id, comment_type, checkbox_index, checkbox_text, is_checked, user_id) 
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                is_checked = VALUES(is_checked),
                checkbox_text = VALUES(checkbox_text),
                updated_at = CURRENT_TIMESTAMP
            ";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $commentId, 
                $commentType, 
                $checkboxIndex, 
                $checkboxText, 
                $isChecked ? 1 : 0, 
                $userId
            ]);
            
        } catch (Exception $e) {
            error_log("Error al guardar estado de checkbox: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener estados de checkboxes para un comentario
     */
    public function getCheckboxStates($commentId, $commentType) {
        try {
            $sql = "
                SELECT checkbox_index, checkbox_text, is_checked, user_id, updated_at
                FROM Comment_Checkbox_States 
                WHERE comment_id = ? AND comment_type = ?
                ORDER BY checkbox_index ASC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$commentId, $commentType]);
            
            $states = [];
            while ($row = $stmt->fetch()) {
                $states[$row['checkbox_index']] = [
                    'text' => $row['checkbox_text'],
                    'is_checked' => (bool)$row['is_checked'],
                    'user_id' => $row['user_id'],
                    'updated_at' => $row['updated_at']
                ];
            }
            
            return $states;
            
        } catch (Exception $e) {
            error_log("Error al obtener estados de checkbox: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Eliminar estados de checkboxes cuando se elimina un comentario
     */
    public function deleteCheckboxStates($commentId, $commentType) {
        try {
            $sql = "DELETE FROM Comment_Checkbox_States WHERE comment_id = ? AND comment_type = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$commentId, $commentType]);
            
        } catch (Exception $e) {
            error_log("Error al eliminar estados de checkbox: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Crear tabla si no existe (para instalación automática)
     */
    public function createTableIfNotExists() {
        try {
            $sql = "
            CREATE TABLE IF NOT EXISTS `Comment_Checkbox_States` (
                `state_id` int(11) NOT NULL AUTO_INCREMENT,
                `comment_id` int(11) NOT NULL,
                `comment_type` enum('task','subtask') NOT NULL,
                `checkbox_index` int(11) NOT NULL,
                `checkbox_text` text NOT NULL,
                `is_checked` tinyint(1) DEFAULT 0,
                `user_id` int(11) NOT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`state_id`),
                UNIQUE KEY `unique_checkbox` (`comment_id`, `comment_type`, `checkbox_index`),
                KEY `idx_comment_type` (`comment_id`, `comment_type`),
                KEY `idx_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
            ";
            
            return $this->db->exec($sql) !== false;
            
        } catch (Exception $e) {
            error_log("Error al crear tabla Comment_Checkbox_States: " . $e->getMessage());
            return false;
        }
    }
}
?>
