<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

// Obtener el nombre del director desde la sesión
$nombre_director = $_SESSION['nombre'];

// Incluir la conexión a la base de datos
require_once '../db.php';

// Obtener la lista de todos los profesores
try {
    $stmt = $pdo->prepare("SELECT p.profesor_id, u.nombre, u.correo, p.especialidad, p.telefono 
                           FROM profesores p 
                           JOIN usuarios u ON p.usuario_id = u.usuario_id");
    $stmt->execute();
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener la lista de profesores: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Profesores - Director</title>
    <link rel="stylesheet" href="CSS/gestion_usuarios.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra superior -->
    <header class="navbar">
        <div class="navbar-left">
            <span>Gestión de Usuarios</span>
        </div>
        <div class="navbar-right">
            <span>Bienvenido, <?php echo htmlspecialchars($nombre_director); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </header>

    <!-- Cuerpo del documento -->
    <main class="main-container">
        <h1>Consultar Profesores</h1>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Control de navegación -->
        <div class="button-container">
            <a href="dashboard_director.php" class="control-button">
                <i class="bi bi-house-door"></i>
                <span>Panel principal</span>
            </a>   
            <a href="gestion_usuarios.php" class="control-button">
                <i class="bi bi-person-plus"></i>
                <span>Agregar Profesor</span>
            </a>
            <a href="asignar_profesor.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Asignar profesor a materias</span>
            </a>
            <a href="consultar_profesores.php" class="control-button">
                <i class="bi bi-people"></i>
                <span>Consultar Profesores</span>
            </a>

        </div>

        <!-- Tabla de profesores -->
        <table class="profesores-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo Electrónico</th>
                    <th>Especialidad</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($profesores) > 0): ?>
                    <?php foreach ($profesores as $profesor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($profesor['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($profesor['correo']); ?></td>
                            <td><?php echo htmlspecialchars($profesor['especialidad']); ?></td>
                            <td><?php echo htmlspecialchars($profesor['telefono']); ?></td>
                            <td>
                                <!-- Botón Editar -->
                                <a href="editar_profesor.php?profesor_id=<?php echo $profesor['profesor_id']; ?>" class="btn-editar">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                                <!-- Botón Eliminar -->
                                <a href="eliminar_profesor.php?profesor_id=<?php echo $profesor['profesor_id']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este profesor?');">
                                    <i class="bi bi-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-data">No hay profesores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- Pie de página -->
    <footer class="footer">
        <span id="fecha-hora"></span>
    </footer>

    <!-- Script para la fecha y hora -->
    <script src="../JS/dashboard_director.js"></script>
</body>
</html>