<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$errores = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol      = $_POST['rol'] ?? 'usuario';

    if ($nombre === '' || $correo === '' || $password === '') {
        $errores[] = 'Todos los campos son obligatorios.';
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresá un correo válido.';
    }
    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($rol !== 'usuario' && $rol !== 'admin') {
        $rol = 'usuario';
    }

    if (empty($errores)) {
        $busca = $conexionbd->prepare("SELECT id FROM registro WHERE correo = ? OR nombre = ?");
        $busca->execute([$correo, $nombre]);

        if ($busca->fetch()) {
            $errores[] = 'Ese usuario o correo ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO registro (nombre, dni, correo, password, rol) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conexionbd->prepare($sql);
            $stmt->execute([$nombre, '-', $correo, $hash, $rol]);

            $mensaje = 'Usuario registrado correctamente.';
        }
    }
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario (Admin)</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="registro-card">
        <h1>Registrar Nuevo Usuario</h1>
        <p>Alta desde el panel administrativo</p>

        <?php if ($mensaje !== ''): ?>
            <p style="color: green; text-align: center; margin-bottom: 1rem;"><?= escapar($mensaje) ?></p>
        <?php endif; ?>

        <?php if (!empty($errores)): ?>
            <ul style="color: #d52d2d; margin-bottom: 1rem; padding-left: 1.5rem;">
                <?php foreach ($errores as $e): ?>
                    <li><?= escapar($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="registrar_admin.php" method="POST" id="registroForm">
            <fieldset>
                <div>
                    <label for="nombre">Nombre de Usuario</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div>
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div>
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div>
                    <label for="rol">Rol del usuario</label>
                    <select id="rol" name="rol">
                        <option value="usuario">Usuario común</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>

                <div>
                    <button type="submit">Guardar Usuario</button>
                    <a href="admin_usuarios.php"><button type="button">Volver</button></a>
                </div>
            </fieldset>
        </form>
    </div>

</body>
</html>