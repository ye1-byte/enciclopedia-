<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscar el usuario por email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verificar si el usuario existe y si la contraseña es correcta
    if ($user && password_verify($password, $user['password'])) {
        // Iniciar sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        
        header("Location: portal.php"); // Redirigir a la página principal
        exit();
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Correo o contraseña incorrectos.'];
        header("Location: login.php");
        exit();
    }
}
?>