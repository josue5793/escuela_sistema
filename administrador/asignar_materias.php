<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require '../db.php';

// Inicializar mensaje
$mensaje = "";

// Procesar el formulario de asignación de materias
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nivel_id = $_POST['nivel_id'] ?? null;
    $profesor_id = $_POST['profesor_id'] ?? null;
    $materia_id = $_POST['materia_id'] ?? null;
    $periodo_id = $_POST['periodo_id'] ?? null;

    if ($nivel_id && $profesor_id && $materia_id && $periodo_id) {
        // Validar si ya existe la asignación
        $query_check = "SELECT * FROM profesor_materia WHERE profesor_id = :profesor_id AND materia_id = :materia_id AND periodo_id = :periodo_id";
        $stmt_check = $pdo->prepare($query_check);
        $stmt_check->execute([
            ':profesor_id' => $profesor_id,
            ':materia_id' => $materia_id,
            ':periodo_id' => $periodo_id,
        ]);

        if ($stmt_check->rowCount() > 0) {
            $mensaje = "El profesor ya tiene asignada esta materia en este periodo.";
        } else {
            // Insertar la asignación
            $query_insert = "INSERT INTO profesor_materia (profesor_id, materia_id, periodo_id) VALUES (:profesor_id, :materia_id, :periodo_id)";
            $stmt_insert = $pdo->prepare($query_insert);

            if ($stmt_insert->execute([
                ':profesor_id' => $profesor_id,
                ':materia_id' => $materia_id,
                ':periodo_id' => $periodo_id,
            ])) {
                $mensaje = "Materia asignada exitosamente.";
            } else {
                $mensaje = "Error al asignar la materia.";
            }
        }
    } else {
        $mensaje = "Debe seleccionar un nivel, un profesor, una materia y un periodo.";
    }
}

// Obtener los niveles
$query_niveles = "SELECT nivel_id, nivel_nombre FROM niveles ORDER BY nivel_nombre";
$niveles = $pdo->query($query_niveles)->fetchAll(PDO::FETCH_ASSOC);

// Obtener los periodos activos
$query_periodos = "SELECT periodo_id, nombre FROM periodos WHERE activo = 1 ORDER BY nombre";
$periodos = $pdo->query($query_periodos)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Materias</title>
    <link rel="stylesheet" href="CSS/asignar_materias.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script>
        $(document).ready(function() {
            // Cuando cambia el nivel
            $('#nivel_id').change(function() {
                var nivel_id = $(this).val();
                if (nivel_id) {
                    // Cargar profesores
                    $.get('get_profesores.php', { nivel_id: nivel_id }, function(data) {
                        $('#profesor_id').html(data);
                    });

                    // Cargar materias
                    $.get('get_materias.php', { nivel_id: nivel_id }, function(data) {
                        $('#materia_id').html(data);
                    });
                } else {
                    $('#profesor_id').html('<option value="">Selecciona un nivel primero</option>');
                    $('#materia_id').html('<option value="">Selecciona un nivel primero</option>');
                }
            });
        });
    </script>
</head>
<body>

    <header>
        <h1>Asignar Materias a Profesores</h1>
        <a href="logout.php">Cerrar Sesión</a>
    </header>
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
    <main>
        <?php if (!empty($mensaje)): ?>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="nivel_id">Nivel:</label>
            <select id="nivel_id" name="nivel_id" required>
                <option value="">Selecciona un nivel</option>
                <?php foreach ($niveles as $nivel): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>"><?php echo htmlspecialchars($nivel['nivel_nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="profesor_id">Profesor:</label>
            <select id="profesor_id" name="profesor_id" required>
                <option value="">Selecciona un nivel primero</option>
            </select>

            <label for="materia_id">Materia:</label>
            <select id="materia_id" name="materia_id" required>
                <option value="">Selecciona un nivel primero</option>
            </select>

            <label for="periodo_id">Periodo:</label>
            <select id="periodo_id" name="periodo_id" required>
                <option value="">Selecciona un periodo</option>
                <?php foreach ($periodos as $periodo): ?>
                    <option value="<?php echo $periodo['periodo_id']; ?>"><?php echo htmlspecialchars($periodo['nombre']); ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Asignar Materia</button>
        </form>
    </main>
</body>
</html>
