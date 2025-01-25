<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../login.php");
    exit;
}

// Conexión a la base de datos
require '../db.php'; // Asegúrate de que este archivo contenga la conexión PDO adecuada

// Inicializar mensaje de éxito o error
$mensaje = "";

// Obtener el ID de la materia a editar
$materia_id = $_GET['id'] ?? '';

if (empty($materia_id)) {
    header("Location: materias.php");
    exit;
}

// Obtener los datos de la materia
try {
    $stmt = $pdo->prepare("SELECT * FROM materias WHERE materia_id = :materia_id");
    $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
    $stmt->execute();
    $materia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$materia) {
        $mensaje = "Materia no encontrada.";
    }
} catch (PDOException $e) {
    die("Error al obtener la materia: " . htmlspecialchars($e->getMessage()));
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $nombre_materia = trim($_POST['nombre_materia']);
    $nivel_id = $_POST['nivel_id'] ?? '';

    // Validar datos
    if (empty($nombre_materia)) {
        $mensaje = "El nombre de la materia es obligatorio.";
    } elseif (empty($nivel_id)) {
        $mensaje = "El nivel de la materia es obligatorio.";
    } else {
        try {
            // Preparar la consulta para actualizar la materia
            $stmt = $pdo->prepare("UPDATE materias SET nombre = :nombre, nivel_id = :nivel_id WHERE materia_id = :materia_id");
            $stmt->bindParam(':nombre', $nombre_materia);
            $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
            $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $mensaje = "Materia actualizada exitosamente.";
            } else {
                $mensaje = "Error al actualizar la materia.";
            }
        } catch (PDOException $e) {
            $mensaje = "Error en la consulta: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Obtener todos los niveles
try {
    $niveles_result = $pdo->query("SELECT nivel_id, nivel_nombre FROM niveles");
} catch (PDOException $e) {
    die("Error al obtener los niveles: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Materia</title>
    <link rel="stylesheet" href="CSS/materias2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <main class="main-container">
        <header class="navbar">
            <div class="navbar-container">
                <h1>Editar Materia</h1>
                <div class="navbar-right">
                    <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
                </div>
            </div>
        </header>

        <section class="welcome-section">
            <h2>Editar materia existente</h2>
            <p>Actualiza la información de la materia seleccionada</p>
        </section>

        <div class="button-container">
            <a href="administrador_dashboard.php" class="control-button">
                <i class="bi bi-house-door"></i>
                <span>Panel Administrador</span>
            </a>
            <a href="materias.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Volver a Materias</span>
            </a>
        </div>

        <h1>Editar Materia</h1>

        <!-- Formulario de edición -->
        <form action="" method="POST" class="formulario">
            <label for="nombre_materia">Nombre de la Materia:</label>
            <input type="text" id="nombre_materia" name="nombre_materia" 
                   value="<?php echo htmlspecialchars($materia['nombre'] ?? ''); ?>" 
                   placeholder="Escribe el nombre de la materia" required>

            <label for="nivel_id">Seleccionar Nivel:</label>
            <select id="nivel_id" name="nivel_id" required>
                <option value="">Selecciona un nivel</option>
                <?php foreach ($niveles_result as $nivel): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>" 
                        <?php echo ($nivel['nivel_id'] == ($materia['nivel_id'] ?? '')) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Actualizar Materia</button>
        </form>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>
    </main>
</body>
</html>