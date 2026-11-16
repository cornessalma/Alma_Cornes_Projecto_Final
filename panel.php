<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Personalización en rojo Pinterest para el panel */
        .navbar-pinterest {
            background-color: #e60023 !important;
        }
        .btn-pinterest {
            background-color: #e60023;
            border-color: #e60023;
            color: white;
        }
        .btn-pinterest:hover {
            background-color: #ad081b;
            border-color: #ad081b;
            color: white;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark navbar-pinterest">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Mi Panel</span>
             
            <div>
                <?php if (isset($_SESSION["rol"]) && $_SESSION["rol"] === 'admin'): ?>
                    <a href="admin_usuarios.php" class="btn btn-light btn-sm">Administrar cuentas</a>
                <?php else: ?>
                    <a href="mi_cuenta.php" class="btn btn-light btn-sm">Tu cuenta</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-light btn-sm">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                
                <h1 class="h4">Bienvenido/a, <?php echo htmlspecialchars($_SESSION["usuario"], ENT_QUOTES, 'UTF-8'); ?> 👋</h1>
                <p class="mb-0 text-secondary">Iniciaste correctamente! :)</p>

                <hr>
                
                <a href="pinterest.html" class="btn btn-pinterest">Toca acá para ir a la página</a>
            </div>
        </div>
    </div>

</body> 
</html>