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
                
                // Si estamos en /desarrollo/rinotrack/public/, usar esa ruta
                if (currentPath.includes('/desarrollo/rinotrack/public/')) {
                    baseUrl = '/desarrollo/rinotrack/public/';
                }
                // Si estamos en /rinotrack/public/, usar esa ruta
                else if (currentPath.includes('/rinotrack/public/')) {
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
    <?php 
    $route = $_GET['route'] ?? '';
    $isLeaderPage = (isset($currentPage) && $currentPage === 'clan_leader') || (strpos($route, 'clan_leader') === 0);
    if ($isLeaderPage): ?>
    <header class="leader-nav">
        <div class="leader-nav__inner">
            <a href="?route=clan_leader" class="leader-nav__brand">
                <img src="<?php echo Utils::asset('assets/img/logo.png'); ?>" alt="Polaris" class="leader-nav__logo"/>
                <span class="leader-nav__title">Polaris Líder</span>
            </a>
            <button class="leader-nav__toggle" aria-label="Abrir menú" onclick="document.querySelector('.leader-nav').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="leader-nav__menu">
                <a href="?route=clan_leader" class="leader-nav__link <?php echo ($route === 'clan_leader' || $route === 'clan_leader/dashboard') ? 'active' : ''; ?>"><i class="fas fa-home"></i><span>Dashboard</span></a>
                <a href="?route=clan_leader/members" class="leader-nav__link <?php echo ($route === 'clan_leader/members') ? 'active' : ''; ?>"><i class="fas fa-users"></i><span>Miembros</span></a>
                <a href="?route=clan_leader/projects" class="leader-nav__link <?php echo ($route === 'clan_leader/projects') ? 'active' : ''; ?>"><i class="fas fa-folder"></i><span>Proyectos</span></a>
                <a href="?route=clan_leader/tasks" class="leader-nav__link <?php echo (strpos($route, 'clan_leader/tasks') === 0 || $route === 'clan_leader/get-task-details') ? 'active' : ''; ?>"><i class="fas fa-tasks"></i><span>Tareas</span></a>
                <a href="?route=clan_leader/kpi-dashboard" class="leader-nav__link <?php echo ($route === 'clan_leader/kpi-dashboard') ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i><span>KPI</span></a>
                <a href="?route=clan_leader/collaborator-availability" class="leader-nav__link <?php echo ($route === 'clan_leader/collaborator-availability') ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i><span>Disponibilidad</span></a>
                <a href="?route=clan_leader/profile" class="leader-nav__link <?php echo ($route === 'clan_leader/profile') ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i><span>Perfil</span></a>
            </nav>
        </div>
        <style>
        .leader-nav { position: sticky; top: 0; z-index: 1000; background: #ffffff; border-bottom: 1px solid #e5e7eb; }
        .leader-nav__inner { max-width: 1200px; margin: 0 auto; padding: 10px 16px; display: flex; align-items: center; gap: 12px; }
        .leader-nav__brand { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
        .leader-nav__logo { width: 28px; height: 28px; }
        .leader-nav__title { font-weight: 700; color: #1e3a8a; }
        .leader-nav__menu { display: flex; align-items: center; gap: 8px; margin-left: auto; flex-wrap: wrap; }
        .leader-nav__link { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 10px; text-decoration: none; color: #374151; font-weight: 600; transition: all .2s ease; border: 1px solid transparent; }
        .leader-nav__link i { color: #1e3a8a; }
        .leader-nav__link:hover { background: #eef2ff; border-color: #c7d2fe; color: #1e3a8a; }
        .leader-nav__link.active { background: #1e3a8a; color: #ffffff; border-color: #1e3a8a; }
        .leader-nav__link.active i, .leader-nav__link.active span { color: #ffffff; }
        .leader-nav__toggle { display: none; margin-left: auto; background: #1e3a8a; color: #ffffff; border: 1px solid #1e3a8a; width: 36px; height: 36px; border-radius: 8px; align-items: center; justify-content: center; }
        @media (max-width: 900px) {
            .leader-nav__toggle { display: inline-flex; }
            .leader-nav__menu { display: none; position: absolute; left: 0; right: 0; top: 54px; background: #ffffff; border-bottom: 1px solid #e5e7eb; padding: 8px 12px 12px; }
            .leader-nav.open .leader-nav__menu { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
            .leader-nav__link { justify-content: center; }
        }
        @media (max-width: 520px) {
            .leader-nav.open .leader-nav__menu { grid-template-columns: 1fr; }
        }
        </style>
    </header>
    <?php endif; ?>
    <?php echo $content ?? ''; ?>
</body>
</html>