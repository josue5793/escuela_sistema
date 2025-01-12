<?php
session_start();
require_once 'db.php'; // Incluir la conexión a la base de datos

// Verificar si el usuario está logueado y tiene el rol de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Profesor</title>
    <link rel="stylesheet" href="CSS/dashboard_profesor.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Panel del Profesor</h1>
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
            <h2>Bienvenid@ al Panel del Profesor</h2>
            <p>Desde aquí puedes gestionar tus materias, registrar calificaciones, tomar asistencia y consultar tus notificaciones. Usa los botones a continuación para navegar a las diferentes secciones.</p>
        </section>

        <!-- Ciclo Escolar Activo -->
        <section class="active-cycle">
            <h2>Ciclo Escolar Activo</h2>
            <p><?php echo isset($ciclo_escolar['ciclo_nombre']) ? htmlspecialchars($ciclo_escolar['ciclo_nombre']) : "No hay un ciclo escolar activo."; ?></p>
        </section>

        <!-- Niveles -->
        <?php if (!empty($niveles)): ?>
        <section class="teacher-levels">
            <h2>Niveles en los que trabajas</h2>
            <ul>
                <?php foreach ($niveles as $nivel): ?>
                    <li><?php echo htmlspecialchars($nivel['nivel_nombre']); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>

        <!-- Botones de control -->
        <div class="button-container">
            <a href="mis_materias.php" class="control-button">
                <i class="bi bi-journal-bookmark"></i>
                <span>Mis Materias</span>
            </a>
            <a href="registro_calificaciones.php" class="control-button">
                <i class="bi bi-pencil-square"></i>
                <span>Calificaciones</span>
            </a>
            <a href="asistencia.php" class="control-button">
                <i class="bi bi-clipboard-check"></i>
                <span>Asistencia</span>
            </a>
            <a href="notificaciones.php" class="control-button">
                <i class="bi bi-envelope"></i>
                <span>Notificaciones</span>
            </a>
            <a href="calendario.php" class="control-button">
                <i class="bi bi-calendar"></i>
                <span>Calendario Académico</span>
            </a>
        </div>
    </main>
</body>
</html>
