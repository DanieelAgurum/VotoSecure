<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuestas - VotoSeguro</title>
    <link rel="icon" type="image/x-icon" href="../img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/votosecure/css/estilos.css">
    <link rel="stylesheet" href="/votosecure/css/candidatos.css">
</head>

<body>
    <!-- Navbar -->
    <?php include '../components/nav.php'; ?>

    <!-- Sección de Propuesta -->
    <section class="proposal-section">
        <div class="proposal-container">

            <?php
            // Obtener el ID del candidato
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            // Datos de los candidatos
            $candidatos = [
                // PRESIDENCIA
                1 => [
                    'nombre' => 'Carlos Martínez',
                    'partido' => 'Partido Azul',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Juntos transformaremos nuestro país',
                    'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'biografia' => 'Carlos Martínez es un economista con más de 20 años de experiencia en el sector público y privado.',
                    'propuesta' => "MISIÓN:\n\nTransformar nuestro país en una nación próspera, justa y equitativa.\n\nPROPUESTAS PRINCIPALES:\n\n🎓 EDUCACIÓN\n- Universalizar el acceso a educación de calidad\n- Incrementar el presupuesto educativo al 8% del PIB\n- Crear 500,000 becas anuales\n\n💼 ECONOMÍA\n- Generar 2 millones de empleos formales\n- Reducir impuestos a pequeñas empresas\n- Programas de capacitación gratuitos\n\n🏥 SALUD\n- Construir 100 hospitales en zonas marginadas\n- Garantizar medicamentos gratuitos para adultos mayores\n- Mejorar infraestructura del sistema de salud"
                ],
                2 => [
                    'nombre' => 'María González',
                    'partido' => 'Partido Rojo',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Por un México con dignidad y oportunidades',
                    'video' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                    'biografia' => 'María González es una abogado y defensora de derechos humanos con más de 15 años de experiencia.',
                    'propuesta' => "MISIÓN:\n\nLuchar por un México donde cada familia tenga oportunidades reales de prosperar.\n\nPROPUESTAS PRINCIPALES:\n\n🏠 VIVIENDA\n- Construir 1 millón de viviendas sociales\n- Créditos preferenciales para jóvenes\n- Regularizar asentamientos humanos\n\n👵 SEGURIDAD SOCIAL\n- Aumentar pensiones para adultos mayores\n- Seguro de desempleo temporal\n- Centros de atención para discapacidad\n\n📈 DESARROLLO ECONÓMICO\n- Apoyar emprendedores locales\n- Modernizar infraestructura vial\n- Promover turismo nacional"
                ],
                3 => [
                    'nombre' => 'Roberto Sánchez',
                    'partido' => 'Partido Verde',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Desarrollo sostenible para las futuras generaciones',
                    'video' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                    'biografia' => 'Roberto Sánchez es un ingeniero ambiental con experiencia en políticas de sostenibilidad.',
                    'propuesta' => "MISIÓN:\n\nConstruir un futuro sostenible donde el desarrollo económico conviva con la naturaleza.\n\nPROPUESTAS PRINCIPALES:\n\n🌳 MEDIO AMBIENTE\n- Alcanzar cero emisiones de carbono para 2050\n- Proteger áreas naturales\n- Promover agricultura sostenible\n\n🚗 MOVILIDAD SOSTENIBLE\n- Expandir transporte público eléctrico\n- Crear ciclovías en todas las ciudades\n- Incentivar vehículos eléctricos"
                ],
                4 => [
                    'nombre' => 'Patricia Rivera',
                    'partido' => 'Partido Gris',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Unidad y progreso para México',
                    'video' => 'https://www.youtube.com/watch?v=L_jWHffIx5E',
                    'biografia' => 'Patricia Rivera es una diplomática con amplia experiencia internacional.',
                    'propuesta' => "MISIÓN:\n\nUnir a todos los sectores para construir un país más justo y competitivo.\n\nPROPUESTAS PRINCIPALES:\n\n🤝 UNIDAD NACIONAL\n- Diálogo con todos los sectores políticos\n- Construir consensos para reformas\n- Gobernar para todos los mexicanos\n\n📊 MODERNIZACIÓN\n- Digitalización de servicios gubernamentales\n- Conectividad internet en todo el país\n- Trámites en línea"
                ],
                5 => [
                    'nombre' => 'Jorge Ramírez',
                    'partido' => 'Partido Naranja',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Libertad y prosperidad para todos',
                    'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'biografia' => 'Jorge Ramírez es un empresario exitoso que ha fundado múltiples empresas.',
                    'propuesta' => "MISIÓN:\n\nConstruir un México donde la libertad económica sea el motor del desarrollo.\n\nPROPUESTAS PRINCIPALES:\n\n💰 ECONOMÍA\n- Reducción de impuestos a PYMES\n- Eliminación de trabas burocráticas\n- Zonas francas para inversiones\n\n📈 EMPLEO\n- 2 millones de empleos formales\n- Capacitación vocacional\n- Apoyo a jóvenes emprendedores"
                ],
                6 => [
                    'nombre' => 'Ana Castro',
                    'partido' => 'Partido Dorado',
                    'puesto' => 'Presidente',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Orden, seguridad y prosperidad',
                    'video' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                    'biografia' => 'Ana Castro es una exmilitar con carrera en seguridad nacional.',
                    'propuesta' => "MISIÓN:\n\nRestaurar el orden y la seguridad para que las familias vivan en paz.\n\nPROPUESTAS PRINCIPALES:\n\n🛡️ SEGURIDAD\n- Fortalecer fuerzas armadas\n- Contra el crimen organizado\n- Aumentar penas para delitos graves\n\n⚖️ JUSTICIA\n- Sistema de justicia más ágil\n- Cárceles de alta seguridad\n- Protección a víctimas"
                ],
                // GOBERNACIÓN
                7 => [
                    'nombre' => 'Laura Hernández',
                    'partido' => 'Partido Nacional',
                    'puesto' => 'Gobernador',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Seguridad y desarrollo para nuestro estado',
                    'video' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                    'biografia' => 'Laura Hernández es una política con experiencia en gestión pública y seguridad.',
                    'propuesta' => "MISIÓN:\n\nTransformar nuestro estado en referente de desarrollo y seguridad.\n\nPROPUESTAS PRINCIPALES:\n\n🛡️ SEGURIDAD\n- Fortalecer policía estatal\n- Sistemas de vigilancia inteligente\n- Coordinación con federación\n\n🏗️ INFRAESTRUCTURA\n- 500 km de nuevas autopistas\n- Modernizar aeropuertos\n- Ampliar red de agua potable"
                ],
                8 => [
                    'nombre' => 'Miguel Torres',
                    'partido' => 'Partido Azul',
                    'puesto' => 'Gobernador',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Trabajo y desarrollo regional',
                    'video' => 'https://www.youtube.com/watch?v=kJQP7kiw5Fk',
                    'biografia' => 'Miguel Torres es un ingeniero agrónomo especializado en desarrollo rural.',
                    'propuesta' => "MISIÓN:\n\nPromover el desarrollo integral mediante el fortalecimiento del sector agropecuario.\n\nPROPUESTAS PRINCIPALES:\n\n🌾 AGRICULTURA\n- Programas de apoyo al campesino\n- Tecnificación del riego\n- Capacitación agrícola\n\n🐟 PESCA\n- Modernización de flota pesquera\n- Centros de acopio\n- Certificación para exportación"
                ],
                9 => [
                    'nombre' => 'Silvia Meadows',
                    'partido' => 'Partido Verde',
                    'puesto' => 'Gobernador',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Medio ambiente y desarrollo sostenible',
                    'video' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                    'biografia' => 'Silvia Meadows es una bióloga comprometida con la conservación.',
                    'propuesta' => "MISIÓN:\n\nConstruir un estado verde donde el desarrollo sea sostenible.\n\nPROPUESTAS PRINCIPALES:\n\n🌳 MEDIO AMBIENTE\n- Proteger áreas naturales\n- Programas de reforestación\n- Economía circular\n\n♻️ SOSTENIBILIDAD\n- Energías renovables\n- Tratamiento de aguas\n- Reciclaje obligatorio"
                ],
                // ALCALDÍA
                10 => [
                    'nombre' => 'Antonio López',
                    'partido' => 'Partido Morado',
                    'puesto' => 'Alcalde',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Un municipio moderno y seguro',
                    'video' => 'https://www.youtube.com/watch?v=fJ9rUzIMcZQ',
                    'biografia' => 'Antonio López es un empresario local comprometido con el desarrollo comunitario.',
                    'propuesta' => "MISIÓN:\n\nHacer de nuestro municipio un lugar seguro y próspero.\n\nPROPUESTAS PRINCIPALES:\n\n🏘️ URBANISMO\n- Regeneración de espacios públicos\n- Parques en cada colonia\n- Alumbrado eficiente\n\n🚌 TRANSPORTE\n- Rutas optimizadas\n- Semáforos inteligentes\n- Estacionamientos"
                ],
                11 => [
                    'nombre' => 'Carmen Ruiz',
                    'partido' => 'Partido Rojo',
                    'puesto' => 'Alcalde',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Tu voz en el ayuntamiento',
                    'video' => 'https://www.youtube.com/watch?v=fJ9rUzIMcZQ',
                    'biografia' => 'Carmen Ruiz es una líder comunitaria que trabaja directamente con vecinos.',
                    'propuesta' => "MISIÓN:\n\nSer la voz de los ciudadanos en el ayuntamiento.\n\nPROPUESTAS PRINCIPALES:\n\n🌳 ESPACIOS PÚBLICOS\n- Nuevos parques y jardines\n- Áreas recreativas\n- Jardines comunitarios\n\n🏀 DEPORTE\n- Canchas deportivas\n- Torneos locales\n- Programas juveniles"
                ],
                12 => [
                    'nombre' => 'Daniel Ortega',
                    'partido' => 'Partido Naranja',
                    'puesto' => 'Alcalde',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Limpieza y orden municipal',
                    'video' => 'https://www.youtube.com/watch?v=L_jWHffIx5E',
                    'biografia' => 'Daniel Ortega es un especialista en gestión ambiental.',
                    'propuesta' => "MISIÓN:\n\nConvertir nuestro municipio en una ciudad limpia y ordenada.\n\nPROPUESTAS PRINCIPALES:\n\n🗑️ RESIDUOS\n- Sistema de reciclaje\n- Recolección separada\n- Plantas de tratamiento\n\n🚛 MOVILIDAD\n- Ciclovías seguras\n- Estaciones de bicicleta\n- Peatonalización"
                ],
                13 => [
                    'nombre' => 'Elena Navarro',
                    'partido' => 'Partido Dorado',
                    'puesto' => 'Alcalde',
                    'foto' => '/votosecure/img/image.png',
                    'eslogan' => 'Cultura y turismo para todos',
                    'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'biografia' => 'Elena Navarro es una historiadora y promotora cultural.',
                    'propuesta' => "MISIÓN:\n\nPosicionar nuestro municipio como destino turístico y centro cultural.\n\nPROPUESTAS PRINCIPALES:\n\n🏛️ PATRIMONIO\n- Restauración de históricos\n- Rutas turísticas\n- Museos locales\n\n🎭 EVENTOS\n- Festivales culturales\n- Ferias y exposiciones\n- Teatro al aire libre"
                ]
            ];

            // Obtener datos del candidato
            $candidato = isset($candidatos[$id]) ? $candidatos[$id] : null;
            ?>

            <?php if (!$candidato): ?>
                <!-- Vista previa de todas las propuestas -->
                <div class="all-proposals-container">
                    <h1 class="all-proposals-title">Propuestas de los Candidatos</h1>
                    <p class="all-proposals-subtitle">Conoce las propuestas de todos los candidatos</p>

                    <div class="all-proposals-grid">
                        <?php foreach ($candidatos as $key => $cand): ?>
                            <div class="proposal-preview-card">
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

                                <div class="proposal-preview-content">
                                    <h4>Propuesta Principal:</h4>
                                    <p><?= substr($cand['propuesta'], 0, 200) ?>...</p>
                                </div>

                                <a href="propuesta.php?id=<?= $key ?>" class="btn-view-proposal">
                                    Ver Propuesta Completa →
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="proposal-card">
                    <div class="proposal-header">
                        <img src="<?= $candidato['foto'] ?>"
                            alt="<?= $candidato['nombre'] ?>"
                            class="proposal-photo">

                        <h1 class="proposal-name"><?= $candidato['nombre'] ?></h1>
                        <p class="proposal-party"><?= $candidato['partido'] ?></p>
                        <p class="proposal-position">Candidato a: <?= $candidato['puesto'] ?></p>

                        <?php if (!empty($candidato['eslogan'])): ?>
                            <p class="proposal-slogan">"<?= htmlspecialchars($candidato['eslogan']) ?>"</p>
                        <?php endif; ?>
                    </div>

                    <div class="proposal-body">
                        <?php if (!empty($candidato['video'])): ?>
                            <div class="proposal-video">
                                <?php if (strpos($candidato['video'], 'youtube') !== false || strpos($candidato['video'], 'youtu.be') !== false): ?>
                                    <?php
                                    $videoId = '';
                                    if (strpos($candidato['video'], 'youtu.be') !== false) {
                                        $videoId = str_replace('https://youtu.be/', '', $candidato['video']);
                                    } else {
                                        parse_str(parse_url($candidato['video'], PHP_URL_QUERY), $params);
                                        $videoId = isset($params['v']) ? $params['v'] : '';
                                    }
                                    ?>
                                    <iframe src="https://www.youtube.com/embed/<?= $videoId ?>"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($candidato['biografia'])): ?>
                            <div class="proposal-biography">
                                <h3>📝 Biografía</h3>
                                <p><?= nl2br(htmlspecialchars($candidato['biografia'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="proposal-title-section">
                            <span class="proposal-icon">📋</span>
                            <h2>Propuesta de Campaña</h2>
                        </div>
                        <div class="proposal-text">
                            <?= nl2br($candidato['propuesta']) ?>
                        </div>
                    </div>

                    <div class="proposal-actions">
                        <a href="candidatos.php" class="btn-back">← Volver a Candidatos</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Footer -->
    <?php include '../components/footer.php'; ?>

    <!-- Chatbot -->
    <?php include '../components/chatbot.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/votosecure/js/nav.js"></script>
    <script src="/votosecure/js/abrirChatbot.js"></script>
</body>

</html>