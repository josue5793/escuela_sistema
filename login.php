<?php
// Incluir conexión a la base de datos
require_once 'db.php';

// Iniciar sesión
session_start();

// Manejo de mensajes de error
$error = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'acceso_denegado') {
        $error = 'Acceso denegado. Por favor, inicia sesión.';
    } elseif ($_GET['error'] === 'credenciales_invalidas') {
        $error = 'Credenciales inválidas. Inténtalo de nuevo.';
    } elseif ($_GET['error'] === 'usuario_no_encontrado') {
        $error = 'El usuario no existe en el sistema.';
    }
}

// Verificar si el método de la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Preparar y ejecutar consulta
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar contraseña
        if (password_verify($contrasena, $usuario['contrasena'])) {
            // Guardar datos en sesión
            $_SESSION['usuario_id'] = $usuario['usuario_id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirigir según el rol
            if ($usuario['rol'] === 'administrador') {
                header("Location: administrador.php");
            } elseif ($usuario['rol'] === 'profesor') {
                header("Location: profesor.php");
            } elseif ($usuario['rol'] === 'director') {
                header("Location: director.php");
            } else {
                header("Location: login.php?error=acceso_denegado");
            }
            exit;
        } else {
            header("Location: login.php?error=credenciales_invalidas");
            exit;
        }
    } else {
        header("Location: login.php?error=usuario_no_encontrado");
        exit;
    }

    $stmt->close();
} else {
    // Si el método no es POST, mostrar el formulario
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
