<?php
include('../db.php');
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

$message = "";

// Obtener lista de niveles
$query_niveles = "SELECT nivel_id, nivel_nombre FROM niveles";
$stmt_niveles = $pdo->query($query_niveles);
$result_niveles = $stmt_niveles->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de profesores
$query_profesores = "SELECT p.profesor_id, u.nombre, p.especialidad 
                     FROM profesores p
                     JOIN usuarios u ON p.usuario_id = u.usuario_id";
$stmt_profesores = $pdo->query($query_profesores);
$result_profesores = $stmt_profesores->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario de asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_nivel'])) {
    $profesor_id = intval($_POST['profesor_id']);
    $nivel_id = intval($_POST['nivel_id']);

    // Validar si ya existe la asignación
    $query_check = "SELECT * FROM profesor_nivel WHERE profesor_id = :profesor_id AND nivel_id = :nivel_id";
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->execute([':profesor_id' => $profesor_id, ':nivel_id' => $nivel_id]);
    $result_check = $stmt_check->fetch();

    if ($result_check) {
        $message = "Este nivel ya está asignado a este profesor.";
    } else {
        // Insertar la asignación
        $query_asignar = "INSERT INTO profesor_nivel (profesor_id, nivel_id) VALUES (:profesor_id, :nivel_id)";
        $stmt_asignar = $pdo->prepare($query_asignar);

        if ($stmt_asignar->execute([':profesor_id' => $profesor_id, ':nivel_id' => $nivel_id])) {
            $message = "Nivel asignado correctamente.";
        } else {
            $message = "Error al asignar nivel.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Niveles</title>
    <link rel="stylesheet" href="css/asignar_niveles.css"> <!-- Ajusta según tu diseño -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    
<main class="main-container">  
    <h1>Asignar Niveles</h1>
    <div class="button-container">
        <a href="administrador_dashboard.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Regresar</span>
        </a>
        <a href="consulta_profesores.php" class="control-button">
            <i class="bi bi-person-lines-fill"></i>
            <span>Consulta de profesores y especialidad</span>
        </a>
        <a href="asignar_niveles.php" class="control-button">
            <i class="bi bi-pen"></i>
            <span>Asignación de Niveles</span>
        </a>
        <a href="asignar_materias.php" class="control-button">
            <i class="bi bi-book"></i>
            <span>Asignar Materias</span>
        </a>
    </div>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="profesor_id">Profesor:</label>
        <select name="profesor_id" required>
            <option value="">Selecciona un profesor</option>
            <?php foreach ($result_profesores as $profesor): ?>
                <option value="<?php echo $profesor['profesor_id']; ?>">
                    <?php echo htmlspecialchars($profesor['nombre']) . " (" . htmlspecialchars($profesor['especialidad']) . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="nivel_id">Nivel:</label>
        <select name="nivel_id" required>
            <option value="">Selecciona un nivel</option>
            <?php foreach ($result_niveles as $nivel): ?>
                <option value="<?php echo $nivel['nivel_id']; ?>">
                    <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="asignar_nivel">Asignar Nivel</button>
    </form>
</main>
</body>
</html>
