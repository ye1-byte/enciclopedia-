<?php
$page_title = 'Formulario de Registro';
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Plantilla de un artrópodo vacío, incluyendo los nuevos campos
$insecto = [
    'id' => null, 'arthropodo' => '', 'orden_familia' => '', 'biologia_caracteristicas' => '',
    'aparato_bucal' => '', 'habito_alimenticio' => '', 'tipo_metamorfosis' => '',
    'danio_benefico' => '', 'enemigos_naturales' => '', 'nombre_producto_quimico' => '', 
    'ingrediente_activo' => '', 'dosis_ica' => '', 'imagen' => ''
];
$action_url = "guardar.php";

// Si hay un ID en la URL, estamos en modo "Editar"
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $page_title = "Editar Registro";
    $action_url = "guardar.php?id=$id";
    
    // Obtenemos los datos del registro desde la BD
    $sql = "SELECT * FROM artropodos WHERE id = ?";
    $params = [$id];

    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $sql .= " AND usuario_id = ?";
        $params[] = $_SESSION['user_id'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $insecto_from_db = $stmt->fetch();

    if ($insecto_from_db) {
        $insecto = $insecto_from_db;
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: No tienes permisos para editar este registro o no existe.'];
        header('Location: index.php');
        exit();
    }
}

// Opciones para los nuevos menús desplegables
$opciones_metamorfosis = ['Holometábola', 'Hemimetábola Paurometabola', ' Hemimetábola Bathmedometabola'];
$opciones_habito = ['Fitófago', 'Depredador', 'Parasitoide', 'Saprófago', 'Polinizador', 'Hematófago'];
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title h4 mb-0"><i class="bi bi-bug-fill me-2"></i><?php echo $page_title; ?></h1>
            </div>
            <div class="card-body p-4">
                <form id="arthropod-form" action="<?php echo $action_url; ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($insecto['imagen']); ?>">
                    
                    <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
                    <fieldset class="mb-4">
                        <legend class="h5 text-success border-bottom pb-2 mb-3">Información Básica</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="arthropodo" class="form-label fw-bold">Nombre del Artrópodo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="arthropodo" name="arthropodo" value="<?php echo htmlspecialchars($insecto['arthropodo']); ?>" required>
                                <div class="invalid-feedback">Por favor, ingresa el nombre del artrópodo.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="orden_familia" class="form-label fw-bold">Orden/Familia</label>
                                <input type="text" class="form-control" id="orden_familia" name="orden_familia" value="<?php echo htmlspecialchars($insecto['orden_familia']); ?>">
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECCIÓN 2: CARACTERÍSTICAS BIOLÓGICAS -->
                    <fieldset class="mb-4">
                        <legend class="h5 text-info border-bottom pb-2 mb-3">Características Biológicas</legend>
                        
                        <!-- NUEVOS CAMPOS AÑADIDOS AQUÍ -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipo_metamorfosis" class="form-label">Tipo de Metamorfosis</label>
                                <select class="form-select" id="tipo_metamorfosis" name="tipo_metamorfosis">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($opciones_metamorfosis as $opcion): ?>
                                        <option value="<?php echo $opcion; ?>" <?php if ($insecto['tipo_metamorfosis'] == $opcion) echo 'selected'; ?>>
                                            <?php echo $opcion; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="aparato_bucal" class="form-label">Aparato Bucal</label>
                                <input type="text" class="form-control" id="aparato_bucal" name="aparato_bucal" value="<?php echo htmlspecialchars($insecto['aparato_bucal']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="habito_alimenticio" class="form-label">Hábito Alimenticio</label>
                                <select class="form-select" id="habito_alimenticio" name="habito_alimenticio">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($opciones_habito as $opcion): ?>
                                        <option value="<?php echo $opcion; ?>" <?php if ($insecto['habito_alimenticio'] == $opcion) echo 'selected'; ?>>
                                            <?php echo $opcion; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
<div class="mb-3">
    <label for="dosis_controlador" class="form-label">Dosis de Liberación (para Controladores)</label>
    <input type="text" class="form-control" id="dosis_controlador" name="dosis_controlador" value="<?php echo htmlspecialchars($insecto['dosis_controlador'] ?? ''); ?>" placeholder="Ej. 100 individuos/ha">
</div>
                        <div class="mb-3">
                            <label for="biologia_caracteristicas" class="form-label">Biología y Características (Descripción General)</label>
                            <textarea class="form-control" id="biologia_caracteristicas" name="biologia_caracteristicas" rows="3"><?php echo htmlspecialchars($insecto['biologia_caracteristicas']); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="danio_benefico" class="form-label">Daño / Beneficio</label>
                                <textarea class="form-control" id="danio_benefico" name="danio_benefico" rows="3"><?php echo htmlspecialchars($insecto['danio_benefico']); ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="enemigos_naturales" class="form-label">Enemigos Biológicos / Naturales</label>
                                <textarea class="form-control" id="enemigos_naturales" name="enemigos_naturales" rows="3"><?php echo htmlspecialchars($insecto['enemigos_naturales']); ?></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECCIÓN 3: CONTROL QUÍMICO -->
                    <fieldset class="mb-4">
                        <legend class="h5 text-warning border-bottom pb-2 mb-3">Control Químico</legend>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nombre_producto_quimico" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="nombre_producto_quimico" name="nombre_producto_quimico" value="<?php echo htmlspecialchars($insecto['nombre_producto_quimico']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ingrediente_activo" class="form-label">Ingrediente Activo</label>
                                <input type="text" class="form-control" id="ingrediente_activo" name="ingrediente_activo" value="<?php echo htmlspecialchars($insecto['ingrediente_activo']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="dosis_ica" class="form-label">Dosis Recomendada</label>
                                <input type="text" class="form-control" id="dosis_ica" name="dosis_ica" value="<?php echo htmlspecialchars($insecto['dosis_ica']); ?>">
                            </div>
                        </div>
                    </fieldset>

                    <!-- SECCIÓN 4: IMAGEN -->
                    <fieldset>
                        <legend class="h5 text-secondary border-bottom pb-2 mb-3">Imagen</legend>
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="imagen" class="form-label">Seleccionar Nueva Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg,image/png,image/gif">
                                <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Máximo 5MB.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-2 fw-bold">Vista Previa:</p>
                                <?php if (!empty($insecto['imagen']) && file_exists('uploads/' . $insecto['imagen'])): ?>
                                    <img id="image-preview" src="uploads/<?php echo htmlspecialchars($insecto['imagen']); ?>" alt="Imagen actual" class="img-thumbnail" style="max-height: 150px;">
                                <?php else: ?>
                                    <img id="image-preview" src="assets/images/placeholder.png" alt="Vista previa" class="img-thumbnail" style="max-height: 150px;">
                                <?php endif; ?>
                            </div>
                        </div>
                    </fieldset>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="text-end mt-4 border-top pt-3">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success"><i class="bi bi-save-fill me-2"></i>Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script para la validación de Bootstrap 5
    const form = document.getElementById('arthropod-form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // Script para la vista previa de la imagen
    const fileInput = document.getElementById('imagen');
    const imagePreview = document.getElementById('image-preview');
    
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>

<?php
require 'footer.php';
?>
