<?php
// Título de la página.
$page_title = 'Términos y Condiciones de Uso';

// Incluimos la cabecera.
require 'header.php';

// Esta página es pública, por lo que no requiere protección de sesión.
?>

<!-- Contenido específico de la página de Términos y Condiciones -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="card-title display-5 mb-4">Términos y Condiciones</h1>
                    
                    <h5 class="mt-4">1. Aceptación de los Términos</h5>
                    <p class="text-muted">
                        Al registrarte y utilizar la Enciclopedia Interactiva de Artrópodos (en adelante, "la Plataforma"), aceptas cumplir con los presentes Términos y Condiciones. Si no estás de acuerdo con alguna parte de los términos, no podrás utilizar nuestros servicios.
                    </p>

                    <h5 class="mt-4">2. Uso de la Plataforma</h5>
                    <p class="text-muted">
                        Te comprometes a utilizar la Plataforma de manera responsable y con fines educativos y científicos. No está permitido subir contenido ilegal, ofensivo, o que infrinja los derechos de autor de terceros. Eres responsable de la veracidad y precisión de la información que aportas.
                    </p>

                    <h5 class="mt-4">3. Propiedad del Contenido</h5>
                    <p class="text-muted">
                        Tú conservas los derechos de autor sobre el contenido original (fotos, descripciones) que subes a la Plataforma. Sin embargo, al subir contenido, nos otorgas una licencia mundial, no exclusiva y libre de regalías para usar, reproducir, distribuir y mostrar dicho contenido en conexión con el servicio de la Plataforma.
                    </p>

                    <h5 class="mt-4">4. Cuentas de Usuario</h5>
                    <p class="text-muted">
                        Eres responsable de mantener la confidencialidad de tu contraseña y de todas las actividades que ocurran en tu cuenta. Debes notificarnos inmediatamente sobre cualquier uso no autorizado de tu cuenta.
                    </p>
                    
                    <h5 class="mt-4">5. Modificación de los Términos</h5>
                    <p class="text-muted">
                        Nos reservamos el derecho de modificar estos términos en cualquier momento. Te notificaremos sobre los cambios importantes. El uso continuado de la Plataforma después de dichas modificaciones constituirá tu aceptación de los nuevos términos.
                    </p>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="index.php" class="btn btn-primary">
                            <i class="bi bi-house-door me-2"></i>Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
