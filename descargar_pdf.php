<?php
// Incluimos la conexión a la base de datos y las funciones.
require 'conexion.php'; 

// Incluimos la librería MPDF a través del autoload de Composer.
require_once __DIR__ . '/vendor/autoload.php';

// Protección: solo usuarios logueados pueden descargar.
if (!isset($_SESSION['user_id'])) {
    die('Acceso denegado. Debes iniciar sesión para descargar el reporte.');
}

// Obtenemos todos los registros de la base de datos.
$insectos = fetchAll("SELECT * FROM artropodos ORDER BY arthropodo");

if (empty($insectos)) {
    die('No se encontraron registros para generar el PDF.');
}

// Creamos una instancia de MPDF con configuración optimizada para A4.
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
    'default_font_size' => 11,
    'default_font' => 'arial'
]);

// Configuramos el título del documento
$mpdf->SetTitle('Enciclopedia de Artrópodos');
$mpdf->SetAuthor('Sistema de Gestión Artrópodos');

// --- ESTILOS CSS OPTIMIZADOS PARA EL PDF ---
$css = "
    body { 
        font-family: Arial, sans-serif; 
        line-height: 1.3; 
        color: #333; 
        margin: 0; 
        padding: 0; 
    }
    
    .ficha-container { 
        width: 100%; 
        border: 1px solid #ddd; 
        border-radius: 6px; 
        overflow: hidden; 
        margin-bottom: 15px;
        page-break-inside: avoid;
    }
    
    .header-section { 
        text-align: center; 
        padding: 12px; 
        background: #f8f9fa; 
        border-bottom: 1px solid #e9ecef; 
    }
    
    .insect-image { 
        width: 120px; 
        height: 120px; 
        object-fit: cover; 
        border-radius: 6px; 
        border: 2px solid #28a745; 
        margin-bottom: 8px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    
    .species-title { 
        color: #28a745; 
        font-size: 18px; 
        font-weight: bold; 
        margin: 5px 0; 
        line-height: 1.2;
    }
    
    .content-section { 
        padding: 15px; 
    }
    
    .info-row { 
        margin-bottom: 12px; 
        padding-bottom: 10px; 
        border-bottom: 1px solid #e9ecef; 
    }
    
    .info-row:last-child { 
        border-bottom: none; 
        margin-bottom: 0; 
        padding-bottom: 0;
    }
    
    .label { 
        color: #28a745; 
        font-weight: bold; 
        font-size: 12px; 
        margin-bottom: 5px; 
        display: block; 
    }
    
    .content { 
        color: #555; 
        font-size: 11px; 
        line-height: 1.4; 
        text-align: justify; 
        margin: 0;
    }
    
    .taxonomia-grid { 
        width: 100%; 
        border-collapse: collapse; 
        margin: 5px 0;
    }
    
    .taxonomia-grid td { 
        width: 50%; 
        padding: 5px 8px; 
        vertical-align: top; 
        font-size: 11px;
        border: 1px solid #dee2e6;
        background: #ffffff;
    }
    
    .caracteristicas-grid { 
        width: 100%; 
        border-collapse: collapse; 
        margin: 5px 0;
    }
    
    .caracteristicas-grid td { 
        width: 33.33%; 
        padding: 6px 8px; 
        vertical-align: top; 
        font-size: 10px;
        border: 1px solid #dee2e6;
        background: #ffffff;
        text-align: center;
    }
    
    .product-section { 
        background: #f8f9fa; 
        margin: 0 -15px -15px; 
        padding: 12px 15px; 
        border-top: 1px solid #e9ecef; 
    }
    
    .product-grid { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 8px; 
    }
    
    .product-grid td { 
        width: 33.33%; 
        text-align: center; 
        padding: 8px 5px; 
        border: 1px solid #dee2e6; 
        background: white; 
        vertical-align: top;
    }
    
    .product-item .label { 
        margin-bottom: 3px; 
        font-size: 10px; 
        color: #28a745;
    }
    
    .product-item .value { 
        font-weight: bold; 
        color: #333; 
        font-size: 10px; 
        line-height: 1.2;
    }
    
    .descripcion-texto {
        font-size: 10px;
        line-height: 1.3;
        text-align: justify;
        margin: 0;
    }
    
    /* Evitar cortes de página innecesarios */
    .no-break {
        page-break-inside: avoid;
    }
    
    /* Ajustes para texto largo */
    .texto-largo {
        max-height: none;
        overflow: visible;
    }
";

// Escribimos los estilos en el PDF.
$mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

// --- FUNCIÓN PARA LIMPIAR Y TRUNCAR TEXTO ---
function limpiarTexto($texto, $maxLength = null) {
    $texto = trim($texto);
    if (empty($texto) || $texto === '') {
        return 'N/A';
    }
    
    // Limpiar caracteres especiales pero mantener saltos de línea
    $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    
    if ($maxLength && strlen($texto) > $maxLength) {
        $texto = substr($texto, 0, $maxLength) . '...';
    }
    
    return $texto;
}

// --- FUNCIÓN PARA PROCESAR IMAGEN ---
function procesarImagen($nombreImagen) {
    if (empty($nombreImagen)) {
        return '';
    }
    
    $imagePath = 'uploads/' . $nombreImagen;
    if (file_exists($imagePath)) {
        return $imagePath;
    }
    
    return '';
}

// --- GENERACIÓN DE LAS FICHAS ---
$totalInsectos = count($insectos);

foreach ($insectos as $index => $insecto) {
    
    // Procesamos la imagen
    $imageData = procesarImagen($insecto['imagen']);
    
    // Dividimos 'orden_familia' en dos partes si es posible.
    $taxonomia = explode('/', $insecto['orden_familia'] ?? '');
    $orden = limpiarTexto($taxonomia[0] ?? '');
    $familia = limpiarTexto($taxonomia[1] ?? '');
    
    // Limpiamos y preparamos los textos
    $arthropodo = limpiarTexto($insecto['arthropodo']);
    $tipoMetamorfosis = limpiarTexto($insecto['tipo_metamorfosis'] ?? '');
    $aparatoBucal = limpiarTexto($insecto['aparato_bucal'] ?? '');
    $habitoAlimenticio = limpiarTexto($insecto['habito_alimenticio'] ?? '');
    $biologia = limpiarTexto($insecto['biologia_caracteristicas'] ?? '', 500);
    $danioBenefico = limpiarTexto($insecto['danio_benefico'] ?? '', 400);
    $enemigosNaturales = limpiarTexto($insecto['enemigos_naturales'] ?? '', 400);
    $dosisControlador = limpiarTexto($insecto['dosis_controlador'] ?? '', 300);
    $nombreProducto = limpiarTexto($insecto['nombre_producto_quimico'] ?? '');
    $ingredienteActivo = limpiarTexto($insecto['ingrediente_activo'] ?? '');
    $dosisIca = limpiarTexto($insecto['dosis_ica'] ?? '');

    // Construimos el HTML optimizado para la ficha de este insecto.
    $html = '
    <div class="ficha-container no-break">
        <div class="header-section">
            '.($imageData ? '<img src="'.$imageData.'" alt="'.$arthropodo.'" class="insect-image">' : '').'
            <div class="species-title">'.$arthropodo.'</div>
        </div>
        
        <div class="content-section">
            <div class="info-row">
                <span class="label">Taxonomía</span>
                <table class="taxonomia-grid">
                    <tr>
                        <td><strong>Orden:</strong> '.$orden.'</td>
                        <td><strong>Familia:</strong> '.$familia.'</td>
                    </tr>
                </table>
            </div>
            
            <div class="info-row">
                <span class="label">Características Biológicas</span>
                <table class="caracteristicas-grid">
                    <tr>
                        <td><strong>Metamorfosis:</strong><br><span style="font-weight:normal;">'.$tipoMetamorfosis.'</span></td>
                        <td><strong>Aparato Bucal:</strong><br><span style="font-weight:normal;">'.$aparatoBucal.'</span></td>
                        <td><strong>Hábito Alimenticio:</strong><br><span style="font-weight:normal;">'.$habitoAlimenticio.'</span></td>
                    </tr>
                </table>
            </div>
            
            '.($biologia !== 'N/A' ? '<div class="info-row">
                <span class="label">Biología (Descripción General)</span>
                <div class="content descripcion-texto">'.nl2br($biologia).'</div>
            </div>' : '').'
            
            '.($danioBenefico !== 'N/A' ? '<div class="info-row">
                <span class="label">Daño / Beneficio</span>
                <div class="content descripcion-texto">'.nl2br($danioBenefico).'</div>
            </div>' : '').'
            
            '.($enemigosNaturales !== 'N/A' ? '<div class="info-row">
                <span class="label">Enemigos Biológicos / Naturales</span>
                <div class="content descripcion-texto">'.nl2br($enemigosNaturales).'</div>
            </div>' : '').'
            
            '.($dosisControlador !== 'N/A' ? '<div class="info-row">
                <span class="label">Dosis de Liberación (Controlador)</span>
                <div class="content descripcion-texto">'.nl2br($dosisControlador).'</div>
            </div>' : '').'
        </div>
        
        <div class="product-section">
            <span class="label">Información de Control Químico</span>
            <table class="product-grid">
                <tr>
                    <td>
                        <div class="product-item">
                            <div class="label">Nombre de Producto:</div>
                            <div class="value">'.$nombreProducto.'</div>
                        </div>
                    </td>
                    <td>
                        <div class="product-item">
                            <div class="label">Ingrediente Activo:</div>
                            <div class="value">'.$ingredienteActivo.'</div>
                        </div>
                    </td>
                    <td>
                        <div class="product-item">
                            <div class="label">Dosis Recomendada:</div>
                            <div class="value">'.$dosisIca.'</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    ';

    // Escribimos el HTML de esta ficha en el PDF.
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

    // Si no es el último insecto, añadimos un salto de página controlado.
    if ($index < $totalInsectos - 1) {
        $mpdf->AddPage();
    }
}

// --- CONFIGURACIÓN FINAL Y SALIDA DEL PDF ---
try {
    // Configuramos las propiedades del documento
    $mpdf->SetCreator('Sistema de Gestión de Artrópodos');
    $mpdf->SetSubject('Enciclopedia de Artrópodos - Fichas Técnicas');
    
    // Generamos el PDF y lo enviamos al navegador
    $nombreArchivo = 'Enciclopedia_Arthropodos_' . date('Y-m-d_H-i-s') . '.pdf';
    $mpdf->Output($nombreArchivo, 'I'); // 'I' para mostrar en navegador, 'D' para forzar descarga
    
} catch (Exception $e) {
    error_log('Error al generar PDF: ' . $e->getMessage());
    die('Error al generar el PDF. Por favor, contacte al administrador.');
}

exit;
?>