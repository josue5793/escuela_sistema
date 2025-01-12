<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

try {
    // Obtener lista de grupos con sus niveles de la base de datos
    $sql = "
        SELECT g.id_grupo, n.nivel_nombre, g.grado, g.turno 
        FROM grupos g
        JOIN niveles n ON g.nivel_id = n.nivel_id
    ";
    $stmt = $pdo->query($sql);
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los grupos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos</title>
    <link rel="stylesheet" href="CSS/gestionar_grupos2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Grupos</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenedor de botones -->
    <div class="button-container">
        <!-- Botón para ir al Panel del Administrador -->
        <a href="administrador.php" class="control-button">
            <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
            <span>Panel Administrador</span>
        </a>

        <!-- Botón para agregar un nuevo grupo -->
        <a href="agregar_grupo.php" class="control-button">
            <i class="bi bi-plus-circle"></i> <!-- Ícono de agregar -->
            <span>Agregar Nuevo Grupo</span>
        </a>
    </div>

    <!-- Contenido Principal -->
    <main class="main-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nivel</th>
                    <th>Grado</th>
                    <th>Turno</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($grupos)) {
                    foreach ($grupos as $row) {
                        echo "<tr>
                                <td>{$row['id_grupo']}</td>
                                <td>{$row['nivel_nombre']}</td>
                                <td>{$row['grado']}</td>
                                <td>{$row['turno']}</td>
                                <td>
                                    <a href='editar_grupos.php?id={$row['id_grupo']}' class='action-button edit'>
                                        <i class='bi bi-pencil'></i> Editar
                                    </a> |
                                    <a href=\"eliminar_grupo.php?id={$row['id_grupo']}\" class='action-button delete' onclick=\"return confirm('¿Estás seguro de eliminar este grupo?');\">
                                        <i class='bi bi-trash'></i> Eliminar
                                    </a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay grupos registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>
