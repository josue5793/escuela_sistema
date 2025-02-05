<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

// Obtener el nombre del director desde la sesión
$nombre_director = $_SESSION['nombre'];

// Incluir la conexión a la base de datos
require_once '../db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control del Director</title>
    <link rel="stylesheet" href="CSS/dashboard_director.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra superior -->
    <header class="navbar">
        <div class="navbar-left">
            <span>Panel de Control del Director</span>
        </div>
        <div class="navbar-right">
            <span>Bienvenido, <?php echo htmlspecialchars($nombre_director); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </header>

    <!-- Cuerpo del documento -->
    <main class="main-container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <h1>Bienvenido al Panel de Control del Director</h1>
            <p>Desde aquí puedes gestionar usuarios, grupos, alumnos, profesores, materias, periodos escolares y generar reportes. Utiliza los botones a continuación para acceder a las diferentes funcionalidades.</p>
        </section>

        <!-- Botones de opciones -->
        <div class="button-container">
            <a href="gestion_usuarios.php" class="control-button">
                <i class="bi bi-people"></i>
                <span>Administrar y gestionar profesores</span>
            </a>
            
            <a href="registro_materias.php" class="control-button">
                <i class="bi bi-backpack3"></i>
                <span>Registro de Materias</span>
            </a>
            <a href="gestion_grupos.php" class="control-button">
                <i class="bi bi-collection"></i>
                <span>Gestionar Grupos</span>
            </a>
            <a href="gestion_alumnos.php" class="control-button">
                <i class="bi bi-mortarboard"></i>
                <span>Gestionar Alumnos</span>
            </a>
            <a href="consulta_calificaciones.php" class="control-button">
                <i class="bi bi-clipboard-check"></i>
                <span>Consultar Calificaciones</span>
            </a>

            <a href="generar_reportes.php" class="control-button">
                <i class="bi bi-bar-chart"></i>
                <span>Generar Reportes</span>
            </a>
            <a href="mensajes.php" class="control-button">
                <i class="bi bi-envelope"></i>
                <span>Enviar Mensajes</span>
            </a>
            <a href="periodos_escolares.php" class="control-button">
                <i class="bi bi-calendar"></i>
                <span>Periodos Escolares</span>
            </a>
        </div>
    </main>

    <!-- Pie de página -->
    <footer class="footer">
        <span id="fecha-hora"></span>
    </footer>

    <!-- Script para la fecha y hora -->
    <script src="../JS/dashboard_director.js"></script>
</body>
</html>