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

        <!-- Buscador de Candidatos -->
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Buscar candidato por nombre, partido o cargo...">
            <span class="search-icon">🔍</span>
        </div>

        <!-- Sección: Presidente -->
        <div class="election-section" data-position="presidente">
            <div class="election-category">
                <span class="category-dot"></span>
                <h2 class="category-title">Presidencia</h2>
            </div>
            <div class="candidates-grid" id="candidatesGridPresidente">
                <!-- Candidato 1 -->
                <div class="candidate-card h-100" data-name="carlos martinez" data-party="partido azul" data-position="presidente">
                    <div class="card-body text-center">
                        <div class="avatar">👨‍💼</div>
                        <h5 class="fw-bold mt-3">Carlos Martínez</h5>
                        <span class="partido-badge">Partido Azul</span>
                        <p class="mt-3">Educación digital, innovación tecnológica y desarrollo sostenible.</p>
                        <a href="propuesta.php?id=1" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 2 -->
                <div class="candidate-card h-100" data-name="maría gonzález" data-party="partido rojo" data-position="presidente">
                    <div class="card-body text-center">
                        <div class="avatar">👩‍💼</div>
                        <h5 class="fw-bold mt-3">María González</h5>
                        <span class="partido-badge">Partido Rojo</span>
                        <p class="mt-3">Crecimiento económico, empleo formal y bienestar social.</p>
                        <a href="propuesta.php?id=2" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 3 -->
                <div class="candidate-card h-100" data-name="roberto sánchez" data-party="partido verde" data-position="presidente">
                    <div class="card-body text-center">
                        <div class="avatar">👨‍💼</div>
                        <h5 class="fw-bold mt-3">Roberto Sánchez</h5>
                        <span class="partido-badge">Partido Verde</span>
                        <p class="mt-3">Transición energética y políticas ambientales sostenibles.</p>
                        <a href="propuesta.php?id=3" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 4 -->
                <div class="candidate-card h-100" data-name="patricia rivera" data-party="partido gris" data-position="presidente">
                    <div class="card-body text-center">
                        <div class="avatar">👩‍💼</div>
                        <h5 class="fw-bold mt-3">Patricia Rivera</h5>
                        <span class="partido-badge">Partido Gris</span>
                        <p class="mt-3">Justicia social, salud pública y educación gratuita.</p>
                        <a href="propuesta.php?id=4" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 5 -->
                <div class="candidate-card h-100" data-name="jorge ramírez" data-party="partido naranja" data-position="presidente">
                    <div class="card-body text-center">
                        <div class="avatar">👨‍💼</div>
                        <h5 class="fw-bold mt-3">Jorge Ramírez</h5>
                        <span class="partido-badge">Partido Naranja</span>
                        <p class="mt-3">Libertad económica, reducción de impuestos y emprendedores.</p>
                        <a href="propuesta.php?id=5" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 6 -->
                <div class="candidate-card h-100" data-name="ana castro" data-party="partido dorado" data-position="presidente">
                    <div class="card-body text-center">
                        <div class="avatar">👩‍💼</div>
                        <h5 class="fw-bold mt-3">Ana Castro</h5>
                        <span class="partido-badge">Partido Dorado</span>
                        <p class="mt-3">Seguridad nacional, orden y respeto a las leyes.</p>
                        <a href="propuesta.php?id=6" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Gobernador -->
        <div class="election-section" data-position="gobernador">
            <div class="election-category">
                <span class="category-dot"></span>
                <h2 class="category-title">Gobernación</h2>
            </div>
            <div class="candidates-grid" id="candidatesGridGobernador">
                <!-- Candidato 7 -->
                <div class="candidate-card h-100" data-name="laura hernández" data-party="partido nacional" data-position="gobernador">
                    <div class="card-body text-center">
                        <div class="avatar">👩‍💼</div>
                        <h5 class="fw-bold mt-3">Laura Hernández</h5>
                        <span class="partido-badge">Partido Nacional</span>
                        <p class="mt-3">Desarrollo regional, infraestructura y obras públicas.</p>
                        <a href="propuesta.php?id=7" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 8 -->
                <div class="candidate-card h-100" data-name="miguel torres" data-party="partido azul" data-position="gobernador">
                    <div class="card-body text-center">
                        <div class="avatar">👨‍💼</div>
                        <h5 class="fw-bold mt-3">Miguel Torres</h5>
                        <span class="partido-badge">Partido Azul</span>
                        <p class="mt-3">Agricultura, pesca y desarrollo costero sostenible.</p>
                        <a href="propuesta.php?id=8" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 9 -->
                <div class="candidate-card h-100" data-name="silvia meadows" data-party="partido verde" data-position="gobernador">
                    <div class="card-body text-center">
                        <div class="avatar">👩‍💼</div>
                        <h5 class="fw-bold mt-3">Silvia Meadows</h5>
                        <span class="partido-badge">Partido Verde</span>
                        <p class="mt-3">Conservación de recursos naturales y ecoturismo.</p>
                        <a href="propuesta.php?id=9" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Alcalde -->
        <div class="election-section" data-position="alcalde">
            <div class="election-category">
                <span class="category-dot"></span>
                <h2 class="category-title">Alcaldía</h2>
            </div>
            <div class="candidates-grid" id="candidatesGridAlcalde">
                <!-- Candidato 10 -->
                <div class="candidate-card h-100" data-name="antonio lópez" data-party="partido morado" data-position="alcalde">
                    <div class="card-body text-center">
                        <div class="avatar">👨‍💼</div>
                        <h5 class="fw-bold mt-3">Antonio López</h5>
                        <span class="partido-badge">Partido Morado</span>
                        <p class="mt-3">Seguridad ciudadana, transporte y servicios municipales.</p>
                        <a href="propuesta.php?id=10" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 11 -->
                <div class="candidate-card h-100" data-name="carmen ruiz" data-party="partido rojo" data-position="alcalde">
                    <div class="card-body text-center">
                        <div class="avatar">👩‍💼</div>
                        <h5 class="fw-bold mt-3">Carmen Ruiz</h5>
                        <span class="partido-badge">Partido Rojo</span>
                        <p class="mt-3">Espacios públicos, parques y áreas recreativas.</p>
                        <a href="propuesta.php?id=11" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 12 -->
                <div class="candidate-card h-100" data-name="daniel ortega" data-party="partido naranja" data-position="alcalde">
                    <div class="card-body text-center">
                        <div class="avatar">👨‍💼</div>
                        <h5 class="fw-bold mt-3">Daniel Ortega</h5>
                        <span class="partido-badge">Partido Naranja</span>
                        <p class="mt-3">Gestión de residuos, reciclaje y ciudad limpia.</p>
                        <a href="propuesta.php?id=12" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
                </div>

                <!-- Candidato 13 -->
                <div class="candidate-card h-100" data-name="elena navarro" data-party="partido dorado" data-position="alcalde">
                    <div class="card-body text-center">
                        <div class="avatar">👩‍💼</div>
                        <h5 class="fw-bold mt-3">Elena Navarro</h5>
                        <span class="partido-badge">Partido Dorado</span>
                        <p class="mt-3">Cultura, turismo y patrimonio histórico municipal.</p>
                        <a href="propuesta.php?id=13" class="btn btn-accent mt-3 w-100">Ver Propuesta</a>
                    </div>
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
