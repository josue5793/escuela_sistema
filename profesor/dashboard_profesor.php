<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit;
}

// Obtener el nombre del profesor desde la sesión
$nombre_profesor = $_SESSION['nombre'];

// Incluir la conexión a la base de datos
require_once '../db.php';
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
    <!-- Barra superior -->
    <header class="navbar">
        <div class="navbar-left">
            <span>Panel de Control del Profesor</span>
        </div>
        <div class="navbar-right">
            <span>Bienvenido, <?php echo htmlspecialchars($nombre_profesor); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </header>

    <!-- Cuerpo del documento -->
    <main class="main-container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <h1>Bienvenido al Panel de Control del Profesor</h1>
            <p>Aquí puedes ver tus materias asignadas, grupos y consultar información relevante para tu desempeño docente.</p>
        </section>

        <!-- Botones de opciones -->
        <div class="button-container">
            <a href="materias_asignadas.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Mis Materias</span>
            </a>
            <a href="grupos_asignados.php" class="control-button">
                <i class="bi bi-people"></i>
                <span>Mis Grupos</span>
            </a>
            <a href="calendario.php" class="control-button">
                <i class="bi bi-calendar"></i>
                <span>Calendario</span>
            </a>
            <a href="consultar_calificaciones.php" class="control-button">
                <i class="bi bi-clipboard-check"></i>
                <span>Calificaciones</span>
            </a>
            <a href="mensajes.php" class="control-button">
                <i class="bi bi-envelope"></i>
                <span>Mensajes</span>
            </a>
        </div>
    </main>

    <!-- Pie de página -->
    <footer class="footer">
        <span id="fecha-hora"></span>
    </footer>

    <!-- Script para la fecha y hora -->
    <script src="../JS/dashboard_profesor.js"></script>
</body>
</html>
