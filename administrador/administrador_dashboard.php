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
    <link rel="stylesheet" href="CSS/administrador_dashboard3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Panel de Administrador</h1>
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
            <h2>Bienvenid@ al Panel de Administrador</h2>
            <p>Desde aquí puedes gestionar los usuarios, grupos, alumnos, profesores, materias y generar reportes. Usa los botones a continuación para navegar a las diferentes secciones del sistema. Si necesitas agregar más opciones en el futuro, estas aparecerán en este panel.</p>
        </section>

        <!-- Botones de control -->
        <div class="button-container">
            <!-- Gestión de Usuarios y Perfiles -->
            <div class="button-group">
                <a href="gestionar_usuarios.php" class="control-button">
                    <i class="bi bi-person-fill"></i>
                    <span>Usuarios</span>
                </a>
                <a href="gestionar_perfiles.php" class="control-button">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Gestionar Perfiles</span>
                </a>
            </div>

            <!-- Gestión de Alumnos y Grupos -->
            <div class="button-group">
                <a href="gestionar_alumnos.php" class="control-button">
                    <i class="bi bi-mortarboard"></i>
                    <span>Alumnos</span>
                </a>
                <a href="gestionar_grupos.php" class="control-button">
                    <i class="bi bi-people-fill"></i>
                    <span>Gestión de Grupos</span>
                </a>
            </div>

            <!-- Gestión de Profesores y Directores -->
            <div class="button-group">
                <a href="gestionar_profesores.php" class="control-button">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Profesores</span>
                </a>
                <a href="asignar_directores.php" class="control-button">
                    <i class="bi bi-person-gear"></i>
                    <span>Asignar Directores</span>
                </a>
            </div>

            <!-- Gestión de Materias y Colegiaturas -->
            <div class="button-group">
                <a href="materias.php" class="control-button">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Registro de Materias</span>
                </a>
                <a href="colegiaturas.php" class="control-button">
                    <i class="bi bi-cash"></i>
                    <span>Colegiaturas</span>
                </a>
            </div>

            <!-- Gestión de Niveles -->
            <div class="button-group">
                <a href="gestion_niveles.php" class="control-button">
                    <i class="bi bi-layers-fill"></i>
                    <span>Gestión de Niveles</span>
                </a>
            </div>

            <!-- Reportes -->
            <div class="button-group">
                <a href="reportes.php" class="control-button">
                    <i class="bi bi-graph-up"></i>
                    <span>Generación de Reportes</span>
                </a>
            </div>
        </div>
    </main>
</body>
</html>