<?php
// Título de la página.
$page_title = 'Configuración de la Cuenta';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// --- LÓGICA PARA ACTUALIZAR LA CONFIGURACIÓN ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Los checkboxes no se envían si no están marcados, por eso verificamos si existen.
    // Convertimos la existencia del checkbox a un valor booleano (1 para sí, 0 para no).
    $recibir_notificaciones = isset($_POST['recibir_notificaciones']) ? 1 : 0;
    $perfil_publico = isset($_POST['perfil_publico']) ? 1 : 0;

    try {
        // Preparamos la consulta para actualizar las preferencias del usuario.
        $sql = "UPDATE usuarios SET recibir_notificaciones = ?, perfil_publico = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$recibir_notificaciones, $perfil_publico, $user_id]);

        // Guardamos un mensaje de éxito en la sesión.
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Tu configuración ha sido guardada con éxito.'];

    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hubo un error al guardar tu configuración.'];
        // Para depuración: error_log($e->getMessage());
    }

    // Redirigimos a la misma página para evitar reenvíos del formulario.
    header('Location: configuracion.php');
    exit();
}

// --- OBTENER LA CONFIGURACIÓN ACTUAL DEL USUARIO ---
try {
    $stmt = $pdo->prepare("SELECT recibir_notificaciones, perfil_publico FROM usuarios WHERE id = ?");
    $stmt->execute([$user_id]);
    $configuracion = $stmt->fetch();
} catch (PDOException $e) {
    // En caso de error, usamos valores por defecto para no romper la página.
    $configuracion = ['recibir_notificaciones' => 1, 'perfil_publico' => 1];
}
?>

<!-- Contenido específico de la página de Configuración -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 class="display-5">Configuración</h1>
                <p class="lead text-muted">Personaliza tu experiencia en la enciclopedia y gestiona tus preferencias de privacidad.</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <form action="configuracion.php" method="POST">
                        <h5 class="card-title mb-4">Preferencias de Notificaciones</h5>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="recibir_notificaciones" name="recibir_notificaciones" <?php if ($configuracion['recibir_notificaciones']) echo 'checked'; ?>>
                            <label class="form-check-label" for="recibir_notificaciones">
                                Recibir notificaciones por correo electrónico
                                <small class="d-block text-muted">Recibe un email cuando haya actividad relevante, como comentarios en tus registros o anuncios importantes.</small>
                            </label>
                        </div>

                        <hr>

                        <h5 class="card-title mt-4 mb-4">Configuración de Privacidad</h5>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="perfil_publico" name="perfil_publico" <?php if ($configuracion['perfil_publico']) echo 'checked'; ?>>
                            <label class="form-check-label" for="perfil_publico">
                                Perfil público
                                <small class="d-block text-muted">Permite que otros usuarios vean tu nombre y tus contribuciones en una página de perfil pública.</small>
                            </label>
                        </div>
                        
                        <hr>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                <i class="bi bi-save-fill me-2"></i>Guardar Configuración
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
