<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
?>
<<<<<<< HEAD
=======

>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="CSS/administrador_dashboard.css">
<<<<<<< HEAD
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
=======
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
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
            <a href="gestionar_usuarios.php" class="control-button">
<<<<<<< HEAD
                <i class="bi bi-person-fill"></i>
                <span>Usuarios</span>
            </a>
            <a href="gestionar_grupos.php" class="control-button">
                <i class="bi bi-people-fill"></i>
=======
                <i class="bi bi-person"></i>
                <span>Usuarios</span>
            </a>
            <a href="gestionar_grupos.php" class="control-button">
                <i class="bi bi-people"></i>
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
                <span>Gestión de Grupos</span>
            </a>
            <a href="gestionar_alumnos.php" class="control-button">
                <i class="bi bi-mortarboard"></i>
                <span>Alumnos</span>
            </a>
            <a href="gestionar_profesores.php" class="control-button">
<<<<<<< HEAD
                <i class="bi bi-person-check-fill"></i>
                <span>Profesores</span>
            </a>
            <a href="materias.php" class="control-button">
                <i class="bi bi-journal-bookmark-fill"></i>
                <span>Registro de Materias</span>
            </a>
            <a href="reportes.php" class="control-button">
                <i class="bi bi-graph-up"></i>
=======
                <i class="bi bi-person-badge"></i>
                <span>Profesores</span>
            </a>
            <a href="materias.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Registro de Materias</span>
            </a>
            <a href="reportes.php" class="control-button">
                <i class="bi bi-bar-chart"></i>
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
                <span>Generación de Reportes</span>
            </a>
            <a href="gestionar_perfiles.php" class="control-button">
                <i class="bi bi-person-lines-fill"></i>
                <span>Gestionar Perfiles</span>
            </a>
<<<<<<< HEAD
            <a href="colegiaturas.php" class="control-button">
                <i class="bi bi-cash"></i>
                <span>Colegiaturas</span>
            </a>
            <a href="asignar_directores.php" class="control-button">
                <i class="bi bi-person-gear"></i>
=======
            <a href="administrar_periodos.php" class="control-button">
                <i class="bi bi-calendar"></i>
                <span>Periodos</span>
            </a>
            <a href="administrar_sitio.php" class="control-button">
                <i class="bi bi-gear"></i>
                <span>Administrar Sitio</span>
            </a>
            <!-- Nuevo botón para Asignar Directores -->
            <a href="asignar_directores.php" class="control-button">
                <i class="bi bi-person-workspace"></i>
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
                <span>Asignar Directores</span>
            </a>
        </div>
    </main>
</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
