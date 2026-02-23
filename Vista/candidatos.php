<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos - VotoSeguro</title>
    <link rel="icon" type="image/x-icon" href="img/vs.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/votosecure/css/estilos.css">
    <link rel="stylesheet" href="/votosecure/css/candidatos.css">
</head>

<body>
    <!-- Navbar -->
    <?php include '../components/nav.php'; ?>

    <!-- Sección de Candidatos -->
    <section class="candidates-section">
        <h1 class="candidates-title">Conoce a los Candidatos</h1>

        <div class="candidates-grid">
            <!-- Candidato 1 -->
            <div class="candidate-card">
                <img src="/votosecure/img/image.png"
                    alt="Carlos Martínez"
                    class="candidate-photo">

                <div class="candidate-info">
                    <h3 class="candidate-name">Carlos Martínez</h3>
                    <p class="candidate-party">Partido Azul</p>
                    <p class="candidate-position">
                        <span>Cargo:</span> Presidente
                    </p>
                    <a href="propuestas.php?id=1" class="btn-proposal">
                        📋 Ver Propuesta
                    </a>
                </div>
            </div>

            <!-- Candidato 2 -->
            <div class="candidate-card">
                <img src="/votosecure/img/image.png"
                    alt="María González"
                    class="candidate-photo">

                <div class="candidate-info">
                    <h3 class="candidate-name">María González</h3>
                    <p class="candidate-party">Partido Rojo</p>
                    <p class="candidate-position">
                        <span>Cargo:</span> Presidente
                    </p>
                    <a href="propuestas.php?id=2" class="btn-proposal">
                        📋 Ver Propuesta
                    </a>
                </div>
            </div>

            <!-- Candidato 3 -->
            <div class="candidate-card">
                <img src="/votosecure/img/image.png"
                    alt="Roberto Sánchez"
                    class="candidate-photo">

                <div class="candidate-info">
                    <h3 class="candidate-name">Roberto Sánchez</h3>
                    <p class="candidate-party">Partido Verde</p>
                    <p class="candidate-position">
                        <span>Cargo:</span> Presidente
                    </p>
                    <a href="propuestas.php?id=3" class="btn-proposal">
                        📋 Ver Propuesta
                    </a>
                </div>
            </div>

            <!-- Candidato 4 -->
            <div class="candidate-card">
                <img src="/votosecure/img/image.png"
                    alt="Laura Hernández"
                    class="candidate-photo">

                <div class="candidate-info">
                    <h3 class="candidate-name">Laura Hernández</h3>
                    <p class="candidate-party">Partido Nacional</p>
                    <p class="candidate-position">
                        <span>Cargo:</span> Gobernador
                    </p>
                    <a href="propuestas.php?id=4" class="btn-proposal">
                        📋 Ver Propuesta
                    </a>
                </div>
            </div>

            <!-- Candidato 5 -->
            <div class="candidate-card">
                <img src="/votosecure/img/image.png"
                    alt="Antonio López"
                    class="candidate-photo">

                <div class="candidate-info">
                    <h3 class="candidate-name">Antonio López</h3>
                    <p class="candidate-party">Partido Morado</p>
                    <p class="candidate-position">
                        <span>Cargo:</span> Alcalde
                    </p>
                    <a href="propuestas.php?id=5" class="btn-proposal">
                        📋 Ver Propuesta
                    </a>
                </div>
            </div>

            <!-- Candidato 6 -->
            <div class="candidate-card">
                <img src="/votosecure/img/image.png"
                    alt="Patricia Rivera"
                    class="candidate-photo">

                <div class="candidate-info">
                    <h3 class="candidate-name">Patricia Rivera</h3>
                    <p class="candidate-party">Partido Gris</p>
                    <p class="candidate-position">
                        <span>Cargo:</span> Presidente
                    </p>
                    <a href="propuestas.php?id=6" class="btn-proposal">
                        📋 Ver Propuesta
                    </a>
                </div>
            </div>
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