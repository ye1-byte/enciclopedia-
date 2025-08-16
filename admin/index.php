<?php
$page_title = 'Panel de Administración';
// Usamos ../ para salir de la carpeta /admin y encontrar los archivos de plantilla
require '../header.php'; 
require '_proteccion.php'; // Protección de la sección de administración
?>

<h1 class="mb-4">Panel de Administración</h1>
<p>Desde aquí puedes gestionar los usuarios, ver el historial de cambios y revisar el feedback de la comunidad.</p>

<div class="row">
    <!-- Columna de Gestión de Contenido -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Gestión de Contenido</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="historial.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-history me-2"></i>Ver Historial de Cambios
                </a>
                <a href="<?php echo BASE_URL; ?>index.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-pencil-square me-2"></i>Gestionar Registros de Artrópodos
                </a>
            </div>
        </div>
    </div>

    <!-- Columna de Gestión de Comunidad -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Gestión de Comunidad</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="usuarios.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-people-fill me-2"></i>Gestionar Usuarios
                </a>
                <!-- ***** ENLACE AÑADIDO AQUÍ ***** -->
                <a href="notificaciones.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-broadcast me-2"></i>Enviar Notificaciones
                </a>
                <a href="reportes.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-flag-fill me-2"></i>Gestionar Reportes
                </a>
                <a href="sugerencias.php" class="list-group-item list-group-item-action">
                    <i class="bi bi-lightbulb-fill me-2"></i>Gestionar Sugerencias
                </a>
            </div>
        </div>
    </div>
</div>

<a href="<?php echo BASE_URL; ?>portal.php" class="btn btn-secondary mt-3">
    <i class="bi bi-arrow-left me-2"></i>Volver al Portal
</a>

<?php
require '../footer.php';
?>
