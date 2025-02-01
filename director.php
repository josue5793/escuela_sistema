<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
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
    <link rel="stylesheet" href="CSS/director.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Panel del director</h1>
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
            <h2>Bienvenid@ Director</h2>
            <p>Desde aquí puedes gestionar los usuarios, grupos, alumnos, profesores, materias y generar reportes. Usa los botones a continuación para navegar a las diferentes secciones del sistema. Si necesitas agregar más opciones en el futuro, estas aparecerán en este panel.</p>
        </section>

        <!-- Botones de control -->
        <div class="button-container">
           
            <a href="gestionar_grupos.php" class="control-button">
                <i class="bi bi-people"></i>
                <span>Gestión de Grupos</span>
            </a>
            <a href="gestionar_alumnos.php" class="control-button">
                <i class="bi bi-mortarboard"></i>
                <span>Alumnos</span>
            </a>
            <a href="gestionar_profesores.php" class="control-button">
                <i class="bi bi-person-badge"></i>
                <span>Profesores</span>
            </a>
            <a href="materias.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Registro de Materias</span>
            </a>
            <a href="reportes.php" class="control-button">
                <i class="bi bi-bar-chart"></i>
                <span>Generación de Reportes</span>
            </a>
            <!-- Nuevo botón para gestionar perfiles -->
            <a href="gestionar_perfiles.php" class="control-button">
                <i class="bi bi-person-lines-fill"></i>
                <span>Gestionar Perfiles</span>
            </a>
            <a href="administrar_periodos.php" class="control-button">
        <i class="bi bi-calendar"></i>
        <span>Periodos</span>
    </a>
            <!-- Nuevo botón para administrar sitio -->
    <a href="administrar_sitio.php" class="control-button">
        <i class="bi bi-gear"></i>
        <span>Administrar Sitio</span>
    </a>
        </div>
    </main>
</body>
</html>
