<?php
// Título de la página.
$page_title = 'Explorador de Taxonomía';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// (Futuro) Aquí iría la lógica para obtener datos taxonómicos de la base de datos,
// posiblemente de tablas dedicadas para órdenes, familias, géneros, etc.
?>

<!-- Contenido específico de la página de Taxonomía -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Explorador Taxonómico</h1>
        <p class="lead text-muted">Navega por el árbol de la vida de los artrópodos.</p>
    </div>

    <!-- Marcador de posición para el contenido futuro -->
    <div class="text-center p-5 border rounded bg-white">
        <i class="bi bi-diagram-3 display-1 text-primary opacity-50"></i>
        <h3 class="mt-3">Clasificación en Desarrollo</h3>
        <p class="lead text-muted">
            Estamos construyendo una herramienta para explorar la jerarquía taxonómica desde el reino hasta la especie.
        </p>
        <p>
            Próximamente podrás visualizar las relaciones entre diferentes grupos de artrópodos.
        </p>
        <a href="index.php" class="btn btn-primary mt-3">
            <i class="bi bi-house-door me-2"></i>Volver al Inicio
        </a>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
