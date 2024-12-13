<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Inicializar variables para mensajes y datos
$mensaje = "";
$nombre_grupo = "";
$grado = "";
$turno = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre_grupo = trim($_POST['nombre_grupo']);
    $grado = trim($_POST['grado']);
    $turno = trim($_POST['turno']);

    // Validar datos
    if (empty($nombre_grupo) || empty($grado) || empty($turno)) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        // Insertar grupo en la base de datos
        $sql = "INSERT INTO grupos (nombre_grupo, grado, turno) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $nombre_grupo, $grado, $turno);
            if ($stmt->execute()) {
                $mensaje = "Grupo agregado exitosamente.";
                $nombre_grupo = $grado = $turno = ""; // Limpiar los campos
            } else {
                $mensaje = "Error al agregar el grupo: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensaje = "Error en la preparación de la consulta: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Grupo</title>
    <link rel="stylesheet" href="css/agregar_grupo.css">
</head>
<body>
    <!-- Menú Superior -->
    <header>
        <nav>
            <ul>
                <li><a href="administrador.php">Inicio</a></li>
                <li><a href="gestionar_grupos.php">Gestionar Grupos</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <h1>Agregar Nuevo Grupo</h1>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nombre_grupo">Nombre del Grupo:</label>
                <input type="text" id="nombre_grupo" name="nombre_grupo" value="<?php echo htmlspecialchars($nombre_grupo); ?>" required>
            </div>

            <div class="form-group">
                <label for="grado">Grado:</label>
                <input type="text" id="grado" name="grado" value="<?php echo htmlspecialchars($grado); ?>" required>
            </div>

            <div class="form-group">
                <label for="turno">Turno:</label>
                <input type="text" id="turno" name="turno" value="<?php echo htmlspecialchars($turno); ?>" required>
            </div>

            <button type="submit" class="btn">Agregar Grupo</button>
        </form>
    </main>
</body>
</html>
