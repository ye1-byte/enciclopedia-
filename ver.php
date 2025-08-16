<?php
// Título de la página
$page_title = 'Detalles del Artrópodo';
require 'header.php';

// Protección: nos aseguramos de que solo los usuarios que han iniciado sesión puedan ver esta página.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 1. Verificamos si se ha proporcionado un ID válido
$id = $_GET['id'] ?? null;
if (!is_numeric($id) || empty($id)) {
    // Si el ID no es válido, mostramos un error
    $insecto = false;
    $error_message = "ID de registro no válido.";
} else {
    // 2. Lógica para obtener los datos de la base de datos
    $id = (int)$id;
    try {
        // Preparamos la consulta para seleccionar el registro específico
        // La condición 'user_id = ?' es CRUCIAL para la seguridad,
        // ya que evita que un usuario vea registros de otro.
        $stmt = $pdo->prepare("SELECT * FROM artropodos WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $insecto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$insecto) {
            $error_message = "Registro no encontrado o no tienes permisos para verlo.";
        }
    } catch (PDOException $e) {
        $error_message = "Error al cargar el registro: " . $e->getMessage();
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <h4 class="alert-heading">¡Error!</h4>
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                    <hr>
                    <a href="explorar.php" class="btn btn-danger">
                        <i class="fas fa-undo"></i> Volver a Explorar
                    </a>
                </div>
            <?php else: ?>

            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white text-center">
                    <h1 class="card-title mb-0">
                        <?php echo htmlspecialchars($insecto['arthropodo']); ?>
                    </h1>
                    <p class="mb-0 fs-5"><?php echo htmlspecialchars($insecto['orden_familia']); ?></p>
                </div>
                
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-5 d-flex justify-content-center align-items-center">
                            <div class="text-center">
                                <img src="uploads/<?php echo htmlspecialchars($insecto['imagen']); ?>" 
                                     class="img-fluid rounded border shadow-sm" 
                                     alt="Imagen de <?php echo htmlspecialchars($insecto['arthropodo']); ?>"
                                     onerror="this.onerror=null;this.src='https://via.placeholder.com/400x300.png?text=Sin+Imagen';">
                            </div>
                        </div>

                        <div class="col-md-7">
                            <h4 class="text-info mt-2 mt-md-0">
                                <i class="fas fa-microscope me-2"></i> Biología y Características
                            </h4>
                            <p class="text-secondary"><?php echo nl2br(htmlspecialchars($insecto['biologia_caracteristicas'])); ?></p>
                            
                            <h4 class="text-info mt-4">
                                <i class="fas fa-leaf me-2"></i> Daño o Beneficio
                            </h4>
                            <p class="text-secondary"><?php echo nl2br(htmlspecialchars($insecto['danio_benefico'])); ?></p>

                            <h4 class="text-info mt-4">
                                <i class="fas fa-spider me-2"></i> Enemigos Naturales
                            </h4>
                            <p class="text-secondary"><?php echo nl2br(htmlspecialchars($insecto['enemigos_naturales'])); ?></p>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <h4 class="text-warning">
                            <i class="fas fa-flask me-2"></i> Control Químico
                        </h4>
                        <div class="row mt-3">
                            <div class="col-md-4 mb-3">
                                <strong>Producto:</strong><br>
                                <span class="text-secondary"><?php echo htmlspecialchars($insecto['nombre_producto_quimico']); ?></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong>Ingrediente Activo:</strong><br>
                                <span class="text-secondary"><?php echo htmlspecialchars($insecto['ingrediente_activo']); ?></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong>Dosis ICA:</strong><br>
                                <span class="text-secondary"><?php echo htmlspecialchars($insecto['dosis_ica']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <a href="explorar.php" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                    <a href="formulario.php?id=<?php echo htmlspecialchars($insecto['id']); ?>" 
                       class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i> Editar
                    </a>
                    <a href="eliminar.php?id=<?php echo htmlspecialchars($insecto['id']); ?>" 
                       class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
                        <i class="fas fa-trash-alt me-2"></i> Eliminar
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require 'footer.php';
?>