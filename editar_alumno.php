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

// Obtener la matrícula del alumno a editar
$matricula = isset($_GET['matricula']) ? trim($_GET['matricula']) : '';
$alumno = null;

// Obtener datos del alumno
if (!empty($matricula)) {
    $sql = "SELECT * FROM alumnos WHERE matricula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();
    $alumno = $result->fetch_assoc();
    $stmt->close();
}

// Obtener lista de grupos
$sql_grupos = "SELECT * FROM grupos";
$result_grupos = $conn->query($sql_grupos);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alumno</title>
    <link rel="stylesheet" href="css/gestionar_alumnos.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="director.php">Inicio</a></li>
                <li><a href="gestionar_profesores.php">Profesores</a></li>
                <li><a href="gestionar_alumnos.php">Alumnos</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Editar Alumno</h1>

        <?php if ($alumno): ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="editar">
                <label for="apellido_paterno">Apellido Paterno:</label>
                <input type="text" name="apellido_paterno" id="apellido_paterno" value="<?php echo htmlspecialchars($alumno['apellido_paterno']); ?>" required>
                <label for="apellido_materno">Apellido Materno:</label>
                <input type="text" name="apellido_materno" id="apellido_materno" value="<?php echo htmlspecialchars($alumno['apellido_materno']); ?>" required>
                <label for="nombres">Nombres:</label>
                <input type="text" name="nombres" id="nombres" value="<?php echo htmlspecialchars($alumno['nombres']); ?>" required>
                <label for="direccion">Dirección:</label>
                <textarea name="direccion" id="direccion"><?php echo htmlspecialchars($alumno['direccion']); ?></textarea>
                <label for="grado_escolar">Grado Escolar:</label>
                <input type="text" name="grado_escolar" id="grado_escolar" value="<?php echo htmlspecialchars($alumno['grado_escolar']); ?>" required>
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($alumno['telefono']); ?>">
                <label for="grupo_id">Grupo:</label>
                <select name="grupo_id" id="grupo_id" required>
                    <?php while ($grupo = $result_grupos->fetch_assoc()): ?>
                        <option value="<?php echo $grupo['id_grupo']; ?>" <?php if ($grupo['id_grupo'] == $alumno['grupo_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($grupo['nombre_grupo']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <label for="fotografia">Fotografía:</label>
                <input type="file" name="fotografia" id="fotografia">
                <button type="submit">Actualizar</button>
            </form>
        <?php else: ?>
            <p>No se encontró al alumno con la matrícula proporcionada.</p>
        <?php endif; ?>
    </main>
</body>
</html>
