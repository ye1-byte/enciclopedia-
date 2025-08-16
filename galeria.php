<?php
// Título de la página.
$page_title = 'Galería de Imágenes';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lógica para obtener todas las imágenes de la base de datos.
// Seleccionamos solo los registros que tienen un nombre de imagen no vacío.
try {
    $stmt = $pdo->query("SELECT id, arthropodo, imagen FROM artropodos WHERE imagen IS NOT NULL AND imagen != '' ORDER BY id DESC");
    $imagenes = $stmt->fetchAll();
} catch (PDOException $e) {
    // En caso de error, creamos un array vacío para no romper la página.
    $imagenes = [];
    // Podríamos registrar el error si quisiéramos: error_log($e->getMessage());
}
?>

<!-- Contenido específico de la página de Galería -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Galería Visual</h1>
        <p class="lead text-muted">Una colección de todas las imágenes aportadas por la comunidad.</p>
    </div>

    <?php if (empty($imagenes)): ?>
        <!-- Mensaje si no hay imágenes en la base de datos -->
        <div class="text-center p-5 border rounded bg-white">
            <i class="bi bi-camera display-1 text-secondary opacity-50"></i>
            <h3 class="mt-3">La galería está vacía</h3>
            <p class="lead text-muted">
                Añade nuevos registros con imágenes para empezar a construir esta colección visual.
            </p>
            <a href="formulario.php" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-2"></i>Añadir un registro
            </a>
        </div>
    <?php else: ?>
        <!-- Cuadrícula de imágenes responsiva -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($imagenes as $img): ?>
                <?php $imageUrl = 'uploads/' . htmlspecialchars($img['imagen']); ?>
                <?php if (file_exists($imageUrl)): // Comprobamos que el archivo de imagen realmente exista ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm gallery-card">
                            <a href="#" 
                               data-bs-toggle="modal" 
                               data-bs-target="#imageModal"
                               data-bs-image-url="<?php echo $imageUrl; ?>"
                               data-bs-image-title="<?php echo htmlspecialchars($img['arthropodo']); ?>">
                                <img src="<?php echo $imageUrl; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($img['arthropodo']); ?>" style="height: 200px; object-fit: cover;">
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
