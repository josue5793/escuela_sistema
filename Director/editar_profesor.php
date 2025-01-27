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

// Obtener el ID del profesor a editar desde la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de profesor no válido.");
}
$profesor_id = $_GET['id'];

// Obtener los datos actuales del profesor
try {
    $stmt = $pdo->prepare("SELECT p.profesor_id, u.nombre, u.correo, p.especialidad, p.telefono 
                           FROM profesores p 
                           JOIN usuarios u ON p.usuario_id = u.usuario_id 
                           WHERE p.profesor_id = :profesor_id");
    $stmt->execute([':profesor_id' => $profesor_id]);
    $profesor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profesor) {
        die("Profesor no encontrado.");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos del profesor: " . $e->getMessage());
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $especialidad = $_POST['especialidad'];
    $telefono = $_POST['telefono'];

    try {
        // Actualizar la información del profesor en la tabla 'usuarios'
        $stmt = $pdo->prepare("UPDATE usuarios 
                               SET nombre = :nombre, correo = :correo 
                               WHERE usuario_id = (SELECT usuario_id FROM profesores WHERE profesor_id = :profesor_id)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':profesor_id' => $profesor_id
        ]);

        // Actualizar la información del profesor en la tabla 'profesores'
        $stmt = $pdo->prepare("UPDATE profesores 
                               SET especialidad = :especialidad, telefono = :telefono 
                               WHERE profesor_id = :profesor_id");
        $stmt->execute([
            ':especialidad' => $especialidad,
            ':telefono' => $telefono,
            ':profesor_id' => $profesor_id
        ]);

        $mensaje = "Profesor actualizado exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al actualizar el profesor: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Profesor - Director</title>
    <link rel="stylesheet" href="CSS/gestion_usuarios.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra superior -->
    <header class="navbar">
        <div class="navbar-left">
            <span>Gestión de Usuarios</span>
        </div>
        <div class="navbar-right">
            <span>Bienvenido, <?php echo htmlspecialchars($nombre_director); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </header>

    <!-- Cuerpo del documento -->
    <main class="main-container">
        <h1>Editar Profesor</h1>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulario para editar el profesor -->
        <form method="POST" action="editar_profesor.php?id=<?php echo $profesor_id; ?>">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($profesor['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($profesor['correo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="especialidad">Especialidad:</label>
                <input type="text" id="especialidad" name="especialidad" value="<?php echo htmlspecialchars($profesor['especialidad']); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($profesor['telefono']); ?>" required>
            </div>
            <button type="submit" class="btn">Guardar Cambios</button>
        </form>
    </main>

    <!-- Pie de página -->
    <footer class="footer">
        <span id="fecha-hora"></span>
    </footer>

    <!-- Script para la fecha y hora -->
    <script src="../JS/dashboard_director.js"></script>
</body>
</html>