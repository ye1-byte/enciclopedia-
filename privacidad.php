<?php
// Título de la página.
$page_title = 'Política de Privacidad';

// Incluimos la cabecera.
require 'header.php';

// Esta página es pública, por lo que no requiere protección de sesión.

// (Futuro) El contenido de esta página debería ser revisado por un profesional legal.
?>

<!-- Contenido específico de la página de Política de Privacidad -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="card-title display-5 mb-4">Política de Privacidad</h1>
                    
                    <h5 class="mt-4">1. Introducción</h5>
                    <p class="text-muted">
                        Bienvenido a la Enciclopedia Interactiva de Artrópodos. Nos comprometemos a proteger tu privacidad y a ser transparentes sobre los datos que recopilamos y cómo los utilizamos. Esta política de privacidad explica nuestras prácticas de información.
                    </p>

                    <h5 class="mt-4">2. Información que Recopilamos</h5>
                    <p class="text-muted">
                        Recopilamos información que nos proporcionas directamente al registrarte, como tu nombre y dirección de correo electrónico. También almacenamos el contenido que generas, como los registros de artrópodos, imágenes y sugerencias que envías.
                    </p>

                    <h5 class="mt-4">3. Cómo Usamos tu Información</h5>
                    <p class="text-muted">
                        Utilizamos la información que recopilamos para:
                        <ul>
                            <li>Proveer, mantener y mejorar nuestra aplicación.</li>
                            <li>Personalizar tu experiencia de usuario.</li>
                            <li>Asociar tus contribuciones (registros, sugerencias) a tu perfil.</li>
                            <li>Comunicarnos contigo sobre tu cuenta o sobre actualizaciones del servicio.</li>
                        </ul>
                    </p>

                    <h5 class="mt-4">4. Cómo Compartimos tu Información</h5>
                    <p class="text-muted">
                        No compartimos tu información personal con terceros, excepto para cumplir con la ley o proteger nuestros derechos. La información de tus contribuciones (como el nombre del artrópodo y la imagen) es visible para otros usuarios de la plataforma, pero tu información de contacto personal (email) se mantiene privada.
                    </p>
                    
                    <h5 class="mt-4">5. Seguridad de los Datos</h5>
                    <p class="text-muted">
                        Tomamos medidas razonables para proteger tu información contra pérdida, robo, uso indebido y acceso no autorizado. Las contraseñas se almacenan de forma encriptada (hashed) y nunca en texto plano.
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
