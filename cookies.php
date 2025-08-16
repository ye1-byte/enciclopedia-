<?php
// Título de la página.
$page_title = 'Política de Cookies';

// Incluimos la cabecera.
require 'header.php';

// Esta página es pública, por lo que no requiere protección de sesión.
?>

<!-- Contenido específico de la página de Política de Cookies -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="card-title display-5 mb-4">Política de Cookies</h1>
                    
                    <h5 class="mt-4">1. ¿Qué son las Cookies?</h5>
                    <p class="text-muted">
                        Una cookie es un pequeño archivo de texto que un sitio web almacena en tu ordenador o dispositivo móvil cuando visitas el sitio. Permite que el sitio web recuerde tus acciones y preferencias (como el inicio de sesión, el idioma, el tamaño de la fuente y otras preferencias de visualización) durante un período de tiempo, para que no tengas que volver a introducirlas cada vez que vuelvas al sitio o navegues de una página a otra.
                    </p>

                    <h5 class="mt-4">2. ¿Cómo Usamos las Cookies?</h5>
                    <p class="text-muted">
                        En nuestra Plataforma, utilizamos principalmente **cookies de sesión**. Estas son esenciales para el funcionamiento de la aplicación y nos permiten:
                        <ul>
                            <li><strong>Mantener tu sesión iniciada:</strong> La cookie más importante que utilizamos es la que nos permite saber que has iniciado sesión. Sin ella, tendrías que introducir tu usuario y contraseña en cada página.</li>
                            <li><strong>Recordar mensajes temporales:</strong> Usamos cookies de sesión para mostrarte mensajes de notificación, como "Registro guardado con éxito", después de que realizas una acción.</li>
                        </ul>
                        Estas cookies son temporales y se eliminan automáticamente cuando cierras tu navegador.
                    </p>

                    <h5 class="mt-4">3. Cookies de Terceros</h5>
                    <p class="text-muted">
                        Actualmente, no utilizamos cookies de terceros para seguimiento, publicidad o análisis. Nuestro uso de cookies se limita estrictamente a la funcionalidad esencial de la Plataforma.
                    </p>

                    <h5 class="mt-4">4. Cómo Controlar las Cookies</h5>
                    <p class="text-muted">
                        Puedes controlar y/o eliminar las cookies como desees. Para más detalles, consulta <a href="https://www.aboutcookies.org" target="_blank">aboutcookies.org</a>. Puedes eliminar todas las cookies que ya están en tu ordenador y puedes configurar la mayoría de los navegadores para evitar que se coloquen. Sin embargo, si haces esto, es posible que tengas que ajustar manualmente algunas preferencias cada vez que visites un sitio y algunos servicios y funcionalidades pueden no funcionar, como el inicio de sesión en nuestra Plataforma.
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
