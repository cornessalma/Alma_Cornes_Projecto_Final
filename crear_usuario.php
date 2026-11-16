<?php
session_start();
require "conexion.php";

$error = "";
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre   = trim($_POST["nombre"] ?? "");
    $correo   = trim($_POST["correo"] ?? "");
    $password = $_POST["password"] ?? "";
    $clave2   = $_POST["clave2"] ?? "";

    if ($nombre === "" || $correo === "" || $password === "" || $clave2 === "") {
        $error = "Completá todos los campos.";
    } elseif ($password !== $clave2) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresá un correo electrónico válido.";
    } else {
        $busca = $conexionbd->prepare("SELECT id FROM registro WHERE correo = ? OR nombre = ?");
        $busca->execute([$correo, $nombre]);

        if ($busca->fetch()) {
            $error = "Ese nombre de usuario o correo ya está registrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Se envía '-' en dni para cumplir con la columna de la tabla sin pedirlo en el formulario
            $alta = $conexionbd->prepare("INSERT INTO registro (nombre, dni, correo, password) VALUES (?, ?, ?, ?)");
            $alta->execute([$nombre, '-', $correo, $hash]);

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

        <?php if ($error !== ""): ?>
            <p style="color: #d52d2d; text-align: center; margin-bottom: 1rem;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form action="crear_usuario.php" method="POST" id="registroForm">
            <fieldset>

                <div>
                    <label for="nombre">Nombre de Usuario</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: maurobob77" required>
                </div>

                <div>
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" placeholder="Ej: nombreapellido@gmail.com" required>
                </div>

                <div>
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="12345678" required>
                </div>

                <div>
                    <label for="clave2">Repetir Contraseña</label>
                    <input type="password" id="clave2" name="clave2" placeholder="12345678" required>
                </div>
                
                <div>
                    <button type="submit">Confirmar</button>
                </div>

            </fieldset>
        </form>
    </div>

</body>
</html>