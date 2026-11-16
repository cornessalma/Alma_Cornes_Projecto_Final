<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$sql = "SELECT id, nombre, correo, fecha_archivado 
        FROM registro 
        WHERE fecha_archivado IS NOT NULL 
        ORDER BY fecha_archivado DESC";
$stmt = $conexionbd->prepare($sql);
$stmt->execute();
$archivados = $stmt->fetchAll();

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Archivados</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="registro-card" style="max-width: 900px;">
        <h1>Usuarios Archivados</h1>
        <p>Historial de cuentas desactivadas temporalmente</p>

        <div style="margin-bottom: 1.5rem;">
            <a href="admin_usuarios.php"><button type="button">Volver a Usuarios Activos</button></a>
        </div>

        <?php if (empty($archivados)): ?>
            <p style="text-align: center; padding: 1rem;">No hay usuarios archivados.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #3f78c3;">
                        <th style="padding: 8px;">ID</th>
                        <th style="padding: 8px;">Nombre</th>
                        <th style="padding: 8px;">Correo</th>
                        <th style="padding: 8px;">Fecha Archivado</th>
                        <th style="padding: 8px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($archivados as $u): ?>
                        <tr style="border-bottom: 1px solid #cbd5e1;">
                            <td style="padding: 8px;"><?= escapar((string)$u['id']) ?></td>
                            <td style="padding: 8px;"><?= escapar($u['nombre']) ?></td>
                            <td style="padding: 8px;"><?= escapar($u['correo']) ?></td>
                            <td style="padding: 8px;"><?= escapar($u['fecha_archivado']) ?></td>
                            <td style="padding: 8px;">
                                <form action="reactivar_usuario.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" style="background: none; border: none; color: #0b7689; cursor: pointer; text-decoration: underline; padding: 0; font-size: inherit;">Reactivar</button>
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