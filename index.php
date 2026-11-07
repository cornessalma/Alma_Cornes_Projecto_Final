<?php
session_start();

if (isset($_SESSION["usuario"])) {
    header('Location: panel.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>

    <link rel="stylesheet" href="styles.css">

</head>
<body>


    <div class="registro-card">
        <h1>Inicio de Sesión</h1>

        <form action="#" method="POST" id="registroForm">
            <fieldset>

                <div>
                    <label for="nombre">Nombre de Usuario</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: maurobob77" required>
                </div>


                <div>
                    <label for="contraseña">Contraseña</label>
                    <input type="text" id="contraseña" name="pass" placeholder="Ej: h00la123" required>
                </div>


                
                <div>
                    <button type="submit">Confirmar</button>
                    <button type="reset">Crear Cuenta</button>
                </div>

        </form>
    </div>

</body>
</html>