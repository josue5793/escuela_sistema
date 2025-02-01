<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

require_once '../db.php';

// Obtener el nivel_id del director
try {
    $stmt = $pdo->prepare("SELECT d.nivel_id, n.nivel_nombre 
                           FROM directores d 
                           JOIN niveles n ON d.nivel_id = n.nivel_id 
                           WHERE d.usuario_id = :usuario_id");
    $stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
    $director = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$director) {
        throw new Exception("El director no está asignado a un nivel.");
    }

    $nivel_id_director = $director['nivel_id'];
    $nivel_nombre_director = $director['nivel_nombre'];
} catch (PDOException $e) {
    die("Error al obtener el nivel del director: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}

// Procesar el formulario de agregar grupo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grado = $_POST['grado'];
    $turno = $_POST['turno'];

    try {
        $stmt = $pdo->prepare("INSERT INTO grupos (nivel_id, grado, turno) VALUES (:nivel_id, :grado, :turno)");
        $stmt->execute([
            ':nivel_id' => $nivel_id_director,
            ':grado' => $grado,
            ':turno' => $turno
        ]);

        header("Location: gestion_grupos.php");
        exit;
    } catch (PDOException $e) {
        die("Error al agregar el grupo: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Grupo</title>
    <link rel="stylesheet" href="CSS/gestion_grupos.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Agregar Grupo</h1>
            <div class="navbar-right">
                <span>Director: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="main-container">
        <form method="POST" action="agregar_grupo.php">
            <div class="form-group">
                <label for="grado">Grado:</label>
                <input type="text" id="grado" name="grado" required>
            </div>
            <div class="form-group">
                <label for="turno">Turno:</label>
                <input type="text" id="turno" name="turno" required>
            </div>
            <button type="submit" class="submit-button">Agregar Grupo</button>
        </form>
    </main>
</body>
</html>