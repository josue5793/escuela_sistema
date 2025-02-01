<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once '../db.php';

// Verificar si se ha recibido el ID del usuario a editar
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo "ID de usuario no especificado o inválido.";
    exit;
}

$usuario_id = intval($_GET['id']);

try {
    // Obtener datos del usuario de la base de datos
    $sql = "SELECT u.*, r.nombre AS rol_nombre 
            FROM usuarios u 
            LEFT JOIN roles r ON u.rol_id = r.rol_id 
            WHERE u.usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Usuario no encontrado.";
        exit;
    }

    // Obtener la lista de roles disponibles
    $roles_query = "SELECT rol_id, nombre FROM roles";
    $roles_stmt = $pdo->query($roles_query);
    $roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar el formulario cuando se envíen los datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitizar y validar entradas
        $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $correo = filter_var($_POST['correo'] ?? '', FILTER_VALIDATE_EMAIL);
        $rol_id = filter_var($_POST['rol_id'] ?? '', FILTER_VALIDATE_INT);
        $contrasena_nueva = $_POST['contrasena'] ?? '';

        if (!$nombre || !$correo || !$rol_id) {
            echo "Por favor, complete todos los campos obligatorios.";
        } else {
            // Iniciar la transacción
            $pdo->beginTransaction();

            if (!empty($contrasena_nueva)) {
                // Si se proporciona una nueva contraseña, actualizarla
                $contrasena_hash = password_hash($contrasena_nueva, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, contrasena = :contrasena, rol_id = :rol_id WHERE usuario_id = :usuario_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':contrasena', $contrasena_hash, PDO::PARAM_STR);
            } else {
                $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo, rol_id = :rol_id WHERE usuario_id = :usuario_id";
                $stmt = $pdo->prepare($sql);
            }

            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Confirmar la transacción
                $pdo->commit();
                header("Location: gestionar_usuarios.php?success=1");
                exit;
            } else {
                // Revertir cambios si ocurre un error
                $pdo->rollBack();
                throw new Exception("Error al actualizar el usuario.");
            }
        }
    }
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="CSS/editar_usuario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Usuarios</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>
    <main class="main-container">
    <section class="welcome-section">
            <h2>Editar usuario</h2>
            <p>Corrija las opciones del usuario. Modifique lo necesario</p>
        </section>

        <!-- Botones de control -->
        <div class="button-container">
        <a href="administrador_dashboard.php" class="control-button">
                <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
                <span>Panel Administrador</span>
            </a>
        
        <a href="agregar_usuario.php" class="control-button">
                <i class="bi bi-person-plus"></i>
                <span>Agregar Usuario</span>
            </a>
            <!-- Botón para ir al Panel de Administrador -->
           
            <a href="gestionar_usuarios.php" class="control-button">
                <i class="bi bi-people"></i> <!-- Ícono de casa -->
                <span>Gestionar Usuario</span>
            </a>
        </div>

        <h1>Editar Usuario</h1>
        <form action="" method="POST" class="form-container">
    <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="correo">Correo Electrónico:</label>
        <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="contrasena">Nueva Contraseña (opcional):</label>
        <input type="password" name="contrasena" id="contrasena">
    </div>
    
    <div class="form-group">
        <label for="rol_id">Rol:</label>
        <select name="rol_id" id="rol_id" required>
            <?php foreach ($roles as $rol): ?>
                <option value="<?php echo $rol['rol_id']; ?>" 
                    <?php echo ($rol['rol_id'] == $usuario['rol_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($rol['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <button type="submit" class="form-submit">Guardar Cambios</button>
</form>

       
    </main>
</body>
</html>
