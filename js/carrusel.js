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
document.getElementById('hamburger').addEventListener('click', function () {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
});

// Cerrar menú al hacer clic en un enlace
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', function () {
        document.querySelector('.nav-links').classList.remove('active');
    });
});