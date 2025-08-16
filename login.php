<?php
$page_title = 'Iniciar Sesión';
require 'header.php';

// Si el usuario ya ha iniciado sesión, lo redirigimos al index.
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
                <h1 class="card-title text-center h3 mb-3 font-weight-normal">Iniciar Sesión</h1>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
                <?php endif; ?>
                <form action="accion_login.php" method="post">
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Entrar</button>
                    <p class="mt-3 text-center">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require 'footer.php';
?>