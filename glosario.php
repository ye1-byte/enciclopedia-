<?php
// --- FASE 1: LÓGICA Y PROCESAMIENTO ---
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$is_admin = ($_SESSION['user_role'] ?? 'usuario') === 'admin';

// Procesamiento de acciones CRUD (solo para admins)
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'add':
                $termino = trim($_POST['termino']);
                $definicion = trim($_POST['definicion']);
                if (!empty($termino) && !empty($definicion)) {
                    $check = $pdo->prepare("SELECT COUNT(*) FROM glosario WHERE LOWER(termino) = LOWER(?)");
                    $check->execute([$termino]);
                    if ($check->fetchColumn() == 0) {
                        $stmt = $pdo->prepare("INSERT INTO glosario (termino, definicion, usuario_id) VALUES (?, ?, ?)");
                        $stmt->execute([$termino, $definicion, $_SESSION['user_id']]);
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Término agregado exitosamente.'];
                    } else {
                        $_SESSION['message'] = ['type' => 'warning', 'text' => 'El término ya existe en el glosario.'];
                    }
                } else {
                    $_SESSION['message'] = ['type' => 'danger', 'text' => 'El término y la definición son obligatorios.'];
                }
                break;

            case 'edit':
                $id = (int)$_POST['id'];
                $termino = trim($_POST['termino']);
                $definicion = trim($_POST['definicion']);
                if (!empty($termino) && !empty($definicion) && $id > 0) {
                    $stmt = $pdo->prepare("UPDATE glosario SET termino = ?, definicion = ? WHERE id = ?");
                    $stmt->execute([$termino, $definicion, $id]);
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Término actualizado exitosamente.'];
                }
                break;

            case 'delete':
                $id = (int)$_POST['id'];
                if ($id > 0) {
                    $stmt = $pdo->prepare("DELETE FROM glosario WHERE id = ?");
                    $stmt->execute([$id]);
                    $_SESSION['message'] = ['type' => 'info', 'text' => 'Término eliminado exitosamente.'];
                }
                break;
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Error en la base de datos: ' . $e->getMessage()];
    }
    header('Location: glosario.php');
    exit();
}

// --- FASE 2: PREPARACIÓN DE DATOS PARA LA VISTA ---
$page_title = 'Glosario de Términos';

// Lógica de búsqueda
$searchTerm = $_GET['q'] ?? '';
$sql = "SELECT g.*, u.nombre as nombre_usuario FROM glosario g LEFT JOIN usuarios u ON g.usuario_id = u.id";
$params = [];

if (!empty($searchTerm)) {
    $sql .= " WHERE g.termino LIKE ? OR g.definicion LIKE ?";
    $searchParam = "%" . $searchTerm . "%";
    $params = [$searchParam, $searchParam];
}
$sql .= " ORDER BY g.termino ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$terminos = $stmt->fetchAll();

// Si no hay términos en la BD, insertamos el glosario inicial
if (empty($terminos) && empty($searchTerm)) {
    $glosario_inicial = [
        'Áfidos' => 'Insectos pertenecientes a la familia Aphididae del orden Hemíptera, también conocidos como pulgones o piojillos.',
        'Afelínido' => 'Diminuta avispita perteneciente a la familia Aphelinidae del orden Himenóptera, normalmente parasitoides de pulgones, chanchitos blancos, mosquitas blancas o escamas.',
        'Albedo' => 'Sección de color blanquecino de la epidermis de los frutos del genero Citrus, la que está constituida por una masa gruesa, esponjosa y blanquecina.',
        'Áptera' => 'Sin alas.',
        'Arrenotóquico' => 'Reproducción asexual (partenogénesis) donde la progenie son todos machos.',
        'Artejos' => 'Cualquier pieza o segmento que forme parte de un segmento.',
        'Baja toxicidad' => 'Tener un efecto tóxico mínimo sobre organismos no objetivos.',
        'Bioracional' => 'Tener una influencia negativa mínima sobre el ambiente y sus habitantes (ejemplo, un insecticida bioracional).',
        'Amplio-espectro (insecticida)' => 'Activo contra una amplia gama de insectos.',
        'Bivoltina' => 'Insectos que en un año presentan dos generaciones.',
        'Bracónido' => 'Los Bracónidos (Braconidae) son la segunda familia más grande de Himenóptera (perteneciente a la superfamilia Ichneumonoidea) con al menos 40.000 especies; tiene distribución mundial y es diversa en todas las áreas. Son avispas principalmente de hábitos parásitos, el estado larval se desarrolla sobre o dentro del cuerpo de sus presas (otros insectos).',
        'Canopia' => 'La "parte verde" de la planta, el "dosel" del árbol.',
        'Capullo' => 'Funda de seda formada por una larva de insecto para la pupación.',
        'Carina' => 'Pliegue central que posee una escama (queresa).',
        'Casta' => 'Formas en las que se dividen los individuos de las colonias en los insectos sociales. Por ejemplo, la reina, las obreras, soldados, etcétera.',
        'Cauda' => 'Cola, cualquier proceso semejante a una cola, últimos segmentos abdominales.',
        'Cercos' => 'Son apéndices pares, a menudo sensoriales, en el extremo posterior de muchos insectos; las pinzas de las tijeretas (dermápteros) son cercos modificados.',
        'Clasper' => 'Es una estructura (órgano modificado o parte de éste) que utilizan los machos para sostener a la hembra durante el copulación.',
        'Coccinélido' => 'Insecto de la familia Coccinelidae del orden Coleóptera, cuyas larvas y adultos son eficientes depredadores de diversas plagas como áfidos y chanchitos blancos.',
        'Coleóptero/Coleoptera' => 'Orden de insectos que poseen aparato bucal masticador y un par de alas gruesas coriáceas llamadas élitros, que cubren un par de de alas membranosas que permanecen plegadas, bajo las anteriores. La mayoría de los coleópteros son fitófagos, y muchas especies pueden constituir plagas de los cultivos, siendo las larvas las que causan la mayor parte de los daños agrícolas y forestales.',
        'Control biológico' => 'El uso de organismos vivos, tales como depredadores, de parasitoides, y de patógenos, para controlar insectos plaga, malas hierbas, o enfermedades. Típicamente involucra una cierta actividad humana.',
        'Control biológico clásico' => 'La importación de enemigos naturales extranjeros para el control de plagas introducidas previamente, o nativas.',
        'Control biológico aumentativo o inundativo' => 'Consiste en liberaciones a gran escala de enemigos naturales durante el desarrollo del cultivo con el fin de controlar plagas.',
        'Control biológico inoculativo' => 'Consiste en la liberación de una cantidad moderada de enemigos naturales, generalmente temprano en la temporada con el fin que se reproduzcan. Se utiliza frecuentemente en el control de plagas en invernaderos.',
        'Cornículos' => 'Túbulos o proyecciones melíferos erectos o semierectos en cantidad de dos presentes en la parte abdominal dorsal de los áfidos o pulgones.',
        'Crash' => 'Disminución dramática de la densidad de una especie.',
        'Cremaster' => 'El ápice del último segmento del abdomen de la pupa; las espinas terminales del abdomen, que ayuda a la pupa –cuando es subterránea– a desplazarse en la tierra o en pupas arbóreas para suspenderse.',
        'Crochets' => 'Cada uno de los elementos cuniculares esclerosados, en forma de gancho y dispuesto en filas o círculo en los espuripedios (falsas patas) de la larva de lepidópteros.',
        'Cuarentenario' => 'Cualquier insecto que presente restricciones de ingreso en un país, afectando a los productos hortofrutícolas de exportación, los cuales son rechazados durante las inspecciones fitosanitarias.',
        'Depresión de la endogamia (inbreeding depression)' => 'La cruza entre individuos emparentados produce disminución de viabilidad y fertilidad en la descendencia.',
        'Depredador' => 'Organismo que se alimenta de otro causándole daños totales que pueden acarrear la muerte.',
        'Depredador generalista' => 'Enemigo natural que puede depredar un amplio rango de especies.',
        'Deutoninfa' => 'Segundo estado ninfal de la metamorfosis de algunos ácaros.',
        'Dimorfismo' => 'Diferencia entre individuos de la misma especie, puede ser estacional, sexual o geográfica.',
        'Drench' => 'Tratamiento de poscosecha que consiste en sumergir en cera y/o pesticida la fruta.',
        'Ectoparasitoides' => 'Parásito que vive sobre su huésped.',
        'Edeago' => 'Órgano copulador del macho.',
        'Elitros' => 'Son las alas anteriores, modificadas por endurecimiento (esclerotización), de ciertos órdenes de insectos (Coleóptero y Hemíptero). Sirven como protección para las alas posteriores que están inmediatamente debajo y que sirven para ejecutar el vuelo.',
        'Encírtido' => 'Insecto del orden Himenóptera en su mayoría parasitoides de áfidos, escamas y mosquitas blancas.',
        'Endémico' => 'Se dice de la planta o animal, originaria de un país o región.',
        'Endoparasitoides' => 'Parasito que vive en el interior de su huésped.',
        'Endosimbiontes' => 'Individuos de una especie que residen dentro de las células de otra especie, en una asociación estrecha.',
        'Entomofauna' => 'Conjunto de todas las clases de insecto de una región.',
        'Escapo' => 'Segmento basal o primer artejo de antenas.',
        'Escutelo' => 'Escudo de forma triangular de los insectos del orden Hemíptera, dispuesto generalmente en el dorso entre las alas.',
        'Espiráculos' => 'Las aberturas externas del sistema (traqueal) de respiración del insecto.',
        'Estadio (etapa de vida)' => 'Etapa de un insecto entre mudas sucesivas, por ejemplo, primer estadio es la etapa entre eclosión del huevo a la primera muda. Se utiliza para las etapas en larvas y ninfas de insectos.',
        'Estafilínido' => 'Insecto de la familia Staphylinidae del orden Coleóptera que incluye especies saprófagas, fitófagas y depredadoras.',
        'Estridulación' => 'Ruido producido por algunos insectos al frotar partes de su cuerpo como alas o patas, ejemplo, grillos.',
        'Exocarpio' => 'Capa más externa de la pared del ovario o fruto.',
        'Exoesqueleto' => 'Recubrimiento formado por la cutícula, generalmente duro, que envuelve el cuerpo de los artrópodos y que proporciona sostén al cuerpo actuando como un esqueleto.',
        'Exuvio' => 'Tegumento abandonado de un estado juvenil en la metamorfosis.',
        'Fitness' => 'Éxito reproductivo relativo de un genotipo medido como sobrevivencia, fecundidad u otros parámetros del ciclo de vida.',
        'Fitoseídos' => 'Ácaros de la familia Phytoseiidae, orden Acariformes algunas especies son depredadores de huevos, larvas y adultos de insectos y ácaros fitófagos.',
        'Foresia' => 'Tipo de relación entre organismos, parecida al mutualismo en la que un individuo transporta a otro o una espora o semilla de otro. Es una relación de beneficio unilateral, pues solo una especie obtiene una ganancia. En este caso la ganancia es el desplazamiento.',
        'Gáster' => 'En Himenóptera segmentos abdominales ubicados a continuación del pedicelo.',
        'Generación' => 'Periodo desde cualquier estado en el ciclo de vida al mismo estado de vida en la descendencia. Típicamente de huevo a huevo.',
        'Genotipo' => 'Conjunto de genes que posee un organismo.',
        'Gregarios' => 'Tendencia de los animales a vivir juntos.',
        'Hemolinfa' => 'Término usado para referirse a la sangre de los insectos, corresponde a un plasma claro, debido que en la mayoría de los casos no posee hemoglobina, que es la que da el color a la sangre de los animales.',
        'Hermafrodita' => 'Un organismo que presenta ambos órganos reproductores, femenino y masculino.',
        'Himenóptero/Himenoptera' => 'Orden de insectos que se caracteriza por poseer dos pares de alas membranosas, con escasas nervaduras y grandes celdillas en éstas, ejemplo, avispas.',
        'Hiperparásito' => 'Un parásito cuyo hospedero es otro parásito.',
        'Homocigosis' => 'Depresión del vigor debido a consanguinidad producto de una alta tasa de endogamia.',
        'Homocromía' => 'Homogeneidad de color.',
        'Homóptera' => 'Suborden del Orden Hemíptera que se incluye insectos que se caracteriza por tener un aparato bucal picador chupador y las alas homogéneas, ejemplo, áfidos, conchuelas, escamas y chanchitos blancos.',
        'Hospederos' => 'Organismo en el cual otro organismo pasa parte o toda su vida y del que obtiene alimento o protección.',
        'Host feeding' => 'Alimentarse de los fluidos corporales de insectos que son parasitados o no parasitadas.',
        'Idiobiontes' => 'Parasitoide que se desarrolla dentro del hospedador, encontrándose éste muerto o paralizado.',
        'Imagos' => 'El último estado o adulto, en la metamorfosis del insecto, en insecto perfecto.',
        'Larva' => 'Estado inmaduro entre el huevo y pupa de los insectos teniendo una completa metamorfosis donde adonde los inmaduros difieren radicalmente del adulto (ejemplo, orugas, gusanos).',
        'Lepidóptero/Lepidoptera' => 'Orden de insectos que en estado adulto poseen alas cubiertas de pequeñas escamas.',
        'Ligamaza (= Mielecilla)' => 'La descarga líquida azucarada del ano de ciertos insectos (Homóptera) por ejemplo áfidos y escamas.',
        'Mesocarpio' => 'Estrata intermedia de la pared del ovario o fruto, ubicada entre el epicarpio y endocarpio.',
        'Mesófilo' => 'Tejido parenquimatoso situado entre las epidermis superior e inferior de la hoja.',
        'Muda' => 'Cambio periódico del exoesqueleto en los artrópodos.',
        'Multivoltina' => 'Tener más de una generación por la temporada.',
        'Neuroptero/Neuroptera' => 'Orden de insectos con cuatro alas reticuladas, aparato bucal con mandíbulas, cabeza libre, tórax escasamente aglutinado y metamorfosis completa.',
        'Ninfa' => 'En la metamorfosis incompleta, el insecto que nace con una forma similar a la adulta.',
        'Obtecta' => 'Pupa o crisálida que poseen los lepidópteros en la cual las alas y los apéndices están comprimidos sobre el cuerpo y con casi la mayoría de los segmentos abdominales son inmóviles.',
        'Ocelo' => 'Ojos simples de algunos insectos adultos y estados larvales, son estructuras fotoreceptoras (para los estímulos luminosos), que funcionan como órganos de la visión en artrópodos Típicamente hay tres, que forman un triángulo invertido dorsal.',
        'Ortóptera/Ortoptera' => 'Orden que agrupa a insectos cuyas alas anteriores cubren longitudinalmente el segundo par de alas que se encuentra protegido y doblado, tiene el aparato bucal con mandíbulas y metamorfosis incompleta, ejemplo, saltamontes y katídidos.',
        'Osmeterio' => 'Órgano de defensa de las larvas de los papiliónidos, ubicada en el dorso del protórax, este órgano se evierte cuando la larva es perturbada emitiendo un fuerte olor que contiene ácido isobutírico, esta sustancia podría estar dirigida contra moscas y avispas parasitoideas, recientemente se ha sugerido que las secreciones del osmeterio, emiten feromonas de alarma que atraen a las hormigas, estas ayudan a la oruga cuando es amenazada.',
        'Ovipositor u ovopositor' => 'En la hembra de los insectos el órgano por el cual los huevos son depositados; son prolongaciones articuladas de los últimos segmentos abdominales.',
        'Ovipostura' => 'Todos los huevos que una hembra coloca de una sola vez.',
        'Ovovivíparas' => 'cuando los huevos permanecen dentro del cuerpo de la hembra hasta su eclosión. Ésta puede producirse inmediatamente antes de la puesta.',
        'Parasitoide' => 'Organismo dependiente de otro durante su vida juvenil, provocándole generalmente la muerte, siendo el adulto de vida libre. Los parasitoides se diferencian de los verdaderos parásitos en que matan a su huésped.',
        'Parasitoide secundario' => 'organismo que vive a expensas de un parasitoide y su hospedero.',
        'Parénquima' => 'Tejido vegetativo no especializado.',
        'Partenogénesis' => 'Desarrollo de un insecto, de huevo a adulto, sin la fertilización.',
        'Pigidio' => 'Últimos segmentos abdominales fusionados del cuerpo de las hembras de la familia Diaspididae.',
        'Plasticidad (de una población)' => 'Es la capacidad de cambio (adaptación) de una población determinada por el genotipo de esta.',
        'Polífaga' => 'Que se alimenta de varias especies de organismos.',
        'Postoma' => '= Cefalotórax (en arácnidos).',
        'Pronoto' => 'Pared superior endurecida del cuerpo, a menudo similar a una placa, situada apenas detrás de la cabeza de un insecto.',
        'Propatas' => 'Falsas patas presentes en las larvas de lepidópteros y de algunos himenópteros.',
        'Prosota' => '= Cefalotórax (en arácnidos).',
        'Protoninfa' => 'Primer estadio ninfal de cuatro pares de patas en la metamorfosis de algunos ácaros, estadio posterior al denominado larva.',
        'Protórax' => 'Primer segmento del tórax en el que van insertas el primer par de patas.',
        'Pseudopatas' => 'Patas no son articuladas, no son verdaderas patas (en orugas).',
        'Pteromálido' => 'Insectos de la familia Pteromalidae del orden Himenóptera, muchos de ellos parasitoides de coleópteros, lepidópteros y dípteros o depredadores de huevos de escamas y conchuelas.',
        'Pterotecas' => 'Estructura que darán origen a las alas.',
        'Pupa' => 'Estado juvenil de diferenciación generalmente inmóvil, en el que el insecto no se alimenta y que sigue al estado larvario y precede al estado adulto.',
        'Rábula' => 'Estructura en la boca de los caracoles que sirve para raspar los alimentos.',
        'Queresa (= Escama)' => 'Insecto de la familia Diaspididae del orden Hemíptera, que se caracteriza por poseer una cubierta o caparazón.',
        'Saco ovígero' => 'Cavidad o estructura en el ovario que contiene o en el cual se depositan los huevos, común en insectos de algunas familias del orden Hemíptera.',
        'Semioquímicos' => 'Sustancias químicas que transmiten informaciones entre organismos, produciendo una respuesta de comportamiento o fisiológica, la cual puede ser ventajosa o desventajosa.',
        'Sésil' => 'Estructura que se prolonga sin una base o pedicelo.',
        'Setas' => 'Estructura hueca, delgada como pelo; o gruesa "como cerda, movible en la base. Se desarrollan a partir de la epidermis.',
        'Sírfido' => 'Insectos de la familia Syrphidae del orden Díptera, cuyas larvas son depredadora de chanchitos blancos y áfidos.',
        'Taquínido' => 'Insectos de la familia Tachinidae del orden Díptera, las larvas son parasitoide de diversas especies de insectos de importancia agrícola.',
        'Telitóquica' => 'Tipo de reproducción por partenogénesis donde todos los descendientes son hembras.',
        'Trofalaxis' => 'Esto es una alimentación de boca en boca, en el cual los aparatos bucales de los insectos entran en contacto y traspasan entre ellas nutrientes o sustancias de reconocimiento como las feromonas. Puede tener lugar entre dos adultos o entre adulto y larva.',
        'Umbral económico' => 'Densidad de una plaga a partir de la cual los daños que se ocasiona son superiores al costo de las medidas de control que los evitaría.',
        'Uncus' => 'Estructura en forma de gancho curvado, que forma parte de la genitales externa de machos del orden Lepidóptera.',
        'Univoltina' => 'Especie con una sola generación anual.',
        'Vivípara(o)' => 'Especie que se multiplica por medio de crías vivas y cuyos embriones se forman en el interior del cuerpo materno pudiendo nutrirse en éste.'
    ];
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO glosario (termino, definicion, usuario_id) VALUES (?, ?, ?)");
        
        foreach ($glosario_inicial as $termino => $definicion) {
            $stmt->execute([$termino, $definicion, $_SESSION['user_id']]);
        }
        
        $pdo->commit();
        
        // Recargar términos después de la inserción
        $stmt = $pdo->prepare("SELECT * FROM glosario ORDER BY termino ASC");
        $stmt->execute();
        $terminos = $stmt->fetchAll();

    } catch (Exception $e) {
        $pdo->rollback();
        $_SESSION['message'] = ['type' => 'danger', 'text' => "Error al inicializar el glosario: " . $e->getMessage()];
    }
}

// --- FASE 3: VISUALIZACIÓN ---
require 'header.php';
?>

<!-- Contenido específico de la página de Glosario -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Glosario Entomológico</h1>
        <p class="lead text-muted">Un diccionario de términos clave para entender el mundo de los artrópodos.</p>
    </div>

    <!-- Botones de acción (solo para admins) -->
    <?php if ($is_admin): ?>
    <div class="text-center mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTermModal">
            <i class="bi bi-plus-lg"></i> Agregar Término
        </button>
    </div>
    <?php endif; ?>

    <!-- Barra de búsqueda -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" 
                   placeholder="Buscar términos o definiciones..." 
                   value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <!-- Lista de términos del glosario -->
    <?php if (empty($terminos)): ?>
        <div class="alert alert-info text-center"><h4>No se encontraron términos</h4></div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($terminos as $termino): ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo htmlspecialchars($termino['termino']); ?></h5>
                        <?php if ($is_admin): ?>
                        <div>
                            <button class="btn btn-sm btn-warning edit-term-btn" 
                                    data-id="<?php echo $termino['id']; ?>"
                                    data-termino="<?php echo htmlspecialchars($termino['termino']); ?>"
                                    data-definicion="<?php echo htmlspecialchars($termino['definicion']); ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-term-btn" 
                                    data-id="<?php echo $termino['id']; ?>"
                                    data-termino="<?php echo htmlspecialchars($termino['termino']); ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($termino['definicion'])); ?></p>
                    <small class="text-muted">Agregado por: <?php echo htmlspecialchars($termino['nombre_usuario'] ?? 'Sistema'); ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($is_admin): ?>
<!-- Modal para agregar término -->
<div class="modal fade" id="addTermModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Nuevo Término</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="termino" class="form-label">Término *</label>
                        <input type="text" class="form-control" id="termino" name="termino" required>
                    </div>
                    <div class="mb-3">
                        <label for="definicion" class="form-label">Definición *</label>
                        <textarea class="form-control" id="definicion" name="definicion" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar término -->
<div class="modal fade" id="editTermModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Término</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editTermForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_termino" class="form-label">Término *</label>
                        <input type="text" class="form-control" id="edit_termino" name="termino" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_definicion" class="form-label">Definición *</label>
                        <textarea class="form-control" id="edit_definicion" name="definicion" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteTermModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el término <strong id="delete_term_name"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" class="d-inline" id="deleteTermForm">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejo del botón editar
    document.querySelectorAll('.edit-term-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_termino').value = this.dataset.termino;
            document.getElementById('edit_definicion').value = this.dataset.definicion;
            new bootstrap.Modal(document.getElementById('editTermModal')).show();
        });
    });
    
    // Manejo del botón eliminar
    document.querySelectorAll('.delete-term-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('delete_id').value = this.dataset.id;
            document.getElementById('delete_term_name').textContent = this.dataset.termino;
            new bootstrap.Modal(document.getElementById('deleteTermModal')).show();
        });
    });
});
</script>
<?php endif; ?>

<?php require 'footer.php'; ?>
