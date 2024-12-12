<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="css/administrador.css">

</head>
<body>
    <!-- Menú Superior -->
    <header>
        <nav>
            <ul>
                <li><a href="administrador.php">Inicio</a></li>
                <li><a href="gestionar_usuarios.php">Usuarios</a></li>
                <li><a href="gestionar_alumnos.php">Alumnos</a></li>
                <li><a href="gestionar_profesores.php">Profesores</a></li>
                <li><a href="reportes.php">Reportes</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h1>
        <h2>Este es tu panel de administrador. Usa el menú para navegar por las opciones disponibles.</h2>
    </main>
</body>
</html>
