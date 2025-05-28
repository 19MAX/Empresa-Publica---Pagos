document.addEventListener("DOMContentLoaded", function () {
    // Mostrar el preloader cuando la página comienza a cargarse
    window.onbeforeunload = function () {
        showPreloader();
    };

    // Ocultar el preloader cuando la página ha terminado de cargarse
    window.onload = function () {
        hidePreloader();
    };

    // También ocultar el preloader cuando el DOM está completamente cargado
    document.onreadystatechange = function () {
        if (document.readyState === "complete") {
            hidePreloader();
        }
    };

    // Ocultar el preloader después de 5 segundos como máximo (fallback)
    setTimeout(hidePreloader, 5000);

    function showPreloader() {
        document.getElementById("preloader").style.display = "flex";
    }

    function hidePreloader() {
        document.getElementById("preloader").style.display = "none";
    }
});