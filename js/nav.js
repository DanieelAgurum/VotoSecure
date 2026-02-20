let lastScroll = 0;
const navbar = document.getElementById("mainNavbar");

window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 50 && currentScroll > lastScroll) {
        // Scroll hacia abajo
        navbar.classList.add("shrink");
    } else if (currentScroll < lastScroll) {
        // Scroll hacia arriba
        navbar.classList.remove("shrink");
    }

    lastScroll = currentScroll;
});
