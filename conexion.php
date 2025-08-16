<?php
/**
 * Configuración de Base de Datos y Sesiones
 * Enciclopedia de Artrópodos - Versión 2.0
 */

// --- CONFIGURACIÓN DE ERRORES Y LOGS ---
ini_set('display_errors', 1); // 1 para desarrollo, 0 para producción
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Crear directorio de logs si no existe
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// --- CONFIGURACIÓN DE SESIONES SEGURA ---
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    // Regenerar ID de sesión periódicamente para seguridad
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // Cada 5 minutos
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// --- CONFIGURACIÓN DE RUTAS ---
// Es CRUCIAL que esta ruta sea la correcta para tu proyecto
// Si tu proyecto está en htdocs/insectos_excel, usa '/insectos_excel/'
// Si tu proyecto está directamente en htdocs, usa '/'
if (!defined('URL_BASE')) {
    define('URL_BASE', '/insectos_excel/');
}

// --- CONFIGURACIÓN DE BASE DE DATOS ---
$config = [
    'host'      => 'localhost',
    'dbname'    => 'enciclopedia_db',
    'user'      => 'root',
    'pass'      => '',
    'charset'   => 'utf8mb4'
];

$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$options = [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES      => false,
];

// --- ESTABLECER CONEXIÓN ---
try {
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
} catch (PDOException $e) {
    error_log("Error de conexión a la BD: " . $e->getMessage());
    // En un sitio en producción, mostrarías una página de error amigable.
    die("Error de conexión. Por favor, inténtalo de nuevo más tarde.");
}

// --- FUNCIONES DE UTILIDAD (TU NUEVA CAJA DE HERRAMIENTAS) ---

/**
 * Ejecuta una consulta preparada de forma segura.
 */
function executeQuery($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Error SQL: " . $e->getMessage() . " | Query: " . $sql);
        return false;
    }
}

/**
 * Obtiene un solo registro de la base de datos.
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * Obtiene múltiples registros de la base de datos.
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * Verifica si el usuario ha iniciado sesión.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirige a la página de login si el usuario no ha iniciado sesión.
 */
function requireLogin($loginPage = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $loginPage");
        exit;
    }
}

/**
 * Verifica si el usuario es un administrador.
 */
function isAdmin() {
    global $pdo;
    if (!isLoggedIn()) return false;
    
    // Guardamos el rol en la sesión para no consultar la BD cada vez
    if (!isset($_SESSION['user_role'])) {
        $user = fetchOne("SELECT rol FROM usuarios WHERE id = ?", [$_SESSION['user_id']]);
        $_SESSION['user_role'] = $user ? $user['rol'] : 'usuario';
    }
    return $_SESSION['user_role'] === 'admin';
}

/**
 * Redirige si el usuario no es un administrador.
 */
function requireAdmin($redirectPage = 'index.php') {
    if (!isAdmin()) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'No tienes permisos para acceder a esta sección.'];
        header("Location: $redirectPage");
        exit;
    }
}

/**
 * Crea y guarda una nueva notificación para un usuario.
 * @param int $usuario_id El ID del usuario que recibirá la notificación.
 * @param string $mensaje El mensaje de la notificación.
 * @param string|null $link (Opcional) La URL a la que se dirigirá al hacer clic.
 * @return bool Retorna true si la notificación se insertó correctamente, false en caso contrario.
 */
function crearNotificacion($usuario_id, $mensaje, $link = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO notificaciones (usuario_id, mensaje, link, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$usuario_id, $mensaje, $link]);
        return true;
    } catch (PDOException $e) {
        error_log("Error al crear notificación: " . $e->getMessage());
        return false;
    }
}
?>