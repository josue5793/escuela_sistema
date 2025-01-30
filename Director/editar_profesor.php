<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

// Incluir la conexión a la base de datos
require_once '../db.php';

// Obtener el profesor_id desde la URL
if (!isset($_GET['profesor_id'])) {
    header("Location: consultar_profesores.php");
    exit;
}

$profesor_id = $_GET['profesor_id'];

// Obtener los datos del profesor
try {
    $stmt = $pdo->prepare("SELECT p.profesor_id, u.nombre, u.correo, p.especialidad, p.telefono 
                           FROM profesores p 
                           JOIN usuarios u ON p.usuario_id = u.usuario_id 
                           WHERE p.profesor_id = :profesor_id");
    $stmt->execute([':profesor_id' => $profesor_id]);
    $profesor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profesor) {
        throw new Exception("Profesor no encontrado.");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos del profesor: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $especialidad = $_POST['especialidad'];
    $telefono = $_POST['telefono'];

    try {
        // Actualizar los datos del profesor
        $stmt = $pdo->prepare("UPDATE profesores p 
                               JOIN usuarios u ON p.usuario_id = u.usuario_id 
                               SET u.nombre = :nombre, u.correo = :correo, 
                                   p.especialidad = :especialidad, p.telefono = :telefono 
                               WHERE p.profesor_id = :profesor_id");
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':especialidad' => $especialidad,
            ':telefono' => $telefono,
            ':profesor_id' => $profesor_id
        ]);

        // Redirigir con un mensaje de éxito
        header("Location: consultar_profesores.php?mensaje=Profesor actualizado correctamente");
        exit;
    } catch (PDOException $e) {
        $error = "Error al actualizar los datos del profesor: " . $e->getMessage();
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
            <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
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

        <!-- Control de navegación -->
        <div class="button-container">
            <a href="dashboard_director.php" class="control-button">
                <i class="bi bi-house-door"></i>
                <span>Panel principal</span>
            </a>   
            <a href="gestion_usuarios.php" class="control-button">
                <i class="bi bi-person-plus"></i>
                <span>Agregar Profesor</span>
            </a>
            <a href="consultar_profesores.php" class="control-button">
                <i class="bi bi-people"></i>
                <span>Consultar Profesores del Nivel</span>
            </a>
            <a href="editar_profesor.php" class="control-button">
                <i class="bi bi-pencil-square"></i>
                <span>Editar Profesor</span>
            </a>
        </div>

        <!-- Formulario de edición -->
        <form method="POST" class="form-editar-profesor">
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
            <button type="submit" class="btn-guardar">Guardar Cambios</button>
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