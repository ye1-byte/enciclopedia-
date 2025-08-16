<?php
$page_title = 'Registro de Usuario';
require 'header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card mt-5">
            <div class="card-body">
                <h1 class="card-title text-center h3 mb-3 font-weight-normal">Crear una Cuenta</h1>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
                <?php endif; ?>
                <form action="accion_registro.php" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-lg btn-success btn-block" type="submit">Registrarse</button>
                    <p class="mt-3 text-center">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require 'footer.php';
?>