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
    <section id="candidatos">
        <div class="container">
            <h2>Candidatos</h2>
            <div class="grid">
                <div class="card">
                    <div class="card-header">👨‍💼</div>
                    <div class="card-body">
                        <div class="card-title">Candidato A</div>
                        <div class="card-subtitle">Partido Azul</div>
                        <div class="card-description">Enfocado en educación y desarrollo sostenible para el país.</div>
                        <button class="btn">Ver Perfil</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">👩‍💼</div>
                    <div class="card-body">
                        <div class="card-title">Candidata B</div>
                        <div class="card-subtitle">Partido Rojo</div>
                        <div class="card-description">Trabajando por la economía, empleo y bienestar social.</div>
                        <button class="btn">Ver Perfil</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">👨‍💼</div>
                    <div class="card-body">
                        <div class="card-title">Candidato C</div>
                        <div class="card-subtitle">Partido Verde</div>
                        <div class="card-description">Comprometido con el medio ambiente y políticas verdes.</div>
                        <button class="btn">Ver Perfil</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Elecciones y Votaciones -->
    <section id="elecciones" style="background: #f8f9fa;">
        <div class="container">
            <h2>Elecciones y Votaciones</h2>
            <div class="grid">
                <div class="card">
                    <div class="card-header">🗳️</div>
                    <div class="card-body">
                        <div class="card-title">Elección Presidencial 2024</div>
                        <div class="card-description">Selecciona tu candidato preferido para la presidencia del país. Vota con seguridad y confianza.</div>
                        <button class="btn">Votar Ahora</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">📋</div>
                    <div class="card-body">
                        <div class="card-title">Referéndum 2024</div>
                        <div class="card-description">Participa en decisiones importantes sobre políticas públicas que afectarán el futuro.</div>
                        <button class="btn">Participa</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">🏛️</div>
                    <div class="card-body">
                        <div class="card-title">Elecciones Legislativas</div>
                        <div class="card-description">Elige a los representantes que te acompañarán en el congreso y trabjarán por ti.</div>
                        <button class="btn">Elegir</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="container py-5">
        <h2 class="section-title text-center mb-4">Preguntas Frecuentes</h2>

        <div class="accordion accordion-custom" id="faqAccordion">

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        ¿Quiénes pueden votar?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Todas las personas registradas en el padrón electoral pueden emitir su voto
                        dentro del periodo establecido.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        ¿Cómo se emite el voto?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        El voto se realiza seleccionando al candidato o partido de tu preferencia
                        y confirmando la elección en la plataforma.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        ¿Mi voto es confidencial?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Sí. El sistema garantiza la privacidad y confidencialidad del voto mediante
                        mecanismos de seguridad y cifrado.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        ¿Puedo cambiar mi voto después de enviarlo?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        No. Una vez confirmado, el voto queda registrado de forma definitiva.
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- Chatbot -->
    <?php include 'components/chatbot.php'; ?>

    <script>
        // Carrusel
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;

        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            const btns = document.querySelectorAll('.carousel-btn');
            btns.forEach(btn => btn.classList.remove('active'));

            slides[n].classList.add('active');
            btns[n].classList.add('active');
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(currentSlide);
        }

        function goToSlide(n) {
            currentSlide = n;
            showSlide(currentSlide);
        }

        // FAQ
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const allAnswers = document.querySelectorAll('.faq-answer');
            const allQuestions = document.querySelectorAll('.faq-question');

            allAnswers.forEach(a => a.classList.remove('show'));
            allQuestions.forEach(q => q.lastElementChild.textContent = '+');

            answer.classList.add('show');
            element.lastElementChild.textContent = '−';
        }

        // Hamburger
        document.getElementById('hamburger').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        });

        // Cerrar menú al hacer clic en un enlace
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelector('.nav-links').classList.remove('active');
            });
        });
    </script>

    <script src="js/nav.js"></script>
    <script src="js/abrirChatbot.js"></script>
</body>

</html>