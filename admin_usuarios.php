<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}


$sql = "SELECT id, nombre, correo, rol, fecha_creacion 
        FROM registro 
        WHERE fecha_archivado IS NULL 
        ORDER BY id DESC";

$stmt = $conexionbd->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll();

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="registro-card" style="max-width: 900px;">
        <h1>Panel de Administración</h1>
        <p>Gestión de usuarios activos en el sistema</p>

        <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;">
            <div>
                <a href="panel.php"><button type="button">Volver al Panel</button></a>
                <a href="registrar_admin.php"><button type="button">Registrar Nuevo Usuario</button></a>
            </div>
            <div>
                <a href="usuarios_archivados.php"><button type="button">Ver Archivados</button></a>
                <a href="logout.php"><button type="button">Cerrar Sesión</button></a>
            </div>
        </div>

        <?php if (empty($usuarios)): ?>
            <p style="text-align: center; padding: 1rem;">No se encontraron usuarios.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #3f78c3;">
                        <th style="padding: 8px;">ID</th>
                        <th style="padding: 8px;">Nombre</th>
                        <th style="padding: 8px;">Correo</th>
                        <th style="padding: 8px;">Rol</th>
                        <th style="padding: 8px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr style="border-bottom: 1px solid #cbd5e1;">
                            <td style="padding: 8px;"><?= escapar((string)$u['id']) ?></td>
                            <td style="padding: 8px;"><?= escapar($u['nombre']) ?></td>
                            <td style="padding: 8px;"><?= escapar($u['correo']) ?></td>
                            <td style="padding: 8px;"><?= escapar($u['rol']) ?></td>
                            <td style="padding: 8px; display: flex; gap: 5px;">
                                <a href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>
                                |
                                <form action="archivar_usuario.php" method="POST" style="display: inline;" onsubmit="return confirm('¿Seguro que querés archivar este usuario?');">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" style="background: none; border: none; color: #d52d2d; cursor: pointer; text-decoration: underline; padding: 0; font-size: inherit;">Archivar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>
</html>