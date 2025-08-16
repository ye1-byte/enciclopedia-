<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
require '../conexion.php'; 
require '_proteccion.php';
require '../funciones.php'; // Necesitamos la función crear_notificacion()

// Procesamos el formulario de envío de notificaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_notificacion'])) {
    $destinatario = $_POST['destinatario'] ?? '';
    $mensaje = trim($_POST['mensaje'] ?? '');
    $link = trim($_POST['link'] ?? '');

    if (empty($mensaje)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'El mensaje de la notificación no puede estar vacío.'];
    } else {
        try {
            if ($destinatario === 'todos') {
                // Obtenemos los IDs de todos los usuarios
                $stmt = $pdo->query("SELECT id FROM usuarios");
                $usuarios_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                $notificaciones_enviadas = 0;
                foreach ($usuarios_ids as $usuario_id) {
                    if (crear_notificacion($pdo, $usuario_id, $mensaje, $link)) {
                        $notificaciones_enviadas++;
                    }
                }
                $_SESSION['message'] = ['type' => 'success', 'text' => "Notificación enviada con éxito a {$notificaciones_enviadas} usuarios."];
            }
            // (Futuro) Aquí se podría añadir lógica para enviar a un usuario específico
        } catch (PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al enviar las notificaciones.'];
        }
    }
    header('Location: notificaciones.php');
    exit();
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
$page_title = 'Enviar Notificaciones';

// --- FASE 3: VISUALIZACIÓN ---
require '../header.php'; 
?>

<!-- Contenido HTML de la página -->
<h2 class="mb-4">Enviar Notificaciones a Usuarios</h2>
<a href="index.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al Panel</a>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Crear Nueva Notificación</h5>
        <p class="card-text text-muted">El mensaje que envíes aparecerá en la campana de notificaciones de los usuarios seleccionados.</p>
        <form action="notificaciones.php" method="POST">
            <div class="mb-3">
                <label for="destinatario" class="form-label">Destinatario</label>
                <select name="destinatario" id="destinatario" class="form-select">
                    <option value="todos">Todos los Usuarios</option>
                    <!-- (Futuro) Se podría añadir un campo de búsqueda para usuarios específicos -->
                </select>
            </div>
            <div class="mb-3">
                <label for="mensaje" class="form-label">Mensaje</label>
                <textarea name="mensaje" id="mensaje" class="form-control" rows="4" required placeholder="Escribe aquí el mensaje que quieres enviar..."></textarea>
            </div>
            <div class="mb-3">
                <label for="link" class="form-label">Enlace (Opcional)</label>
                <input type="text" name="link" id="link" class="form-control" placeholder="Ej: guias.php">
                <small class="form-text text-muted">El enlace es relativo a la raíz del sitio. Si un usuario hace clic en la notificación, será dirigido aquí.</small>
            </div>
            <button type="submit" name="enviar_notificacion" class="btn btn-primary">
                <i class="bi bi-send-fill me-2"></i>Enviar Notificación
            </button>
        </form>
    </div>
</div>

<?php
// Finalmente, incluimos el pie de página.
require '../footer.php';
?>
