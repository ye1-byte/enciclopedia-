<?php
// Título de la página.
$page_title = 'Centro de Ayuda';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lógica para obtener todas las preguntas frecuentes de la base de datos.
try {
    $stmt = $pdo->query("SELECT * FROM ayuda_faq ORDER BY categoria, orden ASC");
    $faqs_raw = $stmt->fetchAll();

    // Agrupamos las preguntas por categoría para mostrarlas ordenadamente.
    $faqs_agrupadas = [];
    foreach ($faqs_raw as $faq) {
        $faqs_agrupadas[$faq['categoria']][] = $faq;
    }

} catch (PDOException $e) {
    $faqs_agrupadas = [];
    // Podríamos registrar el error si quisiéramos: error_log($e->getMessage());
}
?>

<!-- Contenido específico de la página de Ayuda -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Centro de Ayuda</h1>
        <p class="lead text-muted">Encuentra respuestas a las preguntas más comunes sobre el uso de la plataforma.</p>
    </div>

    <?php if (empty($faqs_agrupadas)): ?>
        <div class="alert alert-info text-center">
            <h4>No hay artículos de ayuda disponibles en este momento.</h4>
        </div>
    <?php else: ?>
        <?php foreach ($faqs_agrupadas as $categoria => $faqs): ?>
            <h3 class="mb-3 mt-4"><?php echo htmlspecialchars($categoria); ?></h3>
            <div class="accordion" id="accordion-<?php echo str_replace(' ', '', $categoria); ?>">
                <?php foreach ($faqs as $faq): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?php echo $faq['id']; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $faq['id']; ?>">
                                <?php echo htmlspecialchars($faq['pregunta']); ?>
                            </button>
                        </h2>
                        <div id="collapse-<?php echo $faq['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordion-<?php echo str_replace(' ', '', $categoria); ?>">
                            <div class="accordion-body">
                                <?php echo nl2br(htmlspecialchars($faq['respuesta'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Sección de Contacto Adicional -->
    <div class="text-center p-5 mt-5 border rounded bg-white">
        <i class="bi bi-envelope-paper-heart display-1 text-primary opacity-50"></i>
        <h3 class="mt-3">¿No encontraste lo que buscabas?</h3>
        <p class="lead text-muted">
            Si tu duda no está resuelta en esta sección, no dudes en ponerte en contacto con nosotros.
        </p>
        <a href="contacto.php" class="btn btn-primary mt-3">
            <i class="bi bi-send me-2"></i>Contactar a Soporte
        </a>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
