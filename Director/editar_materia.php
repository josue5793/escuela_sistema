<?php
session_start();
require_once '../db.php';

// Verificar si el usuario está logueado y es director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: login.php");
    exit;
}

// Obtener el nivel_id del director
$director_id = $_SESSION['usuario_id'];
$query = "SELECT nivel_id FROM directores WHERE usuario_id = :usuario_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':usuario_id', $director_id, PDO::PARAM_INT);
$stmt->execute();
$director = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$director) {
    die("No se encontró información del director.");
}

$nivel_id = $director['nivel_id'];

// Obtener los grupos asociados al nivel del director
$query = "SELECT id_grupo, grado, turno FROM grupos WHERE nivel_id = :nivel_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
$stmt->execute();
$grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener la materia a editar
$materia_id = isset($_GET['editar']) ? intval($_GET['editar']) : null;

if (!$materia_id) {
    die("No se ha especificado una materia para editar.");
}

// Obtener los datos de la materia
$query = "SELECT materia_id, nombre FROM materias WHERE materia_id = :materia_id AND nivel_id = :nivel_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
$stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
$stmt->execute();
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    die("No se encontró la materia o no tienes permiso para editarla.");
}

// Obtener los grupos asignados a la materia
$query = "SELECT grupo_id FROM materia_grupo WHERE materia_id = :materia_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
$stmt->execute();
$grupos_asignados = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_materia'])) {
    $nombre_materia = trim($_POST['nombre_materia']);
    $grupos_seleccionados = $_POST['grupos']; // Array de grupos seleccionados

    if (!empty($nombre_materia) && !empty($grupos_seleccionados)) {
        // Actualizar el nombre de la materia
        $query = "UPDATE materias SET nombre = :nombre WHERE materia_id = :materia_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nombre', $nombre_materia, PDO::PARAM_STR);
        $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
        $stmt->execute();

        // Eliminar las relaciones antiguas en materia_grupo
        $query = "DELETE FROM materia_grupo WHERE materia_id = :materia_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
        $stmt->execute();

        // Insertar las nuevas relaciones en materia_grupo
        foreach ($grupos_seleccionados as $grupo_id) {
            $query = "INSERT INTO materia_grupo (materia_id, grupo_id) VALUES (:materia_id, :grupo_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        $mensaje = "Materia actualizada exitosamente.";
        header("Location: registro_materias.php");
        exit;
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .mensaje {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .mensaje.exito {
            background-color: #d4edda;
            color: #155724;
        }
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Materia</h1>

        <?php if (isset($mensaje)): ?>
            <div class="mensaje exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="mensaje error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulario de edición de materias -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="nombre_materia">Nombre de la Materia:</label>
                <input type="text" id="nombre_materia" name="nombre_materia" value="<?php echo htmlspecialchars($materia['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label>Grupos:</label>
                <?php foreach ($grupos as $grupo): ?>
                    <div>
                        <input type="checkbox" name="grupos[]" value="<?php echo $grupo['id_grupo']; ?>"
                            <?php echo (in_array($grupo['id_grupo'], $grupos_asignados)) ? 'checked' : ''; ?>>
                        Grado <?php echo $grupo['grado']; ?> - <?php echo $grupo['turno']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="form-group">
                <input type="hidden" name="editar_materia" value="1">
                <button type="submit">Guardar Cambios</button>
                <a href="registro_materias.php" style="margin-left: 10px;">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>