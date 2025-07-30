# RinoTrack - Arquitectura MVC

## Descripción
Este proyecto ha sido migrado a una arquitectura MVC (Modelo-Vista-Controlador) para mejor organización y mantenibilidad del código.

## Estructura del Proyecto

```
RinoTrack/
├── app/                          # Aplicación principal
│   ├── controllers/              # Controladores (lógica de aplicación)
│   │   ├── AuthController.php    # Manejo de autenticación
│   │   └── DashboardController.php # Manejo del dashboard
│   ├── models/                   # Modelos (lógica de datos)
│   │   ├── Auth.php              # Modelo de autenticación
│   │   ├── User.php              # Modelo de usuarios
│   │   └── Utils.php             # Utilidades
│   ├── views/                    # Vistas (presentación)
│   │   ├── layout.php            # Layout principal
│   │   ├── login.php             # Vista de login
│   │   └── dashboard.php         # Vista del dashboard
│   └── bootstrap.php             # Bootstrap de la aplicación
├── config/                       # Configuraciones
│   ├── app.php                   # Configuración principal
│   └── database.php              # Configuración de base de datos
├── public/                       # Archivos públicos
│   ├── assets/                   # Recursos estáticos
│   │   ├── css/
│   │   │   └── styles.css        # Estilos CSS
│   │   └── js/
│   │       └── script.js         # JavaScript
│   ├── .htaccess                 # Configuración Apache
│   └── index.php                 # Router principal (punto de entrada)
├── .htaccess                     # Redirección a public/
└── README_MVC.md                 # Esta documentación
```

## Componentes Principales

### Modelos
- **User.php**: Maneja operaciones relacionadas con usuarios (CRUD, búsquedas)
- **Auth.php**: Maneja autenticación, sesiones y seguridad
- **Utils.php**: Funciones utilitarias (sanitización, validación, redirecciones)

### Controladores
- **AuthController.php**: Maneja login, logout y verificación de autenticación
- **DashboardController.php**: Maneja la lógica del dashboard principal

### Vistas
- **layout.php**: Template base para todas las páginas
- **login.php**: Formulario de inicio de sesión
- **dashboard.php**: Panel principal del usuario

## Rutas Disponibles

- `/` o `/login` - Página de inicio de sesión
- `/process-login` - Procesar formulario de login (POST)
- `/dashboard` - Panel principal (requiere autenticación)
- `/logout` - Cerrar sesión

## Características

### Seguridad
- Protección contra ataques CSRF
- Validación de entrada en frontend y backend
- Límite de intentos de login por IP y usuario
- Sanitización de datos
- Sesiones seguras

### Funcionalidades
- Login con username o email
- Opción "Recordarme" con tokens seguros
- Dashboard responsivo
- Manejo de errores
- Logging de intentos de login

## Configuración

### 1. Base de Datos
Edita `config/database.php` con tus credenciales:
```php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'tu_usuario');
define('DB_PASSWORD', 'tu_contraseña');
define('DB_NAME', 'rinotrack');
```

### 2. Aplicación
Edita `config/app.php` para ajustar configuraciones:
```php
define('APP_URL', 'http://tu-dominio.com/RinoTrack/');
define('APP_DEBUG', false); // false en producción
```

### 3. Servidor Web
- **Opción 1**: Configurar DocumentRoot a la carpeta `public/`
- **Opción 2**: Usar el `.htaccess` en la raíz para redireccionar

## Migración desde el Sistema Anterior

Los archivos antiguos pueden mantenerse como backup:
- `index.html` → ahora es `app/views/login.php`
- `login.php` → lógica ahora en `AuthController.php`
- `dashboard.php` → ahora es `app/views/dashboard.php`
- `config.php` → dividido en `config/app.php` y `config/database.php`

## Ventajas de la Arquitectura MVC

1. **Separación de responsabilidades**: Cada componente tiene una función específica
2. **Mantenibilidad**: Código más organizado y fácil de mantener
3. **Escalabilidad**: Fácil agregar nuevas funcionalidades
4. **Reutilización**: Componentes reutilizables
5. **Testabilidad**: Más fácil de testear individualmente
6. **Seguridad**: Mejores prácticas de seguridad implementadas

## Próximos Pasos

Para expandir la aplicación, considera:
- Agregar más modelos (Projects, Tasks, etc.)
- Implementar un sistema de permisos/roles
- Agregar APIs REST
- Implementar cache
- Agregar tests unitarios