<?php
/**
 * Configuración de la aplicación
 */

// Configuración de la aplicación
define('APP_NAME', 'RinoTrack');
define('APP_URL', 'https://rinotrack.rinorisk.com/rinotrack/public/');
define('APP_DEBUG', true);

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS

// Configuración de errores (solo en desarrollo)
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Incluir archivos de configuración
require_once __DIR__ . '/database.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}