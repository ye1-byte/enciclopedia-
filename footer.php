<?php
// footer.php

// Asegúrate de que la constante URL_BASE esté definida
// Si no lo está, la definimos aquí para evitar errores fatales.
// Esto es una solución de respaldo robusta.
if (!defined('URL_BASE')) {
    // Es CRUCIAL que esta ruta sea la correcta para tu proyecto
    // Si tu proyecto está en htdocs/insectos_excel, usa '/insectos_excel/'
    // Si tu proyecto está directamente en htdocs, usa '/'
    define('URL_BASE', '/insectos_excel/');
}
?>
    </main>
    <footer class="bg-dark text-light mt-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-success mb-3"><i class="bi bi-bug-fill me-2"></i>Enciclopedia</h5>
                    <p class="small text-muted">Una plataforma colaborativa para entomólogos, estudiantes y amantes de la naturaleza.</p>
                </div>
                <div class="col-lg-2 col-md-3 col-6 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3">Navegación</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo URL_BASE; ?>index.php" class="text-light text-decoration-none small">Inicio</a></li>
                        <li><a href="<?php echo URL_BASE; ?>explorar.php" class="text-light text-decoration-none small">Explorar</a></li>
                        <li><a href="<?php echo URL_BASE; ?>galeria.php" class="text-light text-decoration-none small">Galería</a></li>
                        <li><a href="<?php echo URL_BASE; ?>glosario.php" class="text-light text-decoration-none small">Glosario</a></li>
                        <li><a href="<?php echo URL_BASE; ?>registros.php" class="text-light text-decoration-none small">Mis Registros</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3 col-6 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3">Recursos</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo URL_BASE; ?>guias.php" class="text-light text-decoration-none small">Guías de Identificación</a></li>
                        <li><a href="<?php echo URL_BASE; ?>bibliografia.php" class="text-light text-decoration-none small">Bibliografía</a></li>
                        <li><a href="<?php echo URL_BASE; ?>api.php" class="text-light text-decoration-none small">API para Desarrolladores</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-3 col-6 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3">Comunidad y Soporte</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo URL_BASE; ?>sugerencias.php" class="text-light text-decoration-none small">Buzón de Sugerencias</a></li>
                        <li><a href="<?php echo URL_BASE; ?>reportar.php" class="text-light text-decoration-none small">Reportar un Problema</a></li>
                        <li><a href="<?php echo URL_BASE; ?>ayuda.php" class="text-light text-decoration-none small">Centro de Ayuda</a></li>
                        <li><a href="<?php echo URL_BASE; ?>contacto.php" class="text-light text-decoration-none small">Contacto</a></li>
                        <li><a href="<?php echo URL_BASE; ?>changelog.php" class="text-light text-decoration-none small">Novedades (Changelog)</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-12 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo URL_BASE; ?>privacidad.php" class="text-light text-decoration-none small">Política de Privacidad</a></li>
                        <li><a href="<?php echo URL_BASE; ?>terminos.php" class="text-light text-decoration-none small">Términos de Uso</a></li>
                        <li><a href="<?php echo URL_BASE; ?>cookies.php" class="text-light text-decoration-none small">Política de Cookies</a></li>
                        <li><a href="<?php echo URL_BASE; ?>licencias.php" class="text-light text-decoration-none small">Licencias</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            &copy; <?php echo date('Y'); ?> Todos los derechos reservados.
        </div>
    </footer>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Imagen Ampliada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" class="img-fluid" alt="Imagen ampliada" style="max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', function(event) {
                const triggerElement = event.relatedTarget;
                const imageUrl = triggerElement.getAttribute('data-bs-image-url');
                const imageTitle = triggerElement.getAttribute('data-bs-image-title') || 'Imagen';
                const modalTitle = imageModal.querySelector('.modal-title');
                const modalImage = imageModal.querySelector('#modalImage');
                modalTitle.textContent = imageTitle;
                modalImage.src = imageUrl;
            });
        }
    });
    </script>
</body>
</html>