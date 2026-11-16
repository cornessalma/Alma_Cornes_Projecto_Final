<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$consulta = $conexionbd->prepare("SELECT id, nombre, correo, rol, fecha_creacion FROM registro WHERE nombre = :nombre AND fecha_archivado IS NULL LIMIT 1");
$consulta->execute(['nombre' => $_SESSION['usuario']]);
$datos = $consulta->fetch();

if (!$datos) {
    header('Location: logout.php');
    exit;
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
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="registro-card">
        <h1>Mis Datos Personales</h1>
        <p>Información de tu cuenta registrada</p>

        <div style="margin-bottom: 1.5rem; line-height: 2;">
            <p><strong>Nombre de usuario:</strong> <?= escapar($datos['nombre']) ?></p>
            <p><strong>Correo electrónico:</strong> <?= escapar($datos['correo']) ?></p>
            <p><strong>Rol:</strong> <?= escapar($datos['rol']) ?></p>
            <p><strong>Fecha de registro:</strong> <?= escapar($datos['fecha_creacion']) ?></p>
        </div>

        <div style="display: flex; gap: 10px; justify-content: center;">
            <a href="panel.php"><button type="button">Volver al Panel</button></a>
            <a href="logout.php"><button type="button">Cerrar Sesión</button></a>
        </div>
    </div>

</body>
</html>