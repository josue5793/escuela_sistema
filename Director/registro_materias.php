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

// Procesar el formulario de alta de materias
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_materia']) && isset($_POST['grupos'])) {
    $nombre_materia = trim($_POST['nombre_materia']);
    $grupos_seleccionados = $_POST['grupos']; // Array de grupos seleccionados

    if (!empty($nombre_materia) && !empty($grupos_seleccionados)) {
        // Insertar la nueva materia
        $query = "INSERT INTO materias (nombre, nivel_id) VALUES (:nombre, :nivel_id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nombre', $nombre_materia, PDO::PARAM_STR);
        $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener el ID de la materia recién insertada
        $materia_id = $pdo->lastInsertId();

        // Insertar las relaciones en materia_grupo
        foreach ($grupos_seleccionados as $grupo_id) {
            $query = "INSERT INTO materia_grupo (materia_id, grupo_id) VALUES (:materia_id, :grupo_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        $mensaje = "Materia registrada exitosamente.";
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}

// Obtener las materias asociadas al nivel del director
$query = "SELECT m.materia_id, m.nombre, GROUP_CONCAT(g.grado, ' - ', g.turno SEPARATOR ', ') AS grupos
          FROM materias m
          JOIN materia_grupo mg ON m.materia_id = mg.materia_id
          JOIN grupos g ON mg.grupo_id = g.id_grupo
          WHERE m.nivel_id = :nivel_id
          GROUP BY m.materia_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
$stmt->execute();
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Materias</title>
    <link rel="stylesheet" href="CSS/registro_materias.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra superior -->
    <header class="navbar">
        <div class="navbar-left">
            <span>Registro de Materias</span>
        </div>

        <div class="navbar-right">
            <span>Bienvenido, <?php echo htmlspecialchars($director_id); ?></span>  <!-- corregir el nombre del director -->
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </header>
    <main class="main-container">
        <h1>Registro de Materias</h1>
        
<!--Botones de navegacion -->
<div class="button-container">
            <a href="dashboard_director.php" class="control-button">
                <i class="bi bi-house-door"></i>
                <span>Panel principal</span>
            </a>   
            <a href="gestion_usuarios.php" class="control-button">
                <i class="bi bi-person-plus"></i>
                <span>Agregar Profesor</span>
            </a>
            <a href="asignar_materias_profesor.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Asignar profesor a materias</span>
            </a>
            <a href="consultar_profesores.php" class="control-button">
                <i class="bi bi-people"></i>
                <span>Consultar Profesores</span>
            </a>
            <a href="registro_materias.php" class="control-button">
                <i class="bi bi-backpack3"></i>
                <span>Registro de Materias</span>
            </a>

        </div>
    
    </main>

    <div class="container">
        

        <?php if (isset($mensaje)): ?>
            <div class="mensaje exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="mensaje error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Formulario de registro de materias -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="nombre_materia">Nombre de la Materia:</label>
                <input type="text" id="nombre_materia" name="nombre_materia" required>
            </div>
            <div class="form-group">
                <label>Grupos:</label>
                <?php foreach ($grupos as $grupo): ?>
                    <div>
                        <input type="checkbox" name="grupos[]" value="<?php echo $grupo['id_grupo']; ?>">
                        Grado <?php echo $grupo['grado']; ?> - <?php echo $grupo['turno']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="form-group">
                <button type="submit">Registrar Materia</button>
            </div>
        </form>

        <!-- Listado de materias registradas -->
        <h2>Materias Registradas</h2>
        <?php if (count($materias) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Grupos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($materias as $materia): ?>
                        <tr>
                            <td><?php echo $materia['materia_id']; ?></td>
                            <td><?php echo $materia['nombre']; ?></td>
                            <td><?php echo $materia['grupos']; ?></td>
                            <td class="acciones">
                                <a href="editar_materia.php?editar=<?php echo $materia['materia_id']; ?>">Editar</a>
                                <a href="?eliminar=<?php echo $materia['materia_id']; ?>" onclick="return confirm('¿Estás seguro de eliminar esta materia?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay materias registradas para este nivel.</p>
        <?php endif; ?>
    </div>
</body>
</html>