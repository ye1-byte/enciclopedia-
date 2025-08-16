<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
// Incluimos los archivos de conexión y protección ANTES de cualquier salida HTML.
require '../conexion.php'; 
require '_proteccion.php';

// Lógica para cambiar el estado de un reporte.
// Este bloque se ejecuta solo si se ha enviado una acción por la URL.
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $reporte_id = $_GET['id'];
    $nuevo_estado = '';

    if ($action === 'resolver') {
        $nuevo_estado = 'Resuelto';
    } elseif ($action === 'pendiente') {
        $nuevo_estado = 'Pendiente';
    }

    if ($nuevo_estado) {
        try {
            $stmt = $pdo->prepare("UPDATE reportes SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevo_estado, $reporte_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'El estado del reporte ha sido actualizado.'];
        } catch (PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al actualizar el reporte.'];
        }
    }
    // Redirigimos para limpiar la URL y mostrar el mensaje de feedback.
    header('Location: reportes.php');
    exit();
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
// Hacemos un JOIN para obtener el nombre del usuario que reportó.
$stmt = $pdo->query(
    "SELECT r.*, u.nombre as nombre_usuario, u.email as email_usuario
     FROM reportes r
     JOIN usuarios u ON r.usuario_id = u.id
     ORDER BY r.fecha_reporte DESC"
);
$reportes = $stmt->fetchAll();

// Definimos el título de la página.
$page_title = 'Gestionar Reportes';

// --- FASE 3: VISUALIZACIÓN ---
// Ahora que toda la lógica ha terminado, incluimos el header.
require '../header.php'; 
?>

<!-- Contenido HTML de la página -->
<h2 class="mb-4">Gestionar Reportes de Usuarios</h2>
<a href="index.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al Panel</a>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>URL</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reportes)): ?>
                <tr><td colspan="7" class="text-center">No hay reportes para mostrar.</td></tr>
            <?php else: ?>
                <?php foreach ($reportes as $reporte): ?>
                <tr>
                    <td>
                        <?php if ($reporte['estado'] == 'Pendiente'): ?>
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        <?php else: ?>
                            <span class="badge bg-success">Resuelto</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($reporte['fecha_reporte'])); ?></td>
                    <td>
                        <?php echo htmlspecialchars($reporte['nombre_usuario']); ?><br>
                        <small class="text-muted"><?php echo htmlspecialchars($reporte['email_usuario']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($reporte['tipo_reporte']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($reporte['descripcion'])); ?></td>
                    <td>
                        <?php if (!empty($reporte['url_problema'])): ?>
                            <a href="<?php echo htmlspecialchars($reporte['url_problema']); ?>" target="_blank" title="<?php echo htmlspecialchars($reporte['url_problema']); ?>">
                                Ver enlace
                            </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($reporte['estado'] == 'Pendiente'): ?>
                            <a href="reportes.php?action=resolver&id=<?php echo $reporte['id']; ?>" class="btn btn-success btn-sm" title="Marcar como Resuelto">
                                <i class="bi bi-check-lg"></i>
                            </a>
                        <?php else: ?>
                            <a href="reportes.php?action=pendiente&id=<?php echo $reporte['id']; ?>" class="btn btn-secondary btn-sm" title="Marcar como Pendiente">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        <?php endif; ?>
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
