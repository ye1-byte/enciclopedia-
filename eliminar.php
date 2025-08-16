<?php
require 'conexion.php';
require 'funciones.php'; // Incluimos nuestro nuevo archivo de funciones

// Protección...
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }

if (isset($_POST['id'])) {
    $idToDelete = $_POST['id'];

    // Obtener datos ANTES de borrar para poder registrarlos
    $stmt = $pdo->prepare("SELECT arthropodo, imagen FROM artropodos WHERE id = ?");
    $stmt->execute([$idToDelete]);
    $result = $stmt->fetch();

    if ($result) {
        // Borrar archivo de imagen...
        if (!empty($result['imagen']) && file_exists('uploads/' . $result['imagen'])) {
            unlink('uploads/' . $result['imagen']);
        }
        
        // Registrar la acción
        $descripcion = "Eliminó el registro de artrópodo: " . $result['arthropodo'] . " (ID: " . $idToDelete . ")";
        registrar_historial($pdo, $_SESSION['user_id'], 'ELIMINACIÓN DE REGISTRO', $descripcion);

        // Borrar el registro de la base de datos
        $stmt = $pdo->prepare("DELETE FROM artropodos WHERE id = ?");
        $stmt->execute([$idToDelete]);
        
        $_SESSION['message'] = ['type' => 'info', 'text' => 'Registro eliminado correctamente.'];
    }
}
header('Location: index.php');
exit();
?>