<?php
// Título de la página.
$page_title = 'Mis Registros';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtenemos el ID del usuario actual desde la sesión.
$current_user_id = $_SESSION['user_id'];

// Preparamos y ejecutamos una consulta para obtener SOLO los registros
// creados por el usuario actual.
$stmt = $pdo->prepare("SELECT * FROM artropodos WHERE usuario_id = ? ORDER BY arthropodo");
$stmt->execute([$current_user_id]);
$mis_registros = $stmt->fetchAll();
?>

<!-- Contenido específico de la página Mis Registros -->
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5">Mis Registros</h1>
        <a href="formulario.php" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-lg me-2"></i>Añadir Nuevo
        </a>
    </div>

    <?php if (empty($mis_registros)): ?>
        <!-- Mensaje si el usuario no ha creado ningún registro -->
        <div class="text-center p-5 border rounded bg-white">
            <i class="bi bi-journal-x display-1 text-secondary opacity-50"></i>
            <h3 class="mt-3">Aún no has añadido registros</h3>
            <p class="lead text-muted">
                ¡Anímate a ser el primero en contribuir! Tus aportes son valiosos para la comunidad.
            </p>
            <a href="formulario.php" class="btn btn-success mt-3">
                <i class="bi bi-plus-circle me-2"></i>Crear mi primer registro
            </a>
        </div>
    <?php else: ?>
        <!-- Mostramos los registros en una cuadrícula de tarjetas -->
        <div class="row">
            <?php foreach ($mis_registros as $registro): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($registro['imagen']) && file_exists('uploads/' . $registro['imagen'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($registro['imagen']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($registro['arthropodo']); ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($registro['arthropodo']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($registro['orden_familia']); ?></h6>
                            <p class="card-text small">
                                <?php 
                                    // Mostramos un extracto de la biología
                                    $extracto = htmlspecialchars($registro['biologia_caracteristicas']);
                                    echo strlen($extracto) > 100 ? substr($extracto, 0, 100) . '...' : $extracto;
                                ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white text-end">
                            <a href="formulario.php?id=<?php echo $registro['id']; ?>" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <!-- (Opcional) Podrías añadir un botón de "Ver Detalles" que lleve a una página individual -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
