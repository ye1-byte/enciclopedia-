<?php
// Título de la página.
$page_title = 'Portal Principal';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtenemos el nombre del usuario para un saludo personalizado.
$user_name = $_SESSION['user_name'] ?? 'Usuario';
?>

<!-- Contenido específico de la página del Portal -->
<div class="container mt-4">

    <!-- Saludo personalizado -->
    <div class="text-center mb-5">
        <h1 class="display-5">Bienvenido al Portal, <?php echo htmlspecialchars($user_name); ?></h1>
        <p class="lead text-muted">Desde aquí puedes acceder a todas las secciones de la enciclopedia.</p>
    </div>

    <!-- Cuadrícula de tarjetas de navegación -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        <!-- Tarjeta 1: Enciclopedia -->
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <i class="bi bi-book-half display-3 text-primary mb-3"></i>
                    <h5 class="card-title">Enciclopedia</h5>
                    <p class="card-text small text-muted flex-grow-1">Explora, busca y añade nuevos registros a la colección principal.</p>
                    <a href="index.php" class="btn btn-primary mt-auto"><i class="bi bi-list-ul me-2"></i>Ver Colección</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Mi Actividad -->
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <i class="bi bi-person-badge display-3 text-success mb-3"></i>
                    <h5 class="card-title">Mi Actividad</h5>
                    <p class="card-text small text-muted flex-grow-1">Gestiona tu perfil, tus contribuciones y tu configuración.</p>
                    <a href="registros.php" class="btn btn-success mt-auto"><i class="bi bi-journal-text me-2"></i>Mis Registros</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Comunidad -->
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <i class="bi bi-people-fill display-3 text-info mb-3"></i>
                    <h5 class="card-title">Comunidad</h5>
                    <p class="card-text small text-muted flex-grow-1">Participa, envía sugerencias y reporta problemas.</p>
                    <a href="sugerencias.php" class="btn btn-info mt-auto"><i class="bi bi-lightbulb me-2"></i>Sugerencias</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Recursos (CORREGIDA) -->
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <i class="bi bi-mortarboard-fill display-3 text-warning mb-3"></i>
                    <h5 class="card-title">Recursos</h5>
                    <p class="card-text small text-muted flex-grow-1">Consulta guías, glosario, bibliografía y más herramientas de aprendizaje.</p>
                    <div class="list-group list-group-flush mt-auto">
                        <a href="guias.php" class="list-group-item list-group-item-action"><i class="bi bi-book me-2"></i>Guías de Campo</a>
                        <a href="glosario.php" class="list-group-item list-group-item-action"><i class="bi bi-translate me-2"></i>Glosario</a>
                        <a href="bibliografia.php" class="list-group-item list-group-item-action"><i class="bi bi-journals me-2"></i>Bibliografía</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta 5: Galería Visual -->
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <i class="bi bi-images display-3 text-danger mb-3"></i>
                    <h5 class="card-title">Galería Visual</h5>
                    <p class="card-text small text-muted flex-grow-1">Explora todos los registros a través de sus imágenes.</p>
                    <a href="galeria.php" class="btn btn-danger mt-auto"><i class="bi bi-camera me-2"></i>Ir a la Galería</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 6: Datos ICA -->
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <i class="bi bi-bar-chart-line-fill display-3 text-secondary mb-3"></i>
                    <h5 class="card-title">Datos Fitosanitarios</h5>
                    <p class="card-text small text-muted flex-grow-1">Consulta el tablero de control interactivo del ICA.</p>
                    <a href="explorar.php" class="btn btn-secondary mt-auto"><i class="bi bi-graph-up me-2"></i>Ver Dashboard</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
