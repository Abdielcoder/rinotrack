<?php
// Script de debug para verificar el routing de project_tasks
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Utils.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Auth.php';
require_once __DIR__ . '/../app/models/Project.php';
require_once __DIR__ . '/../app/models/Clan.php';
require_once __DIR__ . '/../app/models/Role.php';
require_once __DIR__ . '/../app/models/Task.php';
require_once __DIR__ . '/../app/models/Subtask.php';
require_once __DIR__ . '/../app/models/SubtaskAssignment.php';
require_once __DIR__ . '/../app/models/Gamification.php';
require_once __DIR__ . '/../app/models/Notification.php';
require_once __DIR__ . '/../app/services/Mailer.php';
require_once __DIR__ . '/../app/services/EmailTemplate.php';
require_once __DIR__ . '/../app/services/NotificationService.php';
require_once __DIR__ . '/../app/controllers/ClanLeaderController.php';

echo "<h1>Debug Project Tasks</h1>";

// Verificar autenticación
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    echo "<p style='color: red;'>❌ Usuario no autenticado</p>";
    echo "<p><a href='?route=login'>Ir al login</a></p>";
    exit;
}

echo "<p style='color: green;'>✅ Usuario autenticado</p>";

// Obtener usuario actual
$userModel = new User();
$currentUser = $auth->getCurrentUser();
echo "<h2>Usuario Actual:</h2>";
echo "<pre>" . print_r($currentUser, true) . "</pre>";

// Verificar roles
$roleModel = new Role();
$userRoles = $roleModel->getUserRoles($currentUser['user_id']);
echo "<h2>Roles del Usuario:</h2>";
echo "<pre>" . print_r($userRoles, true) . "</pre>";

// Verificar si es líder de clan
$isClanLeader = $roleModel->userHasRole($currentUser['user_id'], Role::LIDER_CLAN);
$isAdmin = $roleModel->userHasMinimumRole($currentUser['user_id'], Role::ADMIN);

echo "<h2>Permisos:</h2>";
echo "<p>Es Líder de Clan: " . ($isClanLeader ? "✅ SÍ" : "❌ NO") . "</p>";
echo "<p>Es Admin: " . ($isAdmin ? "✅ SÍ" : "❌ NO") . "</p>";

// Verificar clan del usuario
$userClan = $userModel->getUserClan($currentUser['user_id']);
echo "<h2>Clan del Usuario:</h2>";
echo "<pre>" . print_r($userClan, true) . "</pre>";

// Verificar proyecto 213
$projectModel = new Project();
$project = $projectModel->findById(213);
echo "<h2>Proyecto 213:</h2>";
echo "<pre>" . print_r($project, true) . "</pre>";

// Verificar tareas del proyecto
$taskModel = new Task();
$tasks = $taskModel->getByProjectWithPrivacy(213, $currentUser['user_id']);
echo "<h2>Tareas del Proyecto 213:</h2>";
echo "<p>Total de tareas: " . count($tasks) . "</p>";
echo "<pre>" . print_r($tasks, true) . "</pre>";

// Verificar routing
echo "<h2>Información de Routing:</h2>";
echo "<p>Route: " . ($_GET['route'] ?? 'No definido') . "</p>";
echo "<p>Project ID: " . ($_GET['project_id'] ?? 'No definido') . "</p>";
echo "<p>Action: " . ($_GET['action'] ?? 'No definido') . "</p>";

// Verificar si debería usar ClanLeaderController
if ($isClanLeader || $isAdmin) {
    echo "<p style='color: green;'>✅ Debería usar ClanLeaderController</p>";
} else {
    echo "<p style='color: red;'>❌ NO debería usar ClanLeaderController</p>";
}

echo "<h2>URLs de Prueba:</h2>";
echo "<p><a href='?route=clan_leader/tasks&project_id=213'>Clan Leader Tasks con Project ID 213</a></p>";
echo "<p><a href='?route=clan_leader/tasks'>Clan Leader Tasks (todas)</a></p>";
echo "<p><a href='?route=clan_member/tasks'>Clan Member Tasks</a></p>";
?>
