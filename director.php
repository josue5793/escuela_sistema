<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión y si tiene el rol adecuado
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    // Redirigir al login con un mensaje de error
    header("Location: login.php?error=acceso_denegado");
    exit;
}

// Obtener el nombre del director desde la sesión
$nombre_director = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Director';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Director</title>
    <link rel="stylesheet" href="css/director.css">
</head>
<body>
    <!-- Menú Superior -->
    <header>
        <nav>
            <ul>
                <li><a href="director.php">Inicio</a></li>
                <li><a href="gestionar_profesores.php">Profesores</a></li>
                <li><a href="gestionar_alumnos.php">Alumnos</a></li>
                <li><a href="reportes.php">Reportes</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenedor Principal -->
    <div class="main-container">
        <h1>Bienvenido, <?php echo htmlspecialchars($nombre_director); ?>!</h1>

        <!-- Sección de Estadísticas -->
        <section class="stats">
            <div class="stat-card">
                <h2>Alumnos</h2>
                <p>250</p>
            </div>
            <div class="stat-card">
                <h2>Profesores</h2>
                <p>35</p>
            </div>
            <div class="stat-card">
                <h2>Grupos</h2>
                <p>10</p>
            </div>
        </section>

        <!-- Accesos Rápidos -->
        <section class="quick-links">
            <h2>Accesos Rápidos</h2>
            <div class="links-container">
                <a href="gestionar_profesores.php" class="quick-link">Gestionar Profesores</a>
                <a href="gestionar_alumnos.php" class="quick-link">Gestionar Alumnos</a>
                <a href="reportes.php" class="quick-link">Generar Reportes</a>
            </div>
        </section>
    </div>
</body>
</html>
