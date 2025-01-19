<?php
session_start();
require_once 'db.php';

// Verificar si el usuario está logueado y tiene el rol de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: login.php");
    exit;
}

// Obtener el profesor logueado
$usuario_id = $_SESSION['usuario_id'];
$query_profesor = "SELECT p.profesor_id, u.nombre, p.especialidad, p.telefono, GROUP_CONCAT(n.nivel_nombre ORDER BY n.nivel_nombre ASC) AS niveles
                   FROM profesores p
                   JOIN usuarios u ON p.usuario_id = u.usuario_id
                   LEFT JOIN profesor_nivel pn ON p.profesor_id = pn.profesor_id
                   LEFT JOIN niveles n ON pn.nivel_id = n.nivel_id
                   WHERE p.usuario_id = :usuario_id
                   GROUP BY p.profesor_id";
$stmt_profesor = $pdo->prepare($query_profesor);
$stmt_profesor->execute(['usuario_id' => $usuario_id]);
$profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);

// Manejar la adición de un nuevo nivel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_nivel'])) {
    $nivel_nombre = $_POST['nivel_nombre'];
    $profesor_id = $profesor['profesor_id']; // Obtener el ID del profesor logueado

    // Verificar si el nivel ya está asignado
    $check_query = "SELECT * FROM profesor_nivel pn
                    JOIN niveles n ON pn.nivel_id = n.nivel_id
                    WHERE pn.profesor_id = :profesor_id AND n.nivel_nombre = :nivel_nombre";
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute(['profesor_id' => $profesor_id, 'nivel_nombre' => $nivel_nombre]);
    if ($check_stmt->rowCount() > 0) {
        echo "<script>alert('Este nivel ya está asignado.');</script>";
    } else {
        // Asignar el nivel al profesor
        $nivel_query = "SELECT nivel_id FROM niveles WHERE nivel_nombre = :nivel_nombre";
        $nivel_stmt = $pdo->prepare($nivel_query);
        $nivel_stmt->execute(['nivel_nombre' => $nivel_nombre]);
        $nivel = $nivel_stmt->fetch(PDO::FETCH_ASSOC);

        if ($nivel) {
            $insert_query = "INSERT INTO profesor_nivel (profesor_id, nivel_id) VALUES (:profesor_id, :nivel_id)";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->execute(['profesor_id' => $profesor_id, 'nivel_id' => $nivel['nivel_id']]);
            header("Location: gestionar_niveles.php?nivel_agregado=true");
            exit;
        }
    }
}

// Consultar los niveles disponibles
$query_niveles = "SELECT * FROM niveles";
$stmt_niveles = $pdo->query($query_niveles);
$niveles_disponibles = $stmt_niveles->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Niveles - Dashboard del Profesor</title>
    <link rel="stylesheet" href="css/gestionar_niveles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestionar Niveles</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($profesor['nombre'] ?? ''); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Mensajes de éxito -->
        <?php if (isset($_GET['nivel_agregado'])): ?>
            <div class="alert alert-success">Nivel asignado correctamente.</div>
        <?php endif; ?>

        <!-- Sección para agregar un nuevo nivel -->
        <section class="form-section">
            <h2>Asignar Nuevo Nivel</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="nivel_nombre">Seleccionar Nivel:</label>
                    <select name="nivel_nombre" id="nivel_nombre" required>
                        <option value="" disabled selected>Seleccione un nivel</option>
                        <?php foreach ($niveles_disponibles as $nivel): ?>
                            <option value="<?php echo htmlspecialchars($nivel['nivel_nombre']); ?>"><?php echo htmlspecialchars($nivel['nivel_nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="agregar_nivel" class="submit-button">Asignar Nivel</button>
            </form>
        </section>

        <!-- Sección de niveles asignados al profesor -->
        <section class="niveles-section">
            <h2>Niveles Asignados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Nivel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($profesor['niveles'])): ?>
                        <?php foreach (explode(',', $profesor['niveles']) as $nivel): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($nivel); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td>No tienes niveles asignados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
