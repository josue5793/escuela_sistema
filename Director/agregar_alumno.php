<?php
session_start();
require_once '../db.php';

// Verificar si el usuario está logueado y es director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: login.php");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];

// Consultar el nivel del director en la base de datos
$stmt = $pdo->prepare("SELECT nivel_id FROM directores WHERE usuario_id = ?");
$stmt->execute([$usuarioId]);
$director = $stmt->fetch(PDO::FETCH_ASSOC);

if ($director && $director['nivel_id']) {
    $nivelDirector = $director['nivel_id'];
} else {
    die("Error: No se pudo determinar el nivel del director.");
}


// Obtener los grupos disponibles dentro del nivel del director
$stmtGrupos = $pdo->prepare("SELECT id_grupo, grado, turno FROM grupos WHERE nivel_id = ?");
$stmtGrupos->execute([$nivelDirector]);
$grupos = $stmtGrupos->fetchAll(PDO::FETCH_ASSOC);

// Función para generar matrícula automática
function generarMatricula($pdo) {
    $stmt = $pdo->query("SELECT MAX(alumno_id) AS max_id FROM alumnos");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $numero = $row['max_id'] ? $row['max_id'] + 1 : 1;
    return 'A0' . $numero;
}

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $direccion = trim($_POST['direccion']) ?: null;
    $telefono = trim($_POST['telefono']) ?: null;
    $grupo_id = $_POST['grupo_id'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?: null;
    $matricula = generarMatricula($pdo);

    // Verificar si el alumno ya existe
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM alumnos WHERE nombres = ? AND apellidos = ?");
    $stmtCheck->execute([$nombres, $apellidos]);
    if ($stmtCheck->fetchColumn() > 0) {
        echo "<script>alert('El alumno ya existe en la base de datos.');</script>";
    } else {
        // Insertar el alumno
        $stmt = $pdo->prepare("INSERT INTO alumnos (matricula, nombres, apellidos, direccion, telefono, grupo_id, nivel_id, fecha_nacimiento, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$matricula, $nombres, $apellidos, $direccion, $telefono, $grupo_id, $nivelDirector, $fecha_nacimiento]);
        echo "<script>alert('Alumno agregado exitosamente.'); window.location='gestion_alumnos.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Alumno</title>
    <link rel="stylesheet" href="CSS/gestion_alumnos.css">
</head>
<body>
    <h2>Agregar Alumno</h2>
    <form method="POST">
        <label>Nombres:</label>
        <input type="text" name="nombres" required>
        
        <label>Apellidos:</label>
        <input type="text" name="apellidos" required>
        
        <label>Dirección:</label>
        <input type="text" name="direccion">
        
        <label>Teléfono:</label>
        <input type="text" name="telefono">
        
        <label>Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento">
        
        <label>Grupo:</label>
        <select name="grupo_id" required>
            <?php foreach ($grupos as $grupo): ?>
                <option value="<?php echo $grupo['id_grupo']; ?>">
                    <?php echo htmlspecialchars($grupo['grado'] . ' ' . $grupo['turno']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Agregar Alumno</button>
    </form>
</body>
</html>