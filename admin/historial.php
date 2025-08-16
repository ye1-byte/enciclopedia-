<?php
$page_title = 'Historial de Cambios';
require '../header.php';
require '_proteccion.php';

// Hacemos un JOIN para obtener el nombre del usuario junto con el historial
$stmt = $pdo->query(
    "SELECT h.id, h.accion, h.descripcion, h.fecha, u.nombre 
     FROM historial h
     JOIN usuarios u ON h.usuario_id = u.id
     ORDER BY h.fecha DESC"
);
$logs = $stmt->fetchAll();
?>

<h2 class="mb-4">Historial de Cambios</h2>
<a href="index.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo $log['fecha']; ?></td>
                <td><?php echo htmlspecialchars($log['nombre']); ?></td>
                <td><span class="badge badge-info"><?php echo $log['accion']; ?></span></td>
                <td><?php echo htmlspecialchars($log['descripcion']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require '../footer.php';
?>