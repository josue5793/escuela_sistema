<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Verificar si se ha recibido el ID del usuario a editar
if (!isset($_GET['id'])) {
    echo "ID de usuario no especificado.";
    exit;
}

$usuario_id = $_GET['id'];

// Obtener datos del usuario de la base de datos
$sql = "SELECT * FROM usuarios WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Usuario no encontrado.";
    exit;
}

$usuario = $result->fetch_assoc();

// Procesar el formulario cuando se envíen los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];
    $contrasena_nueva = $_POST['contrasena'];

    // Si se proporciona una nueva contraseña, actualizarla
    if (!empty($contrasena_nueva)) {
        $contrasena_hash = password_hash($contrasena_nueva, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre = ?, correo = ?, contrasena = ?, rol = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $correo, $contrasena_hash, $rol, $usuario_id);
    } else {
        $sql = "UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $correo, $rol, $usuario_id);
    }

    if ($stmt->execute()) {
        header("Location: gestionar_usuarios.php");
        exit;
    } else {
        echo "Error al actualizar el usuario: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="css/editar_usuario.css">
</head>
<body>
    <main class="main-container">
        <h1>Editar Usuario</h1>
        <form action="" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
            
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
            
            <label for="contrasena">Nueva Contraseña (opcional):</label>
            <input type="password" name="contrasena" id="contrasena">
            
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="administrador" <?php echo $usuario['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                <option value="profesor" <?php echo $usuario['rol'] === 'profesor' ? 'selected' : ''; ?>>Profesor</option>
                <option value="director" <?php echo $usuario['rol'] === 'director' ? 'selected' : ''; ?>>Director</option>
            </select>
            
            <button type="submit">Guardar Cambios</button>
        </form>
        <a href="gestionar_usuarios.php" class="btn">Volver</a>
    </main>
</body>
</html>
