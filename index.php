<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VotoSeguro - Sistema de Votaciones</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Navbar */
        nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            color: white;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: #ffd700;
        }

        .hamburger {
            display: none;
            cursor: pointer;
            color: white;
            font-size: 1.5rem;
        }

        /* Carrusel */
        .carousel {
            width: 100%;
            overflow: hidden;
            background: #f8f9fa;
            padding: 40px 0;
        }

        .carousel-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            padding: 0 20px;
        }

        .carousel-wrapper {
            position: relative;
            width: 100%;
            height: 300px;
        }

        .carousel-slide {
            display: none;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 40px;
            color: white;
            animation: fadeIn 0.5s;
        }

        .carousel-slide.active {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .carousel-slide h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .carousel-slide p {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .carousel-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .carousel-btn {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ccc;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .carousel-btn.active {
            background: #667eea;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1.2rem;
            transition: 0.3s;
        }

        .carousel-nav:hover {
            background: rgba(0,0,0,0.8);
        }

        .carousel-prev {
            left: 20px;
        }

        .carousel-next {
            right: 20px;
        }

        /* Secciones */
        section {
            padding: 60px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #667eea;
            font-size: 2.5rem;
        }

        /* FAQ */
        .faq-item {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .faq-question {
            background: #667eea;
            color: white;
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }

        .faq-question:hover {
            background: #764ba2;
        }

        .faq-answer {
            display: none;
            padding: 20px;
            background: #f8f9fa;
        }

        .faq-answer.show {
            display: block;
        }

        /* Candidatos */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .card-body {
            padding: 25px;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .card-subtitle {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .card-description {
            color: #666;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            font-size: 1rem;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        /* Contacto */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .contact-item {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .contact-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .contact-item h3 {
            color: #667eea;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        /* Footer */
        footer {
            background: #2c3e50;
            color: white;
            padding: 40px 20px;
            margin-top: 40px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-section h3 {
            margin-bottom: 15px;
            color: #667eea;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section a {
            color: #bbb;
            text-decoration: none;
            transition: 0.3s;
        }

        .footer-section a:hover {
            color: #667eea;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid #444;
            padding-top: 20px;
            color: #bbb;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 60px;
                right: 0;
                background: #667eea;
                flex-direction: column;
                width: 100%;
                text-align: center;
                gap: 0;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                padding: 15px;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }

            .hamburger {
                display: block;
            }

            h1, h2 {
                font-size: 1.8rem;
            }

            .carousel-slide {
                height: 200px;
                padding: 20px;
            }

            .carousel-slide h2 {
                font-size: 1.3rem;
            }

            .carousel-slide p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="nav-container">
            <div class="logo">🗳️ VotoSeguro</div>
            <ul class="nav-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#candidatos">Candidatos</a></li>
                <li><a href="#elecciones">Elecciones</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
            <div class="hamburger" id="hamburger">☰</div>
        </div>
    </nav>

    <!-- Carrusel -->
    <section id="inicio" class="carousel">
        <div class="carousel-container">
            <div class="carousel-wrapper">
                <div class="carousel-slide active">
                    <h2>Bienvenido a VotoSeguro</h2>
                    <p>La plataforma más segura para ejercer tu derecho al voto. Participa en elecciones de forma transparente y confiable.</p>
                </div>
                <div class="carousel-slide">
                    <h2>Seguridad y Transparencia</h2>
                    <p>Nuestro sistema utiliza tecnología de punta para garantizar la integridad de cada voto. Tu participación es importante.</p>
                </div>
                <div class="carousel-slide">
                    <h2>Participa Ahora</h2>
                    <p>Descubre los candidatos, conoce las propuestas y emite tu voto de forma segura. Juntos construimos el futuro.</p>
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
    <section id="faq">
        <div class="container">
            <h2>Preguntas Frecuentes</h2>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>¿Cómo me registro para votar?</span>
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    Debes ingresar tu cédula de identidad válida y completar el formulario de registro con tus datos personales. El proceso toma menos de 5 minutos.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>¿Es seguro votar en línea?</span>
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    Sí, utilizamos encriptación de nivel militar y protocolos de seguridad de punta para proteger tu voto y datos personales.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>¿Puedo cambiar mi voto después de emitirlo?</span>
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    Una vez que has emitido tu voto, no puede ser modificado. Esto garantiza la integridad y confidencialidad del proceso electoral.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span>¿Mis datos personales son confidenciales?</span>
                    <span>+</span>
                </div>
                <div class="faq-answer">
                    Absolutamente. Tus datos se mantienen completamente confidenciales y no se compartirán con terceros bajo ninguna circunstancia.
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" style="background: #f8f9fa;">
        <div class="container">
            <h2>Contacto y Ayuda</h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <h3>Llamadas</h3>
                    <p>+1 (555) 123-4567</p>
                    <p>Lun-Vie: 8:00 AM - 6:00 PM</p>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">📧</div>
                    <h3>Email</h3>
                    <p>soporte@votoseguro.com</p>
                    <p>Respuesta en 24 horas</p>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">💬</div>
                    <h3>Chat</h3>
                    <p>Disponible en tiempo real</p>
                    <p>Lun-Dom: 24/7</p>
                </div>
            </div>
            <div style="max-width: 500px; margin: 0 auto;">
                <form>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="mensaje">Mensaje</label>
                        <textarea id="mensaje" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn" style="width: 100%;">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Sobre VotoSeguro</h3>
                <p>Plataforma confiable para elecciones electrónicas seguras y transparentes.</p>
            </div>
            <div class="footer-section">
                <h3>Enlaces Rápidos</h3>
                <ul>
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#candidatos">Candidatos</a></li>
                    <li><a href="#elecciones">Elecciones</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Legal</h3>
                <ul>
                    <li><a href="#">Privacidad</a></li>
                    <li><a href="#">Términos de Servicio</a></li>
                    <li><a href="#">Política de Cookies</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Síguenos</h3>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 VotoSeguro. Todos los derechos reservados.</p>
        </div>
    </footer>

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
</body>
</html>