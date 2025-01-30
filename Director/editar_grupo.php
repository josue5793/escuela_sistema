<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

require_once '../db.php';

// Obtener el ID del grupo a editar
$grupo_id = $_GET['id'] ?? null;

if (!$grupo_id) {
    header("Location: gestion_grupos.php");
    exit;
}

// Obtener los datos del grupo
try {
    $stmt = $pdo->prepare("SELECT * FROM grupos WHERE id_grupo = :id_grupo");
    $stmt->execute([':id_grupo' => $grupo_id]);
    $grupo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$grupo) {
        throw new Exception("Grupo no encontrado.");
    }
} catch (PDOException $e) {
    die("Error al obtener el grupo: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grado = $_POST['grado'];
    $turno = $_POST['turno'];

    try {
        $stmt = $pdo->prepare("UPDATE grupos SET grado = :grado, turno = :turno WHERE id_grupo = :id_grupo");
        $stmt->execute([
            ':grado' => $grado,
            ':turno' => $turno,
            ':id_grupo' => $grupo_id
        ]);

        header("Location: gestion_grupos.php");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar el grupo: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Grupo</title>
    <link rel="stylesheet" href="CSS/gestion_grupos.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Editar Grupo</h1>
            <div class="navbar-right">
                <span>Director: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="main-container">
        <form method="POST" action="editar_grupo.php?id=<?php echo $grupo_id; ?>">
            <div class="form-group">
                <label for="grado">Grado:</label>
                <input type="text" id="grado" name="grado" value="<?php echo htmlspecialchars($grupo['grado']); ?>" required>
            </div>
            <div class="form-group">
                <label for="turno">Turno:</label>
                <input type="text" id="turno" name="turno" value="<?php echo htmlspecialchars($grupo['turno']); ?>" required>
            </div>
            <button type="submit" class="submit-button">Guardar Cambios</button>
        </form>
    </main>
</body>
</html>