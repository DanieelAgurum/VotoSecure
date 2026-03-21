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


    <!-- Candidatos -->
    <section id="candidatos" class="py-5">
        <div class="container">
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
    <section id="elecciones" class="py-5">
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

    <!-- Consulta -->
    <div class="cse-wrapper container mb-4">
        <!-- Tarjeta -->
        <div class="cse-card">
            <h2 class="cse-heading">Consulta tu <span class="cse-heading-accent">sector</span> electoral</h2>
            <p class="cse-description">
                Ingresa tu CURP para conocer la casilla y ubicación asignada donde deberás ejercer tu voto el día de la jornada electoral.
            </p>
            <div class="cse-divider"></div>
            <form id="cse-form" novalidate autocomplete="off">
                <label for="cse-curp-input" class="cse-field-label">
                    <span class="cse-req-dot"></span>
                    Clave Única de Registro de Población (CURP)
                </label>
                <div class="cse-input-wrap">
                    <input
                        type="text"
                        id="cse-curp-input"
                        class="cse-input"
                        maxlength="18"
                        placeholder="AAAA000000AAAAAA00"
                        aria-describedby="cse-hint"
                        inputmode="text"
                        spellcheck="false" />
                    <span class="cse-input-icon" id="cse-icon-wrap">
                        <svg id="cse-icon-default" viewBox="0 0 24 24" fill="#b0a090">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                        </svg>
                        <svg id="cse-icon-ok" viewBox="0 0 24 24" fill="#1A7340" style="display:none">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                        </svg>
                        <svg id="cse-icon-err" viewBox="0 0 24 24" fill="#C0392B" style="display:none">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                        </svg>
                    </span>
                </div>
                <div class="cse-hint" id="cse-hint">
                    <span id="cse-hint-text">La CURP tiene exactamente 18 caracteres alfanuméricos.</span>
                    <span class="cse-char-counter" id="cse-char-count">0/18</span>
                </div>
                <button type="submit" class="cse-btn">
                    <svg viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                    </svg>
                    Consultar sector electoral
                </button>
                <div class="cse-chips">
                    <div class="cse-chip">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                        </svg>
                        Datos protegidos
                    </div>
                    <div class="cse-chip">
                        <svg viewBox="0 0 24 24">
                            <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                        </svg>
                        Consulta 24/7
                    </div>
                    <div class="cse-chip">
                        <svg viewBox="0 0 24 24">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z" />
                        </svg>
                        Resultado inmediato
                    </div>
                </div>
            </form>
            <!-- Resultado -->
            <div class="cse-result" id="cse-result">
                <div class="cse-result__title" id="cse-result-title"></div>
                <div class="cse-result__body" id="cse-result-body"></div>
            </div>
        </div>
        <div class="cse-footer">
            Servicio público gratuito
        </div>
    </div>

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
    <script src="js/contador.js"></script>
</body>

</html>