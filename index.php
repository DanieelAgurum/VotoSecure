<?php
$intents = include __DIR__ . '/Modelo/config/chatbot.php';
require_once __DIR__ . '/Modelo/config/conexion.php';
require_once __DIR__ . '/Modelo/eleccionesMdl.php';
$eleccionesMdl = new EleccionesMdl($conexion);
$resultado = $eleccionesMdl->obtenerEleccionesActivas();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VotoSeguro - Sistema de Votaciones</title>
    <link rel="icon" type="image/x-icon" href="img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>
    <!-- Navbar -->
    <?php include 'components/nav.php'; ?>

    <!-- Carrusel -->
    <section id="inicio" class="carousel">
        <div class="carousel-container">
            <div class="carousel-wrapper">
                <div class="carousel-slide active">
                    <h2>Bienvenido a VotoSeguro</h2>
                    <p class="text-center">La plataforma más segura para ejercer tu derecho al voto. Participa en elecciones de forma transparente y confiable.</p>
                </div>
                <div class="carousel-slide">
                    <h2>Seguridad y Transparencia</h2>
                    <p class="text-center">Nuestro sistema utiliza tecnología de punta para garantizar la integridad de cada voto. Tu participación es importante.</p>
                </div>
                <div class="carousel-slide">
                    <h2>Participa Ahora</h2>
                    <p class="text-center">Descubre los candidatos, conoce las propuestas y emite tu voto de forma segura. Juntos construimos el futuro.</p>
                </div>
                <button class="carousel-nav carousel-prev" onclick="prevSlide()">❮</button>
                <button class="carousel-nav carousel-next" onclick="nextSlide()">❯</button>
            </div>
            <div class="carousel-controls">
                <button class="carousel-btn active" onclick="goToSlide(0)"></button>
                <button class="carousel-btn" onclick="goToSlide(1)"></button>
                <button class="carousel-btn" onclick="goToSlide(2)"></button>
            </div>
        </div>
    </section>



    <div class="text-center mb-5">
        <h2 class="fw-bold section-title">Candidatos Oficiales</h2>
        <p class="section-subtitle">Conoce sus propuestas antes de votar</p>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card candidato-card h-100 text-center">
                <div class="card-body">
                    <div class="avatar">👨‍💼</div>

                    <h5 class="fw-bold mt-3">Candidato A</h5>
                    <span class="partido-badge">Partido Azul</span>

                    <p class="mt-3">
                        Educación digital, innovación tecnológica y desarrollo sostenible.
                    </p>

                    <a href="#" class="btn btn-accent mt-3 w-100">
                        Ver Perfil
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card candidato-card h-100 text-center">
                <div class="card-body">
                    <div class="avatar">👩‍💼</div>

                    <h5 class="fw-bold mt-3">Candidata B</h5>
                    <span class="partido-badge">Partido Rojo</span>

                    <p class="mt-3">
                        Crecimiento económico, empleo formal y bienestar social.
                    </p>

                    <a href="#" class="btn btn-accent mt-3 w-100">
                        Ver Perfil
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card candidato-card h-100 text-center">
                <div class="card-body">
                    <div class="avatar">👨‍💼</div>

                    <h5 class="fw-bold mt-3">Candidato C</h5>
                    <span class="partido-badge">Partido Verde</span>

                    <p class="mt-3">
                        Transición energética y políticas ambientales sostenibles.
                    </p>

                    <a href="#" class="btn btn-accent mt-3 w-100">
                        Ver Perfil
                    </a>
                </div>
            </div>
        </div>

    </div>
    </div>
    </section>

    <!-- Elecciones -->
    <section id="elecciones" class="py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Procesos Activos</h2>
                <p class="text-muted">Votación segura y cifrada</p>
            </div>
            <div class="elecciones-scroll">
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>

                    <?php while ($row = mysqli_fetch_assoc($resultado)) : ?>

                        <?php
                        $ahora = new DateTime();
                        $inicio = new DateTime($row['fecha_inicio']);
                        $fin = new DateTime($row['fecha_fin']);

                        if ($ahora < $inicio) {
                            $estadoVisual = "proxima";
                        } elseif ($ahora > $fin) {
                            $estadoVisual = "finalizada";
                        } else {
                            $estadoVisual = "activa";
                        }

                        $fecha_fin = $row['fecha_fin'];
                        $id = $row['id_eleccion'];
                        ?>
                        <div class="proceso-wrapper">
                            <div class="card proceso-card text-center">
                                <div class="card-body d-flex flex-column">
                                    <div class="flex-grow-1">
                                        <div class="icon-box">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <h5 class="fw-bold mt-3 titulo-eleccion">
                                            <?= htmlspecialchars($row['nombre_eleccion']) ?>
                                        </h5>
                                        <p class="descripcion-eleccion">
                                            <?= htmlspecialchars($row['descripcion_eleccion']) ?>
                                        </p>
                                    </div>
                                    <?php if ($estadoVisual == "activa"): ?>
                                        <div class="countdown mt-2 mb-3"
                                            data-fecha="<?= $row['fecha_fin'] ?>"
                                            id="countdown-<?= $row['id_eleccion'] ?>">
                                        </div>
                                        <button class="btn btn-accent w-100">
                                            Votos en vivo
                                        </button>
                                    <?php elseif ($estadoVisual == "proxima"): ?>
                                        <div class="countdown mt-2 mb-3"
                                            data-fecha="<?= $row['fecha_inicio'] ?>"
                                            id="countdown-<?= $row['id_eleccion'] ?>">
                                        </div>
                                        <button class="btn btn-warning w-100" disabled>
                                            Próximamente
                                        </button>
                                    <?php else: ?>
                                        <div class="badge bg-danger w-100 mb-3 py-2">
                                            Finalizada
                                        </div>
                                        <button class="btn btn-secondary w-100">
                                            Ver Resultados
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            No hay procesos activos en este momento.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <div class="faq-section m-3">
        <h2 class="faq-title">Preguntas Frecuentes</h2>

        <div class="faq-carousel mt-2">
            <?php foreach ($intents as $intentKey => $intentData): ?>
                <?php if (!isset($intentData['faq'])) continue; ?>

                <div class="faq-card">
                    <div class="faq-card-inner">
                        <h4><?= $intentData['faq']['question']; ?></h4>
                        <p><?= $intentData['faq']['answer']; ?></p>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- Chatbot -->
    <?php include 'components/chatbot.php'; ?>

    <script src="js/carrusel.js"></script>
    <script src="js/nav.js"></script>
    <script src="js/abrirChatbot.js"></script>
    <script src="js/contador.js"></script>
</body>

</html>