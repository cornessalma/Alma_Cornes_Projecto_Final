<?php
session_start();
require "conexion.php";

$error = "";
$exito = "";

// Solo procesamos el alta si el formulario se envió por POST.
// Si alguien abre la página con GET, baja directo al HTML y muestra el formulario vacío.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Leemos lo que escribió el usuario. trim() saca espacios de los costados
    $nombre = trim($_POST["nombre"]?? "");
    $dni = trim($_POST["dni"]?? "");
    $correo = trim($_POST["correo"]?? "");
    $password   = $_POST["password"]?? "";
    $clave2  = $_POST["clave2"]?? "";

    // Validación 1: ningún campo puede quedar vacío
    if ($nombre  === "" || $correo === ""|| $dni === ""|| $password === "" || $clave2 === "") {
        $error = "Completá todos los campos.";

    // Validación 2: las dos contraseñas deben ser idénticas
    } elseif ($password !== $clave2) {
        $error = "Las contraseñas no coinciden.";

    // Validación 3: largo mínimo de la contraseña
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";

         } elseif (!preg_match('/^\d{7,8}$/', $dni)) {
        $error = "El DNI debe  tener al menos 8 caracteres.";

    } else {
        // ¿Ya existe ese nombre de usuario? El "?" es un marcador:
        // el valor real viaja aparte y así se evita la inyección SQL
        $busca = $conexionbd->prepare("SELECT id FROM registro WHERE dni = ? OR correo = ? OR nombre = ?");
        $busca->execute([$dni,$correo,$nombre]);

        // fetch() devuelve la fila si la encontró, o false si no existe
        if ($busca->fetch()) {
            $error = "Ese usuario ya está registrado.";
        } else {
            // Convierte la contraseña en un hash irreversible.
            // En la base NUNCA se guarda la contraseña en texto plano.
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // INSERT con dos marcadores: los valores van en un array aparte
           $alta = $conexionbd->prepare("INSERT INTO registro (nombre, dni, correo, password) VALUES (?, ?, ?, ?)");
            $alta->execute([$nombre, $dni, $correo, $hash]);

            $exito = "Usuario creado correctamente.";

            $_SESSION["usuario"] = $nombre;
            $_SESSION["rol"] = "usuario";
            header("Location: panel.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <link rel="stylesheet" href="styles.css">

</head>
<body>


    <div class="registro-card">
        <h1>Registro</h1>

        <form action="crear_usuario.php" method="POST" id="registroForm">
            <fieldset>

                <div>
                    <label for="nombre">Nombre de Usuario</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: maurobob77" required>
                </div>


                <div>
                    <label for="correo">Correo electronico</label>
                    <input type="email" id="correo" name="correo" placeholder="Ej: nombreapellido@gmail.com" required>
                </div>

                <div>
                    <label for="dni">DNI</label>
                    <input type="text" id="dni" name="dni" placeholder="Ej: h00la123" required>
                </div>

                <div>
    <label for="password">Contraseña </label>
    <input type="text" id="password" name="password" placeholder="12345678 " required>
</div>

<div>
    <label for="clave2">Repetir Contraseña </label>
    <input type="text" id="clave2" name="clave2" placeholder="12345678 " required>
</div>
                
                <div>
                    <button type="submit">Confirmar</button>
                    
                </div>
</fieldset>
        </form>
    </div>

</body>
</html>