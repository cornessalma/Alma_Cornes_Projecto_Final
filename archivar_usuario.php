<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_usuarios.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id !== false && $id !== null && $id >= 1) {
    $sql = "UPDATE registro 
            SET fecha_archivado = CURRENT_TIMESTAMP 
            WHERE id = :id AND fecha_archivado IS NULL";
    $stmt = $conexionbd->prepare($sql);
    $stmt->execute(['id' => $id]);
}

header('Location: admin_usuarios.php');
exit;