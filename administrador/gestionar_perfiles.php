<<<<<<< HEAD
<?php
session_start();
require_once '../db.php'; // Conexión a la base de datos usando PDO
=======

<?php
//Este código presenta un error
//revisar detalladamente
session_start();
require_once '../db.php';
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener lista de roles de la base de datos
<<<<<<< HEAD
try {
    $sql_roles = "SELECT rol_id, nombre FROM roles";
    $stmt_roles = $pdo->query($sql_roles);
    $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los roles: " . htmlspecialchars($e->getMessage()));
}
=======
$sql_roles = "SELECT rol_id, nombre FROM roles";
$result_roles = $conn->query($sql_roles);
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc

// Verificar si se está editando un rol
$rol_a_editar = null;
if (isset($_GET['id'])) {
    $rol_id = $_GET['id'];
<<<<<<< HEAD
    if (!is_numeric($rol_id)) {
        die("ID de rol no válido.");
    }

    try {
        $sql_editar = "SELECT * FROM roles WHERE rol_id = :rol_id";
        $stmt = $pdo->prepare($sql_editar);
        $stmt->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
        $stmt->execute();
        $rol_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al obtener el rol: " . htmlspecialchars($e->getMessage()));
    }
=======
    $sql_editar = "SELECT * FROM roles WHERE rol_id = ?";
    $stmt = $conn->prepare($sql_editar);
    $stmt->bind_param("i", $rol_id);
    $stmt->execute();
    $rol_a_editar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles</title>
<<<<<<< HEAD
    <link rel="stylesheet" href="CSS/gestionar_perfiles.css"> <!-- Asegúrate de que este archivo exista -->
=======
    <link rel="stylesheet" href="CSS/.css">
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Roles</h1>
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

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Sección de advertencia -->
<<<<<<< HEAD
        <div class="button-container">
=======
                <div class="button-container">
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
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
<<<<<<< HEAD
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $rol): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rol['rol_id']); ?></td>
                                <td><?php echo htmlspecialchars($rol['nombre']); ?></td>
                                <td>
                                    <a href="gestionar_perfiles.php?id=<?php echo $rol['rol_id']; ?>" class="action-button edit">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No hay roles registrados.</td>
                        </tr>
                    <?php endif; ?>
=======
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
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
                </tbody>
            </table>
        </section>

        <!-- Formulario para agregar o editar un rol -->
        <section class="form-section">
            <h2><?php echo $rol_a_editar ? 'Editar Rol' : 'Agregar Nuevo Rol'; ?></h2>
            <form action="procesar_rol.php" method="POST">
                <?php if ($rol_a_editar): ?>
<<<<<<< HEAD
                    <input type="hidden" name="rol_id" value="<?php echo htmlspecialchars($rol_a_editar['rol_id']); ?>">
=======
                    <input type="hidden" name="rol_id" value="<?php echo $rol_a_editar['rol_id']; ?>">
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
                <?php endif; ?>
                <label for="nombre">Nombre del Rol:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $rol_a_editar ? htmlspecialchars($rol_a_editar['nombre']) : ''; ?>" required>

                <button type="submit"><?php echo $rol_a_editar ? 'Actualizar Rol' : 'Agregar Rol'; ?></button>
            </form>
        </section>
<<<<<<< HEAD
    </main>
</body>
</html>
=======

        <!-- Botón para regresar al Panel de Administrador -->
        
    </main>
</body>
</html>
>>>>>>> da46b6c9ee917a6dcce0ae856323ab1f6cb78ccc
