<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require_once 'db.php';

// Manejar la creación de un nuevo periodo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    if (!empty($nombre) && !empty($fecha_inicio) && !empty($fecha_fin)) {
        $stmt = $conn->prepare("INSERT INTO periodos (nombre, fecha_inicio, fecha_fin, activo) VALUES (?, ?, ?, ?)");
        $activo = isset($_POST['activo']) ? 1 : 0;
        $stmt->bind_param("sssi", $nombre, $fecha_inicio, $fecha_fin, $activo);

        if ($stmt->execute()) {
            $mensaje = "Periodo creado exitosamente.";
        } else {
            $error = "Error al crear el periodo.";
        }

        $stmt->close();
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}

// Obtener todos los periodos
$periodos = $conn->query("SELECT * FROM periodos ORDER BY fecha_inicio DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="CSS/administrar_periodos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Panel de Administrador</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Sección de bienvenida -->
        <section class="welcome-section">
            <h2>Asignación de Periodos</h2>
            <p>Consulta y da de alta periodos</p>
        </section>

        <!-- Botones de control -->
        <div class="button-container">
            <a href="administrador.php" class="control-button">
                <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
                <span>Panel Administrador</span>
            </a>
        </div>

        <!-- Formulario para dar de alta nuevos periodos -->
        <section class="new-period-section">
            <h3>Crear Nuevo Periodo</h3>
            <?php if (isset($mensaje)): ?>
                <p class="success-message"><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                <div>
                    <label for="nombre">Nombre del Periodo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div>
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                <div>
                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>
                </div>
                <div>
                    <label for="activo">Activo:</label>
                    <input type="checkbox" id="activo" name="activo">
                </div>
                <button type="submit">Crear Periodo</button>
            </form>
        </section>

        <!-- Tabla de periodos existentes -->
        <section class="existing-periods-section">
            <h3>Periodos Existentes</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha de Inicio</th>
                        <th>Fecha de Fin</th>
                        <th>Activo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($periodo = $periodos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($periodo['periodo_id']); ?></td>
                            <td><?php echo htmlspecialchars($periodo['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($periodo['fecha_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($periodo['fecha_fin']); ?></td>
                            <td><?php echo $periodo['activo'] ? 'Sí' : 'No'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
