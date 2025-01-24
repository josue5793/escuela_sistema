
<?php
//Este código presenta un error
//revisar detalladamente
session_start();
require_once '../db.php';

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener lista de roles de la base de datos
$sql_roles = "SELECT rol_id, nombre FROM roles";
$result_roles = $conn->query($sql_roles);

// Verificar si se está editando un rol
$rol_a_editar = null;
if (isset($_GET['id'])) {
    $rol_id = $_GET['id'];
    $sql_editar = "SELECT * FROM roles WHERE rol_id = ?";
    $stmt = $conn->prepare($sql_editar);
    $stmt->bind_param("i", $rol_id);
    $stmt->execute();
    $rol_a_editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles</title>
    <link rel="stylesheet" href="CSS/.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Roles</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Sección de advertencia -->
                <div class="button-container">
            <a href="administrador_dashboard.php" class="control-button">
                <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
                <span>Regresar al Panel de Administrador</span>
            </a>
        </div>
        <section class="warning-section">
            <h2>Advertencia</h2>
            <p>Realizar cambios en los roles puede afectar los permisos y accesos dentro del sistema. Asegúrate de que los cambios sean correctos.</p>
        </section>

        <!-- Tabla de roles -->
        <section class="table-section">
            <h2>Roles Existentes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_roles->num_rows > 0) {
                        while ($row = $result_roles->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['rol_id']}</td>
                                    <td>{$row['nombre']}</td>
                                    <td>
                                        <a href='gestionar_roles.php?id={$row['rol_id']}' class='action-button edit'>
                                            <i class='bi bi-pencil'></i> Editar
                                        </a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No hay roles registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Formulario para agregar o editar un rol -->
        <section class="form-section">
            <h2><?php echo $rol_a_editar ? 'Editar Rol' : 'Agregar Nuevo Rol'; ?></h2>
            <form action="procesar_rol.php" method="POST">
                <?php if ($rol_a_editar): ?>
                    <input type="hidden" name="rol_id" value="<?php echo $rol_a_editar['rol_id']; ?>">
                <?php endif; ?>
                <label for="nombre">Nombre del Rol:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $rol_a_editar ? htmlspecialchars($rol_a_editar['nombre']) : ''; ?>" required>

                <button type="submit"><?php echo $rol_a_editar ? 'Actualizar Rol' : 'Agregar Rol'; ?></button>
            </form>
        </section>

        <!-- Botón para regresar al Panel de Administrador -->
        
    </main>
</body>
</html>
