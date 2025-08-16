<?php
// Título de la página.
$page_title = 'Licencias y Atribuciones';

// Incluimos la cabecera.
require 'header.php';

// Esta página es pública, por lo que no requiere protección de sesión.
?>

<!-- Contenido específico de la página de Licencias -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="card-title display-5 mb-4">Licencias y Atribuciones</h1>
                    <p class="lead text-muted mb-5">
                        Esta plataforma ha sido construida gracias al generoso trabajo de la comunidad de código abierto. A continuación, se detallan las licencias de las principales herramientas utilizadas.
                    </p>

                    <div class="mb-4">
                        <h5>Bootstrap</h5>
                        <p class="text-muted">
                            Un potente framework de front-end para un desarrollo web más rápido y sencillo. Publicado bajo la <a href="https://github.com/twbs/bootstrap/blob/main/LICENSE" target="_blank">Licencia MIT</a>.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5>Bootstrap Icons</h5>
                        <p class="text-muted">
                            Una librería de iconos de alta calidad, diseñada para integrarse perfectamente con Bootstrap. Publicada bajo la <a href="https://github.com/twbs/icons/blob/main/LICENSE.md" target="_blank">Licencia MIT</a>.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5>Google Fonts (Inter & Merriweather)</h5>
                        <p class="text-muted">
                            Una librería de fuentes web de código abierto. Las fuentes utilizadas están publicadas bajo la <a href="https://scripts.sil.org/cms/scripts/page.php?site_id=nrsi&id=OFL" target="_blank">SIL Open Font License</a>.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5>MPDF</h5>
                        <p class="text-muted">
                            Una librería PHP para generar archivos PDF desde HTML. Publicada bajo la <a href="https://github.com/mpdf/mpdf/blob/development/LICENSE.md" target="_blank">Licencia Pública General de GNU v2.0</a>.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h5>PhpSpreadsheet</h5>
                        <p class="text-muted">
                            Una librería PHP para leer y escribir archivos de hojas de cálculo. Publicada bajo la <a href="https://github.com/PHPOffice/PhpSpreadsheet/blob/master/LICENSE" target="_blank">Licencia Pública Menor de GNU v2.1</a>.
                        </p>
                    </div>

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
