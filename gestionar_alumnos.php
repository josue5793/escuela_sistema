<?php
session_start();

// Verificar si el usuario está logueado y tiene permisos
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['administrador', 'director'])) {
    header("Location: login.php?error=acceso_denegado");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alumnos</title>
    <link rel="stylesheet" href="CSS/gestionar_alumnos2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Alumnos</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="main-container">
    <!-- Sección de bienvenida -->
    <section class="welcome-section">
        <h2>Administracion de alumnos</h2>
        <p>Desde aquí puedes gestionar la informacion de cada alumno.</p>
    </section>

    <!-- Botones de control -->
    <div class="button-container">
        <a href="agregar_alumno.php" class="control-button">
            <i class="bi bi-person"></i>
            <span>Agregar Alumno</span>
        </a>
        <a href="consultar_alumnos.php" class="control-button">
            <i class="bi bi-person-badge"></i>
            <span>Consultar Alumnos</span>
        </a>
        
        <a href="administrador.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Panel Administrador</span>
        </a>
    </div>
</main>

    
</body>
</html>
