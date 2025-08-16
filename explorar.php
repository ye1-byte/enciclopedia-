<?php
// Título de la página.
$page_title = 'Explorador de Datos ICA';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!-- Contenido específico de la página Explorar -->
<div class="container-fluid mt-4">
    <div class="text-center mb-4">
        <h1 class="display-5">Visualizador de Datos Fitosanitarios</h1>
        <p class="lead text-muted">
            Explora el tablero de control oficial del ICA directamente desde nuestra plataforma.
        </p>
    </div>

    <!-- Contenedor para el iframe -->
    <div class="card shadow-sm">
        <div class="card-body p-2">
            <!-- Iframe para incrustar el dashboard de Power BI -->
            <iframe 
                title="Reporte Fitosanitario ICA" 
                width="100%" 
                height="800" 
                src="https://app.powerbi.com/view?r=eyJrIjoiNDRmNWU1ZjAtZjM2ZC00NzhjLWE5YTctMGI5ZjQ4YTc5ZWVkIiwidCI6ImI3YWVkYTBjLTY0Y2QtNDlkMi05YTRkLTMwNjIzNjc0MzJlMyIsImMiOjR9" 
                frameborder="0" 
                allowFullScreen="true"
                style="border-radius: 5px;">
            </iframe>
        </div>
        <div class="card-footer text-center text-muted small">
            Datos proporcionados por el Instituto Colombiano Agropecuario (ICA).
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
