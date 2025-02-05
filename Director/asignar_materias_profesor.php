<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

// Incluir la conexión a la base de datos
require_once '../db.php';

// Función para obtener el nivel del director
function obtener_nivel_director($usuario_id, $pdo) {
    $query = "SELECT nivel_id FROM directores WHERE usuario_id = :usuario_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['nivel_id'];
    }
    
    return null;
}

// Obtener el nivel del director autenticado
$nivel_director = obtener_nivel_director($_SESSION['usuario_id'], $pdo);

if ($nivel_director === null) {
    die("Error: No se pudo obtener el nivel del director.");
}

// Obtener la lista de profesores del nivel del director
$query = "SELECT p.profesor_id, u.nombre 
          FROM profesores p
          JOIN usuarios u ON p.usuario_id = u.usuario_id
          JOIN profesor_nivel pn ON p.profesor_id = pn.profesor_id
          WHERE pn.nivel_id = :nivel_id";

$stmt = $pdo->prepare($query);
$stmt->bindParam(":nivel_id", $nivel_director, PDO::PARAM_INT);
$stmt->execute();
$profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener la lista de materias del nivel del director
$query = "SELECT materia_id, nombre FROM materias WHERE nivel_id = :nivel_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":nivel_id", $nivel_director, PDO::PARAM_INT);
$stmt->execute();
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar la asignación de materias
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profesor_id = $_POST['profesor_id'];
    $materia_id = $_POST['materia_id'];
    $periodo_id = $_POST['periodo_id'];

    if (!empty($profesor_id) && !empty($materia_id) && !empty($periodo_id)) {
        $query = "INSERT INTO profesor_materia (profesor_id, materia_id, periodo_id) VALUES (:profesor_id, :materia_id, :periodo_id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":profesor_id", $profesor_id, PDO::PARAM_INT);
        $stmt->bindParam(":materia_id", $materia_id, PDO::PARAM_INT);
        $stmt->bindParam(":periodo_id", $periodo_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Materia asignada con éxito.</p>";
        } else {
            echo "<p style='color: red;'>Error al asignar la materia.</p>";
        }
    } else {
        echo "<p style='color: red;'>Todos los campos son obligatorios.</p>";
    }
}

// Obtener los periodos activos
$query = "SELECT periodo_id, nombre FROM periodos WHERE activo = 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$periodos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Materias a Profesores</title>
</head>
<body>
    <h2>Asignar Materias a Profesores</h2>
    
    <form method="POST">
        <label for="profesor_id">Profesor:</label>
        <select name="profesor_id" required>
            <option value="">Seleccione un profesor</option>
            <?php foreach ($profesores as $profesor): ?>
                <option value="<?= $profesor['profesor_id']; ?>"><?= $profesor['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="materia_id">Materia:</label>
        <select name="materia_id" required>
            <option value="">Seleccione una materia</option>
            <?php foreach ($materias as $materia): ?>
                <option value="<?= $materia['materia_id']; ?>"><?= $materia['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="periodo_id">Periodo:</label>
        <select name="periodo_id" required>
            <option value="">Seleccione un periodo</option>
            <?php foreach ($periodos as $periodo): ?>
                <option value="<?= $periodo['periodo_id']; ?>"><?= $periodo['nombre']; ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Asignar Materia</button>
    </form>
</body>
</html>
