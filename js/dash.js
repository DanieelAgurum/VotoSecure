document.addEventListener("DOMContentLoaded", function () {


    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.querySelector(".main-content");

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("active");

            if (mainContent) {
                mainContent.classList.toggle("active");
            }
        });
    }
    const userMenuBtn = document.getElementById("userMenu");

    if (userMenuBtn) {

        const dropdownMenu = userMenuBtn.nextElementSibling;

        userMenuBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle("show");
        });

        // Cerrar si se hace click fuera
        document.addEventListener("click", function () {
            dropdownMenu.classList.remove("show");
        });
    }

    
});
