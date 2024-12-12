<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Obtener lista de usuarios de la base de datos
$sql = "SELECT usuario_id, nombre, correo, rol FROM usuarios";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="css/gestion_usuarios.css">
</head>
<body>
    <!-- Menú Superior -->
    <header>
        <nav>
            <ul>
                <li><a href="administrador.php">Inicio</a></li>
                <li><a href="gestionar_usuarios.php">Usuarios</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <h1>Gestión de Usuarios</h1>
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
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['usuario_id']}</td>
                                <td>{$row['nombre']}</td>
                                <td>{$row['correo']}</td>
                                <td>{$row['rol']}</td>
                                <td>
                                    <a href='editar_usuario.php?id={$row['usuario_id']}'>Editar</a> |
                                    <a href='eliminar_usuario.php?id={$row['usuario_id']}' onclick='return confirm(\"¿Estás seguro de eliminar este usuario?\");'>Eliminar</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="agregar_usuario.php" class="btn">Agregar Nuevo Usuario</a>
    </main>
</body>
</html>
