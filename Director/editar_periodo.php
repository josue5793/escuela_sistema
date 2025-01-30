<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

require_once '../db.php';

// Obtener el ID del periodo a editar
$periodo_id = $_GET['id'] ?? null;

if (!$periodo_id) {
    header("Location: periodos_escolares.php");
    exit;
}

// Obtener los datos del periodo
try {
    $stmt = $pdo->prepare("SELECT p.*, c.nivel_id 
                           FROM periodos p 
                           JOIN ciclos_escolares c ON p.ciclo_id = c.ciclo_id 
                           WHERE p.periodo_id = :periodo_id");
    $stmt->execute([':periodo_id' => $periodo_id]);
    $periodo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$periodo) {
        throw new Exception("Periodo no encontrado.");
    }

    // Verificar que el periodo pertenezca al nivel del director
    $stmt = $pdo->prepare("SELECT nivel_id FROM directores WHERE usuario_id = :usuario_id");
    $stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
    $director = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($periodo['nivel_id'] !== $director['nivel_id']) {
        throw new Exception("No tienes permisos para editar este periodo.");
    }
} catch (PDOException $e) {
    die("Error al obtener el periodo: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_periodo = $_POST['nombre_periodo'];
    $fecha_inicio_periodo = $_POST['fecha_inicio_periodo'];
    $fecha_fin_periodo = $_POST['fecha_fin_periodo'];

    // Validar que las fechas no se solapen con otros periodos del mismo ciclo
    try {
        $stmt = $pdo->prepare("SELECT * FROM periodos 
                               WHERE ciclo_id = :ciclo_id 
                               AND periodo_id != :periodo_id 
                               AND (
                                   (fecha_inicio <= :fecha_fin AND fecha_fin >= :fecha_inicio)
                               )");
        $stmt->execute([
            ':ciclo_id' => $periodo['ciclo_id'],
            ':periodo_id' => $periodo_id,
            ':fecha_inicio' => $fecha_inicio_periodo,
            ':fecha_fin' => $fecha_fin_periodo
        ]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("El periodo se solapa con otro periodo existente.");
        }

        // Actualizar el periodo
        $stmt = $pdo->prepare("UPDATE periodos 
                               SET nombre = :nombre, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin 
                               WHERE periodo_id = :periodo_id");
        $stmt->execute([
            ':nombre' => $nombre_periodo,
            ':fecha_inicio' => $fecha_inicio_periodo,
            ':fecha_fin' => $fecha_fin_periodo,
            ':periodo_id' => $periodo_id
        ]);

        header("Location: periodos_escolares.php");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar el periodo: " . $e->getMessage());
    } catch (Exception $e) {
        die($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Periodo</title>
    <link rel="stylesheet" href="CSS/editar_periodo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Editar Periodo</h1>
            <div class="navbar-right">
                <span>Director: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenedor de botones de navegación -->
    <div class="button-container">
        <!-- Botón para regresar al panel del director -->
        <a href="dashboard_director.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Panel del Director</span>
        </a>
        <!-- Botón para regresar a la gestión de periodos -->
        <a href="periodos_escolares.php" class="control-button">
            <i class="bi bi-arrow-left"></i>
            <span>Regresar</span>
        </a>
    </div>

    <!-- Contenido Principal -->
    <main class="main-container">
        <h2>Editar Periodo: <?php echo htmlspecialchars($periodo['nombre']); ?></h2>

        <!-- Formulario para editar el periodo -->
        <form method="POST" action="editar_periodo.php?id=<?php echo $periodo_id; ?>">
            <div class="form-group">
                <label for="nombre_periodo">Nombre del Periodo:</label>
                <input type="text" id="nombre_periodo" name="nombre_periodo" value="<?php echo htmlspecialchars($periodo['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_inicio_periodo">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio_periodo" name="fecha_inicio_periodo" value="<?php echo htmlspecialchars($periodo['fecha_inicio']); ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin_periodo">Fecha de Fin:</label>
                <input type="date" id="fecha_fin_periodo" name="fecha_fin_periodo" value="<?php echo htmlspecialchars($periodo['fecha_fin']); ?>" required>
            </div>
            <button type="submit" class="filter-button">Guardar Cambios</button>
        </form>
    </main>
</body>
</html>