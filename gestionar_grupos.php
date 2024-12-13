<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Obtener lista de grupos de la base de datos
$sql = "SELECT id_grupo, nombre_grupo, grado, turno FROM grupos";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos</title>
    <link rel="stylesheet" href="CSS/gestionar_grupos.css">
</head>
<body>
    <!-- Menú Superior -->
    <header>
        <nav>
            <ul>
                <li><a href="administrador.php">Inicio</a></li>
                <li><a href="gestionar_grupos.php">Grupos</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <h1>Gestión de Grupos</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Grupo</th>
                    <th>Grado</th>
                    <th>Turno</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id_grupo']}</td>
                                <td>{$row['nombre_grupo']}</td>
                                <td>{$row['grado']}</td>
                                <td>{$row['turno']}</td>
                                <td>
                                    <a href='editar_grupos.php?id={$row['id_grupo']}'>Editar</a> |
                                    <a href=\"eliminar_grupo.php?id={$row['id_grupo']}\" onclick=\"return confirm('¿Estás seguro de eliminar este grupo?');\">Eliminar</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay grupos registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="agregar_grupo.php" class="btn">Agregar Nuevo Grupo</a>
    </main>
</body>
</html>
