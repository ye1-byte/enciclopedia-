<?php
// Título de la página.
$page_title = 'Novedades y Actualizaciones';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// En una aplicación real, estos datos vendrían de una tabla en la base de datos.
// Por ahora, los definimos en un array para mostrar el historial de nuestro desarrollo.
$changelog = [
    '1.2' => [
        'fecha' => '15 de Agosto de 2025',
        'titulo' => 'Versión 1.2 - El Ecosistema Interactivo',
        'estado' => 'Estable', // NUEVO CAMPO
        'items' => [
            ['tipo' => 'NUEVO', 'texto' => 'Implementada una API RESTful (v1) para acceso programático a los datos, con filtros avanzados.'],
            ['tipo' => 'NUEVO', 'texto' => 'Añadido un sistema completo de avatares de usuario, con subida de imágenes en la página de perfil.'],
            ['tipo' => 'MEJORA', 'texto' => 'La sección de comentarios ahora es dinámica (AJAX), permitiendo añadir y eliminar comentarios sin recargar la página.'],
            ['tipo' => 'NUEVO', 'texto' => 'El Portal (`portal.php`) se ha convertido en el centro de navegación principal de la aplicación.'],
            ['tipo' => 'MEJORA', 'texto' => 'Se ha mejorado la navegación con un botón contextual "Volver al Portal" en todas las secciones internas.'],
            ['tipo' => 'NUEVO', 'texto' => 'La sección de Guías (`guias.php`) ahora es una biblioteca funcional que permite a los usuarios subir y eliminar archivos PDF.'],
            ['tipo' => 'NUEVO', 'texto' => 'El Glosario (`glosario.php`) es ahora una base de datos dinámica con funciones de agregar, editar y eliminar para administradores.'],
            ['tipo' => 'NUEVO', 'texto' => 'Implementado un Centro de Ayuda (`ayuda.php`) funcional con una sección de Preguntas Frecuentes (FAQ).'],
            ['tipo' => 'CRÍTICO', 'texto' => 'Actualización tecnológica completa del front-end a Bootstrap 5, mejorando el diseño y la compatibilidad.'],
        ]
    ],
    '1.0' => [
        'fecha' => '14 de Agosto de 2025',
        'titulo' => 'Versión 1.0 - La Experiencia Completa',
        'estado' => 'Antigua', // NUEVO CAMPO
        'items' => [
            ['tipo' => 'NUEVO', 'texto' => 'Implementado un sistema de reportes funcional para que los usuarios informen de errores.'],
            ['tipo' => 'NUEVO', 'texto' => 'Añadido un sistema de sugerencias para que la comunidad proponga mejoras.'],
            ['tipo' => 'MEJORA', 'texto' => 'Rediseño completo de la interfaz (UX/UI) con una paleta de colores profesional, nueva tipografía y mejor jerarquía visual.'],
            ['tipo' => 'MEJORA', 'texto' => 'Añadida una barra de búsqueda funcional en la página principal para filtrar registros.'],
            ['tipo' => 'NUEVO', 'texto' => 'Creadas páginas de marcador de posición para futuras secciones (Galería, Guías, Taxonomía, etc.).']
        ]
    ],
    '0.8' => [
        'fecha' => 'Agosto de 2025',
        'titulo' => 'Versión 0.8 - El Salto a Aplicación Web',
        'estado' => 'Obsoleta', // NUEVO CAMPO
        'items' => [
            ['tipo' => 'NUEVO', 'texto' => 'Implementado un sistema de Registro y Login para usuarios.'],
            ['tipo' => 'NUEVO', 'texto' => 'Creado un Panel de Administración para gestionar usuarios y ver el historial.'],
            ['tipo' => 'NUEVO', 'texto' => 'Añadido un sistema de roles (Usuario y Administrador).'],
            ['tipo' => 'NUEVO', 'texto' => 'Implementado un historial de cambios que registra todas las creaciones, ediciones y eliminaciones.'],
        ]
    ],
    '0.5' => [
        'fecha' => 'Agosto de 2025',
        'titulo' => 'Versión 0.5 - La Profesionalización',
        'estado' => 'Obsoleta', // NUEVO CAMPO
        'items' => [
            ['tipo' => 'CRÍTICO', 'texto' => 'Migración completa de la base de datos desde un archivo Excel a un sistema profesional MySQL.'],
            ['tipo' => 'MEJORA', 'texto' => 'Implementado un sistema de plantillas (header.php y footer.php) para unificar el diseño y facilitar el mantenimiento.'],
            ['tipo' => 'MEJORA', 'texto' => 'El código se actualizó para usar PDO, el método moderno y seguro de conexión a bases de datos.'],
        ]
    ],
    '0.1' => [
        'fecha' => 'Agosto de 2025',
        'titulo' => 'Versión 0.1 - El Prototipo Inicial',
        'estado' => 'Obsoleta', // NUEVO CAMPO
        'items' => [
            ['tipo' => 'NUEVO', 'texto' => 'Creación de la aplicación inicial utilizando un archivo de Excel como base de datos.'],
            ['tipo' => 'NUEVO', 'texto' => 'Funcionalidad básica para Añadir, Editar y Eliminar registros.'],
            ['tipo' => 'NUEVO', 'texto' => 'Implementada la subida de imágenes y la generación de reportes en PDF.'],
        ]
    ]
];

?>

<!-- Contenido específico de la página de Novedades -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Registro de Cambios</h1>
        <p class="lead text-muted">Mantente al día con las últimas mejoras y nuevas funcionalidades de la enciclopedia.</p>
    </div>

    <!-- Línea de tiempo de versiones -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php foreach ($changelog as $version => $data): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Versión <?php echo $version; ?></h4>
                            <small><?php echo $data['fecha']; ?></small>
                        </div>
                        <?php
                            // Lógica para determinar el color de la insignia de estado
                            $estado_class = 'bg-secondary'; // Por defecto
                            if ($data['estado'] == 'Estable') $estado_class = 'bg-success';
                            if ($data['estado'] == 'Antigua') $estado_class = 'bg-warning text-dark';
                        ?>
                        <span class="badge <?php echo $estado_class; ?>"><?php echo $data['estado']; ?></span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $data['titulo']; ?></h5>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($data['items'] as $item): 
                                $badge_class = 'bg-info'; // Por defecto
                                if ($item['tipo'] == 'NUEVO') $badge_class = 'bg-success';
                                if ($item['tipo'] == 'MEJORA') $badge_class = 'bg-primary';
                                if ($item['tipo'] == 'CRÍTICO') $badge_class = 'bg-danger';
                            ?>
                                <li class="list-group-item d-flex align-items-start">
                                    <span class="badge <?php echo $badge_class; ?> me-3 mt-1"><?php echo $item['tipo']; ?></span>
                                    <span><?php echo $item['texto']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
