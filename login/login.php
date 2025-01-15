<?php
session_start();

// Verifica si ya hay una sesión iniciada
if (isset($_SESSION['username'])) {
    // Si hay una sesión, redirige a la página correspondiente
    header('Location: dashboard.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css"> <!-- Aquí enlazas tu CSS -->
    <link rel="icon" href="assets/lock-solid.svg" type="image/x-icon">  <!-- Aquí va el favicon -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Hylarion dashboard</title>
</head>
<body>
    
    <div id="main">

        <h1 id="Title">Login</h1>

        <form action="login1.php" method="post">
        
        <input type="text" id="User" name="username" placeholder="Pon tu usuario" required>
        <i class='bx bxs-user'></i>
        
        <input type="password" id="Password" name="password" placeholder="Pon tu contraseña" required>
        <i class='bx bxs-lock-alt' ></i>

        <button id="Login" type="submit">Iniciar sesión</button>
        </form>

    </div>

    <!-- Archivo javascript -->
    <script src="login.js"></script>

</body>
</html>