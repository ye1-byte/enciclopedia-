<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
// Incluimos los archivos de conexión y protección ANTES de cualquier salida HTML.
require '../conexion.php'; 
require '_proteccion.php';

// Lógica para cambiar el estado de una sugerencia.
// Este bloque se ejecuta solo si se ha enviado el formulario de cambio de estado.
if (isset($_POST['cambiar_estado'])) {
    $sugerencia_id = $_POST['sugerencia_id'];
    $nuevo_estado = $_POST['nuevo_estado'];
    
    // Lista de estados válidos para seguridad.
    $estados_permitidos = ['Recibida', 'En Revisión', 'Aceptada', 'Implementada', 'Rechazada'];

    if (in_array($nuevo_estado, $estados_permitidos)) {
        try {
            $stmt = $pdo->prepare("UPDATE sugerencias SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevo_estado, $sugerencia_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'El estado de la sugerencia ha sido actualizado.'];
        } catch (PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al actualizar la sugerencia.'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Estado no válido.'];
    }
    // Redirigimos para evitar reenvíos y mostrar el mensaje de feedback.
    header('Location: sugerencias.php');
    exit();
}

// Lógica para eliminar una sugerencia
if (isset($_POST['eliminar_sugerencia'])) {
    $sugerencia_id = $_POST['sugerencia_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM sugerencias WHERE id = ?");
        $stmt->execute([$sugerencia_id]);
        $_SESSION['message'] = ['type' => 'info', 'text' => 'La sugerencia ha sido eliminada correctamente.'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al eliminar la sugerencia.'];
    }
    header('Location: sugerencias.php');
    exit();
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
// Hacemos un JOIN para obtener el nombre del usuario que hizo la sugerencia.
$stmt = $pdo->query(
    "SELECT s.*, u.nombre as nombre_usuario
     FROM sugerencias s
     JOIN usuarios u ON s.usuario_id = u.id
     ORDER BY s.fecha_sugerencia DESC"
);
$sugerencias = $stmt->fetchAll();

// Definimos el título de la página.
$page_title = 'Gestionar Sugerencias';

// --- FASE 3: VISUALIZACIÓN ---
// Ahora que toda la lógica ha terminado, incluimos el header para empezar a dibujar la página.
require '../header.php'; 
?>

<!-- Contenido HTML de la página -->
<h2 class="mb-4">Gestionar Sugerencias de la Comunidad</h2>
<a href="index.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al Panel</a>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($sugerencias)): ?>
                <tr><td colspan="6" class="text-center">No hay sugerencias para mostrar.</td></tr>
            <?php else: ?>
                <?php foreach ($sugerencias as $sugerencia): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($sugerencia['fecha_sugerencia'])); ?></td>
                    <td><?php echo htmlspecialchars($sugerencia['nombre_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($sugerencia['titulo']); ?></td>
                    <td style="min-width: 250px;"><?php echo nl2br(htmlspecialchars($sugerencia['descripcion'])); ?></td>
                    <td>
                        <?php
                            $estado_clase = 'bg-secondary';
                            if ($sugerencia['estado'] == 'En Revisión') $estado_clase = 'bg-info';
                            if ($sugerencia['estado'] == 'Aceptada') $estado_clase = 'bg-primary';
                            if ($sugerencia['estado'] == 'Implementada') $estado_clase = 'bg-success';
                            if ($sugerencia['estado'] == 'Rechazada') $estado_clase = 'bg-danger';
                        ?>
                        <span class="badge <?php echo $estado_clase; ?>"><?php echo htmlspecialchars($sugerencia['estado']); ?></span>
                    </td>
                    <td style="min-width: 220px;">
                        <!-- Formulario para cambiar estado -->
                        <form action="sugerencias.php" method="POST" class="d-flex mb-2">
                            <input type="hidden" name="sugerencia_id" value="<?php echo $sugerencia['id']; ?>">
                            <select name="nuevo_estado" class="form-select form-select-sm me-2">
                                <option value="Recibida">Recibida</option>
                                <option value="En Revisión">En Revisión</option>
                                <option value="Aceptada">Aceptada</option>
                                <option value="Implementada">Implementada</option>
                                <option value="Rechazada">Rechazada</option>
                            </select>
                            <button type="submit" name="cambiar_estado" class="btn btn-primary btn-sm" title="Actualizar Estado">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                        <!-- Formulario para eliminar -->
                        <form action="sugerencias.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta sugerencia de forma permanente?');">
                            <input type="hidden" name="sugerencia_id" value="<?php echo $sugerencia['id']; ?>">
                            <button type="submit" name="eliminar_sugerencia" class="btn btn-danger btn-sm w-100">
                                <i class="bi bi-trash me-2"></i>Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Finalmente, incluimos el pie de página.
require '../footer.php';
?>
