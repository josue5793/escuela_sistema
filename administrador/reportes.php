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
    <link rel="stylesheet" href="CSS/reportes2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Generar Reportes</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <h2>Generar reportes</h2>
            <p>DBienvenido a la generacion de reportes. Desde aqui podrás generar archivos PDF de la informacion contenida en la base de datos</p>
        </section>

        <!-- Botones de control -->
        <div class="button-container">
      
                     <a href="administrador_dashboard.php" class="control-button">
                <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
                <span>Panel Administrador</span>
            </a>
           </div>
        <h1> Panel de control</h1>
        <div class="reportes-section">
                 <!-- Botón para generar el reporte en PDF -->
            <a href="personalizar_reporte.php" class="control-button">
            <i class="bi bi-file-earmark-pdf"></i>
            <span>Generar reporte de usuarios del sistema en PDF</span>
            </a>

            </div>
    </main>
</body>
</html>
