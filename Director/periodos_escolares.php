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

// Procesar el formulario para agregar un ciclo escolar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_ciclo'])) {
    $nombre_ciclo = $_POST['nombre_ciclo'];
    $fecha_inicio_ciclo = $_POST['fecha_inicio_ciclo'];
    $fecha_fin_ciclo = $_POST['fecha_fin_ciclo'];

    // Validar que las fechas no se solapen con otros ciclos
    try {
        $stmt = $pdo->prepare("SELECT * FROM ciclos_escolares 
                               WHERE nivel_id = :nivel_id 
                               AND (
                                   (fecha_inicio <= :fecha_fin AND fecha_fin >= :fecha_inicio)
                               )");
        $stmt->execute([
            ':nivel_id' => $nivel_id_director,
            ':fecha_inicio' => $fecha_inicio_ciclo,
            ':fecha_fin' => $fecha_fin_ciclo
        ]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("El ciclo escolar se solapa con otro ciclo existente.");
        }

        // Insertar el nuevo ciclo escolar
        $stmt = $pdo->prepare("INSERT INTO ciclos_escolares (nombre, fecha_inicio, fecha_fin, nivel_id) 
                               VALUES (:nombre, :fecha_inicio, :fecha_fin, :nivel_id)");
        $stmt->execute([
            ':nombre' => $nombre_ciclo,
            ':fecha_inicio' => $fecha_inicio_ciclo,
            ':fecha_fin' => $fecha_fin_ciclo,
            ':nivel_id' => $nivel_id_director
        ]);

        header("Location: periodos_escolares.php");
        exit;
    } catch (PDOException $e) {
        die("Error al agregar el ciclo escolar: " . $e->getMessage());
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

// Procesar el formulario para agregar un periodo de evaluación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_periodo'])) {
    $ciclo_id = $_POST['ciclo_id'];
    $nombre_periodo = $_POST['nombre_periodo'];
    $fecha_inicio_periodo = $_POST['fecha_inicio_periodo'];
    $fecha_fin_periodo = $_POST['fecha_fin_periodo'];

    // Validar que las fechas no se solapen con otros periodos del mismo ciclo
    try {
        $stmt = $pdo->prepare("SELECT * FROM periodos 
                               WHERE ciclo_id = :ciclo_id 
                               AND (
                                   (fecha_inicio <= :fecha_fin AND fecha_fin >= :fecha_inicio)
                               )");
        $stmt->execute([
            ':ciclo_id' => $ciclo_id,
            ':fecha_inicio' => $fecha_inicio_periodo,
            ':fecha_fin' => $fecha_fin_periodo
        ]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("El periodo se solapa con otro periodo existente.");
        }

        // Insertar el nuevo periodo
        $stmt = $pdo->prepare("INSERT INTO periodos (nombre, fecha_inicio, fecha_fin, ciclo_id) 
                               VALUES (:nombre, :fecha_inicio, :fecha_fin, :ciclo_id)");
        $stmt->execute([
            ':nombre' => $nombre_periodo,
            ':fecha_inicio' => $fecha_inicio_periodo,
            ':fecha_fin' => $fecha_fin_periodo,
            ':ciclo_id' => $ciclo_id
        ]);

        header("Location: periodos_escolares.php");
        exit;
    } catch (PDOException $e) {
        die("Error al agregar el periodo: " . $e->getMessage());
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

// Obtener los ciclos escolares y periodos del nivel del director
try {
    // Obtener ciclos escolares
    $stmt = $pdo->prepare("SELECT * FROM ciclos_escolares WHERE nivel_id = :nivel_id ORDER BY fecha_inicio DESC");
    $stmt->execute([':nivel_id' => $nivel_id_director]);
    $ciclos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener periodos de cada ciclo
    foreach ($ciclos as &$ciclo) {
        $stmt = $pdo->prepare("SELECT * FROM periodos WHERE ciclo_id = :ciclo_id ORDER BY fecha_inicio ASC");
        $stmt->execute([':ciclo_id' => $ciclo['ciclo_id']]);
        $ciclo['periodos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error al obtener los ciclos y periodos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Periodos Escolares</title>
    <link rel="stylesheet" href="CSS/periodos_escolares3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Periodos Escolares</h1>
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
    </div>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Bienvenida y descripción -->
        <section class="welcome-section">
            <h2>Bienvenid@, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
            <p>
                En este apartado puedes gestionar los ciclos escolares y periodos de evaluación para el nivel 
                <strong><?php echo htmlspecialchars($nivel_nombre_director); ?></strong>. Aquí puedes agregar nuevos ciclos, 
                definir periodos de evaluación y editar la información existente.
            </p>
        </section>

        <!-- Formulario para agregar ciclo escolar -->
        <form method="POST" action="periodos_escolares.php" class="filter-form">
            <h2>Agregar Ciclo Escolar</h2>
            <div class="form-group">
                <label for="nombre_ciclo">Nombre del Ciclo:</label>
                <input type="text" id="nombre_ciclo" name="nombre_ciclo" required>
            </div>
            <div class="form-group">
                <label for="fecha_inicio_ciclo">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio_ciclo" name="fecha_inicio_ciclo" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin_ciclo">Fecha de Fin:</label>
                <input type="date" id="fecha_fin_ciclo" name="fecha_fin_ciclo" required>
            </div>
            <button type="submit" name="agregar_ciclo" class="filter-button">Agregar Ciclo</button>
        </form>

        <!-- Formulario para agregar periodo de evaluación -->
        <form method="POST" action="periodos_escolares.php" class="filter-form">
            <h2>Agregar Periodo de Evaluación</h2>
            <div class="form-group">
                <label for="ciclo_id">Ciclo Escolar:</label>
                <select id="ciclo_id" name="ciclo_id" required>
                    <?php foreach ($ciclos as $ciclo): ?>
                        <option value="<?php echo $ciclo['ciclo_id']; ?>">
                            <?php echo htmlspecialchars($ciclo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nombre_periodo">Nombre del Periodo:</label>
                <input type="text" id="nombre_periodo" name="nombre_periodo" required>
            </div>
            <div class="form-group">
                <label for="fecha_inicio_periodo">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio_periodo" name="fecha_inicio_periodo" required>
            </div>
            <div class="form-group">
                <label for="fecha_fin_periodo">Fecha de Fin:</label>
                <input type="date" id="fecha_fin_periodo" name="fecha_fin_periodo" required>
            </div>
            <button type="submit" name="agregar_periodo" class="filter-button">Agregar Periodo</button>
        </form>

        <!-- Listado de ciclos y periodos -->
        <h2>Ciclos Escolares y Periodos</h2>
        <?php if (!empty($ciclos)): ?>
            <?php foreach ($ciclos as $ciclo): ?>
                <div class="ciclo-container">
                    <h3><?php echo htmlspecialchars($ciclo['nombre']); ?></h3>
                    <p><strong>Fechas:</strong> <?php echo htmlspecialchars($ciclo['fecha_inicio']); ?> a <?php echo htmlspecialchars($ciclo['fecha_fin']); ?></p>
                    <?php if (!empty($ciclo['periodos'])): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Fechas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ciclo['periodos'] as $periodo): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($periodo['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($periodo['fecha_inicio']); ?> a <?php echo htmlspecialchars($periodo['fecha_fin']); ?></td>
                                        <td>
                                            <a href="editar_periodo.php?id=<?php echo $periodo['periodo_id']; ?>" class="action-button edit">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay periodos registrados para este ciclo.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay ciclos escolares registrados.</p>
        <?php endif; ?>
    </main>
</body>
</html>