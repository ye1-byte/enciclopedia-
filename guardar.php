<?php
// 1. INCLUIMOS LOS ARCHIVOS ESENCIALES
require 'conexion.php';
require 'funciones.php';

// 2. PROTECCIÓN DE LA PÁGINA
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 3. VALIDACIÓN DE DATOS BÁSICA
if (empty($_POST['arthropodo'])) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error: El campo Artrópodo no puede estar vacío.'];
    header('Location: formulario.php');
    exit();
}

// 4. GESTIÓN DE LA SUBIDA DE IMAGEN (la lógica no cambia)
$uploadDir = 'uploads/';
$nombreImagen = $_POST['imagen_actual'] ?? '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
    // (Aquí va la misma validación de imagen que ya teníamos: tipo, tamaño, etc.)
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreImagen = uniqid() . '.' . $extension;
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadDir . $nombreImagen)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al mover el archivo de imagen.'];
        header('Location: formulario.php');
        exit();
    }
}

// 5. PREPARACIÓN DE LA CONSULTA SQL (ACTUALIZADA CON NUEVOS CAMPOS)
$is_insert = false;

if (isset($_GET['id'])) { // Editando un registro existente
    $id = $_GET['id'];
    $sql = "UPDATE artropodos SET 
                arthropodo = ?, 
                orden_familia = ?, 
                biologia_caracteristicas = ?,
                aparato_bucal = ?, 
                habito_alimenticio = ?, 
                tipo_metamorfosis = ?,
                danio_benefico = ?, 
                enemigos_naturales = ?, 
                nombre_producto_quimico = ?, 
                ingrediente_activo = ?, 
                dosis_ica = ?,
                dosis_controlador = ?, 
                imagen = ? 
            WHERE id = ?";
    
    $params = [
        $_POST['arthropodo'], $_POST['orden_familia'], $_POST['biologia_caracteristicas'],
        $_POST['aparato_bucal'], $_POST['habito_alimenticio'], $_POST['tipo_metamorfosis'],
        $_POST['danio_benefico'], $_POST['enemigos_naturales'], $_POST['nombre_producto_quimico'],
        $_POST['ingrediente_activo'], $_POST['dosis_ica'],
        $_POST['dosis_controlador'] ?? null, // Nuevo campo
        $nombreImagen, $id
    ];
    $successMessage = "Registro actualizado con éxito.";

} else { // Agregando un nuevo registro
    $is_insert = true;
    $sql = "INSERT INTO artropodos 
                (usuario_id, arthropodo, orden_familia, biologia_caracteristicas, aparato_bucal, habito_alimenticio, tipo_metamorfosis, danio_benefico, enemigos_naturales, nombre_producto_quimico, ingrediente_activo, dosis_ica, dosis_controlador, imagen) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $_SESSION['user_id'],
        $_POST['arthropodo'], $_POST['orden_familia'], $_POST['biologia_caracteristicas'],
        $_POST['aparato_bucal'], $_POST['habito_alimenticio'], $_POST['tipo_metamorfosis'],
        $_POST['danio_benefico'], $_POST['enemigos_naturales'], $_POST['nombre_producto_quimico'],
        $_POST['ingrediente_activo'], $_POST['dosis_ica'],
        $_POST['dosis_controlador'] ?? null, // Nuevo campo
        $nombreImagen
    ];
    $successMessage = "Registro agregado con éxito.";
}

// 6. EJECUCIÓN Y REGISTRO EN EL HISTORIAL
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($is_insert) {
        $lastId = $pdo->lastInsertId();
        $descripcion = "Creó el registro: '" . $_POST['arthropodo'] . "' (ID: " . $lastId . ")";
        registrar_historial($pdo, $_SESSION['user_id'], 'CREACIÓN DE REGISTRO', $descripcion);
    } else {
        $descripcion = "Editó el registro: '" . $_POST['arthropodo'] . "' (ID: " . $id . ")";
        registrar_historial($pdo, $_SESSION['user_id'], 'EDICIÓN DE REGISTRO', $descripcion);
    }

    $_SESSION['message'] = ['type' => 'success', 'text' => $successMessage];

} catch (PDOException $e) {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Hubo un error al guardar en la base de datos.'];
    // Para depuración: error_log($e->getMessage());
}

// 7. REDIRECCIÓN FINAL
header('Location: index.php');
exit();
?>
