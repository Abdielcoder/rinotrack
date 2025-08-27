# 🌟 Polaris - Sistema de Gestión de Proyectos y Tareas

**P**royecta • **O**rganiza • **L**anza • **A**naliza • **R**evisa • **I**tera • **S**ocializa

*Tu estrella guía en la gestión empresarial.*

## 📋 Descripción General

Polaris (anteriormente RinoTrack) es un sistema integral de gestión de proyectos, tareas y recursos humanos diseñado para empresas que buscan optimizar su productividad y seguimiento de objetivos. El sistema implementa una metodología de organización basada en clanes (departamentos) y roles jerárquicos.

## 🚀 Funcionalidades Actuales

### 👥 Gestión de Usuarios y Roles

#### Sistema de Roles Jerárquico
- **Super Administrador**: Acceso completo al sistema
- **Administrador**: Gestión de usuarios, clanes y proyectos
- **Líder de Clan**: Gestión de su clan y proyectos asignados
- **Usuario Normal**: Participación en proyectos y tareas

#### Funcionalidades de Usuario
- ✅ Registro y autenticación de usuarios
- ✅ Gestión de perfiles con avatares
- ✅ Sistema de "Recordarme" con tokens seguros
- ✅ Control de estado activo/inactivo
- ✅ Búsqueda y filtrado de usuarios
- ✅ Asignación y cambio de roles, propios de clan


### 🏛️ Sistema de Clanes (Departamentos)

#### Organización Empresarial
- ✅ Creación y gestión de clanes por departamento
- ✅ Asignación de miembros a clanes
- ✅ Estructura jerárquica con líderes de clan
- ✅ Estadísticas por clan

#### Clanes Predefinidos
- **Kratos (DTYS)**: Desarrollo Tecnológico y Sistemas
- **Hermes (MKT)**: Marketing
- **Afrodita (RRHH)**: Recursos Humanos
- **Perséfone (SERV)**: Servicio
- **Deméter (ZAX)**: ZAX
- **Helios (COM)**: Comercial
- **GAIA**: Operación/Proyectos
- **Olympo**: Dirección

### 📊 Gestión de Proyectos

#### Administración de Proyectos
- ✅ Creación y edición de proyectos
- ✅ Asignación de proyectos a clanes
- ✅ Seguimiento de progreso automático
- ✅ Proyectos con fechas límite
- ✅ Estados de proyecto (abierto, activo, completado)
- ✅ Proyectos personales por usuario

#### Tipos de Proyecto
- **Proyectos Regulares**: Proyectos específicos con objetivos definidos
- **Tareas Recurrentes**: Actividades repetitivas del clan
- **Tareas Eventuales**: Actividades esporádicas
- **Proyectos Personales**: Tareas individuales del usuario

### ✅ Sistema de Tareas

#### Gestión Avanzada de Tareas
- ✅ Creación de tareas con descripción detallada
- ✅ Asignación múltiple de usuarios
- ✅ Sistema de subtareas anidadas
- ✅ Estados de tarea (pendiente, en progreso, completado)
- ✅ Prioridades (baja, media, alta)
- ✅ Fechas límite con alertas
- ✅ Tareas recurrentes con múltiples fechas

#### Funcionalidades de Seguimiento
- ✅ Comentarios en tareas y subtareas
- ✅ Adjuntos de archivos
- ✅ Historial de cambios
- ✅ Notificaciones automáticas
- ✅ Vista Gantt para planificación
- ✅ Filtros avanzados y búsqueda

### 📈 Sistema de KPIs y Métricas

#### Indicadores Clave de Rendimiento
- ✅ KPIs por clan y trimestre
- ✅ Asignación de puntos por proyecto
- ✅ Seguimiento de metas trimestrales
- ✅ Dashboard de métricas en tiempo real
- ✅ Reportes de productividad
- ✅ Análisis de rendimiento por usuario

#### Métricas Disponibles
- Progreso de proyectos por clan
- Tareas completadas vs pendientes
- Cumplimiento de fechas límite
- Distribución de carga de trabajo
- Eficiencia por departamento



### 📧 Sistema de Notificaciones

#### Comunicación Automática
- ✅ Notificaciones por email con plantillas HTML
- ✅ Alertas de tareas próximas a vencer
- ✅ Notificaciones de tareas vencidas
- ✅ Alertas de asignación de proyectos
- ✅ Configuración personalizable de notificaciones

#### Tipos de Notificación
- Asignación de tareas
- Fechas límite próximas
- Tareas vencidas
- Nuevos proyectos en clan
- Cambios de estado en tareas

### 🎨 Interfaz de Usuario

#### Diseño Moderno
- ✅ Interfaz responsive para dispositivos móviles
- ✅ Temas personalizables (claro/oscuro)
- ✅ Dashboards interactivos
- ✅ Navegación intuitiva por roles
- ✅ Componentes visuales modernos

#### Características UX
- Animaciones suaves
- Iconografía consistente
- Feedback visual inmediato
- Carga de contenido optimizada
- Accesibilidad mejorada

### 🔧 Administración del Sistema

#### Panel de Administración
- ✅ Gestión completa de usuarios
- ✅ Administración de clanes y departamentos
- ✅ Configuración de proyectos globales
- ✅ Gestión de tareas administrativas
- ✅ Configuración de notificaciones
- ✅ Estadísticas del sistema

#### Herramientas de Mantenimiento
- Scripts de limpieza de datos
- Backups automáticos
- Monitoreo de rendimiento
- Logs de auditoría
- Gestión de permisos

## 🛠️ Arquitectura Técnica

### Tecnologías Utilizadas
- **Backend**: PHP 8.3+ con arquitectura MVC
- **Base de Datos**: MariaDB 11.8+
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **Servidor**: Apache/Nginx compatible
- **Email**: SMTP con plantillas HTML

### Estructura del Proyecto
```
RinoTrack/
├── app/
│   ├── controllers/     # Controladores MVC
│   ├── models/          # Modelos de datos
│   ├── services/        # Servicios auxiliares
│   └── views/           # Vistas y plantillas
├── config/              # Configuraciones
├── public/              # Archivos públicos
│   ├── assets/         # CSS, JS, imágenes
│   └── index.php       # Punto de entrada
└── rinotrack.sql       # Base de datos
```

### Características Técnicas
- Arquitectura MVC robusta
- Conexiones PDO seguras
- Sanitización de datos
- Validación client-side y server-side
- Responsive design
- Optimización de rendimiento

## 🔮 Mejoras Planificadas

### Análisis del Perfil de Colaborador

#### Funcionalidades de Perfilado Avanzado
- **Análisis de Personalidad**: Integración de tests psicométricos para determinar perfiles de trabajo
- **Competencias Técnicas**: Sistema de evaluación y seguimiento de habilidades específicas
- **Matriz de Roles**: Asignación automática basada en perfil y competencias
- **Desarrollo Personal**: Planes de crecimiento individualizados

#### Sistema de Evaluación 360°
- **Autoevaluación**: Herramientas para que el colaborador evalúe sus propias competencias
- **Evaluación de Pares**: Sistema de feedback entre colegas del mismo nivel
- **Evaluación de Supervisores**: Valoración por parte de líderes y gerentes
- **Evaluación de Subordinados**: Feedback ascendente para líderes

#### Inteligencia Artificial para RRHH
- **Predicción de Rendimiento**: Algoritmos para predecir el éxito en roles específicos
- **Matching Automático**: Asignación inteligente de tareas basada en perfil
- **Detección de Burnout**: Identificación temprana de agotamiento laboral
- **Recomendaciones de Capacitación**: Sugerencias personalizadas de desarrollo

#### Analytics Avanzado de Colaboradores
- **Métricas de Productividad Individual**: Seguimiento detallado del rendimiento personal
- **Análisis de Colaboración**: Medición de efectividad en trabajo en equipo
- **Índices de Satisfacción Laboral**: Monitoreo del bienestar en el trabajo
- **Predicción de Rotación**: Identificación de riesgo de abandono

#### Desarrollo de Carrera
- **Rutas de Crecimiento**: Mapas de carrera personalizados por perfil
- **Mentoring Digital**: Sistema de mentoría con seguimiento automatizado
- **Planes de Sucesión**: Identificación y preparación de sucesores
- **Banco de Talentos**: Base de datos de competencias y potenciales

#### Bienestar y Engagement
- **Monitoreo de Engagement**: Medición continua del compromiso laboral
- **Programas de Bienestar**: Iniciativas personalizadas de salud laboral
- **Balance Vida-Trabajo**: Herramientas para gestionar el equilibrio personal
- **Reconocimiento Inteligente**: Sistema automático de reconocimientos

### Integraciones Futuras
- **Sistemas de RRHH**: Conexión con plataformas de gestión humana
- **Herramientas de Comunicación**: Integración con Slack, Teams, etc.
- **Plataformas de E-learning**: Conexión con sistemas de capacitación
- **APIs de Personalidad**: Integración con tests psicométricos reconocidos

### Mejoras Técnicas Planificadas
- **API REST Completa**: Para integraciones externas
- **App Móvil Nativa**: Aplicaciones iOS y Android
- **Machine Learning**: Algoritmos de aprendizaje automático
- **Blockchain**: Para certificación de competencias
- **Realidad Virtual**: Training inmersivo para ciertas competencias

## 📞 Información de Contacto

**Desarrollado por**: Equipo Kratos (DTYS)  
**Empresa**: RinoRisk  
**Versión**: 2.0  
**Fecha**: 2025

---

## 📝 Licencia

Este sistema es propiedad de RinoRisk y está protegido por derechos de autor. Uso interno únicamente.

---

*Polaris - Tu estrella guía en la gestión empresarial* ⭐
