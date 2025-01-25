<?php
session_start();
require '../db.php'; // Conexión a la base de datos

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener niveles y directores disponibles
$niveles = [];
$directores = [];
$error = '';

// Obtener niveles
try {
    $stmt = $pdo->query("SELECT nivel_id, nivel_nombre FROM niveles");
    $niveles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener los niveles: " . htmlspecialchars($e->getMessage());
}

// Obtener directores
try {
    $stmt = $pdo->prepare("
        SELECT usuarios.usuario_id, usuarios.nombre
        FROM usuarios
        INNER JOIN roles ON usuarios.rol_id = roles.rol_id
        WHERE roles.nombre = 'director'
    ");
    $stmt->execute();
    $directores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener los directores: " . htmlspecialchars($e->getMessage());
}

// Asignar director
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nivel_id = $_POST['nivel_id'] ?? null;
    $usuario_id = $_POST['director_id'] ?? null;

    if ($nivel_id && $usuario_id) {
        try {
            $stmt = $pdo->prepare("INSERT INTO directores (usuario_id, nivel_id, asignado_por) VALUES (:usuario_id, :nivel_id, :asignado_por)");
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':nivel_id' => $nivel_id,
                ':asignado_por' => $_SESSION['usuario_id']
            ]);
            $success = "Director asignado correctamente.";
        } catch (PDOException $e) {
            $error = "Error al asignar el director: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = "Debe seleccionar un nivel y un director.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Directores</title>
    <link rel="stylesheet" href="CSS/asignar_directores.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Asignar Directores</h1>
            <div class="navbar-right">
                <a href="administrador_dashboard.php" class="back-button">Volver al Panel</a>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="main-container">
        <section class="form-section">
            <h2>Asignar un Director a un Nivel</h2>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php elseif (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="asignar_directores.php" method="POST">
                <label for="nivel_id">Nivel:</label>
                <select name="nivel_id" id="nivel_id" required>
                    <option value="">Seleccione un nivel</option>
                    <?php foreach ($niveles as $nivel): ?>
                        <option value="<?php echo $nivel['nivel_id']; ?>"><?php echo htmlspecialchars($nivel['nivel_nombre']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="director_id">Director:</label>
                <select name="director_id" id="director_id" required>
                    <option value="">Seleccione un director</option>
                    <?php foreach ($directores as $director): ?>
                        <option value="<?php echo $director['usuario_id']; ?>"><?php echo htmlspecialchars($director['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="submit-button">Asignar Director</button>
            </form>
        </section>

        <!-- Tabla para mostrar asignaciones -->
        <section class="table-section">
            <h2>Asignaciones de Directores</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nivel</th>
                        <th>Director</th>
                        <th>Asignado por</th>
                        <th>Fecha de Asignación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $pdo->query("
                            SELECT 
                                niveles.nivel_nombre,
                                usuarios.nombre AS director_nombre,
                                (SELECT nombre FROM usuarios WHERE usuario_id = directores.asignado_por) AS asignado_por_nombre,
                                directores.fecha_asignacion
                            FROM directores
                            INNER JOIN niveles ON directores.nivel_id = niveles.nivel_id
                            INNER JOIN usuarios ON directores.usuario_id = usuarios.usuario_id
                        ");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nivel_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['director_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['asignado_por_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['fecha_asignacion']); ?></td>
                            </tr>
                        <?php endwhile;
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='4'>Error al obtener asignaciones: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
