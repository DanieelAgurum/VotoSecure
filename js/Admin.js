document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("toggleBtn");
    const sidebar = document.getElementById("sidebar");

    if (btn && sidebar) {
        btn.addEventListener("click", function () {
            sidebar.classList.toggle("hide");
        });
    }
});