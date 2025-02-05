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

// Obtener el nivel_id y el nombre del nivel del director
try {
    $stmt = $pdo->prepare("SELECT d.nivel_id, n.nivel_nombre 
                           FROM directores d 
                           JOIN niveles n ON d.nivel_id = n.nivel_id 
                           WHERE d.usuario_id = :usuario_id");
    $stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
    $director = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$director) {
        throw new Exception("El director no está asignado a un nivel.");
    }

    $nivel_id_director = $director['nivel_id'];
    $nivel_nombre_director = $director['nivel_nombre'];
} catch (PDOException $e) {
    die("Error al obtener el nivel del director: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}

// Procesar el formulario para registrar un nuevo profesor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Encriptar la contraseña
    $especialidad = $_POST['especialidad'];
    $telefono = $_POST['telefono'];

    try {
        // Registrar el nuevo usuario en la tabla 'usuarios'
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol_id) VALUES (:nombre, :correo, :contrasena, :rol_id)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':contrasena' => $contrasena,
            ':rol_id' => 2 // Asignar rol de profesor (ajusta según tu base de datos)
        ]);

        // Obtener el ID del usuario recién creado
        $usuario_id = $pdo->lastInsertId();

        // Registrar el nuevo profesor en la tabla 'profesores'
        $stmt = $pdo->prepare("INSERT INTO profesores (usuario_id, especialidad, telefono, nivel_id) VALUES (:usuario_id, :especialidad, :telefono, :nivel_id)");
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':especialidad' => $especialidad,
            ':telefono' => $telefono,
            ':nivel_id' => $nivel_id_director // Usar el nivel_id del director
        ]);

        $mensaje = "Profesor registrado exitosamente.";
    } catch (PDOException $e) {
        $error = "Error al registrar el profesor: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Director</title>
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
        <h1>Gestión de profesores</h1>

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
            <a href="asignar_profesor.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Asignar profesor a materias</span>
            </a>
            
        </div>

        <!-- Formulario para registrar un nuevo profesor -->
        <form method="POST" action="gestion_usuarios.php">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <div class="form-group">
                <label for="especialidad">Especialidad:</label>
                <input type="text" id="especialidad" name="especialidad" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
            </div>
            <div class="form-group">
                <label for="nivel">Nivel:</label>
                <input type="text" id="nivel" name="nivel" value="<?php echo htmlspecialchars($nivel_nombre_director); ?>" readonly>
                <small>El profesor se registrará en el nivel asignado al director.</small>
            </div>
            <button type="submit" class="btn">Registrar Profesor</button>
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