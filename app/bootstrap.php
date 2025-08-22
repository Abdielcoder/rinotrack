<?php
/**
 * Bootstrap de la aplicación
 * Carga todas las dependencias y configuraciones necesarias y configuraciones
 */

// Incluir configuraciones
require_once __DIR__ . '/../config/app.php';

// Autoloader para las clases de la aplicación (PSR-4 Standard)
spl_autoload_register(function ($className) {
    $directories = [
        __DIR__ . '/models/',
        __DIR__ . '/controllers/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
