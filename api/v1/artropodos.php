<?php
// --- CONFIGURACIÓN DEL ENDPOINT DE LA API ---

// Incluimos la conexión y las funciones de utilidad.
// Usamos '..' para subir de nivel desde la carpeta /api/v1/ a la raíz.
require '../../conexion.php';

// Establecemos la cabecera para indicar que la respuesta será en formato JSON.
header('Content-Type: application/json; charset=utf-8');

// --- SEGURIDAD BÁSICA ---
// Verificamos si el usuario ha iniciado sesión. Sin esto, la API sería pública.
if (!isLoggedIn()) {
    // Si no ha iniciado sesión, devolvemos un error de no autorizado.
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Acceso no autorizado. Se requiere iniciar sesión.']);
    exit();
}

// --- LÓGICA DEL ENDPOINT MEJORADA ---

try {
    // Verificamos si se ha solicitado un ID específico en la URL (ej. ?id=5)
    $id = $_GET['id'] ?? null;

    if ($id) {
        // --- BÚSQUEDA POR ID ESPECÍFICO ---
        if (!is_numeric($id)) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'El ID proporcionado no es válido.']);
            exit();
        }
        
        // MEJORA: Hacemos un JOIN con la tabla de usuarios para obtener el nombre del creador.
        $sql = "SELECT a.*, u.nombre as creado_por 
                FROM artropodos a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.id = ?";
        $data = fetchOne($sql, [$id]);

        if (!$data) {
            // Si no se encuentra el registro, devolvemos un error 404.
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Registro no encontrado.']);
            exit();
        }
    } else {
        // --- BÚSQUEDA AVANZADA CON FILTROS ---
        
        // MEJORA: Preparamos la consulta base con el JOIN.
        $sql = "SELECT a.id, a.arthropodo, a.orden_familia, a.habito_alimenticio, a.tipo_metamorfosis, a.imagen, u.nombre as creado_por 
                FROM artropodos a
                LEFT JOIN usuarios u ON a.usuario_id = u.id";
        
        $filters = [];
        $params = [];

        // Añadimos filtros dinámicamente según los parámetros GET recibidos.
        if (!empty($_GET['orden_familia'])) {
            $filters[] = "a.orden_familia LIKE ?";
            $params[] = "%" . $_GET['orden_familia'] . "%";
        }
        if (!empty($_GET['habito_alimenticio'])) {
            $filters[] = "a.habito_alimenticio = ?";
            $params[] = $_GET['habito_alimenticio'];
        }
        if (!empty($_GET['tipo_metamorfosis'])) {
            $filters[] = "a.tipo_metamorfosis = ?";
            $params[] = $_GET['tipo_metamorfosis'];
        }
        if (!empty($_GET['q'])) {
            $filters[] = "a.arthropodo LIKE ?";
            $params[] = "%" . $_GET['q'] . "%";
        }

        // Si hay filtros, los añadimos a la consulta SQL.
        if (!empty($filters)) {
            $sql .= " WHERE " . implode(" AND ", $filters);
        }

        $sql .= " ORDER BY a.arthropodo";

        $data = fetchAll($sql, $params);
    }

    // Si todo fue bien, devolvemos los datos con un código de estado 200 (OK).
    http_response_code(200);
    echo json_encode($data);

} catch (PDOException $e) {
    // En caso de un error de base de datos, devolvemos un error 500.
    http_response_code(500); // Internal Server Error
    error_log("Error en la API: " . $e->getMessage()); // Guardamos el error real en el log
    echo json_encode(['error' => 'Ocurrió un error interno en el servidor.']);
}
?>
