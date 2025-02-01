<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require '../db.php'; // Conexión a la base de datos

$mensaje = "";
$id_asignacion = $_GET['id'] ?? '';

if (empty($id_asignacion)) {
    header("Location: asignar_materias_alumnos.php");
    exit;
}

// Obtener la asignación actual
try {
    $stmt = $pdo->prepare("SELECT gm.id, gm.grupo_id, gm.materia_id, g.nivel_id 
                           FROM grupo_materia gm
                           JOIN grupos g ON gm.grupo_id = g.id_grupo
                           WHERE gm.id = :id");
    $stmt->bindParam(':id', $id_asignacion, PDO::PARAM_INT);
    $stmt->execute();
    $asignacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$asignacion) {
        $mensaje = "Asignación no encontrada.";
    }
} catch (PDOException $e) {
    die("Error al obtener la asignación: " . htmlspecialchars($e->getMessage()));
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grupo_id = $_POST['grupo_id'];
    $materia_id = $_POST['materia_id'];

    if ($grupo_id && $materia_id) {
        try {
            // Validar que la materia no esté ya asignada al grupo (excepto la actual)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM grupo_materia WHERE grupo_id = :grupo_id AND materia_id = :materia_id AND id != :id");
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id_asignacion, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                $mensaje = "La materia ya está asignada a este grupo.";
            } else {
                // Actualizar la asignación
                $stmt = $pdo->prepare("UPDATE grupo_materia SET grupo_id = :grupo_id, materia_id = :materia_id WHERE id = :id");
                $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
                $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id_asignacion, PDO::PARAM_INT);
                $stmt->execute();
                $mensaje = "Asignación actualizada correctamente.";
            }
        } catch (PDOException $e) {
            $mensaje = "Error al actualizar la asignación: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $mensaje = "Por favor, complete todos los campos.";
    }
}

// Obtener niveles, grupos y materias
try {
    $niveles = $pdo->query("SELECT nivel_id, nivel_nombre FROM niveles")->fetchAll(PDO::FETCH_ASSOC);
    $grupos = $pdo->query("SELECT id_grupo, grado, turno FROM grupos")->fetchAll(PDO::FETCH_ASSOC);
    $materias = $pdo->query("SELECT materia_id, nombre FROM materias")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener datos: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Asignación</title>
    <link rel="stylesheet" href="CSS/.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar grupos y materias al seleccionar un nivel
            $('#nivel_id').on('change', function() {
                var nivelId = $(this).val();
                if (nivelId) {
                    // Cargar grupos
                    $.ajax({
                        url: 'cargar_grupos.php',
                        type: 'GET',
                        data: { nivel_id: nivelId },
                        success: function(response) {
                            $('#grupo_id').html(response);
                        }
                    });

                    // Cargar materias
                    $.ajax({
                        url: 'cargar_materias.php',
                        type: 'GET',
                        data: { nivel_id: nivelId },
                        success: function(response) {
                            $('#materia_id').html(response);
                        }
                    });
                } else {
                    // Limpiar los menús si no se selecciona un nivel
                    $('#grupo_id').html('<option value="">Selecciona un grupo</option>');
                    $('#materia_id').html('<option value="">Selecciona una materia</option>');
                }
            });

            // Seleccionar el nivel actual al cargar la página
            var nivelId = "<?php echo $asignacion['nivel_id'] ?? ''; ?>";
            if (nivelId) {
                $('#nivel_id').val(nivelId).trigger('change');
            }
        });
    </script>
</head>
<body>
    <main class="main-container">
        <header>
            <h1>Editar Asignación</h1>
        </header>

        <section>
            <?php if ($mensaje): ?>
                <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="nivel_id">Nivel:</label>
                <select id="nivel_id" name="nivel_id" required>
                    <option value="">Selecciona un nivel</option>
                    <?php foreach ($niveles as $nivel): ?>
                        <option value="<?php echo $nivel['nivel_id']; ?>">
                            <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="grupo_id">Grupo:</label>
                <select id="grupo_id" name="grupo_id" required>
                    <option value="">Selecciona un grupo</option>
                </select>

                <label for="materia_id">Materia:</label>
                <select id="materia_id" name="materia_id" required>
                    <option value="">Selecciona una materia</option>
                </select>

                <button type="submit">Guardar Cambios</button>
            </form>
        </section>

        <section>
            <a href="asignar_materias_alumnos.php">Volver a Asignar Materias</a>
        </section>
    </main>
</body>
</html>