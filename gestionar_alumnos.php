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

// Procesar acciones: Agregar alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $nombres = $_POST['nombres'];
    $direccion = $_POST['direccion'];
    $grado_escolar = $_POST['grado_escolar'];
    $telefono = $_POST['telefono'];
    $grupo_id = $_POST['grupo_id'];
    $fotografia = isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] == 0 ? file_get_contents($_FILES['fotografia']['tmp_name']) : null;

    // Generar matrícula
    $sql_matricula = "SELECT MAX(alumno_id) AS max_id FROM alumnos";
    $result = $conn->query($sql_matricula);
    $row = $result->fetch_assoc();
    $ultimo_id = $row['max_id'] ?? 0;
    $matricula = "A" . str_pad($ultimo_id + 1, 6, "0", STR_PAD_LEFT);

    // Insertar alumno
    $sql = "INSERT INTO alumnos (matricula, apellido_paterno, apellido_materno, nombres, direccion, grado_escolar, telefono, fotografia, grupo_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $matricula, $apellido_paterno, $apellido_materno, $nombres, $direccion, $grado_escolar, $telefono, $fotografia, $grupo_id);
    $mensaje = $stmt->execute() ? "Alumno agregado correctamente." : "Error al agregar al alumno: " . $conn->error;
    $stmt->close();
}

// Obtener lista de alumnos y grupos
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
    <link rel="stylesheet" href="css/gestionar_alumnos.css">
    <script>
        function mostrarSeccion(seccion) {
            document.querySelectorAll(".seccion").forEach(el => el.style.display = "none");
            document.getElementById(seccion).style.display = "block";
        }
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="director.php">Inicio</a></li>
                <li><a href="gestionar_profesores.php">Profesores</a></li>
                <li><a href="#" onclick="mostrarSeccion('alta')">Alta de Nuevo Alumno</a></li>
                <li><a href="#" onclick="mostrarSeccion('buscar')">Buscar Alumno</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Gestionar Alumnos</h1>

        <?php if (!empty($mensaje)): ?>
            <p class="message"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

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
</body>
</html>
