<<<<<<< HEAD
<?php
// ==========================
// CANDIDATOS DESDE BASE DE DATOS
// ==========================
require_once '../Modelo/candidatosMdl.php';

$candidatoMdl = new Candidato();
$todosCandidatos = $candidatoMdl->obtenerCandidatos();

// Agrupar por cargo → secciones dinámicas
$secciones = [];
foreach ($todosCandidatos as $cand) {
    if ($cand['estatus'] !== 'activo') continue;

    $cargo = trim($cand['cargo']);
    if (empty($cargo)) continue;

    $secciones[$cargo][] = [
        'id'             => $cand['id'],
        'nombre'         => $cand['nombre'],
        'apellido'       => $cand['apellido'],
        'partido_nombre' => $cand['partido_nombre'] ?? 'Independiente',
        'foto'           => !empty($cand['foto']) ? $cand['foto'] : '/VotoSecure/img/image.png'
    ];
}

ksort($secciones);
?>

>>>>>>> 87851b1 (,)
=======
>>>>>>> 2b5f066 (Candidatos en la vista usuario)
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"> <link rel="icon" type="image/x-icon" href="/votosecure/img/vs.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos - VotoSeguro</title>
    <link rel="icon" type="image/x-icon" href="img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/votosecure/css/estilos.css">
    <link rel="stylesheet" href="/votosecure/css/candidatos.css">

</head>

<body>
    <!-- Navbar -->
    <?php include '../components/nav.php'; ?>

    <!-- Sección de Candidatos -->
    <section class="candidates-section">
        <h1 class="candidates-title">Conoce a los Candidatos</h1>

        <!-- Buscador de Candidatos -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar candidato por nombre, partido o cargo...">
            <span class="search-icon">🔍</span>
        </div>

        <?php if (empty($secciones)): ?>
            <div class="text-center py-5 my-5">
                <i class="bi bi-people-fill" style="font-size: 4rem; opacity: 0.5; color: #adb5bd;"></i>
                <h3 class="mt-4 mb-1">No hay candidatos activos</h3>
                <p class="lead text-muted">Los candidatos aparecerán aquí cuando sean aprobados por el administrador.</p>
            </div>
        <?php else: ?>
            <?php foreach ($secciones as $cargo => $listaCandidatos): ?>
            <div class="election-section mb-5" data-position="<?= strtolower($cargo) ?>">
                <div class="election-category">
                    <span class="category-dot"></span>
                    <h2 class="category-title"><?= ucwords(strtolower($cargo)) ?></h2>
                </div>
                <div class="candidates-grid" id="candidatesGrid<?= str_replace(' ', '', ucwords(strtolower($cargo))) ?>">
                    <?php foreach ($listaCandidatos as $cand): 
                        $nombreCompleto = trim($cand['nombre'] . ' ' . $cand['apellido']);
                        $partidoNombre = $cand['partido_nombre'] ?? 'Independiente';
                        $fotoSrc = !empty($cand['foto']) ? $cand['foto'] : '/VotoSecure/img/image.png';
                        $avatarEmoji = strpos($nombreCompleto, 'a') !== false || strpos($nombreCompleto, 'A') !== false ? '👩‍💼' : '👨‍💼';
                    ?>
                    <div class="candidate-card h-100" 
                         data-name="<?= strtolower($nombreCompleto) ?>" 
                         data-party="<?= strtolower($partidoNombre) ?>" 
                         data-position="<?= strtolower($cargo) ?>">
                        <div class="card-body text-center">
                            <?php if (strpos($cand['foto'], 'data:image/') === 0): ?>
                                <img src="<?php echo htmlspecialchars($fotoSrc); ?>" alt="<?php echo htmlspecialchars($nombreCompleto); ?>" class="avatar-img" loading="lazy">
                            <?php else: ?>
                                <div class="avatar"><?php echo $avatarEmoji; ?></div>
                                <?php if (!empty($cand['foto'])): ?>
                                    <div class="avatar-overlay" style="background-image: url('<?php echo htmlspecialchars($fotoSrc); ?>');"></div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <h5 class="fw-bold mt-3"><?= htmlspecialchars($nombreCompleto) ?></h5>
                            <span class="partido-badge"><?= htmlspecialchars($partidoNombre) ?></span>
                            <p class="mt-3 text-muted small">Candidato oficial para <?= ucwords(strtolower($cargo)) ?></p>
                            <a href="propuesta.php?id=<?= $cand['id'] ?>" class="btn btn-accent mt-auto w-100">Ver Propuesta</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <?php include '../components/footer.php'; ?>

    <!-- Chatbot -->
    <?php include '../components/chatbot.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/votosecure/js/nav.js"></script>
    <script src="/votosecure/js/abrirChatbot.js"></script>
    
    <!-- Script para el buscador de candidatos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const candidateCards = document.querySelectorAll('.candidate-card');
            const sections = document.querySelectorAll('.election-section');
            
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                candidateCards.forEach(card => {
                    const name = card.getAttribute('data-name') || '';
                    const party = card.getAttribute('data-party') || '';
                    const position = card.getAttribute('data-position') || '';
                    
                    // Buscar en nombre, partido o cargo
                    if (name.includes(searchTerm) || party.includes(searchTerm) || position.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Ocultar secciones vacías
                sections.forEach(section => {
                    const cards = section.querySelectorAll('.candidate-card');
                    let hasVisibleCards = false;
                    
                    cards.forEach(card => {
                        if (card.style.display !== 'none') {
                            hasVisibleCards = true;
                        }
                    });
                    
                    section.style.display = hasVisibleCards ? '' : 'none';
                });
            });
        });
    </script>
</body>

</html>
