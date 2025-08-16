<?php
echo "Paso 1: Intentando incluir conexion.php";
require_once '../conexion.php'; 
echo "Paso 2: conexion.php incluido. Intentando incluir header.php";
require_once '../header.php';
echo "Paso 3: header.php incluido. El HTML se mostrará aquí.";
?>
<p>Si ves esto, todo está funcionando.</p>
<?php
require_once '../footer.php';
echo "Paso 4: footer.php incluido.";
?>