<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
// Incluimos la conexión ANTES de cualquier cosa para poder usar la sesión y la BD.
require 'conexion.php';

// Protección: si el usuario no ha iniciado sesión, no puede estar aquí.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_user_role = $_SESSION['user_role'] ?? 'usuario'; 

// Lógica para procesar la subida de un nuevo PDF.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['pdf_file'])) {
    $titulo = $_POST['titulo'] ?? 'Sin Título';
    $uploadDir = 'uploads/guides/';

    // Validación del archivo
    if ($_FILES['pdf_file']['error'] == 0) {
        if ($_FILES['pdf_file']['type'] == 'application/pdf') {
            if ($_FILES['pdf_file']['size'] <= 10 * 1024 * 1024) { // 10 MB
                $extension = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
                $nombreArchivo = uniqid() . '.' . $extension;
                $uploadFile = $uploadDir . $nombreArchivo;

                if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $uploadFile)) {
                    try {
                        $sql = "INSERT INTO guias (usuario_id, titulo, nombre_archivo) VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$current_user_id, $titulo, $nombreArchivo]);
                        $_SESSION['message'] = ['type' => 'success', 'text' => '¡Guía subida con éxito!'];
                    } catch (PDOException $e) {
                        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al guardar la guía en la base de datos.'];
                    }
                } else {
                    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al mover el archivo subido.'];
                }
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'El archivo no puede superar los 10 MB.'];
            }
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Solo se permiten archivos en formato PDF.'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hubo un error al subir el archivo.'];
    }
    header('Location: guias.php');
    exit();
}

// Lógica para eliminar una guía
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_guide'])) {
    $guia_id = $_POST['guia_id'];

    $stmt = $pdo->prepare("SELECT usuario_id, nombre_archivo FROM guias WHERE id = ?");
    $stmt->execute([$guia_id]);
    $guia = $stmt->fetch();

    if ($guia && ($guia['usuario_id'] == $current_user_id || $current_user_role === 'admin')) {
        try {
            $filePath = 'uploads/guides/' . $guia['nombre_archivo'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $delete_stmt = $pdo->prepare("DELETE FROM guias WHERE id = ?");
            $delete_stmt->execute([$guia_id]);
            $_SESSION['message'] = ['type' => 'info', 'text' => 'La guía ha sido eliminada.'];

        } catch (PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al eliminar la guía.'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'No tienes permisos para eliminar esta guía.'];
    }
    header('Location: guias.php');
    exit();
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
$page_title = 'Guías de Identificación';

// Obtenemos todas las guías para mostrarlas
try {
    $stmt = $pdo->query(
        "SELECT g.*, u.nombre as nombre_usuario
         FROM guias g
         JOIN usuarios u ON g.usuario_id = u.id
         ORDER BY g.fecha_subida DESC"
    );
    $guias = $stmt->fetchAll();
} catch (PDOException $e) {
    $guias = [];
}

// --- FASE 3: VISUALIZACIÓN ---
// Ahora que toda la lógica ha terminado, incluimos el header.
require 'header.php';
?>

<!-- Contenido específico de la página de Guías -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Guías de Campo y Recursos</h1>
        <p class="lead text-muted">Una biblioteca colaborativa de guías de identificación y documentos de interés.</p>
    </div>

    <div class="row">
        <!-- Columna para subir una nueva guía -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Subir una Nueva Guía</h5>
                    <form action="guias.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título del Documento</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="pdf_file" class="form-label">Archivo PDF</label>
                            <input class="form-control" type="file" id="pdf_file" name="pdf_file" accept="application/pdf" required>
                            <small class="form-text text-muted">Tamaño máximo: 10 MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-upload me-2"></i>Subir Guía
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna para mostrar las guías existentes -->
        <div class="col-lg-8">
            <h4>Biblioteca de Guías</h4>
            <?php if (empty($guias)): ?>
                <div class="alert alert-secondary text-center">
                    <i class="bi bi-collection fs-3 d-block mb-2"></i>
                    Aún no se han subido guías. ¡Sé el primero en compartir un documento!
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($guias as $guia): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="uploads/guides/<?php echo htmlspecialchars($guia['nombre_archivo']); ?>" target="_blank" class="text-decoration-none text-dark flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($guia['titulo']); ?></h6>
                                <small class="text-muted">Subido por: <strong><?php echo htmlspecialchars($guia['nombre_usuario']); ?></strong> el <?php echo date('d/m/Y', strtotime($guia['fecha_subida'])); ?></small>
                            </a>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-pdf fs-3 text-danger me-3"></i>
                                <?php if ($guia['usuario_id'] == $current_user_id || $current_user_role === 'admin'): ?>
                                    <form action="guias.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta guía de forma permanente?');">
                                        <input type="hidden" name="guia_id" value="<?php echo $guia['id']; ?>">
                                        <button type="submit" name="delete_guide" class="btn btn-outline-danger btn-sm" title="Eliminar Guía">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
