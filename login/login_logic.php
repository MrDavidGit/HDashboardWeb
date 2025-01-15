<?php
// Indicación de dependencias fuertes
require __DIR__ . '/vendor/autoload.php';
require 'utils.php';


// Importa la clase de la dependencia de libreria
use Dotenv\Dotenv;

// Carga el archivo inmutable env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Guarda en variables los datos del archivo env
$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$dbname = $_ENV['DB_DATABASE'];
$dbUsername = $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['DB_PASSWORD'];

// No use mysqli sobre todo por sintaxis y manejo de errores

try {
    //PDO es una extensión de php que viene por defecto y permite conectarse a distintas bases de datos.

    // Crear el DSN (Data Source Name) para la conexion a base de datos
    // No hay que especificar nada para que funcione con MariaDB
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    // Crea un array asociativo(diccionario en otros lenguajes) para guardar la configuración de forma de llave valor
    // Este lo pilla de variables estaticas de configuración del objeto de la conexión para ponerlas luego asi

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_PERSISTENT => true  // Conexión persistente para evitar sobrecarga innecesaria
    ];

    // Crea el objeto PDO de la conexion reusable a la base de datos
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, $opciones);

    // Configura el objeto PDO para que de errores si hay
    // Solo no use el modo de warning, ni de silent.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Si la conexión se hace correctamente, muestra un mensaje en la consola del servidor
    echo "Conexión exitosa a la base de datos MariaDB";
    // Operador ternario
    echo checkTableExist($pdo, 'Test') ? 'true' : 'false';
    

} catch (PDOException $e) {
    // Sale el error al intentar conectar a la base de datos
    echo "Error al intentar conectar a la base de datos: " . $e->getMessage();
}

    // Solo si hace falta crear las tablas
    //checkTablesExist($pdo);


    // Solo procesa los datos si el formulario fue enviado mediante POST para evitar problemas de rendimiento como al cargar la pagina
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $ip = obtenerIPCliente();
    
    // Comprobación de logeo
    if (checkUserData($pdo, $username, $password)){

        // Añadir conexion exitosa
        addConection($pdo, $username, true, $ip);

        $role = getUserRole($pdo,$username);

        // Crea la sessión con todos los parametros y datos indicados
        crearSession($username, $role);

    } else {

        // Añade a la tabla de conexiones que hubo una conexion fallida
        addConection($pdo, $username, false, $ip);

        // Checkeo para evitar ataques de fuerza bruta
        if(checkFailLogins($pdo, $ip)){

            // Ruta a tu script de bash
            $script_path = 'banip.sh';

            // Ejecutar el script para banear la ip temporalmente
            shell_exec("$script_path $ip");

        }

        // Redirige a la misma pagina de login con el parametro de error
        header('Location: login.php?error=1');
        exit();

        //echo "Usuario o contraseña incorrectos";

    }

}

?>
