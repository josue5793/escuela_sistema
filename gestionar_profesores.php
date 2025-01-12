<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'director') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Profesores</title>
    <link rel="stylesheet" href="CSS/gestionar_profesores2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestionar Profesores</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <h2>Gestión de Docentes</h2>
            <p>Actualización de perfiles de docentes y asignación de materias.</p>
        </section>

        <!-- Botones de control -->
        <div class="button-container">
            <!-- Botón para regresar al panel de administrador -->
            <a href="administrador.php" class="control-button">
                <i class="bi bi-house-door"></i>
                <span>Regresar al panel de administrador</span>
            </a>

            <!-- Botón para consulta de profesores y especialidades -->
            <a href="consulta_profesores.php" class="control-button">
                <i class="bi bi-person-lines-fill"></i>
                <span>Consulta de profesores y especialidad</span>
            </a>

            <!-- Botón para asignación de niveles -->
            <a href="asignar_niveles.php" class="control-button">
                <i class="bi bi-pen"></i>
                <span>Asignación de Niveles</span>
            </a>

            <!-- Botón para asignar materias -->
            <a href="asignar_materias.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Asignar Materias</span>
            </a>
        </div>
    </main>
</body>
</html>
