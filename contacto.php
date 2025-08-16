<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
// Incluimos la conexión ANTES de cualquier cosa para poder usar la sesión y la BD.
require 'conexion.php';

// Protección: si el usuario no ha iniciado sesión, no puede estar aquí.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lógica para procesar el envío del formulario de contacto.
// Este bloque se ejecuta ANTES de mostrar cualquier HTML.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos los datos del formulario de forma segura.
    $nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $asunto = trim(filter_input(INPUT_POST, 'asunto', FILTER_SANITIZE_STRING));
    $mensaje = trim(filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING));

    // Validación simple.
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Por favor, completa todos los campos del formulario.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Por favor, introduce una dirección de correo electrónico válida.'];
    } else {
        // --- CONFIGURACIÓN DEL CORREO ---
        $destinatario = "yefferson.toro5399@ucaldas.edu.co";
        $asunto_email = "Nuevo Mensaje de Contacto: " . $asunto;
        
        $cuerpo_email = "Has recibido un nuevo mensaje desde el formulario de contacto de la Enciclopedia de Artrópodos.\n\n";
        $cuerpo_email .= "--------------------------------------------------\n";
        $cuerpo_email .= "Nombre: " . $nombre . "\n";
        $cuerpo_email .= "Email: " . $email . "\n";
        $cuerpo_email .= "Asunto: " . $asunto . "\n";
        $cuerpo_email .= "--------------------------------------------------\n\n";
        $cuerpo_email .= "Mensaje:\n" . $mensaje . "\n";

        // Cabeceras del correo.
        $headers = "From: " . $nombre . " <" . $email . ">\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Envío del correo.
        // @ suprime el warning en entornos locales donde el envío de correo no está configurado.
        if (@mail($destinatario, $asunto_email, $cuerpo_email, $headers)) {
            $_SESSION['message'] = ['type' => 'success', 'text' => '¡Gracias por tu mensaje! Nos pondremos en contacto contigo pronto.'];
        } else {
            // En un servidor real, esto indicaría un problema. En local, es el comportamiento esperado.
            $_SESSION['message'] = ['type' => 'warning', 'text' => 'El servidor de correo local no está configurado. Tu mensaje no pudo ser enviado, pero la funcionalidad es correcta.'];
        }

        // Redirigimos para evitar reenvíos del formulario al recargar.
        header('Location: contacto.php');
        exit();
    }
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
$page_title = 'Contacto';

// --- FASE 3: VISUALIZACIÓN ---
// Ahora que toda la lógica ha terminado, incluimos el header.
require 'header.php';
?>

<!-- Contenido específico de la página de Contacto -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 class="display-5">Ponte en Contacto</h1>
                <p class="lead text-muted">¿Tienes preguntas, sugerencias o necesitas reportar un problema? Rellena el formulario y te responderemos lo antes posible.</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <form action="contacto.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Tu Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Tu Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="asunto" name="asunto" required>
                        </div>
                        <div class="mb-4">
                            <label for="mensaje" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="6" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                <i class="bi bi-send-fill me-2"></i>Enviar Mensaje
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
