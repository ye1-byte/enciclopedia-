<?php
// Título de la página.
$page_title = 'Bibliografía y Fuentes';

// Incluimos la cabecera.
require 'header.php';

// Protección: solo para usuarios logueados.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// (Futuro) Aquí iría la lógica para obtener una lista de referencias
// desde una tabla en la base de datos llamada 'bibliografia'.
?>

<!-- Contenido específico de la página de Bibliografía -->
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="display-5">Bibliografía y Fuentes de Referencia</h1>
        <p class="lead text-muted">Un compendio de los recursos académicos que respaldan la información de esta enciclopedia.</p>
    </div>

    <!-- Contenido de la Bibliografía -->
    <div class="bg-white p-4 p-md-5 border rounded shadow-sm">
        
        <h3 class="mb-4">Bibliografía Complementaria</h3>
        <ol class="list-group list-group-numbered">
            <li class="list-group-item">ANDREWS, KL. y QUEZADA, J.R. (Eds). 1989. Manejo Integrado de Plagas Insectiles en la Agricultura. Departamento de Protección Vegetal. Escuela Agrícola Panamericana. El Zamorano. Honduras. 623 p.</li>
            <li class="list-group-item">BERGAMIN, J. Contribuição para conhecimento da biologia de broca do café H. hampei. Arquivos do Instituto Biológico São Paulo. 1943</li>
            <li class="list-group-item">BORROR, D.J.; TRIPLEHORN, C.A.; JONSON, N.F. An introduction to the study of insects. 6 ed. Harcourt Brace College Publishers. 1992. 875p.</li>
            <li class="list-group-item">BURN, A.J. TH. COAKER y P.C. JEPSON. (Eds). 1987. Integrated Pest Management. Academic Press. London. 474 p.</li>
            <li class="list-group-item">CARDENAS, R. y POSADA, F. Los insectos y otros habitantes de cafetales y platanales. Comité Departamental de Cafeteros del Quindío. Armenia, Quindío. 2001. 250p.</li>
            <li class="list-group-item">CARDONA, M.C. 1998. Entomología Económica y Manejo de Plagas. Universidad Nacional de Colombia, Sede Palmira. 99 p.</li>
            <li class="list-group-item">Chapman, R,F. The Insects: Structure and Function, Cambridge University Press, Cambridge, 1998. 770p.</li>
            <li class="list-group-item">CISNEROS, F. 1995. Control de Plagas Agrícolas. AGS Electronics- Lima- Perú. 313 p.</li>
            <li class="list-group-item">DENT, D. 1995. Integrated Pest management. Chapman y Hall. London 356p</li>
            <li class="list-group-item">HORN, D. 1988. Ecological Approach to Pest Management. The Guilford Press. New York. 285 p.</li>
            <li class="list-group-item">INSTITUTO COLOMBIANO AGROPECUARIO- ICA-. 1989. Lista de insectos dañinos y otras plagas en Colombia. Boletín Técnico No 43, 4 Edic, Bogotá, 662 p.</li>
            <li class="list-group-item">KING, A. y SAUNDERS, J. 1984. Las plagas invertebradas de cultivos anuales alimenticios en América Central. Una guía para su reconocimiento y control. Costa Rica. Administración de Desarrollo Extranjero. CATIE. 182 p.</li>
            <li class="list-group-item">KOGAN, M. 1986. Ecological Theory and Integrated Pest Management Practice. John Wiley y Sons. New York. 362 p.</li>
            <li class="list-group-item">KONO,T.; PAPP, CH. Handbook of agricultural pests: Aphids, trips, mites, snails, and slugs. Sacramento. State of California. Department of Food and Agriculture. Division of Plant Industry. Laboratory Services Entomology. 1997. 203p.</li>
            <li class="list-group-item">KUNO, G; MULETT, J. y HERNÁNDEZ, M. Patología de insectos con énfasis en las enfermedades infecciosas y sus aplicaciones en el control biológico. Cali: Universidad del Valle, 1982. 212p.</li>
            <li class="list-group-item">METCALF, R.L. y H. LUCKMAN (Eds). 1994. Introduction To Insect Pest Management. Third Ed. John Wiley y Sons. New York.659 p.</li>
            <li class="list-group-item">NAKANO, O.; SILVEIRA, N.S.; ZUCCHI, R.A. Entomología Económica. Sao Paulo, Livroceres, 1981. 314p.</li>
            <li class="list-group-item">ORGANIZACIÓN DE LAS NACIONES UNIDAS PARA LA AGRICULTURA Y LA ALIMENTACIÓN. FAO. Plagas de las hortalizas. Manual de manejo integrado, Santiago de Chile, FAO. 1990. 520p.</li>
            <li class="list-group-item">POINAR, G.O; and THOMAS, G.M. Diagnostic manula for the identification of insect pathogens. New York: Plenum Press, 1978. 217p.</li>
            <li class="list-group-item">PRICE, P.W. 1984. Insect Ecology, 2a Ed. John Wiley Intersciencen New York.</li>
            <li class="list-group-item">SOUTHWOOD, T.R.E. 1978. Ecological methods, with reference to the study of insect populations. 2a Ed. John Wiley and sons, New York. 524 p.</li>
            <li class="list-group-item">VELEZ, R. 1985. Notas Sinópticas de Entomología Económica Colombiana. Secretaría de Agricultura de Antioquia. Medellín, Colombia. 258 p.</li>
        </ol>

        <hr class="my-5">

        <h3 class="mb-4">Publicaciones Periódicas</h3>
        <ul class="list-group">
            <li class="list-group-item">Revista Colombiana de Entomología</li>
            <li class="list-group-item">Anais da Sociedade Entomológica do Brasil</li>
            <li class="list-group-item">Revista Neotropical Entomology</li>
            <li class="list-group-item">Journal of Economic Entomology</li>
            <li class="list-group-item">Environmental Entomology</li>
            <li class="list-group-item">Bulletin of Entomological Research</li>
            <li class="list-group-item">Canadian Entomologist</li>
            <li class="list-group-item">Boletín Científico Centro de Museos Museo de Historia Natural.</li>
            <li class="list-group-item">Revista Manejo Integrado de Plagas y Agroecología. CATIE- Costa Rica</li>
        </ul>

    </div>
</div>

<?php
// Incluimos el pie de página.
require 'footer.php';
?>
