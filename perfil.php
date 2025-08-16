<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
// Incluimos la conexión ANTES de cualquier cosa para poder usar la sesión y la BD.
require 'conexion.php';

// Protección: si el usuario no ha iniciado sesión, no puede estar aquí.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Procesamos los formularios ANTES de enviar cualquier HTML.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- Acción: Actualizar Información del Perfil ---
    if (isset($_POST['update_profile'])) {
        $nombre = trim($_POST['nombre']);
        if (empty($nombre)) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'El nombre no puede estar vacío.'];
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?");
                $stmt->execute([$nombre, $user_id]);
                $_SESSION['user_name'] = $nombre; // Actualizamos el nombre en la sesión
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Tu nombre ha sido actualizado correctamente.'];
            } catch (PDOException $e) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al actualizar el perfil.'];
            }
        }
        header('Location: perfil.php');
        exit();
    }

    // --- Acción: Cambiar Contraseña ---
    if (isset($_POST['update_password'])) {
        $password_actual = $_POST['password_actual'];
        $nueva_password = $_POST['nueva_password'];
        $confirmar_password = $_POST['confirmar_password'];

        $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password_actual, $user['password'])) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'La contraseña actual es incorrecta.'];
        } elseif (empty($nueva_password) || $nueva_password !== $confirmar_password) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Las nuevas contraseñas no coinciden o están vacías.'];
        } else {
            try {
                $passwordHash = password_hash($nueva_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $stmt->execute([$passwordHash, $user_id]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Contraseña actualizada con éxito.'];
            } catch (PDOException $e) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al cambiar la contraseña.'];
            }
        }
        header('Location: perfil.php');
        exit();
    }

    // --- Acción: Actualizar Avatar (CORREGIDA) ---
    if (isset($_POST['update_avatar'])) {
        // Verificar que se subió un archivo
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/avatars/';
            
            // Crear directorio si no existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5 MB (aumentado el límite)
            
            $fileType = strtolower($_FILES['avatar']['type']);
            $fileSize = $_FILES['avatar']['size'];
            
            // Validar tipo de archivo
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Solo se permiten archivos JPG, PNG y GIF.'];
            }
            // Validar tamaño
            elseif ($fileSize > $maxSize) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'El archivo es demasiado grande. Máximo 5MB.'];
            }
            else {
                try {
                    // Obtener avatar anterior para eliminarlo
                    $stmt_old_avatar = $pdo->prepare("SELECT avatar FROM usuarios WHERE id = ?");
                    $stmt_old_avatar->execute([$user_id]);
                    $old_avatar = $stmt_old_avatar->fetchColumn();
                    
                    // Generar nombre único para la nueva imagen
                    $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                    $nombreAvatar = 'user_' . $user_id . '_' . uniqid() . '.' . $extension;
                    $rutaCompleta = $uploadDir . $nombreAvatar;
                    
                    // Mover archivo subido
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $rutaCompleta)) {
                        // Actualizar base de datos
                        $stmt = $pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id = ?");
                        if ($stmt->execute([$nombreAvatar, $user_id])) {
                            // Eliminar avatar anterior si existe
                            if ($old_avatar && file_exists($uploadDir . $old_avatar)) {
                                unlink($uploadDir . $old_avatar);
                            }
                            $_SESSION['message'] = ['type' => 'success', 'text' => 'Avatar actualizado correctamente.'];
                        } else {
                            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al actualizar la base de datos.'];
                        }
                    } else {
                        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al guardar el archivo. Verifica los permisos de la carpeta.'];
                    }
                } catch (Exception $e) {
                    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: ' . $e->getMessage()];
                }
            }
        } else {
            // Manejar diferentes tipos de errores de subida
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => 'El archivo es demasiado grande (límite del servidor).',
                UPLOAD_ERR_FORM_SIZE => 'El archivo es demasiado grande.',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente.',
                UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'Error del servidor: no hay directorio temporal.',
                UPLOAD_ERR_CANT_WRITE => 'Error del servidor: no se puede escribir el archivo.',
                UPLOAD_ERR_EXTENSION => 'La subida fue detenida por una extensión PHP.',
            ];
            
            $error_code = $_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE;
            $error_message = $error_messages[$error_code] ?? 'Error desconocido al subir el archivo.';
            $_SESSION['message'] = ['type' => 'danger', 'text' => $error_message];
        }
        
        header('Location: perfil.php');
        exit();
    }
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
$page_title = 'Mi Perfil';

// Obtenemos los datos del usuario para mostrarlos en la página
$stmt_user = $pdo->prepare("SELECT nombre, email, fecha_registro, avatar FROM usuarios WHERE id = ?");
$stmt_user->execute([$user_id]);
$usuario = $stmt_user->fetch();

// Obtenemos las estadísticas de contribución del usuario
$stmt_stats = $pdo->prepare("SELECT COUNT(*) FROM artropodos WHERE usuario_id = ?");
$stmt_stats->execute([$user_id]);
$total_contribuciones = $stmt_stats->fetchColumn();

// --- FASE 3: VISUALIZACIÓN ---
// Ahora que toda la lógica ha terminado, incluimos el header.
require 'header.php';
?>

<!-- Contenido HTML de la página de Perfil (MEJORADO) -->
<div class="row justify-content-center">
    <div class="col-lg-10">

        <!-- Cabecera del Perfil -->
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <!-- Formulario MEJORADO para subir avatar -->
                <form id="avatar-form" action="perfil.php" method="POST" enctype="multipart/form-data">
                    <div class="avatar-container mx-auto mb-3">
                        <?php if (!empty($usuario['avatar']) && file_exists('uploads/avatars/' . $usuario['avatar'])): ?>
                            <img id="avatar-preview" 
                                 src="uploads/avatars/<?php echo htmlspecialchars($usuario['avatar']); ?>?v=<?php echo time(); ?>" 
                                 alt="Avatar" 
                                 class="profile-avatar">
                        <?php else: ?>
                            <div id="avatar-initials" class="profile-avatar-initial">
                                <span><?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?></span>
                            </div>
                            <img id="avatar-preview" src="" alt="Avatar" class="profile-avatar d-none">
                        <?php endif; ?>
                        <div class="avatar-overlay">
                            <i class="bi bi-camera-fill"></i>
                            <small class="d-block mt-1">Cambiar</small>
                        </div>
                    </div>
                    
                    <input type="file" 
                           id="avatar-upload" 
                           name="avatar" 
                           class="d-none" 
                           accept="image/jpeg,image/jpg,image/png,image/gif"
                           required>
                    
                    <div id="avatar-actions" class="d-none">
                        <button type="submit" name="update_avatar" class="btn btn-success btn-sm me-2">
                            <i class="bi bi-check-lg"></i> Guardar Avatar
                        </button>
                        <button type="button" id="cancel-avatar" class="btn btn-secondary btn-sm">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </button>
                    </div>
                </form>
                
                <h2 class="card-title h4"><?php echo htmlspecialchars($usuario['nombre']); ?></h2>
                <p class="text-muted mb-0"><?php echo htmlspecialchars($usuario['email']); ?></p>
                <p class="text-muted small">Miembro desde: <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></p>
            </div>
        </div>

        <div class="row">
            <!-- Columna de Gestión -->
            <div class="col-md-8">
                <!-- Tarjeta para Editar Información -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title">Editar Información</h5>
                        <form action="perfil.php" method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
                <!-- Tarjeta para Cambiar Contraseña -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title">Cambiar Contraseña</h5>
                        <form action="perfil.php" method="POST">
                            <div class="mb-3">
                                <label for="password_actual" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                            </div>
                            <div class="mb-3">
                                <label for="nueva_password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="nueva_password" name="nueva_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_password" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary">Actualizar Contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Columna de Estadísticas -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-bug-fill display-4 text-success"></i>
                        <h3 class="my-2"><?php echo $total_contribuciones; ?></h3>
                        <p class="text-muted mb-0">Registros Aportados</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// JavaScript MEJORADO para manejo de avatar
document.addEventListener('DOMContentLoaded', function() {
    const avatarContainer = document.querySelector('.avatar-container');
    const avatarUpload = document.getElementById('avatar-upload');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarInitials = document.getElementById('avatar-initials');
    const avatarActions = document.getElementById('avatar-actions');
    const cancelButton = document.getElementById('cancel-avatar');
    const avatarForm = document.getElementById('avatar-form');
    
    let originalSrc = avatarPreview.src;

    // Click en el contenedor abre el selector de archivos
    avatarContainer.addEventListener('click', function() {
        avatarUpload.click();
    });

    // Cuando se selecciona un archivo
    avatarUpload.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Validar tipo de archivo
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Solo se permiten archivos JPG, PNG y GIF.');
                this.value = '';
                return;
            }
            
            // Validar tamaño (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 5MB.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
                avatarPreview.classList.remove('d-none');
                if (avatarInitials) {
                    avatarInitials.classList.add('d-none');
                }
                avatarActions.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    // Botón cancelar
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            avatarUpload.value = '';
            avatarPreview.src = originalSrc;
            avatarActions.classList.add('d-none');
            
            if (originalSrc === '' && avatarInitials) {
                avatarPreview.classList.add('d-none');
                avatarInitials.classList.remove('d-none');
            }
        });
    }
});
</script>

<style>
/* CSS MEJORADO para el avatar */
.avatar-container { 
    position: relative; 
    cursor: pointer; 
    display: inline-block; 
    transition: transform 0.2s ease;
}
.avatar-container:hover {
    transform: scale(1.02);
}

.avatar-overlay {
    position: absolute; 
    top: 0; 
    left: 0; 
    width: 100%; 
    height: 100%;
    border-radius: 50%; 
    background: rgba(0,0,0,0.6); 
    color: white;
    display: flex; 
    flex-direction: column;
    align-items: center; 
    justify-content: center;
    font-size: 1.5rem; 
    opacity: 0; 
    transition: opacity 0.3s ease;
}

.avatar-container:hover .avatar-overlay { 
    opacity: 1; 
}

#avatar-actions {
    margin-top: 1rem;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>