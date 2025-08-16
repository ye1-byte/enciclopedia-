<?php
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($nombre) || empty($email) || empty($password)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Todos los campos son obligatorios.'];
        header("Location: registro.php");
        exit();
    }

    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Este correo electrónico ya está registrado.'];
        header("Location: registro.php");
        exit();
    }

    // Encriptar la contraseña (¡MUY IMPORTANTE!)
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nombre, $email, $passwordHash])) {
        $_SESSION['message'] = ['type' => 'success', 'text' => '¡Registro exitoso! Ahora puedes iniciar sesión.'];
        header("Location: login.php");
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error al registrar el usuario.'];
        header("Location: registro.php");
    }
}
?>