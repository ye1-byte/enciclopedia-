<?php
// Incluimos la conexión y las funciones.
require 'conexion.php';
require 'funciones.php';

// Establecemos la cabecera para indicar que la respuesta será en formato JSON.
header('Content-Type: application/json; charset=utf-8');

// Protección: solo usuarios logueados pueden realizar acciones.
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // No Autorizado
    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión.']);
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_user_role = $_SESSION['user_role'] ?? 'usuario';

try {
    // --- ACCIÓN: AÑADIR UN NUEVO COMENTARIO ---
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
        $artropodo_id = $_POST['artropodo_id'];
        $comentario = trim($_POST['comentario']);

        if (empty($comentario)) {
            throw new Exception('El comentario no puede estar vacío.', 400);
        }

        // Insertar el comentario
        $sql = "INSERT INTO comentarios (artropodo_id, usuario_id, comentario) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$artropodo_id, $current_user_id, $comentario]);
        $comment_id = $pdo->lastInsertId();

        // Devolver el comentario recién creado para mostrarlo en la página
        $new_comment_stmt = $pdo->prepare("SELECT c.*, u.nombre as nombre_usuario FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = ?");
        $new_comment_stmt->execute([$comment_id]);
        $new_comment = $new_comment_stmt->fetch();

        echo json_encode(['success' => true, 'comment' => $new_comment]);
    }
    // --- ACCIÓN: ELIMINAR UN COMENTARIO ---
    elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_comment'])) {
        $comentario_id = $_POST['comentario_id'];

        // Verificar permisos
        $stmt = $pdo->prepare("SELECT usuario_id FROM comentarios WHERE id = ?");
        $stmt->execute([$comentario_id]);
        $comentario = $stmt->fetch();

        if ($comentario && ($comentario['usuario_id'] == $current_user_id || $current_user_role === 'admin')) {
            $delete_stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ?");
            $delete_stmt->execute([$comentario_id]);
            echo json_encode(['success' => true, 'deleted_id' => $comentario_id]);
        } else {
            throw new Exception('No tienes permisos para eliminar este comentario.', 403);
        }
    } else {
        throw new Exception('Acción no válida.', 400);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() > 0 ? $e->getCode() : 500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
