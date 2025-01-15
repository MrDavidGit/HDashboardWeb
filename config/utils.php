<?php

// PHP Doc
/**
 * Función para comprobar si una tabla de la base de datos existe o no
 *
 * @param pdo Objeto de la conexion a la base de datos.
 * @param string El nombre de la tabla a comprobar si existe.
 * @return boolean Si la tabla existe o no.
 */
function checkTableExist(PDO $pdo, string $nombreTabla): bool {

try {

    // Prepara la consulta SQL a la base de datos
    $stmt = $pdo->prepare("SHOW TABLES LIKE :tabla");

    // Ejecuta la consulta tras cambiar el placeholder del nombre de la tabla
    $stmt->execute([':tabla' => $nombreTabla]);

    // Devuelve 0 si no hay una tabla con ese nombre
    // Devuelve 1 si hay una tabla con ese nombre
    return $stmt->rowCount() != 0;



} catch (PDOException $e) {

    // Pone el error de exception concatenando el error con su mensaje con el .
    // Concatena el . a diferencia de el + en otros lenguajes
    echo "Error: " . $e->getMessage();
}}

/**
 * Verifica si un usuario ya existe en la tabla 'usuarios'.
 *
 * @param PDO    $pdo      Conexión a la base de datos usando PDO.
 * @param string $username Nombre del usuario que se desea verificar.
 * 
 * @return bool Devuelve true si el usuario existe, false si no existe.
 */
function usuarioExiste(PDO $pdo, string $username): bool {

    // Consulta SQL para verificar si el usuario existe
    $sql = "SELECT COUNT(*) FROM usuarios WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    
    // Retorna true si el usuario ya existe, false si no
    return $stmt->fetchColumn() == 1;
}


// Función para crear un nuevo usuario
/**
 * Crea un nuevo usuario en la base de datos con su contraseña encriptada con Argon2.
 *
 * @param PDO    $pdo       Conexión a la base de datos usando PDO.
 * @param string $username  Nombre del usuario.
 * @param string $password  Contraseña sin encriptar que será encriptada con Argon2.
 * @param string $rol       Rol del usuario (por defecto es 'Helper').
 */
function crearUsuario(PDO $pdo, string $username, string $password, string $rol){

    try {

        if (usuarioExiste($pdo,$username)) {

            echo "El usuario $username ya existe!";
            return;

        }

        // Encriptar la contraseña utilizando Argon2
        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);  // Argon2

        // Consulta SQL para añadir el nuevo usuario en la tabla
        $sql = "INSERT INTO usuarios (username, password, rol) VALUES (:username, :password, :rol)";
        $stmt = $pdo->prepare($sql);

        // Ejecuta la consulta
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':rol' => $rol
        ]);

    } catch (Exception $e) {

        // Sale cualquier error que ocurra durante la ejecución
        echo "Error al crear el usuario: " . $e->getMessage();

    }
}


/**
 * Comprueba si el nombre de usuario y la contraseña estan bien.
 *
 * @param PDO    $pdo       Conexión a la base de datos usando PDO.
 * @param string $username  Nombre del usuario.
 * @param string $password  Contraseña sin encriptar a comprobar.
 * 
 */

 // Esta pendiente por rendimiento separar a una función mas pequeña el checkeo de contraseña
function checkUserData(PDO $pdo, string $username, string $password){

    try {

        // Consulta SQL para obtener el usuario por el nombre de usuario
        $sql = "SELECT password FROM usuarios WHERE username = :username";

        // Prepara la consulta
        $stmt = $pdo->prepare($sql);

        // Ejecuta la consulta con el placeholder cambiado
        $stmt->execute([':username' => $username]);

        // Comprueba si el usuario existe
        // El fetch recupera la fila de datos
        // El fetch_assoc formatea esos datos como un array asociativo de clave-valor
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si hay usuario siendo asi true y no false comprueba la contraseña del usuario
        if ($usuario) {

            // Comprueba la contraseña usando password_verify()
            if (password_verify($password, $usuario['password'])) {

                return true;  // Contraseña correcta
            } else {

                return false;  // Contraseña incorrecta
            }
        } else {

            return false;  // Usuario no encontrado
        }

    } catch (Exception $e) {

        // Manejar errores si ocurre una excepción
        echo "Error al verificar los datos de login: " . $e->getMessage();
        return false;
    }
}

/**
 * Comprueba si las tablas de la base de datos existen y si no es el caso, las crea.
 *
 * @param PDO    $pdo       Conexión a la base de datos usando PDO.
 * 
 */
function checkTablesExist(PDO $pdo): void {

    // Comprueba si la tabla "usuarios" existe
    if (!checkTableExist($pdo, 'usuarios')) {
        $createUsuariosTable = "
            CREATE TABLE usuarios (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL,
                password VARCHAR(255) NOT NULL,  -- Contraseña encriptada
                old_password VARCHAR(255),  -- Almacena la contraseña anterior
                role ENUM('Helper', 'Mod', 'Admin') NOT NULL DEFAULT 'Helper'  -- Rol de usuario con la primera letra en mayúscula
            );
        ";
        
        // Ejecuta la creación de la tabla "usuarios"
        $pdo->exec($createUsuariosTable);
        echo "Tabla 'usuarios' creada.\n";

    } else {

        echo "La tabla 'usuarios' ya existe.\n";

    }

    // Comprueba si la tabla "conexiones" existe
    if (!checkTableExist($pdo, 'conexiones')) {
        $createConexionesTable = "
            CREATE TABLE conexiones (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                user_id INT(11) NOT NULL,  -- Relacionado con el id del usuario
                ip VARCHAR(45),
                pais VARCHAR(100),
                fecha_hora DATETIME NOT NULL,
                conexion_valida BOOLEAN NOT NULL DEFAULT 1,  -- Indica si la conexión fue válida
                FOREIGN KEY (user_id) REFERENCES usuarios(id)  -- Clave foránea que referencia a la tabla de usuarios
            );
        ";
        
        // Ejecuta la creación de la tabla "conexiones"
        $pdo->exec($createConexionesTable);
        echo "Tabla 'conexiones' creada.\n";

    } else {

        echo "La tabla 'conexiones' ya existe.\n";

    }
}


function cambiarNombreColumna(PDO $pdo, $tabla, $columnaAntigua, $columnaNueva, $tipoColumna) {
    try {
        // Preparar la consulta SQL para cambiar el nombre de la columna
        $sql = "ALTER TABLE $tabla CHANGE $columnaAntigua $columnaNueva $tipoColumna";
        
        // Ejecutar la consulta
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        echo "Columna renombrada exitosamente de $columnaAntigua a $columnaNueva.";
    } catch (PDOException $e) {
        // Manejo de errores
        echo "Error al renombrar la columna: " . $e->getMessage();
    }
}



/**
 * Obtiene la ubicación geográfica de una dirección IP utilizando la API de ipapi.
 *
 * Esta función hace una solicitud HTTP a la API de ipapi para obtener información
 * geográfica de la dirección IP proporcionada. Muestra el país de la IP si está disponible.
 *
 * @param string $ip La dirección IP para la cual se busca la ubicación.
 *
 * @return string El pais del que es esa ip segun la api.
 */
function getLocationForIp($ip): string {

    // Endpoint de la API con la IP proporcionada
    // Disponibles gratis 45 solicitudes por minuto
    $api_url = "http://ip-api.com/json/{$ip}";

    // Inicializamos cURL para comenzar la conexión
    $curl = curl_init();

    // Configuramos la URL y las opciones de cURL
    curl_setopt($curl, CURLOPT_URL, $api_url); // Especificamos la URL a la que se hará la solicitud
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // Para obtener la respuesta como string en lugar de mostrarla directamente

    // Ejecutamos la solicitud cURL
    $response = curl_exec($curl);

    // Verificamos si hubo errores
    if($response === false){
        echo 'Error al realizar la solicitud: ' . curl_error($curl);
    } else {

        // Decodificamos la respuesta JSON
        $data = json_decode($response, true);
        
        // Verificamos si el campo 'country' está presente en la respuesta
        if(isset($data['country'])){

            return $data['country'];
            
        } else {
            echo "No se pudo determinar la ubicación de la IP.";
        }
    }

    // Cerramos cURL
    curl_close($curl);
}

/**
 * Obtiene el ID de un usuario basado en su nombre de usuario.
 *
 * Esta función toma el nombre de usuario como entrada, ejecuta una consulta 
 * en la base de datos para encontrar su ID y lo retorna. 
 * Si no se encuentra el usuario, retorna null.
 *
 * @param PDO    $pdo      La instancia de PDO para la conexión a la base de datos.
 * @param string $username El nombre de usuario que se busca.
 *
 * @return int|null Retorna el ID del usuario si se encuentra, o null si no existe.
 *
 * @throws PDOException En caso de que ocurra un error durante la consulta SQL.
 */
function getUserID($pdo, $username) {

    // Consulta SQL para obtener el ID del usuario basado en el nombre de usuario
    $sql = "SELECT id FROM usuarios WHERE username = :username";
    
    try {
        // Prepara la sentencia SQL
        $stmt = $pdo->prepare($sql);
        
        // Vincula el parámetro de username a la consulta
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        
        // Ejecuta la consulta
        $stmt->execute();
        
        // Obtén el resultado
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si se encontró un usuario, retorna su ID; si no, retorna null
        return $usuario ? $usuario['id'] : null;
        
    } catch (PDOException $e) {

        // Muestra el error en caso de que la consulta falle
        echo "Error al obtener el ID del usuario: " . $e->getMessage();
        return null;
    }
}

// Creo que puedo cambiar la tabla de conexiones y añadir el usuario para optimizar el tema ese en el registro

/**
 * Inserta una nueva conexión en la base de datos.
 *
 * Esta función toma un nombre de usuario, dirección IP, país y si la conexión fue válida, 
 * y los inserta en la tabla `conexiones` de la base de datos. La fecha y hora de la conexión 
 * se registran automáticamente con la función NOW() de MySQL.
 *
 * @param PDO    $pdo             La instancia de PDO para la conexión a la base de datos.
 * @param string $username        El nombre de usuario para el cual se registrará la conexión.
 * @param string $ip              La dirección IP desde la que se realizó la conexión.
 * @param bool   $conexionValida  Indica si la conexión fue válida (true) o no (false).
 *
 * @return void
 *
 * @throws PDOException En caso de que ocurra un error durante la consulta SQL.
 */
function addConection($pdo, $username, $conexionValida, $ip) {

    // Consulta SQL para insertar una nueva fila en la tabla conexiones
    $sql = "INSERT INTO conexiones (user_id, ip, pais, fecha_hora, conexion_valida) 
            VALUES (:user_id, :ip, :pais, NOW(), :conexion_valida)";
    
    try {

        $userID = getUserID($pdo, $username);
        $pais = getLocationForIp($ip);

        // Prepara la sentencia SQL
        $stmt = $pdo->prepare($sql);
        
        // Vincula los parámetros a la consulta
        $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindParam(':pais', $pais, PDO::PARAM_STR);
        $stmt->bindParam(':conexion_valida', $conexionValida, PDO::PARAM_BOOL);
        
        // Ejecuta la consulta
        $stmt->execute();
        
        // Mensaje de éxito
        echo "Conexión agregada exitosamente.";

    } catch (PDOException $e) {

        // Muestra el error en caso de que la consulta falle
        echo "Error al agregar la conexión: " . $e->getMessage();

    }

}

/**
 * Función para obtener la IP del cliente.
 * Si es una conexión local (localhost), devuelve la IP pública usando ipify.org.
 * Si no es localhost, devuelve la IP del cliente directamente.
 *
 * @return string La IP del cliente
 */
function obtenerIPCliente() {
    // Obtener la IP del cliente desde la variable de servidor
    $ip = $_SERVER['REMOTE_ADDR'];

    // Si la IP es localhost (127.0.0.1 o ::1)
    if ($ip === '127.0.0.1' || $ip === '::1') {
        // Obtener la IP pública usando la API de ipify
        $ipPublica = file_get_contents('https://api.ipify.org');
        return $ipPublica !== false ? $ipPublica : 'No se pudo obtener la IP pública';
    }

    // Si no es localhost, devolvemos la IP del cliente
    return $ip;
}

/**
 * Verifica si una IP ha tenido al menos 3 intentos fallidos en la última hora.
 *
 * Esta función toma una conexión PDO a la base de datos y una IP, y consulta
 * si en la última hora ha habido al menos 3 intentos fallidos de conexión
 * (donde 'conexion_valida' es 0) para esa IP.
 *
 * @param PDO $pdo La instancia de conexión PDO a la base de datos.
 * 
 * @return bool Devuelve true si hubo al menos 3 intentos fallidos en la última hora, o false si no.
 */
function checkFailLogins($pdo, $ip) {

    // Fecha y hora de hace una hora
    $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));

    // Consulta para verificar si la IP tuvo al menos 3 intentos fallidos en la última hora
    $sql = "SELECT COUNT(*) as intentos 
            FROM conexiones 
            WHERE ip = :ip 
            AND fecha_hora >= :oneHourAgo 
            AND conexion_valida = 0";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ip' => $ip, 'oneHourAgo' => $oneHourAgo]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si 'intentos' es mayor o igual a 3, la IP tuvo al menos 3 intentos fallidos
    return $result['intentos'] == 3;
}



function isEmpty( $valor): bool {

    // Comprueba si el valor es NULL o está vacío
    return is_null($valor) || $valor === '';

}



function checkOldPassword(PDO $pdo, string $username): ?string {

    try {
        // Consulta SQL para obtener el campo old_password del usuario
        $sql = "SELECT old_password FROM usuarios WHERE username = :username";

        // Prepara la consulta
        $stmt = $pdo->prepare($sql);

        // Ejecuta la consulta con el placeholder cambiado
        $stmt->execute([':username' => $username]);

        // Recupera el dato de old_password
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Comprueba si se obtuvo el valor de old_password y si no está vacío o es NULL
        if ($usuario && !isEmpty($usuario['old_password'])) {
            return $usuario['old_password'];  // Devuelve el valor de old_password si no está vacío
        } else {
            return null;  // Devuelve null si está vacío o es NULL
        }

    } catch (Exception $e) {

        // Maneja errores si ocurre una excepción
        echo "Error al comprobar el campo old_password: " . $e->getMessage();
        return null;

    }
}

// Genera una contraseña random segura
function generatePassword(): string {

    $longitud = 12;

    // Definir los caracteres permitidos
    $letrasMayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $letrasMinusculas = 'abcdefghijklmnopqrstuvwxyz';
    $numeros = '0123456789';
    $simbolos = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    // Combinar todos los caracteres permitidos en un solo string
    $todosCaracteres = $letrasMayusculas . $letrasMinusculas . $numeros . $simbolos;

    // Asegurarse de incluir al menos uno de cada tipo de carácter en la contraseña
    $password = [
        $letrasMayusculas[random_int(0, strlen($letrasMayusculas) - 1)],
        $letrasMinusculas[random_int(0, strlen($letrasMinusculas) - 1)],
        $numeros[random_int(0, strlen($numeros) - 1)],
        $simbolos[random_int(0, strlen($simbolos) - 1)],
    ];

    // Rellenar el resto de la contraseña con caracteres aleatorios sin repetir
    for ($i = 4; $i < $longitud; $i++) {
        $caracter = $todosCaracteres[random_int(0, strlen($todosCaracteres) - 1)];
        
        // Asegurarse de que no se repitan caracteres
        while (in_array($caracter, $password)) {
            $caracter = $todosCaracteres[random_int(0, strlen($todosCaracteres) - 1)];
        }

        $password[] = $caracter;
    }

    // Mezclar los caracteres aleatoriamente para mayor seguridad
    shuffle($password);

    // Convertir el array en un string y devolver la contraseña
    return implode('', $password);
}


function updatePassword(PDO $pdo, string $username, string $nuevaContraseña): void {

    try {
        // Encriptar la nueva contraseña usando el algoritmo Argon2i
        $hashedPassword = password_hash($nuevaContraseña, PASSWORD_ARGON2I);

        // Consulta SQL para actualizar la contraseña del usuario
        $sql = "UPDATE usuarios SET password = :hashedPassword WHERE username = :username";

        // Preparar la consulta
        $stmt = $pdo->prepare($sql);

        // Ejecutar la consulta con los valores adecuados
        $stmt->execute([
            ':hashedPassword' => $hashedPassword,
            ':username' => $username
        ]);

    } catch (Exception $e) {
        // Manejar errores si ocurre una excepción
        echo "Error al actualizar la contraseña: " . $e->getMessage();
    }
}

// Obtener role del usuario de la base de datos
function getUserRole($pdo, $username) {

    try {
        // Preparar la consulta SQL para obtener el rol del usuario
        $sql = "SELECT rol FROM usuarios WHERE username = :username";
        $stmt = $pdo->prepare($sql);

        // Vincular el parámetro :username a la consulta
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si se encuentra un resultado, devolver el rol
        if ($result) {
            return $result['rol']; // Devolver el valor de la columna 'role'
        } else {
            return null; // Devolver null si el usuario no existe
        }

    } catch (PDOException $e) {

        // Manejar errores de la base de datos
        echo "Error al obtener el rol: " . $e->getMessage();
        return null;
    }
}


// Revisa si el usuario tiene la sesion iniciada y si no es el caso redirige a la pagina normal de login
function checkSession() {

     // Llamar a la sesion
     session_start();

    // Verificar si la sesión está iniciada (ej. si existe la variable de sesión 'username')
    if (!isset($_SESSION['username'])) {
        // Si no hay una sesión activa, redirigir al usuario a la página de login
        header("Location: login.php");
        exit(); // Terminar el script para evitar que el resto del código se ejecute
    }

}

// Crea la sesión del usuario
function crearSession($username, $role) {

    // Iniciar la sesión de forma segura antes de cualquier acción
    session_start();

    // Configurar el tiempo máximo de vida de la sesión en el servidor (6 horas)
    ini_set('session.gc_maxlifetime', 21600); // 21600 segundos = 6 horas

    // Configurar la cookie de sesión para que expire al cerrar el navegador
    session_set_cookie_params([
        'lifetime' => 0, // La cookie expira cuando se cierra el navegador
        'path' => '/',   // El alcance de la cookie es para todo el dominio
        //'domain' => 'tudominio.com', // Cambia esto por tu dominio
       /*A cambiarlo a futuro */ 'secure' => false, // Solo envía la cookie a través de HTTPS
        'httponly' => true, // La cookie no es accesible desde JavaScript
        'samesite' => 'Strict' // Previene el envío en solicitudes cross-site (protección CSRF)
    ]);

    // Si el login es exitoso, establece una sesión
    $_SESSION['username'] = $username;

    // Almacenar el rol en la sesión
    $_SESSION['role'] = $role;

    // Regenerar el ID de sesión para evitar ataques de fijación de sesión
    session_regenerate_id(true);

    //Redirige al dashboard.php
    header("Location: dashboard.php");
    exit(); // Terminar el script para evitar que el resto del código se ejecute

}


// Comprueba si el usuario tiene vpn
function checkVPN($ip) {
    // URL de la API para comprobar la IP
    $apiKey = 'TU_API_KEY'; // Reemplaza esto con tu clave API
    $url = "https://vpnapi.io/api/$ip?key=$apiKey";

    // Inicializar cURL
    $ch = curl_init();

    // Configurar la URL y opciones de la solicitud
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar la solicitud y almacenar la respuesta
    $response = curl_exec($ch);

    // Comprobar si hay errores
    if (curl_errno($ch)) {
        echo "Error: " . curl_error($ch);
    } else {
        // Convertir la respuesta JSON en un array
        $data = json_decode($response, true);

        // Verificar si el campo 'vpn' existe y está configurado
        if (isset($data['security']['vpn'])) {
            if ($data['security']['vpn']) {
                echo "La IP $ip está usando una VPN.";
            } else {
                echo "La IP $ip no está usando una VPN.";
            }
        } else {
            echo "No se pudo determinar si la IP $ip está usando una VPN.";
        }
    }

    // Cerrar la conexión cURL
    curl_close($ch);
}


// Comprueba si el role del usuario que esta guardado en su sessión es Admin o no y devuelve true o false segun eso
function checkUserIsAdmin(): bool {

    return $_SESSION['role'] == "Admin" ? true : false;

}

?>