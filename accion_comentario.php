<?php
/**
 * Sistema de Comentarios Mejorado
 * Maneja las acciones de comentarios con validación, seguridad y feedback apropiado
 */

require_once 'conexion.php';
require_once 'funciones.php';

// --- CONFIGURACIÓN INICIAL Y SEGURIDAD ---
header('Content-Type: application/json; charset=utf-8');

// Protección CSRF básica
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Token de seguridad inválido',
        'code' => 'CSRF_ERROR'
    ]);
    exit();
}

// Protección: solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Debes iniciar sesión para realizar esta acción',
        'code' => 'AUTH_REQUIRED'
    ]);
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_user_role = $_SESSION['user_role'] ?? 'usuario';

// --- FUNCIONES AUXILIARES ---

/**
 * Valida y sanitiza el contenido del comentario
 */
function validateComment($comentario) {
    $comentario = trim(strip_tags($comentario));
    
    if (empty($comentario)) {
        return ['valid' => false, 'error' => 'El comentario no puede estar vacío'];
    }
    
    if (strlen($comentario) < 3) {
        return ['valid' => false, 'error' => 'El comentario debe tener al menos 3 caracteres'];
    }
    
    if (strlen($comentario) > 1000) {
        return ['valid' => false, 'error' => 'El comentario no puede exceder 1000 caracteres'];
    }
    
    // Filtro anti-spam básico
    $spam_words = ['spam', 'viagra', 'casino', 'porn'];
    foreach ($spam_words as $word) {
        if (stripos($comentario, $word) !== false) {
            return ['valid' => false, 'error' => 'Contenido no permitido detectado'];
        }
    }
    
    return ['valid' => true, 'content' => $comentario];
}

/**
 * Verifica si existe el artrópodo
 */
function validateArtropodo($artropodo_id, $pdo) {
    $stmt = $pdo->prepare("SELECT id FROM artropodos WHERE id = ?");
    $stmt->execute([$artropodo_id]);
    return $stmt->fetchColumn() !== false;
}

/**
 * Obtiene información del comentario con permisos
 */
function getCommentWithPermissions($comentario_id, $pdo, $user_id, $user_role) {
    $stmt = $pdo->prepare("
        SELECT c.*, u.nombre as usuario_nombre 
        FROM comentarios c 
        JOIN usuarios u ON c.usuario_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$comentario_id]);
    $comentario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$comentario) {
        return null;
    }
    
    // Verificar permisos
    $can_delete = ($comentario['usuario_id'] == $user_id || $user_role === 'admin');
    $comentario['can_delete'] = $can_delete;
    
    return $comentario;
}

/**
 * Registra actividad de comentarios
 */
function logCommentActivity($action, $user_id, $artropodo_id, $comment_id = null, $pdo) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO actividades (usuario_id, accion, tabla_afectada, registro_id, detalles) 
            VALUES (?, ?, 'comentarios', ?, ?)
        ");
        $details = json_encode([
            'artropodo_id' => $artropodo_id,
            'comment_id' => $comment_id,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        $stmt->execute([$user_id, $action, $comment_id, $details]);
    } catch (PDOException $e) {
        // Log error but don't stop execution
        error_log("Error logging activity: " . $e->getMessage());
    }
}

/**
 * Envía notificación al autor del artrópodo
 */
function sendNotification($artropodo_id, $commenter_id, $pdo) {
    try {
        // Obtener el autor del artrópodo
        $stmt = $pdo->prepare("SELECT usuario_id, nombre FROM artropodos WHERE id = ?");
        $stmt->execute([$artropodo_id]);
        $artropodo = $stmt->fetch();
        
        if ($artropodo && $artropodo['usuario_id'] != $commenter_id) {
            // Obtener nombre del comentarista
            $stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = ?");
            $stmt->execute([$commenter_id]);
            $comentarista = $stmt->fetchColumn();
            
            // Insertar notificación
            $mensaje = "{$comentarista} ha comentado en tu registro '{$artropodo['nombre']}'";
            $link = "detalle.php?id={$artropodo_id}#comentarios";
            
            $stmt = $pdo->prepare("
                INSERT INTO notificaciones (usuario_id, mensaje, link, tipo) 
                VALUES (?, ?, ?, 'comentario')
            ");
            $stmt->execute([$artropodo['usuario_id'], $mensaje, $link]);
        }
    } catch (PDOException $e) {
        error_log("Error sending notification: " . $e->getMessage());
    }
}

// --- PROCESAMIENTO DE ACCIONES ---

try {
    $pdo->beginTransaction();
    
    // =================================================================
    // ACCIÓN: AÑADIR COMENTARIO
    // =================================================================
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
        
        $artropodo_id = filter_input(INPUT_POST, 'artropodo_id', FILTER_VALIDATE_INT);
        $comentario_raw = $_POST['comentario'] ?? '';
        
        // Validaciones
        if (!$artropodo_id) {
            throw new Exception('ID de artrópodo inválido', 400);
        }
        
        if (!validateArtropodo($artropodo_id, $pdo)) {
            throw new Exception('El artrópodo especificado no existe', 404);
        }
        
        $validation = validateComment($comentario_raw);
        if (!$validation['valid']) {
            throw new Exception($validation['error'], 400);
        }
        
        $comentario = $validation['content'];
        
        // Rate limiting: máximo 5 comentarios por minuto por usuario
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM comentarios 
            WHERE usuario_id = ? AND fecha >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)
        ");
        $stmt->execute([$current_user_id]);
        if ($stmt->fetchColumn() >= 5) {
            throw new Exception('Has alcanzado el límite de comentarios por minuto', 429);
        }
        
        // Insertar comentario
        $stmt = $pdo->prepare("
            INSERT INTO comentarios (artropodo_id, usuario_id, comentario, fecha) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$artropodo_id, $current_user_id, $comentario]);
        $comment_id = $pdo->lastInsertId();
        
        // Obtener el comentario recién creado con información del usuario
        $stmt = $pdo->prepare("
            SELECT c.*, u.nombre as usuario_nombre, u.avatar
            FROM comentarios c 
            JOIN usuarios u ON c.usuario_id = u.id 
            WHERE c.id = ?
        ");
        $stmt->execute([$comment_id]);
        $new_comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Log activity y enviar notificación
        logCommentActivity('comment_added', $current_user_id, $artropodo_id, $comment_id, $pdo);
        sendNotification($artropodo_id, $current_user_id, $pdo);
        
        $pdo->commit();
        
        // Formatear fecha para mostrar
        $new_comment['fecha_formateada'] = date('d/m/Y H:i', strtotime($new_comment['fecha']));
        $new_comment['can_delete'] = true; // El usuario siempre puede eliminar su propio comentario
        
        echo json_encode([
            'success' => true,
            'message' => 'Comentario añadido exitosamente',
            'data' => [
                'comment' => $new_comment,
                'total_comments' => getTotalComments($artropodo_id, $pdo)
            ]
        ]);
        
    }
    
    // =================================================================
    // ACCIÓN: ELIMINAR COMENTARIO
    // =================================================================
    elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_comment'])) {
        
        $comentario_id = filter_input(INPUT_POST, 'comentario_id', FILTER_VALIDATE_INT);
        
        if (!$comentario_id) {
            throw new Exception('ID de comentario inválido', 400);
        }
        
        // Obtener comentario con permisos
        $comentario = getCommentWithPermissions($comentario_id, $pdo, $current_user_id, $current_user_role);
        
        if (!$comentario) {
            throw new Exception('Comentario no encontrado', 404);
        }
        
        if (!$comentario['can_delete']) {
            throw new Exception('No tienes permisos para eliminar este comentario', 403);
        }
        
        // Eliminar comentario
        $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ?");
        $stmt->execute([$comentario_id]);
        
        // Log activity
        logCommentActivity('comment_deleted', $current_user_id, $comentario['artropodo_id'], $comentario_id, $pdo);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Comentario eliminado exitosamente',
            'data' => [
                'deleted_id' => $comentario_id,
                'total_comments' => getTotalComments($comentario['artropodo_id'], $pdo)
            ]
        ]);
        
    }
    
    // =================================================================
    // ACCIÓN: OBTENER COMENTARIOS (GET)
    // =================================================================
    elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['get_comments'])) {
        
        $artropodo_id = filter_input(INPUT_GET, 'artropodo_id', FILTER_VALIDATE_INT);
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $limit = 10; // Comentarios por página
        $offset = ($page - 1) * $limit;
        
        if (!$artropodo_id || !validateArtropodo($artropodo_id, $pdo)) {
            throw new Exception('Artrópodo inválido', 400);
        }
        
        // Obtener comentarios paginados
        $stmt = $pdo->prepare("
            SELECT c.*, u.nombre as usuario_nombre, u.avatar,
                   (c.usuario_id = ? OR ? = 'admin') as can_delete
            FROM comentarios c 
            JOIN usuarios u ON c.usuario_id = u.id 
            WHERE c.artropodo_id = ? 
            ORDER BY c.fecha DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$current_user_id, $current_user_role, $artropodo_id, $limit, $offset]);
        $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear fechas
        foreach ($comentarios as &$comentario) {
            $comentario['fecha_formateada'] = date('d/m/Y H:i', strtotime($comentario['fecha']));
        }
        
        $total_comments = getTotalComments($artropodo_id, $pdo);
        $total_pages = ceil($total_comments / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'comments' => $comentarios,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'total_comments' => $total_comments,
                    'has_next' => $page < $total_pages,
                    'has_prev' => $page > 1
                ]
            ]
        ]);
        
    } else {
        throw new Exception('Acción no válida', 400);
    }
    
} catch (Exception $e) {
    $pdo->rollBack();
    
    $status_code = $e->getCode() ?: 500;
    http_response_code($status_code);
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $status_code
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor',
        'code' => 'DB_ERROR'
    ]);
    
    // Log the actual database error
    error_log("Database error in comentarios.php: " . $e->getMessage());
}

// --- FUNCIÓN AUXILIAR ---
function getTotalComments($artropodo_id, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comentarios WHERE artropodo_id = ?");
    $stmt->execute([$artropodo_id]);
    return (int)$stmt->fetchColumn();
}
?>