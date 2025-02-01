<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once '../db.php';

// Verificar si se ha recibido el ID del grupo a editar
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo "ID de grupo no especificado o inválido.";
    exit;
}

$grupo_id = intval($_GET['id']);

try {
    // Obtener datos del grupo de la base de datos
    $sql = "SELECT g.id_grupo, g.nivel_id, g.grado, g.turno, n.nivel_nombre 
            FROM grupos g
            JOIN niveles n ON g.nivel_id = n.nivel_id
            WHERE g.id_grupo = :grupo_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
    $stmt->execute();
    $grupo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$grupo) {
        echo "Grupo no encontrado.";
        exit;
    }

    // Obtener la lista de niveles disponibles
    $niveles_query = "SELECT nivel_id, nivel_nombre FROM niveles";
    $niveles_stmt = $pdo->query($niveles_query);
    $niveles = $niveles_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar el formulario cuando se envíen los datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitizar y validar entradas
        $nivel_id = filter_var($_POST['nivel_id'] ?? '', FILTER_VALIDATE_INT);
        $grado = htmlspecialchars(trim($_POST['grado'] ?? ''));
        $turno = htmlspecialchars(trim($_POST['turno'] ?? ''));

        if (!$nivel_id || !$grado || !$turno) {
            echo "Por favor, complete todos los campos obligatorios.";
        } else {
            // Actualizar el grupo en la base de datos
            $sql = "UPDATE grupos SET nivel_id = :nivel_id, grado = :grado, turno = :turno WHERE id_grupo = :grupo_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
            $stmt->bindParam(':grado', $grado, PDO::PARAM_STR);
            $stmt->bindParam(':turno', $turno, PDO::PARAM_STR);
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: gestionar_grupos.php?success=1");
                exit;
            } else {
                throw new Exception("Error al actualizar el grupo.");
            }
        }
    }
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Grupo</title>
    <link rel="stylesheet" href="CSS/editar_grupos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Editar Grupo</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <section class="welcome-section">
            <h2>Editar Grupo</h2>
            <p>Modifique la información del grupo según sea necesario.</p>
        </section>
        <div class="button-container">
        <!-- Botón para ir al Panel del Administrador -->
        <a href="administrador_dashboard.php" class="control-button">
            <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
            <span>Panel Administrador</span>
        </a>

        <!-- Botón para agregar un nuevo grupo -->
        <a href="agregar_grupo.php" class="control-button">
            <i class="bi bi-plus-circle"></i> <!-- Ícono de agregar -->
            <span>Agregar Nuevo Grupo</span>
        </a>
    </div>
        <!-- Formulario de edición -->
        <form action="" method="POST" class="form-container">
            <div class="form-group">
                <label for="nivel_id">Nivel:</label>
                <select name="nivel_id" id="nivel_id" required>
                    <?php foreach ($niveles as $nivel): ?>
                        <option value="<?php echo $nivel['nivel_id']; ?>" 
                            <?php echo ($nivel['nivel_id'] == $grupo['nivel_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="grado">Grado:</label>
                <input type="text" name="grado" id="grado" value="<?php echo htmlspecialchars($grupo['grado']); ?>" required>
            </div>

            <div class="form-group">
                <label for="turno">Turno:</label>
                <input type="text" name="turno" id="turno" value="<?php echo htmlspecialchars($grupo['turno']); ?>" required>
            </div>

            <button type="submit" class="form-submit">Guardar Cambios</button>
        </form>
    </main>
</body>
</html>