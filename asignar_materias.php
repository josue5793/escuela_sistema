<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require 'db.php';

// Inicializar mensaje
$mensaje = "";

// Procesar el formulario de asignación de materias
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profesor_id = $_POST['profesor_id'] ?? null;
    $materia_id = $_POST['materia_id'] ?? null;
    $nivel_id = $_POST['nivel_id'] ?? null;

    if ($profesor_id && $materia_id && $nivel_id) {
        // Validar si el profesor ya tiene asignada esa materia en el nivel seleccionado
        $check_query = "SELECT * FROM profesor_materia 
                        WHERE profesor_id = ? AND materia_id = ? AND periodo_id = ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("iii", $profesor_id, $materia_id, $nivel_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $mensaje = "El profesor ya tiene asignada esta materia en este nivel.";
        } else {
            // Asignar la materia al profesor en el nivel seleccionado
            $assign_query = "INSERT INTO profesor_materia (profesor_id, materia_id, periodo_id) 
                             VALUES (?, ?, ?)";
            $stmt_assign = $conn->prepare($assign_query);
            $stmt_assign->bind_param("iii", $profesor_id, $materia_id, $nivel_id);

            if ($stmt_assign->execute()) {
                $mensaje = "Materia asignada exitosamente.";
            } else {
                $mensaje = "Error al asignar la materia: " . $stmt_assign->error;
            }
        }
        $stmt_check->close();
    } else {
        $mensaje = "Debe seleccionar un profesor, una materia y un nivel.";
    }
}

// Obtener los niveles disponibles
$niveles_query = "SELECT nivel_id, nivel_nombre FROM niveles ORDER BY nivel_nombre";
$niveles_result = $conn->query($niveles_query);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Materias a Profesores</title>
    <link rel="stylesheet" href="CSS/asignar_materias.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cuando se selecciona un nivel, obtener los profesores correspondientes
            $('#nivel_id').change(function() {
                var nivel_id = $(this).val();
                if (nivel_id) {
                    // Obtener los profesores del nivel seleccionado
                    $.ajax({
                        url: 'get_profesores.php',  // Archivo que devolverá los profesores
                        type: 'GET',
                        data: { nivel_id: nivel_id },
                        success: function(response) {
                            $('#profesor_id').html(response);
                            // Vaciar el campo de materias
                            $('#materia_id').html('<option value="">Selecciona un nivel primero</option>');
                        }
                    });

                    // Obtener las materias del nivel seleccionado
                    $.ajax({
                        url: 'get_materias.php',  // Archivo que devolverá las materias
                        type: 'GET',
                        data: { nivel_id: nivel_id },
                        success: function(response) {
                            $('#materia_id').html(response);
                        }
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
    <!-- Barra de navegación -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Asignar Materias a Profesores</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="main-container">
        <h2>Asignar Materias</h2>

        <div class="button-container">
        <a href="administrador.php" class="control-button">
                <i class="bi bi-house-door"></i>
                <span>Regresar</span>
            </a>
            <a href="consulta_profesores.php" class="control-button">
                <i class="bi bi-person-lines-fill"></i>
                <span>Consulta de profesores y especialidad</span>
            </a>

            <!-- Botón para asignación de niveles -->
            <a href="asignar_niveles.php" class="control-button">
                <i class="bi bi-pen"></i>
                <span>Asignación de Niveles</span>
            </a>

            <!-- Botón para asignar materias -->
            <a href="asignar_materias.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Asignar Materias</span>
            </a>
        </div>
        
        <!-- Mostrar mensaje de éxito o error -->
        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <!-- Formulario de asignación -->
        <form action="" method="POST">
            <label for="nivel_id">Nivel:</label>
            <select id="nivel_id" name="nivel_id" required>
                <option value="">Selecciona un nivel</option>
                <?php while ($nivel = $niveles_result->fetch_assoc()): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>"><?php echo htmlspecialchars($nivel['nivel_nombre']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="profesor_id">Profesor:</label>
            <select id="profesor_id" name="profesor_id" required>
                <option value="">Selecciona un nivel primero</option>
            </select>

            <label for="materia_id">Materia:</label>
            <select id="materia_id" name="materia_id" required>
                <option value="">Selecciona un nivel primero</option>
            </select>

            <button type="submit">Asignar Materia</button>
        </form>
    </main>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
