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

// Obtener el nivel_id del director
try {
    $stmt = $pdo->prepare("SELECT d.nivel_id, n.nivel_nombre 
                           FROM directores d 
                           JOIN niveles n ON d.nivel_id = n.nivel_id 
                           WHERE d.usuario_id = :usuario_id");
    $stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
    $director = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$director) {
        throw new Exception("El director no está asignado a un nivel.");
    }

    $nivel_id_director = $director['nivel_id'];
    $nivel_nombre_director = $director['nivel_nombre'];
} catch (PDOException $e) {
    die("Error al obtener el nivel del director: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}

// Obtener la lista de profesores asignados al nivel del director
try {
    $stmt = $pdo->prepare("SELECT p.profesor_id, u.nombre, u.correo, p.especialidad, p.telefono 
                           FROM profesores p 
                           JOIN usuarios u ON p.usuario_id = u.usuario_id 
                           JOIN profesor_nivel pn ON p.profesor_id = pn.profesor_id 
                           WHERE pn.nivel_id = :nivel_id");
    $stmt->execute([':nivel_id' => $nivel_id_director]);
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
        <h1>Consultar Profesores del Nivel: <?php echo htmlspecialchars($nivel_nombre_director); ?></h1>

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
            <a href="consultar_profesores.php" class="control-button">
                <i class="bi bi-people"></i>
                <span>Consultar Profesores del Nivel</span>
            </a>
            <a href="editar_profesor.php" class="control-button">
                <i class="bi bi-pencil-square"></i>
                <span>Editar Profesor</span>
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
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-data">No hay profesores registrados en este nivel.</td>
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