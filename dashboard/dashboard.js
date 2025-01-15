
/* Medición en milisegundos y visible en segundos del tiempo de renderización de la pagina, restando el de carga final al de carga inicial */

let startLoadMs = 0;
let endLoadMs = 0;

document.addEventListener("DOMContentLoaded", function() {
    
    startLoadMs = performance.now();

});

window.onload = function() {

    endLoadMs = performance.now();


    let totalLoadMs = endLoadMs - startLoadMs;


    totalLoadMs = (totalLoadMs / 1000).toFixed(4);


    let div = document.getElementById("loadTime");

    let p = document.createElement("p");

    p.textContent = totalLoadMs + 's';

    div.appendChild(p);



    let loaderCircle = document.querySelector(".bx.bx-loader-circle");

    if (totalLoadMs >= 0.7) {

        loaderCircle.style.color = "#f44336"; // Rojo

    } else if (totalLoadMs >= 0.5) {

        loaderCircle.style.color = "#ff9800"; // Naranja

    } else if (totalLoadMs >= 0.3) {

        loaderCircle.style.color = "#ffeb3b"; // Amarillo

    } else if (totalLoadMs <= 0.3) {

        loaderCircle.style.color = "#83de22";

    }
    

    console.log(totalLoadMs);

};


// Click perfil y abre su ventana

document.getElementById("account").addEventListener("click", function(event) {

    const profileOptions = document.getElementById("profileOptions");
    const triangule = document.getElementById("triangule");

    // Si el menú está cerrado, lo mostramos
    triangule.style.display = "block"
    profileOptions.style.display = "flex"; // Mostramos antes de la animación
    profileOptions.classList.add("open");

    event.stopPropagation();  // Evita que el clic cierre inmediatamente el menú

});

// Detecta clics para cerrar el menu
document.querySelector(".bx.bx-x").addEventListener("click", function(event) {

    const triangule = document.getElementById("triangule");
    const profileOptions = document.getElementById("profileOptions");

    profileOptions.classList.add("closing");


    // Al final de la animación de cierre, ocultamos el menú completamente
    profileOptions.addEventListener('animationend', function(event) {
        if (event.animationName === 'collapseUp') {
            console.log("Animación terminada, cerrando menú");

            // Ocultar completamente el menú y el triángulo después de la animación
            profileOptions.style.display = "none";
            profileOptions.classList.remove("closing");
            triangule.style.display = "none";
        }
    }, { once: true });  // Solo escucha una vez para evitar múltiples activaciones



});

