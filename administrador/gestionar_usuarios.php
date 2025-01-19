<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once '../db.php';

try {
    // Obtener lista de usuarios con el nombre del rol desde la tabla roles
    $sql = "
        SELECT u.usuario_id, u.nombre, u.correo, r.nombre AS rol
        FROM usuarios u
        JOIN roles r ON u.rol_id = r.rol_id
    ";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener nivel predeterminado
    $query_nivel_predeterminado = "SELECT nivel_id FROM niveles LIMIT 1";
    $stmt_nivel = $pdo->query($query_nivel_predeterminado);
    $row_nivel = $stmt_nivel->fetch(PDO::FETCH_ASSOC);

    if ($row_nivel) {
        $nivel_id = $row_nivel['nivel_id']; // Asignar el nivel predeterminado
    } else {
        die("Error: No hay niveles disponibles. Agrega al menos un nivel en la tabla 'niveles'.");
    }

    // Procesar usuarios y asignar los que son profesores a la tabla `profesores`
    foreach ($usuarios as $row) {
        if ($row['rol'] === 'profesor') {
            $usuario_id = $row['usuario_id'];

            // Comprobar si el profesor ya existe en la tabla `profesores`
            $check_query = "SELECT 1 FROM profesores WHERE usuario_id = ?";
            $stmt_check = $pdo->prepare($check_query);
            $stmt_check->execute([$usuario_id]);
            $result_check = $stmt_check->fetch();

            // Si no existe, agregarlo a la tabla `profesores` con un nivel predeterminado
            if (!$result_check) {
                $insert_query = "INSERT INTO profesores (usuario_id, especialidad, nivel_id) VALUES (?, 'Sin asignar', ?)";
                $stmt_insert = $pdo->prepare($insert_query);
                $stmt_insert->execute([$usuario_id, $nivel_id]);
            }
        }
    }
} catch (PDOException $e) {
    die("Error al gestionar usuarios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="CSS/gestionar_usuarios2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Usuarios</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <h2>Bienvenid@ a la Gestión de Usuarios</h2>
            <p>Desde aquí puedes gestionar los usuarios registrados en el sistema. Usa las opciones disponibles para agregar, editar o eliminar usuarios.</p>
        </section>

        <!-- Botones de control -->
        <div class="button-container">
            <a href="agregar_usuario.php" class="control-button">
                <i class="bi bi-person-plus"></i>
                <span>Agregar Usuario</span>
            </a>
            <!-- Botón para ir al Panel de Administrador -->
            <a href="administrador_dashboard.php" class="control-button">
                <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
                <span>Panel Administrador</span>
            </a>
        </div>

        <!-- Tabla de usuarios -->
        <section class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($usuarios)) {
                        foreach ($usuarios as $row) {
                            echo "<tr>
                                    <td>{$row['usuario_id']}</td>
                                    <td>{$row['nombre']}</td>
                                    <td>{$row['correo']}</td>
                                    <td>{$row['rol']}</td>
                                    <td>
                                        <a href='editar_usuario.php?id={$row['usuario_id']}' class='action-button edit'><i class='bi bi-pencil'></i> Editar</a>
                                        <a href='eliminar_usuario.php?id={$row['usuario_id']}' class='action-button delete' onclick='return confirm(\"¿Estás seguro de eliminar este usuario?\");'><i class='bi bi-trash'></i> Eliminar</a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
