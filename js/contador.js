document.addEventListener("DOMContentLoaded", function () {

    const countdowns = document.querySelectorAll(".countdown");

    countdowns.forEach(function (element) {

        const fechaFin = new Date(element.getAttribute("data-fecha")).getTime();

        const intervalo = setInterval(function () {

            const ahora = new Date().getTime();
            const diferencia = fechaFin - ahora;

            if (diferencia <= 0) {
                element.innerHTML = "<span class='text-danger fw-bold'>Finalizada</span>";
                clearInterval(intervalo);
                return;
            }

            const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
            const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

            element.innerHTML = `
                <small class="text-info fw-semibold">
                    <i class="bi bi-hourglass-split"></i> ${dias}d ${horas}h ${minutos}m ${segundos}s restantes
                </small>
            `;

        }, 1000);
    });

});