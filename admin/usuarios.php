<?php
$page_title = 'Gestionar Usuarios';
require '../header.php';
require '_proteccion.php';

$stmt = $pdo->query("SELECT id, nombre, email, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC");
$usuarios = $stmt->fetchAll();
?>

<h2 class="mb-4">Usuarios Registrados</h2>
<a href="index.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td>
                    <span class="badge <?php echo $usuario['rol'] == 'admin' ? 'badge-danger' : 'badge-secondary'; ?>">
                        <?php echo $usuario['rol']; ?>
                    </span>
                </td>
                <td><?php echo $usuario['fecha_registro']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require '../footer.php';
?>