<?php
$page_title = 'Documentación de la API';
require 'header.php';
requireLogin(); // Solo usuarios logueados pueden ver la documentación
?>

<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">API para Desarrolladores</h1>
        <p class="lead text-muted">Accede a los datos de la enciclopedia de forma programática.</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h4 class="card-title">Introducción</h4>
            <p>Nuestra API RESTful te permite obtener los datos de los artrópodos en formato JSON para usarlos en tus propias aplicaciones, investigaciones o herramientas. El acceso a la API está actualmente restringido a usuarios autenticados.</p>
            
            <hr class="my-4">

            <h4 class="card-title">Endpoints Disponibles (Versión 1)</h4>
            
            <!-- Endpoint 1: Obtener todos los artrópodos -->
            <div class="mb-4">
                <h5 class="mt-3">Obtener todos los registros</h5>
                <p>Devuelve una lista de todos los artrópodos en la base de datos.</p>
                <p><strong>Método:</strong> <span class="badge bg-success">GET</span></p>
                <p><strong>Endpoint:</strong></p>
                <pre class="bg-light p-3 rounded"><code><?php echo BASE_URL; ?>api/v1/artropodos.php</code></pre>
                <p><strong>Ejemplo de Respuesta (JSON):</strong></p>
                <pre class="bg-light p-3 rounded"><code>[
    {
        "id": 1,
        "arthropodo": "Hypothenemus hampei",
        "orden_familia": "Coleoptera / Scolytinae",
        "imagen": "imagen1.jpg",
        ...
    },
    {
        "id": 2,
        "arthropodo": "Spodoptera frugiperda",
        "orden_familia": "Lepidoptera / Noctuidae",
        "imagen": "imagen2.png",
        ...
    }
]</code></pre>
            </div>

            <!-- Endpoint 2: Obtener un artrópodo por ID -->
            <div>
                <h5 class="mt-3">Obtener un registro por ID</h5>
                <p>Devuelve los detalles de un artrópodo específico.</p>
                <p><strong>Método:</strong> <span class="badge bg-success">GET</span></p>
                <p><strong>Endpoint:</strong></p>
                <pre class="bg-light p-3 rounded"><code><?php echo BASE_URL; ?>api/v1/artropodos.php?id={ID_DEL_ARTROPODO}</code></pre>
                <p><strong>Ejemplo de Petición:</strong></p>
                <pre class="bg-light p-3 rounded"><code><?php echo BASE_URL; ?>api/v1/artropodos.php?id=1</code></pre>
                <p><strong>Ejemplo de Respuesta (JSON):</strong></p>
                <pre class="bg-light p-3 rounded"><code>{
    "id": 1,
    "usuario_id": 1,
    "arthropodo": "Hypothenemus hampei",
    "orden_familia": "Coleoptera / Scolytinae",
    "biologia_caracteristicas": "La hembra perfora el fruto...",
    ...
}</code></pre>
            </div>
        </div>
    </div>
</div>

<?php
require 'footer.php';
?>
