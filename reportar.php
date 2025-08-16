<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
// Incluimos la conexión ANTES de cualquier cosa para poder usar la sesión y la BD.
require 'conexion.php';

// Protección: si el usuario no ha iniciado sesión, no puede estar aquí.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lógica para procesar el envío del formulario de reporte.
// Este bloque se ejecuta ANTES de mostrar cualquier HTML.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos los datos del formulario.
    $tipo_reporte = $_POST['tipo_reporte'] ?? '';
    $url_problema = $_POST['url_problema'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $usuario_id = $_SESSION['user_id'];

    // Validación simple.
    if (empty($tipo_reporte) || empty($descripcion)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Por favor, selecciona un tipo de reporte y proporciona una descripción.'];
    } else {
        // Si los datos son válidos, los insertamos en la base de datos.
        try {
            $sql = "INSERT INTO reportes (usuario_id, tipo_reporte, url_problema, descripcion) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id, $tipo_reporte, $url_problema, $descripcion]);

            $_SESSION['message'] = ['type' => 'success', 'text' => '¡Gracias por tu reporte! Lo revisaremos lo antes posible.'];
        } catch (PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hubo un error al enviar tu reporte. Por favor, inténtalo de nuevo.'];
        }
    }
    // Redirigimos para evitar reenvíos del formulario.
    header('Location: reportar.php');
    exit();
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
$page_title = 'Reportar un Problema';

// --- FASE 3: VISUALIZACIÓN ---
// Ahora que toda la lógica ha terminado, incluimos el header.
require 'header.php';
?>

<!-- Contenido específico de la página de Reportar Problema -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 class="display-5">Reportar un Problema</h1>
                <p class="lead text-muted">¿Has encontrado un error en los datos, una imagen incorrecta o un fallo técnico? Tu ayuda es fundamental para mantener la calidad de la enciclopedia.</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <form action="reportar.php" method="POST">
                        <div class="mb-4">
                            <label for="tipo_reporte" class="form-label"><strong>Tipo de Problema</strong></label>
                            <select class="form-select" id="tipo_reporte" name="tipo_reporte" required>
                                <option value="" selected disabled>-- Selecciona una categoría --</option>
                                <option value="Error de Datos">Error de Datos (ej. clasificación incorrecta)</option>
                                <option value="Problema con Imagen">Problema con una Imagen (ej. borrosa, incorrecta)</option>
                                <option value="Fallo Técnico">Fallo Técnico (ej. un botón no funciona)</option>
                                <option value="Sugerencia">Sugerencia de Mejora</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="url_problema" class="form-label"><strong>URL del Problema (Opcional)</strong></label>
                            <input type="url" class="form-control" id="url_problema" name="url_problema" placeholder="Pega aquí el enlace de la página con el error">
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label"><strong>Descripción Detallada</strong></label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="6" placeholder="Por favor, describe el problema con el mayor detalle posible." required></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                <i class="bi bi-send-fill me-2"></i>Enviar Reporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
