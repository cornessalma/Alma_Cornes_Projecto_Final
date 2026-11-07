<?php
session_start();
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$usuario  = trim($_POST["nombre"] ?? "");
$password = $_POST["password"] ?? "";

// Solo usuarios no archivados
$consulta = $conexionbd->prepare(
    "SELECT nombre, password, rol FROM registro
     WHERE nombre = ? AND fecha_archivado IS NULL"
);
$consulta->execute([$usuario]);
$fila = $consulta->fetch();

if ($fila && password_verify($password, $fila["password"])) {
    session_regenerate_id(true);
    $_SESSION["usuario"] = $fila["nombre"];
    $_SESSION["rol"]     = $fila["rol"];
    header("Location: panel.php");
    exit;
}

header("Location: index.php?error=1");
exit;