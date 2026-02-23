<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuestas - VotoSeguro</title>
    <link rel="icon" type="image/x-icon" href="img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/votosecure/css/estilos.css">
    <link rel="stylesheet" href="/votosecure/css/candidatos.css">
</head>

<body>
    <!-- Navbar -->
    <?php include '../components/nav.php'; ?>

    <!-- Sección de Propuestas -->
    <section class="proposal-section">
        <div class="proposal-container">

            <?php
            // Obtener el ID del candidato
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            // Datos de ejemplo para los candidatos
            $candidatos = [
                1 => [
                    'nombre' => 'Carlos Martínez',
                    'partido' => 'Partido Azul',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'logo_partido' => '/votosecure/img/image.png',
                    'eslogan' => 'Juntos transformaremos nuestro país',
                    'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'biografia' => 'Carlos Martínez es un economist@ with more than 20 years of experience in the public and private sector. He has been Secretary of Economy and is currently a senator. He has promoted important reforms for the economic development of the country.',
                    'propuesta' => "MISIÓN:\n\nTransformar nuestro país en una nación próspera, justa y equitativa para todas y todos los ciudadanos.\n\nPROPUESTAS PRINCIPALES:\n\n🎓 EDUCACIÓN\n- Universalizar el acceso a educación de calidad desde preescolar hasta universidad\n- Incrementar el presupuesto educativo al 8% del PIB\n- Crear 500,000 becas anuales para estudiantes de bajos recursos\n\n💼 ECONOMÍA\n- Generar 2 millones de empleos formales en los próximos 3 años\n- Reducir impuestos a pequeñas y medianas empresas\n- Implementar programas de capacitación profesional gratuita\n\n🏥 SALUD\n- Construir 100 hospitales en zonas marginadas\n- Garantizar medicamentos gratuitos para personas de la tercera edad\n- Mejorar la infraestructura del sistema de salud público\n\n🌿 MEDIO AMBIENTE\n- Invertir en energías renovables\n- Crear programas de reforestación con 10 millones de árboles\n- Promover el transporte público sostenible"
                ],
                2 => [
                    'nombre' => 'María González',
                    'partido' => 'Partido Rojo',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'logo_partido' => '/votosecure/img/image.png',
                    'eslogan' => 'Por un México con dignidad y oportunidades',
                    'video' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                    'biografia' => 'María González es una abogado y defensora de derechos humanos con más de 15 años de experiencia. Ha liderado importantes iniciativas sociales y trabajado directamente con comunidades vulnerables.',
                    'propuesta' => "MISIÓN:\n\nLuchar por un México donde cada familia tenga oportunidades reales de prosperar y vivir con dignidad.\n\nPROPUESTAS PRINCIPALES:\n\n🏠 VIVIENDA\n- Construir 1 millón de viviendas sociales\n- Otorgar créditos preferenciales para jóvenes que buscan su primera casa\n- Regularizar asentamientos humanos en zonas urbanas\n\n👵 SEGURIDAD SOCIAL\n- Aumentar pensiones para adultos mayores\n- Implementar seguro de desempleo temporal\n- Crear centros de atención para personas con discapacidad\n\n📈 DESARROLLO ECONÓMICO\n- Apoyar a emprendedores locales con financiamientos\n- Modernizar infraestructura vial y de comunicaciones\n- Promover el turismo nacional con programas de incentivo\n\n✋ LUCHA CONTRA LA CORRUPCIÓN\n- Crear Fiscalía Especial Anticorrupción\n- Transparentar todos los contratos gubernamentales\n- Sanciones severas a servidores públicos que cometan corrupción"
                ],
                3 => [
                    'nombre' => 'Roberto Sánchez',
                    'partido' => 'Partido Verde',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'logo_partido' => '/votosecure/img/image.png',
                    'eslogan' => 'Desarrollo sostenible para las futuras generaciones',
                    'video' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                    'biografia' => 'Roberto Sánchez es un ingeniero ambiental con amplia experiencia en políticas de sostenibilidad. Ha coordinar@ programas de reforestación y energía renovable a nivel nacional.',
                    'propuesta' => "MISIÓN:\n\nConstruir un futuro sostenible donde el desarrollo económico conviva en armonía con la naturaleza.\n\nPROPUESTAS PRINCIPALES:\n\n🌳 MEDIO AMBIENTE\n- Alcanzar cero emisiones de carbono para 2050\n- Proteger áreas naturales y crear nuevos parques nacionales\n- Promover agricultura sostenible y orgánica\n\n🚗 MOVILIDAD SOSTENIBLE\n- Expandir redes de transporte público eléctrico\n- Crear ciclovías en todas las ciudades principales\n- Incentivar vehículos eléctricos con impuestos reducidos\n\n💧 RECURSOS HÍDRICOS\n- Modernizar sistemas de riego en el campo\n- Tratar y reutilizar aguas residuales\n- Combatir el desperdicio de agua en ciudades\n\n🏭 INDUSTRIA VERDE\n- Apoyar empresas que implementen tecnologías limpias\n- Crear empleos en el sector de energías renovables\n- Establecer economía circular en procesos industriales"
                ],
                4 => [
                    'nombre' => 'Laura Hernández',
                    'partido' => 'Partido Nacional',
                    'puesto' => 'Gobernador',
                    'foto' => '/votosecure/img/image.png',
                    'logo_partido' => '/votosecure/img/image.png',
                    'eslogan' => 'Seguridad y desarrollo para nuestro estado',
                    'video' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                    'biografia' => 'Laura Hernández es una política con experiencia en gestión pública. Ha ocupado cargos de dirección en seguridad y desarrollo social, con resultados comprobables en sus anteriores responsabilidades.',
                    'propuesta' => "MISIÓN:\n\nTransformar nuestro estado en un referente de desarrollo, seguridad y calidad de vida para sus habitantes.\n\nPROPUESTAS PRINCIPALES:\n\n🛡️ SEGURIDAD\n- Fortalecer la policía y vigilancia en zonas conflictivas\n- Implementar botones de pánico en transporte público\n- Crear centros de atención a víctimas del delito\n\n🏗️ INFRAESTRUCTURA\n- Construir 500 kilómetros de nuevas autopistas\n- Modernizar aeropuertos regionales\n- Ampliar red de agua potable y drenaje\n\n🎓 EDUCACIÓN\n- Becas para 100,000 estudiantes destacados\n- Centros de investigación tecnológica\n- Vinculación universidades-empresas\n\n🏥 SALUD\n- Hospitales rurales en comunidades marginadas\n- Unidades móviles de atención médica\n- Programas de prevención de adicciones"
                ],
                5 => [
                    'nombre' => 'Antonio López',
                    'partido' => 'Partido Morado',
                    'puesto' => 'Alcalde',
                    'foto' => '/votosecure/img/image.png',
                    'logo_partido' => '/votosecure/img/image.png',
                    'eslogan' => 'Un municipio para todos',
                    'video' => 'https://www.youtube.com/watch?v=fJ9rUzIMcZQ',
                    'biografia' => 'Antonio López es un empresario local que ha demostrado compromiso con el desarrollo comunitario. Conoce las necesidades de los habitantes por haber crecido en el municipio.',
                    'propuesta' => "MISIÓN:\n\nHacer de nuestro municipio un lugar seguro, próspero y con alta calidad de vida para todas las familias.\n\nPROPUESTAS PRINCIPALES:\n\n🏘️ URBANISMO\n- Regeneración de espacios públicos\n- Parques y áreas verdes en cada colonia\n- Alumbrado público eficiente y seguro\n\n🚌 TRANSPORTE\n- Mejorar rutas de transporte público\n- Semáforos inteligentes para reducir tráfico\n- Estacionamientos públicos en zonas comerciales\n\n👨‍👩‍👧‍👦 BIENESTAR SOCIAL\n- Centros comunitarios en todas las colonias\n- Programas de apoyo alimentario para familias vulnerables\n- Actividades recreativas y culturales gratuitas\n\n🏪 ECONOMÍA LOCAL\n- Mercados municipales para productores locales\n- Capacitación para pequeños comerciantes\n- Fomentar turismo en atractivos municipales"
                ],
                6 => [
                    'nombre' => 'Patricia Rivera',
                    'partido' => 'Partido Gris',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'logo_partido' => '/votosecure/img/image.png',
                    'eslogan' => 'Unidad y progreso para México',
                    'video' => 'https://www.youtube.com/watch?v=L_jWHffIx5E',
                    'biografia' => 'Patricia Rivera es una diplomática y política con amplia experiencia internacional. Ha representado a México en foros internacionales y trabajado en la construcción de acuerdos nacionales.',
                    'propuesta' => "MISIÓN:\n\nUnir a todos los sectores de la sociedad para construir un país más justo, competitivo y con oportunidades para todos.\n\nPROPUESTAS PRINCIPALES:\n\n🤝 UNIDAD NACIONAL\n- Diálogo con todos los sectores políticos\n- Construir consensos para reformas necesarias\n- Gobernar para todos los mexicanos\n\n📊 MODERNIZACIÓN\n- Digitalización de servicios gubernamentales\n- Conectividad internet en todo el país\n- Trámites gubernamentales en línea\n\n⚖️ JUSTICIA\n- Reformas al sistema de justicia penal\n- Protección a periodistas y defensores de derechos humanos\n- Acceso a la justicia para víctimas\n\n🌟 EXCELENCIA\n- Mérito y capacidad como criterios de ingreso al servicio público\n- Evaluación constante de servidores públicos\n- Combate a la impunidad"
                ]
            ];

            // Obtener datos del candidato o usar el primero por defecto
            $candidato = isset($candidatos[$id]) ? $candidatos[$id] : null;
            ?>

            <?php if (!$candidato): ?>
                <!-- Vista previa de todas las propuestas -->
                <div class="all-proposals-container">
                    <h1 class="all-proposals-title">Propuestas de los Candidatos</h1>
                    <p class="all-proposals-subtitle">Conoce las propuestas de todos los candidatos</p>
                    
                    <!-- Buscador de Propuestas -->
                    <div class="search-container">
                        <input type="text" id="searchProposalsInput" class="search-input" placeholder="Buscar candidato por nombre, partido o cargo...">
                        <span class="search-icon">🔍</span>
                    </div>
                    
                    <div class="all-proposals-grid" id="proposalsGrid">
                        <?php foreach ($candidatos as $key => $cand): ?>
                            <div class="proposal-preview-card" data-name="<?= strtolower($cand['nombre']) ?>" data-party="<?= strtolower($cand['partido']) ?>" data-position="<?= strtolower($cand['puesto']) ?>">
                                <div class="proposal-preview-header">
                                    <img src="<?= $cand['foto'] ?>" 
                                         alt="<?= $cand['nombre'] ?>" 
                                         class="proposal-preview-photo">
                                    <div class="proposal-preview-info">
                                        <h3 class="proposal-preview-name"><?= $cand['nombre'] ?></h3>
                                        <p class="proposal-preview-party"><?= $cand['partido'] ?></p>
                                        <p class="proposal-preview-position"><?= $cand['puesto'] ?></p>
                                    </div>
                                </div>
                                
                                <?php if (!empty($cand['eslogan'])): ?>
                                    <p class="proposal-preview-slogan">"<?= htmlspecialchars($cand['eslogan']) ?>"</p>
                                <?php endif; ?>
                                
                                <!-- Video preview -->
                                <?php if (!empty($cand['video'])): ?>
                                    <div class="proposal-preview-video">
                                        <?php 
                                        $videoId = '';
                                        if (strpos($cand['video'], 'youtube') !== false || strpos($cand['video'], 'youtu.be') !== false) {
                                            if (strpos($cand['video'], 'youtu.be') !== false) {
                                                $videoId = str_replace('https://youtu.be/', '', $cand['video']);
                                            } else {
                                                parse_str(parse_url($cand['video'], PHP_URL_QUERY), $params);
                                                $videoId = isset($params['v']) ? $params['v'] : '';
                                            }
                                        }
                                        ?>
                                        <img src="https://img.youtube.com/vi/<?= $videoId ?>/mqdefault.jpg" 
                                             alt="Video de <?= $cand['nombre'] ?>"
                                             class="proposal-preview-video-thumb"
                                             onclick="openVideoModal('<?= $videoId ?>', '<?= $cand['nombre'] ?>')">
                                        <div class="proposal-preview-video-overlay" onclick="openVideoModal('<?= $videoId ?>', '<?= $cand['nombre'] ?>')">
                                            <span>▶</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="proposal-preview-content">
                                    <h4>Propuesta Principal:</h4>
                                    <p><?= substr($cand['propuesta'], 0, 200) ?>...</p>
                                </div>
                                
                                <a href="propuestas.php?id=<?= $key ?>" class="btn-view-proposal">
                                    Ver Propuesta Completa →
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Propuesta individual -->
                <div class="proposal-card">
                    <div class="proposal-header">
                        <img src="<?= $candidato['foto'] ?>"
                            alt="<?= $candidato['nombre'] ?>"
                            class="proposal-photo">

                        <h1 class="proposal-name"><?= $candidato['nombre'] ?></h1>
                        <p class="proposal-party"><?= $candidato['partido'] ?></p>
                        <p class="proposal-position">Candidato a: <?= $candidato['puesto'] ?></p>
                        
                        <!-- Eslogan -->
                        <?php if (!empty($candidato['eslogan'])): ?>
                            <p class="proposal-slogan">"<?= htmlspecialchars($candidato['eslogan']) ?>"</p>
                        <?php endif; ?>
                    </div>

                    <div class="proposal-body">
                        <!-- Video de campaña -->
                        <?php if (!empty($candidato['video'])): ?>
                            <?php 
                            $videoId = '';
                            if (strpos($candidato['video'], 'youtube') !== false || strpos($candidato['video'], 'youtu.be') !== false) {
                                if (strpos($candidato['video'], 'youtu.be') !== false) {
                                    $videoId = str_replace('https://youtu.be/', '', $candidato['video']);
                                } else {
                                    parse_str(parse_url($candidato['video'], PHP_URL_QUERY), $params);
                                    $videoId = isset($params['v']) ? $params['v'] : '';
                                }
                            }
                            ?>
                            <div class="proposal-video" onclick="openVideoModal('<?= $videoId ?>', '<?= $candidato['nombre'] ?>')">
                                <img src="https://img.youtube.com/vi/<?= $videoId ?>/hqdefault.jpg" 
                                     alt="Video de <?= $candidato['nombre'] ?>"
                                     class="proposal-video-thumb">
                                <div class="proposal-video-overlay">
                                    <span>▶</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Biografía -->
                        <?php if (!empty($candidato['biografia'])): ?>
                            <div class="proposal-biography">
                                <h3>📝 Biografía</h3>
                                <p><?= nl2br(htmlspecialchars($candidato['biografia'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Propuestas -->
                        <div class="proposal-title-section">
                            <span class="proposal-icon">📋</span>
                            <h2>Propuesta de Campaña</h2>
                        </div>
                        <div class="proposal-text">
                            <?= nl2br($candidato['propuesta']) ?>
                        </div>
                    </div>

                    <div class="proposal-actions">
                        <a href="propuestas.php" class="btn-back">← Volver a Propuestas</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Footer -->
    <?php include '../components/footer.php'; ?>

    <!-- Chatbot -->
    <?php include '../components/chatbot.php'; ?>

    <!-- Modal para Video -->
    <div class="video-modal" id="videoModal">
        <div class="video-modal-content">
            <span class="video-modal-close" onclick="closeVideoModal()">&times;</span>
            <div class="video-modal-body">
                <iframe id="videoModalIframe" 
                        src="" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/votosecure/js/nav.js"></script>
    <script src="/votosecure/js/abrirChatbot.js"></script>
    <script>
        // Funcionalidad del buscador de propuestas
        const searchProposalsInput = document.getElementById('searchProposalsInput');
        if (searchProposalsInput) {
            const proposalCards = document.querySelectorAll('.proposal-preview-card');
            
            searchProposalsInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                proposalCards.forEach(card => {
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
            });
        }

        function openVideoModal(videoId, candidateName) {
            // Detectar si es móvil
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                // En móvil, abrir en YouTube
                window.open('https://www.youtube.com/watch?v=' + videoId, '_blank');
            } else {
                // En PC, abrir en modal
                const modal = document.getElementById('videoModal');
                const iframe = document.getElementById('videoModalIframe');
                iframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1';
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeVideoModal() {
            const modal = document.getElementById('videoModal');
            const iframe = document.getElementById('videoModalIframe');
            iframe.src = '';
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('videoModal');
            if (event.target == modal) {
                closeVideoModal();
            }
        }
    </script>
</body>

</html>
