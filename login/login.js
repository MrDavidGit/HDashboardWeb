document.addEventListener("DOMContentLoaded", function () {

    const urlParams = new URLSearchParams(window.location.search);
    const loginError = urlParams.get('error');

    // Si existe el parámetro "error=1", aplica la animación de sacudida al botón de login
    if (loginError === '1') {
        const loginButton = document.getElementById('Login');
        // const errorMessage = document.getElementById('errorMessage');

        // Añadir la clase de sacudida al botón de login
        loginButton.classList.add('shake');

        // Mostrar el mensaje de error genérico
        // errorMessage.style.display = 'block';

        // Eliminar la clase de animación después de que termine para que pueda aplicarse de nuevo
        loginButton.addEventListener('animationend', function() {
            loginButton.classList.remove('shake');
        });

        // A REVISAR
       // Eliminar el parámetro 'error' de la URL después de la animación
       const newUrl = window.location.origin + window.location.pathname;
       window.history.replaceState({}, document.title, newUrl); // Elimina el parámetro de error de la URL

    console.log("Hola");
    }
});
