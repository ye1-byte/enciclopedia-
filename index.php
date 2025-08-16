<?php
$page_title = 'Página Principal';
require 'header.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }

// Lógica de búsqueda
$searchTerm = $_GET['q'] ?? '';
$sql = "SELECT * FROM artropodos";
$params = [];
if (!empty($searchTerm)) {
    $sql .= " WHERE arthropodo LIKE ? OR orden_familia LIKE ? OR ingrediente_activo LIKE ?";
    $searchParam = "%" . $searchTerm . "%";
    $params = [$searchParam, $searchParam, $searchParam];
}
$sql .= " ORDER BY arthropodo";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$insectos = $stmt->fetchAll();
?>

<h1 class="display-5 text-center mb-4">Enciclopedia Interactiva de Artrópodos</h1>
<div class="text-center mb-4">
    <a href="formulario.php" class="btn btn-primary rounded-pill"><i class="bi bi-plus-lg"></i> Agregar Registro</a>
    <a href="descargar_pdf.php" class="btn btn-info rounded-pill" target="_blank"><i class="bi bi-file-pdf"></i> Descargar PDF</a>
</div>

<!-- Formulario de Búsqueda -->
<form action="index.php" method="GET" class="mb-5">
    <div class="input-group input-group-lg">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, familia, ingrediente activo..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
    </div>
</form>

<!-- Acordeón de Bootstrap 5 -->
<div class="accordion" id="accordionArthropods">
    <?php if (empty($insectos)): ?>
        <div class="alert alert-info text-center">
            <h4>No se encontraron resultados</h4>
            <p>No hay registros que coincidan con tu búsqueda. Intenta con otras palabras o <a href="index.php" class="alert-link">mira la lista completa</a>.</p>
        </div>
    <?php else: ?>
        <?php foreach ($insectos as $insecto): ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?php echo $insecto['id']; ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $insecto['id']; ?>">
                    <span class="flex-grow-1 me-3"><?php echo htmlspecialchars($insecto['arthropodo']); ?></span>
                    <span class="badge rounded-pill bg-info text-dark"><?php echo htmlspecialchars($insecto['orden_familia']); ?></span>
                </button>
            </h2>
            <div id="collapse<?php echo $insecto['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionArthropods">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <?php if (!empty($insecto['imagen']) && file_exists('uploads/' . $insecto['imagen'])):
                                $imageUrl = 'uploads/' . htmlspecialchars($insecto['imagen']);
                            ?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" 
                                   data-bs-image-url="<?php echo $imageUrl; ?>" 
                                   data-bs-image-title="<?php echo htmlspecialchars($insecto['arthropodo']); ?>">
                                    <img src="<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($insecto['arthropodo']); ?>" class="img-fluid rounded shadow-sm">
                                </a>
                            <?php else: ?>
                                <div class="text-center text-muted p-5 border rounded">Sin imagen</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <p><strong>Biología (Descripción General):</strong><br><?php echo nl2br(htmlspecialchars($insecto['biologia_caracteristicas'])); ?></p>
                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <strong>Metamorfosis:</strong>
                                    <p class="text-muted"><?php echo htmlspecialchars($insecto['tipo_metamorfosis'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <strong>Aparato Bucal:</strong>
                                    <p class="text-muted"><?php echo htmlspecialchars($insecto['aparato_bucal'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <strong>Hábito Alimenticio:</strong>
                                    <p class="text-muted"><?php echo htmlspecialchars($insecto['habito_alimenticio'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <p><strong>Daño / Beneficio:</strong><br><?php echo nl2br(htmlspecialchars($insecto['danio_benefico'])); ?></p>
                            <p><strong>Enemigos Biológicos / Naturales:</strong><br><?php echo nl2br(htmlspecialchars($insecto['enemigos_naturales'])); ?></p>
                            <?php if (!empty($insecto['dosis_controlador'])): ?>
                                <p><strong>Dosis de Liberación:</strong><br><?php echo nl2br(htmlspecialchars($insecto['dosis_controlador'])); ?></p>
                            <?php endif; ?>
                            <hr>
                            <h5>Control Químico</h5>
                            <p><strong>Nombre del Producto:</strong><br><?php echo htmlspecialchars($insecto['nombre_producto_quimico']); ?></p>
                            <p><strong>Ingrediente Activo:</strong><br><?php echo htmlspecialchars($insecto['ingrediente_activo']); ?></p>
                            <p><strong>Dosis Recomendada:</strong><br><?php echo htmlspecialchars($insecto['dosis_ica']); ?></p>
                        </div>
                    </div>
                    
                    <!-- ***** SECCIÓN DE COMENTARIOS RESTAURADA ***** -->
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Comentarios</h5>
                            <?php
                            $stmt_comentarios = $pdo->prepare("SELECT c.*, u.nombre as nombre_usuario FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.artropodo_id = ? ORDER BY c.fecha DESC");
                            $stmt_comentarios->execute([$insecto['id']]);
                            $comentarios = $stmt_comentarios->fetchAll();
                            ?>

                            <form class="comment-form mb-4" data-artropodo-id="<?php echo $insecto['id']; ?>">
                                <div class="input-group">
                                    <input type="text" name="comentario" class="form-control" placeholder="Añade un comentario..." required>
                                    <button class="btn btn-outline-primary" type="submit">Comentar</button>
                                </div>
                            </form>

                            <div class="comment-list" id="comment-list-<?php echo $insecto['id']; ?>">
                                <?php if (empty($comentarios)): ?>
                                    <p class="text-muted small no-comments">Aún no hay comentarios. ¡Sé el primero!</p>
                                <?php else: ?>
                                    <?php foreach ($comentarios as $comentario): ?>
                                    <div class="d-flex justify-content-between align-items-start mb-2" id="comment-<?php echo $comentario['id']; ?>">
                                        <div>
                                            <strong><?php echo htmlspecialchars($comentario['nombre_usuario']); ?>:</strong>
                                            <p class="mb-1 d-inline"><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                                            <small class="text-muted d-block"><?php echo date('d/m/Y', strtotime($comentario['fecha'])); ?></small>
                                        </div>
                                        <?php if ($comentario['usuario_id'] == $_SESSION['user_id'] || ($_SESSION['user_role'] ?? 'usuario') === 'admin'): ?>
                                        <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="<?php echo $comentario['id']; ?>" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-end mt-3 border-top pt-3">
                        <a href="formulario.php?id=<?php echo $insecto['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i> Editar</a>
                        <form action="eliminar.php" method="post" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
                            <input type="hidden" name="id" value="<?php echo $insecto['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- SCRIPT DE JAVASCRIPT ESPECÍFICO PARA ESTA PÁGINA -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- MANEJO DE ENVÍO DE COMENTARIOS ---
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('add_comment', '1');
            formData.append('artropodo_id', this.dataset.artropodoId);
            
            fetch('api_comentarios.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const commentList = document.getElementById('comment-list-' + this.dataset.artropodoId);
                    const noCommentsMsg = commentList.querySelector('.no-comments');
                    if (noCommentsMsg) noCommentsMsg.remove();
                    
                    const newComment = data.comment;
                    const commentHTML = `
                        <div class="d-flex justify-content-between align-items-start mb-2" id="comment-${newComment.id}">
                            <div>
                                <strong>${escapeHTML(newComment.nombre_usuario)}:</strong>
                                <p class="mb-1 d-inline">${escapeHTML(newComment.comentario)}</p>
                                <small class="text-muted d-block">${new Date(newComment.fecha).toLocaleDateString('es-ES')}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger delete-comment-btn" data-comment-id="${newComment.id}" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>`;
                    commentList.insertAdjacentHTML('afterbegin', commentHTML);
                    this.reset();
                } else {
                    alert('Error: ' + (data.error || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error al enviar el comentario:', error);
                alert('Ocurrió un error inesperado. Revisa la consola para más detalles.');
            });
        });
    });

    // --- MANEJO DE ELIMINACIÓN DE COMENTARIOS (CON DELEGACIÓN DE EVENTOS) ---
    document.getElementById('accordionArthropods').addEventListener('click', function(e) {
        if (e.target && e.target.closest('.delete-comment-btn')) {
            const button = e.target.closest('.delete-comment-btn');
            const commentId = button.dataset.commentId;
            
            if (confirm('¿Seguro que quieres eliminar este comentario?')) {
                const formData = new FormData();
                formData.append('delete_comment', '1');
                formData.append('comentario_id', commentId);

                fetch('api_comentarios.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const commentElement = document.getElementById('comment-' + data.deleted_id);
                        if (commentElement) {
                            commentElement.remove();
                        }
                    } else {
                        alert('Error: ' + (data.error || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar el comentario:', error);
                    alert('Ocurrió un error inesperado.');
                });
            }
        }
    });

    // Función para escapar HTML y prevenir ataques XSS
    function escapeHTML(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
</script>

<?php require 'footer.php'; ?>
