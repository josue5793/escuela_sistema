<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['administrador', 'director'])) {
    header("Location: login.php?error=acceso_denegado");
    exit;
}

// Incluir conexión a la base de datos
require_once 'db.php';

// Mensajes de éxito o error
$mensaje = "";

// Obtener lista de grupos
$sql_grupos = "SELECT * FROM grupos";
$result_grupos = $conn->query($sql_grupos);
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Alumnos</title>
    <link rel="stylesheet" href ="CSS/gestionar_alumnos.css">
</head>
<body>
    <!-- Barra de navegación -->
    <header>
        <nav class="navbar">
            <a href="director.php">Inicio</a>
            <a href="gestionar_profesores.php">Profesores</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <!-- Contenido principal -->
    <main>
        <section class="hero">
            <h1>Gestionar Alumnos</h1>
            <?php if (!empty($mensaje)): ?>
                <p class="message"><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>
        </section>
        
        <!-- Opciones principales -->
        <section class="options">
            <button onclick="mostrarSeccion('alta')">Alta de Nuevo Alumno</button>
            <button onclick="mostrarSeccion('consulta')">Consulta de Alumnos Inscritos</button>
            <button onclick="mostrarSeccion('buscar')">Buscar Alumno</button>
        </section>

        <!-- Sección: Alta de nuevo alumno -->
        <section id="alta" class="seccion" style="display: none;">
            <h2>Alta de Nuevo Alumno</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="agregar">
                <label for="apellido_paterno">Apellido Paterno:</label>
                <input type="text" name="apellido_paterno" id="apellido_paterno" required>
                <label for="apellido_materno">Apellido Materno:</label>
                <input type="text" name="apellido_materno" id="apellido_materno" required>
                <label for="nombres">Nombres:</label>
                <input type="text" name="nombres" id="nombres" required>
                <label for="direccion">Dirección:</label>
                <textarea name="direccion" id="direccion"></textarea>
                <label for="grado_escolar">Grado Escolar:</label>
                <input type="text" name="grado_escolar" id="grado_escolar" required>
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono">
                <label for="grupo_id">Grupo:</label>
                <select name="grupo_id" id="grupo_id" required>
                    <?php while ($grupo = $result_grupos->fetch_assoc()): ?>
                        <option value="<?php echo $grupo['id_grupo']; ?>"><?php echo $grupo['nombre_grupo']; ?></option>
                    <?php endwhile; ?>
                </select>
                <label for="fotografia">Fotografía:</label>
                <input type="file" name="fotografia" id="fotografia">
                <button type="submit">Agregar</button>
            </form>
        </section>

        <!-- Sección: Consulta de alumnos inscritos -->
        <section id="consulta" class="seccion" style="display: none;">
            <h2>Consulta de Alumnos Inscritos</h2>
            <p>Aquí aparecerá una lista de alumnos inscritos con detalles. (En desarrollo)</p>
        </section>

        <!-- Sección: Buscar alumno -->
        <section id="buscar" class="seccion" style="display: none;">
            <h2>Buscar Alumno</h2>
            <form method="GET" action="buscar_alumnos.php">
                <input type="text" name="busqueda" placeholder="Matrícula o apellidos">
                <button type="submit">Buscar</button>
            </form>
            <div id="resultados">
                <!-- Aquí se mostrarán los resultados de la búsqueda -->
            </div>
        </section>
    </main>

    <script>
        function mostrarSeccion(seccion) {
            document.querySelectorAll(".seccion").forEach(el => el.style.display = "none");
            document.getElementById(seccion).style.display = "block";
        }
    </script>
</body>
</html>
