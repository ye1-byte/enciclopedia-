<?php

// Paso 2: Incluir la conexión a la base de datos para poder hacer consultas.
// Usamos '../' para salir de la carpeta /admin y encontrar el archivo en la raíz.
require_once '../conexion.php';

// --- INICIO DE LAS COMPROBACIONES DE SEGURIDAD ---

// Comprobación 1: ¿Existe una sesión de usuario activa?
// Si no existe 'user_id' en la sesión, significa que no ha iniciado sesión.
if (!isset($_SESSION['user_id'])) {
    // Lo redirigimos a la página de login.
    header('Location: ../login.php');
    // Detenemos la ejecución del script para que no se muestre nada más.
    exit();
}

// Comprobación 2: Si ha iniciado sesión, ¿tiene los permisos correctos?
// Consultamos la base de datos para obtener el rol del usuario actual.
try {
    $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Si la consulta no devuelve un usuario o el rol no es 'admin', denegamos el acceso.
    if (!$user || $user['rol'] !== 'admin') {
        // Mostramos un mensaje de error amigable y detenemos el script.
        die('
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Acceso Denegado</title>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            </head>
            <body class="bg-light">
                <div class="container mt-5">
                    <div class="alert alert-danger text-center">
                        <h1 class="alert-heading">Acceso Denegado</h1>
                        <p class="lead">No tienes los permisos necesarios para acceder a esta sección.</p>
                        <hr>
                        <a href="../index.php" class="btn btn-primary">Volver a la Página Principal</a>
                    </div>
                </div>
            </body>
            </html>
        ');
    }
} catch (PDOException $e) {
    // En caso de un error de base de datos, mostramos un mensaje genérico.
    die("Error al verificar los permisos de usuario.");
}

// Si el script llega hasta aquí, significa que el usuario ha iniciado sesión Y es un administrador.
// La página que incluyó este archivo puede continuar cargándose de forma segura.
?>