<?php
session_start();
require_once 'db.php'; // Asegúrate de incluir el archivo de conexión

// Verificar si el usuario está logueado y tiene el rol de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: login.php");
    exit;
}

// Consulta de datos del profesor
$profesor_id = $_SESSION['usuario_id'];
$query = "SELECT u.nombre, u.correo, p.especialidad, p.telefono, p.foto 
          FROM usuarios u
          JOIN profesores p ON u.usuario_id = p.usuario_id 
          WHERE u.usuario_id = ?";
$stmt = $pdo->prepare($query);
$stmt->bindParam(1, $profesor_id, PDO::PARAM_INT);
$stmt->execute();
$profesor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profesor) {
    die("Profesor no encontrado.");
}

// Procesar el formulario de actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $especialidad = $_POST['especialidad'];
    $telefono = $_POST['telefono'];
    $foto = $profesor['foto'];

    // Manejar la subida de la foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $uploadDir = 'uploads/profesores/';
        $destFilePath = $uploadDir . $fileName;

        // Mover la imagen al directorio de subidas
        if (move_uploaded_file($fileTmpPath, $destFilePath)) {
            $foto = $destFilePath;
        }
    }

    // Actualizar los datos del profesor en la base de datos
    $updateStmt = $pdo->prepare("UPDATE usuarios u
                                 JOIN profesores p ON u.usuario_id = p.usuario_id
                                 SET u.nombre = :nombre, u.correo = :correo, p.especialidad = :especialidad, p.telefono = :telefono, p.foto = :foto
                                 WHERE u.usuario_id = :usuario_id");
    $updateStmt->execute([
        'nombre' => $nombre,
        'correo' => $correo,
        'especialidad' => $especialidad,
        'telefono' => $telefono,
        'foto' => $foto,
        'usuario_id' => $profesor_id
    ]);

    // Redirigir al mismo perfil con un mensaje de éxito
    header("Location: ver_perfil.php?actualizado=true");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Perfil - Profesor</title>
    <link rel="stylesheet" href="CSS/ver_perfil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Mi Perfil</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido del Perfil -->
    <main class="main-container">
        <?php if (isset($_GET['actualizado']) && $_GET['actualizado'] == 'true'): ?>
            <div class="alert alert-success">Perfil actualizado correctamente.</div>
        <?php endif; ?>

        <section class="perfil-section">
            <div class="button-container">
                <a href="dashboard_profesor.php" class="control-button">
                    <i class="bi bi-house-door"></i>
                    <span>Inicio</span>
                </a>
                <a href="gestionar_niveles.php" class="control-button">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Niveles</span>
                </a>
            </div>
            
            <h2>Información Personal</h2>
            <form method="POST" enctype="multipart/form-data" class="form-container">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($profesor['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($profesor['correo']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="especialidad">Especialidad:</label>
                    <input type="text" name="especialidad" id="especialidad" value="<?php echo htmlspecialchars($profesor['especialidad']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" id="telefono" value="<?php echo htmlspecialchars($profesor['telefono']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="foto">Foto de perfil:</label>
                    <input type="file" name="foto" id="foto">
                    <?php if ($profesor['foto']): ?>
                        <div class="current-photo">
                            <img src="<?php echo htmlspecialchars($profesor['foto']); ?>" alt="Foto actual" width="100">
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit-button">Guardar Cambios</button>
            </form>
        </section>
    </main>
</body>
</html>
