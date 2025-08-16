<?php
// Título de la página.
$page_title = 'Estadísticas de la Comunidad';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lógica para obtener las estadísticas de la base de datos.
try {
    // Contar total de registros de artrópodos
    $stmt_registros = $pdo->query("SELECT COUNT(*) FROM artropodos");
    $total_registros = $stmt_registros->fetchColumn();

    // Contar total de usuarios registrados
    $stmt_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $total_usuarios = $stmt_usuarios->fetchColumn();
    
    // Contar total de familias distintas (como ejemplo de dato agrupado)
    $stmt_familias = $pdo->query("SELECT COUNT(DISTINCT orden_familia) FROM artropodos");
    $total_familias = $stmt_familias->fetchColumn();

} catch (PDOException $e) {
    // En caso de error, inicializamos las variables para no romper la página.
    $total_registros = 0;
    $total_usuarios = 0;
    $total_familias = 0;
    // Podríamos guardar un mensaje de error aquí si quisiéramos.
}
?>

<!-- Contenido específico de la página de Estadísticas -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Estadísticas de la Enciclopedia</h1>
        <p class="lead text-muted">Un vistazo a los datos y contribuciones de nuestra comunidad.</p>
    </div>

    <!-- Tarjetas de estadísticas principales -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-bug-fill display-3 text-success"></i>
                    <h2 class="card-title display-4 fw-bold my-3"><?php echo number_format($total_registros); ?></h2>
                    <p class="card-text text-muted">Registros de Artrópodos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people-fill display-3 text-primary"></i>
                    <h2 class="card-title display-4 fw-bold my-3"><?php echo number_format($total_usuarios); ?></h2>
                    <p class="card-text text-muted">Usuarios Registrados</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-diagram-3-fill display-3 text-warning"></i>
                    <h2 class="card-title display-4 fw-bold my-3"><?php echo number_format($total_familias); ?></h2>
                    <p class="card-text text-muted">Familias Diferentes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección para futuros gráficos -->
    <div class="mt-5 text-center p-5 border rounded bg-white">
        <i class="bi bi-bar-chart-line display-1 text-secondary opacity-50"></i>
        <h3 class="mt-3">Próximamente: Gráficos Interactivos</h3>
        <p class="lead text-muted">
            Estamos trabajando en visualizaciones de datos para mostrar la distribución de registros, los usuarios más activos y mucho más.
        </p>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
