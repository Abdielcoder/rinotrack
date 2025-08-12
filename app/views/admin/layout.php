<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME . ' - Admin'; ?></title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%230965f7'/%3E%3Ctext x='50%' y='50%' dy='.35em' text-anchor='middle' font-family='Inter, Arial, sans-serif' font-size='32' fill='white'%3ER%3C/text%3E%3C/svg%3E">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
        // Detectar automáticamente la URL base correcta
        (function() {
            'use strict';
            
            // Función para detectar la URL base
            function detectBaseUrl() {
                const currentPath = window.location.pathname;
                let baseUrl = '';
                
                // Si estamos en /rinotrack/public/, usar esa ruta
                if (currentPath.includes('/rinotrack/public/')) {
                    baseUrl = '/rinotrack/public/';
                }
                // Si estamos en /public/, usar esa ruta
                else if (currentPath.includes('/public/')) {
                    baseUrl = '/public/';
                }
                // Si estamos en la raíz, usar /
                else if (currentPath === '/' || currentPath === '') {
                    baseUrl = '/';
                }
                // Por defecto, usar la ruta actual
                else {
                    baseUrl = currentPath.endsWith('/') ? currentPath : currentPath + '/';
                }
                
                console.log('URL base detectada:', baseUrl);
                return baseUrl;
            }
            
            // Función para cargar CSS
            function loadCSS(href, onError) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = href;
                link.onerror = onError;
                document.head.appendChild(link);
            }
            
            // Función para cargar JavaScript
            function loadJS(src, onError) {
                const script = document.createElement('script');
                script.src = src;
                script.onerror = onError;
                document.head.appendChild(script);
            }
            
            // Cargar assets con fallback
            const baseUrl = detectBaseUrl();
            
            // Cargar CSS
            loadCSS(baseUrl + 'assets/css/theme.css', function() {
                console.log('CSS theme.css cargado desde:', baseUrl + 'assets/css/theme.css');
            });
            
            loadCSS(baseUrl + 'assets/css/admin.css', function() {
                console.log('CSS admin.css cargado desde:', baseUrl + 'assets/css/admin.css');
            });
            
            // Cargar JavaScript
            loadJS(baseUrl + 'assets/js/script.js', function() {
                console.log('JS script.js cargado desde:', baseUrl + 'assets/js/script.js');
            });
            
            // Hacer la URL base disponible globalmente
            window.APP_BASE_URL = baseUrl;
        })();
    </script>
    
    <!-- Additional CSS files -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <!-- Including CSS: <?php echo htmlspecialchars($css); ?> -->
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        const APP_URL = '<?php echo APP_URL; ?>';
    </script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <?php if (strpos($js, '<script>') === 0): ?>
                <!-- Including inline JS -->
                <?php echo $js; ?>
            <?php else: ?>
                <!-- Including external JS: <?php echo htmlspecialchars($js); ?> -->
                <script src="<?php echo $js; ?>"></script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php echo $content ?? ''; ?>
</body>
</html>