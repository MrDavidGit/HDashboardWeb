<?php
require '../config/utils.php';



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <link rel="icon" href="../assets/mclogo.png" type="image/png">
    <link rel="stylesheet" href="dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Hylarion Web Dashboard</title>
</head>
<body>


<header id="banner">

    <ul id="ul1">
        
        <!-- Pagina del hosting -->
        <li>
            <a id="link1" class="links" href="https://clients.advinservers.com/login" target="_blank">
                <i class='bx bxs-wallet'></i> Hosting
            </a>
        </li>

        <!-- Pagina del plan -->
        <li>
            <a id="link2" class="links" href="No yet">
                <i class='bx bx-stats'></i> Estadísticas
            </a>
        </li>

        <!-- Pagina de PhPMyAdmin -->
        <li>
            <a id="link3" class="links" href="https://hylarion.net/jQ[PkztmFW]vA!0YT73udJB/index.php" target="_blank">
                <i class='bx bx-data'></i> PhPMyAdmin
            </a>
        </li>

        <!-- Pagina de sanciones -->
        <li>
            <a id="link4" class="links" href="No yet">
                <i class='bx bx-block'></i> Sanciones
            </a>
        </li>

    </ul>

    <div id="account">

        <i class='bx bxs-user'></i>

        <!-- Si quiero quitar el clic para opciones de perfil en el nick, saco esto fuera del div account padre -->
        <div id="username"></div>

    </div>

</header>


    <div id="triangule"></div>

    <div id="profileOptions">

        <i class='bx bx-x'></i>

        <!-- Se llamaba antes configuración de usuario -->
        <p> • Opciones de cuenta</p>
        <p> • Panel de admin</p>
        <p> • Cerrar sesión</p>

    </div>


<!-- Ventana  de opciones del usuario-->
<!-- Faltan ventanas de si se cambio bien el nombre o no -->

<div id="profileOptionsFrame">

    <i class='bx bx-x'></i>


    <h1 id="optionsTitle"> Opciones de usuario </h1>


    <h2 id="option1Title"> Cambiar nombre de usuario x </h2>

    <div id="options1Group">

        <!-- Falta poner el nick que pone el php -->
        <input type="text" id="newUsername" placeholder="Nuevo nombre de usuario">
        <button id="buttonConfirmUsername">Confirmar cambio</button>


    </div>


    <h2 id="option2Title"> Cambiar contraseña </h2>


    <div id="options2Group">

        <input type="password" id="actualPassword" placeholder="Contraseña actual">
        <button id="buttonConfirmPassword">Confirmar cambio</button>
        <!--<div id="generatedPassword"></div>-->

        <!-- Icono que quitar y dejar en el panel extra <i class='bx bx-copy-alt'></i>-->

    </div>

</div>



<!-- Pie de pagina -->
<footer></footer>

<script src="dashboard.js"></script>

<div id="loadTime">

<i class='bx bx-loader-circle' ></i>
        
</div>

</body>
</html>

<?php

?>