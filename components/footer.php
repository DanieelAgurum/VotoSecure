<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/VotoSecure/obtenerLink/obtenerLink.php';
$urlBase = getBaseUrl();
?>
<footer class="footer-custom mt-5">
    <div class="container py-5">
        <div class="row gy-4">

            <!-- Logo / Proyecto -->
            <div class="col-md-4">
                <h5 class="footer-title">VotoSecure</h5>
                <p class="footer-text">
                    Plataforma digital para procesos de votación seguros, transparentes
                    y accesibles para todos los ciudadanos.
                </p>
            </div>

            <!-- Enlaces -->
            <div class="col-md-2">
                <h6 class="footer-subtitle">Navegación</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= $urlBase ?>/">Inicio</a></li>
                    <li><a href="<?= $urlBase ?>/vista/candidatos">Candidatos</a></li>
                    <li><a href="<?= $urlBase ?>/vista/propuestas">Propuestas</a></li>
                </ul>
            </div>

            <!-- Recursos -->
            <div class="col-md-3">
                <h6 class="footer-subtitle">Recursos</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="#faq">Preguntas frecuentes</a></li>
                    <li><a href="#">Cómo votar</a></li>
                    <li><a href="#">Términos y condiciones</a></li>
                    <li><a href="#">Privacidad</a></li>
                </ul>
            </div>

            <!-- Contacto -->
            <div class="col-md-3">
                <h6 class="footer-subtitle">Contacto</h6>
                <p class="footer-text mb-1"><i class="bi bi-envelope"></i> soporte@votosecure.com</p>
                <p class="footer-text"><i class="bi bi-geo"></i> México</p>
            </div>

        </div>
    </div>

    <!-- Barra inferior -->
    <div class="footer-bottom text-center py-3">
        <small>© 2026 VotoSecure. Todos los derechos reservados.</small>
    </div>
</footer>

<script src="<?= $urlBase ?>/js/tooltip.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>