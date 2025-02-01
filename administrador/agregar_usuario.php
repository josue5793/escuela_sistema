<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
require_once '../db.php';

// Procesar el formulario cuando se envían los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $rol = $_POST['rol'] ?? '';

    // Validar entradas
    $errores = [];
    if (empty($nombre)) {
        $errores[] = "El campo 'Nombre' es obligatorio.";
    }
    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El campo 'Correo electrónico' es inválido o está vacío.";
    }
    if (empty($contrasena)) {
        $errores[] = "El campo 'Contraseña' es obligatorio.";
    }
    if (empty($rol)) {
        $errores[] = "Debe seleccionar un rol.";
    }

    if (empty($errores)) {
        try {
            // Preparar la consulta para insertar el nuevo usuario
            $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol_id) 
                    VALUES (:nombre, :correo, :contrasena, 
                            (SELECT rol_id FROM roles WHERE nombre = :rol))";

            $stmt = $pdo->prepare($sql);

            // Encriptar la contraseña
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Asignar valores a los parámetros
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':contrasena', $contrasena_hash, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                header("Location: gestionar_usuarios.php?success=1");
                exit;
            } else {
                $errores[] = "Error al agregar el usuario. Intente nuevamente.";
            }
        } catch (PDOException $e) {
            $errores[] = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Usuario</title>
    <link rel="stylesheet" href="CSS/agregar_usuario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Agregar Nuevo Usuario</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
<<<<<<< HEAD
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
=======
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
            </div>
        </div>
    </header>

    <!-- Botón de Panel del Administrador fuera de la barra de navegación -->
    <div class="admin-panel-button-container">
        <a href="administrador_dashboard.php" class="admin-panel-button">
            <i class="bi bi-house-door"></i> Panel del Administrador
        </a>
    </div>

    <!-- Contenedor Principal -->
    <main class="main-container">
        <div class="form-container">
            <h2>Formulario para Agregar Nuevo Usuario</h2>
            <?php if (!empty($errores)): ?>
                <div class="error-messages">
                    <?php foreach ($errores as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" name="contrasena" id="contrasena" required>
                </div>

                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select name="rol" id="rol" required>
                        <option value="" disabled selected>Seleccione un rol</option>
                        <option value="administrador" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="profesor" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'profesor') ? 'selected' : ''; ?>>Profesor</option>
                        <option value="director" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'director') ? 'selected' : ''; ?>>Director</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Agregar Usuario</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
