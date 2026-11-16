<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}

$errores = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    if ($nombre === '' || $correo === '') {
        $errores[] = 'Todos los campos son obligatorios.';
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresá un correo válido.';
    }

    if (empty($errores)) {
        $verifica = $conexionbd->prepare("SELECT id FROM registro WHERE (correo = ? OR nombre = ?) AND id != ?");
        $verifica->execute([$correo, $nombre, $id]);

        if ($verifica->fetch()) {
            $errores[] = 'Ese nombre de usuario o correo ya está en uso por otro usuario.';
        } else {
            $sql = "UPDATE registro 
                    SET nombre = :nombre, correo = :correo 
                    WHERE id = :id AND fecha_archivado IS NULL";
            $stmt = $conexionbd->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'correo' => $correo,
                'id'     => $id
            ]);
            $mensaje = 'Datos actualizados correctamente.';
        }
    }
}

$consulta = $conexionbd->prepare("SELECT id, nombre, correo FROM registro WHERE id = :id AND fecha_archivado IS NULL LIMIT 1");
$consulta->execute(['id' => $id]);
$usuario = $consulta->fetch();

if (!$usuario) {
    exit('Usuario no encontrado o ya está archivado.');
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
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="registro-card">
        <h1>Editar Usuario</h1>
        <p>Modificando registro #<?= escapar((string)$usuario['id']) ?></p>

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

        <form action="editar_usuario.php?id=<?= $id ?>" method="POST" id="registroForm">
            <fieldset>
                <div>
                    <label for="nombre">Nombre de Usuario</label>
                    <input type="text" id="nombre" name="nombre" value="<?= escapar($usuario['nombre']) ?>" required>
                </div>

                <div>
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" value="<?= escapar($usuario['correo']) ?>" required>
                </div>

                <div>
                    <button type="submit">Guardar Cambios</button>
                    <a href="admin_usuarios.php"><button type="button">Volver</button></a>
                </div>
            </fieldset>
        </form>
    </div>

</body>
</html>