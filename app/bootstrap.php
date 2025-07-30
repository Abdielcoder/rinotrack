<?php
/**
 * Bootstrap de la aplicación
 * Carga todas las dependencias y configuraciones necesarias
 */

// Incluir configuraciones
require_once __DIR__ . '/../config/app.php';

// Autoloader para las clases de la aplicación
function autoloadRinoTrack($className) {
    $directories = [
        __DIR__ . '/models/',
        __DIR__ . '/controllers/',
        __DIR__ . '/views/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    return false;
}

// Registrar autoloader
spl_autoload_register('autoloadRinoTrack');

// Función helper para cargar vistas
function loadView($view, $data = []) {
    extract($data);
    $viewFile = __DIR__ . '/views/' . $view . '.php';
    
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        throw new Exception('Vista no encontrada: ' . $view);
    }
}

// Función helper para redireccionar
function redirect($url) {
    Utils::redirect($url);
}

// Función helper para respuestas JSON
function jsonResponse($data, $statusCode = 200) {
    Utils::jsonResponse($data, $statusCode);
}