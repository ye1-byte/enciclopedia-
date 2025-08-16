<?php
require 'conexion.php';

// Destruir todas las variables de sesión.
session_unset();
session_destroy();

// Redirigir a la página de login
header("Location: login.php");
exit();
?>