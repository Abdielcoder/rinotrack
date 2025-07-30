<?php
/**
 * Configuración de base de datos
 */

define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // Cambia esto por tu contraseña de MySQL en MAMP
define('DB_NAME', 'rinotrack');

/**
 * Clase para manejar la conexión a la base de datos
 */
class Database {
    private static $connection = null;
    
    /**
     * Obtener conexión PDO singleton
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                self::$connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
            } catch (PDOException $e) {
                // En producción, registrar el error en lugar de mostrarlo
                die('Error de conexión a la base de datos: ' . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}