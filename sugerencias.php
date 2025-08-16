<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
require 'conexion.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lógica para procesar el envío de una nueva sugerencia.
// Este bloque se ejecuta ANTES de mostrar cualquier HTML.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_sugerencia'])) {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $usuario_id = $_SESSION['user_id'];

    if (empty($titulo) || empty($descripcion)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Por favor, completa el título y la descripción de tu sugerencia.'];
    } else {
        try {
            $sql = "INSERT INTO sugerencias (usuario_id, titulo, descripcion) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id, $titulo, $descripcion]);
            $_SESSION['message'] = ['type' => 'success', 'text' => '¡Gracias por tu sugerencia! La hemos recibido para su revisión.'];
        } catch (PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hubo un error al enviar tu sugerencia.'];
        }
    }
    // Redirigimos para evitar reenvíos del formulario.
    header('Location: sugerencias.php');
    exit();
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
$page_title = 'Buzón de Sugerencias';

// Lógica para obtener todas las sugerencias y el nombre de quien las envió.
try {
    $stmt = $pdo->query(
        "SELECT s.*, u.nombre as nombre_usuario
         FROM sugerencias s
         JOIN usuarios u ON s.usuario_id = u.id
         ORDER BY s.fecha_sugerencia DESC"
    );
    $sugerencias = $stmt->fetchAll();
} catch (PDOException $e) {
    $sugerencias = [];
}

// --- FASE 3: VISUALIZACIÓN ---
// Ahora que toda la lógica ha terminado, incluimos el header.
require 'header.php';
?>

<!-- Contenido específico de la página de Sugerencias -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Buzón de Sugerencias</h1>
        <p class="lead text-muted">¿Tienes una idea para mejorar la enciclopedia? ¡Compártela con la comunidad!</p>
    </div>

    <div class="row">
        <!-- Columna para enviar sugerencias -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-lightbulb-fill me-2"></i>Enviar una Nueva Idea</h5>
                    <form action="sugerencias.php" method="POST">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título de la idea</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Describe tu sugerencia</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="nueva_sugerencia" class="btn btn-primary w-100">
                            <i class="bi bi-send me-2"></i>Enviar Sugerencia
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna para ver las sugerencias existentes -->
        <div class="col-lg-8">
            <h4>Sugerencias de la Comunidad</h4>
            <?php if (empty($sugerencias)): ?>
                <div class="alert alert-secondary text-center">
                    <i class="bi bi-chat-quote-fill fs-3 d-block mb-2"></i>
                    Aún no hay sugerencias. ¡Sé el primero en proponer una idea!
                </div>
            <?php else: ?>
                <?php foreach ($sugerencias as $sugerencia): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($sugerencia['titulo']); ?></h5>
                                <?php
                                    $estado_clase = 'bg-secondary';
                                    if ($sugerencia['estado'] == 'En Revisión') $estado_clase = 'bg-info';
                                    if ($sugerencia['estado'] == 'Aceptada') $estado_clase = 'bg-primary';
                                    if ($sugerencia['estado'] == 'Implementada') $estado_clase = 'bg-success';
                                    if ($sugerencia['estado'] == 'Rechazada') $estado_clase = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $estado_clase; ?>"><?php echo htmlspecialchars($sugerencia['estado']); ?></span>
                            </div>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($sugerencia['descripcion'])); ?></p>
                            <small class="text-muted">
                                Sugerido por <strong><?php echo htmlspecialchars($sugerencia['nombre_usuario']); ?></strong> el <?php echo date('d/m/Y', strtotime($sugerencia['fecha_sugerencia'])); ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
