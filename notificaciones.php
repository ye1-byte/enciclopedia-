<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
// Incluimos la conexión ANTES de cualquier cosa para poder usar la sesión y la BD.
require 'conexion.php';

// Protección: si el usuario no ha iniciado sesión, no puede estar aquí.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Lógica para manejar la eliminación de una notificación.
// Este bloque se ejecuta ANTES de mostrar cualquier HTML.
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $notificacion_id = $_GET['id'];

    // Borramos una notificación específica que pertenezca al usuario actual para seguridad.
    $stmt = $pdo->prepare("DELETE FROM notificaciones WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$notificacion_id, $user_id]);
    
    // Redirigimos para limpiar la URL y evitar acciones repetidas.
    header('Location: notificaciones.php');
    exit();
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
$page_title = 'Mis Notificaciones';

// Marcamos todas las notificaciones no leídas del usuario como leídas.
try {
    $stmt_mark_read = $pdo->prepare("UPDATE notificaciones SET leido = 1 WHERE usuario_id = ? AND leido = 0");
    $stmt_mark_read->execute([$user_id]);
} catch (PDOException $e) {
    // Manejar el error si es necesario, por ahora lo ignoramos para no romper la página.
}

// Obtenemos todas las notificaciones del usuario para mostrarlas en la lista.
try {
    $stmt = $pdo->prepare("SELECT * FROM notificaciones WHERE usuario_id = ? ORDER BY fecha DESC");
    $stmt->execute([$user_id]);
    $notificaciones = $stmt->fetchAll();
} catch (PDOException $e) {
    $notificaciones = []; // Si hay un error, mostramos la lista vacía.
}

// --- FASE 3: VISUALIZACIÓN ---
// Ahora que toda la lógica ha terminado, incluimos el header.
require 'header.php';
?>

<!-- Contenido específico de la página de Notificaciones -->
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5">Notificaciones</h1>
    </div>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            <?php if (empty($notificaciones)): ?>
                <!-- Mensaje si no hay notificaciones -->
                <div class="list-group-item text-center p-5">
                    <i class="bi bi-bell-slash fs-1 text-secondary opacity-50"></i>
                    <h5 class="mt-3">No tienes notificaciones</h5>
                    <p class="text-muted">Cuando ocurra algo importante relacionado con tu cuenta, aparecerá aquí.</p>
                </div>
            <?php else: ?>
                <!-- Bucle para mostrar cada notificación -->
                <?php foreach ($notificaciones as $notificacion): ?>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">
                                <?php if ($notificacion['link']): ?>
                                    <a href="<?php echo htmlspecialchars($notificacion['link']); ?>" class="text-decoration-none fw-bold">
                                        <?php echo htmlspecialchars($notificacion['mensaje']); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($notificacion['mensaje']); ?>
                                <?php endif; ?>
                            </p>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                <?php echo date('d/m/Y \a \l\a\s H:i', strtotime($notificacion['fecha'])); ?>
                            </small>
                        </div>
                        <a href="notificaciones.php?action=delete&id=<?php echo $notificacion['id']; ?>" class="btn btn-sm btn-outline-danger" title="Eliminar notificación" onclick="return confirm('¿Estás seguro de que deseas eliminar esta notificación?');">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
