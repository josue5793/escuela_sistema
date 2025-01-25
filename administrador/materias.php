<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require '../db.php'; // Asegúrate de que este archivo contenga la conexión PDO adecuada

// Inicializar mensaje de éxito o error
$mensaje = "";

// Procesar el formulario de alta de materia
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
            // Preparar la consulta para insertar la materia con el nivel seleccionado
            $stmt = $pdo->prepare("INSERT INTO materias (nombre, nivel_id) VALUES (:nombre, :nivel_id)");
            $stmt->bindParam(':nombre', $nombre_materia);
            $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $mensaje = "Materia registrada exitosamente.";
            } else {
                $mensaje = "Error al registrar la materia.";
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

// Procesar la búsqueda por nivel
$nivel_filtrado = $_GET['nivel_id'] ?? ''; // Obtener el nivel seleccionado para filtrar
$query = "SELECT m.materia_id, m.nombre, n.nivel_nombre 
          FROM materias m 
          JOIN niveles n ON m.nivel_id = n.nivel_id";
$params = [];

if (!empty($nivel_filtrado)) {
    $query .= " WHERE m.nivel_id = :nivel_id";
    $params[':nivel_id'] = $nivel_filtrado;
}

// Obtener las materias registradas con el filtro si aplica
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener las materias: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Materias</title>
    <link rel="stylesheet" href="CSS/materias2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <main class="main-container">
        <header class="navbar">
            <div class="navbar-container">
                <h1>Agregar Materias</h1>
                <div class="navbar-right">
                    <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
                </div>
            </div>
        </header>

        <section class="welcome-section">
            <h2>Crear nuevas materias</h2>
            <p>Consulta y actualiza la información de las materias</p>
        </section>

        <div class="button-container">
            <a href="administrador_dashboard.php" class="control-button">
                <i class="bi bi-house-door"></i>
                <span>Panel Administrador</span>
            </a>
            <a href="asignar_materias_alumnos.php" class="control-button">
                <i class="bi bi-person"></i>
                <span>Asignar materias a grupos</span>
            </a>
        </div>

        <h1>Alta de Materias</h1>

        <!-- Formulario de registro -->
        <form action="" method="POST" class="formulario">
            <label for="nombre_materia">Nombre de la Materia:</label>
            <input type="text" id="nombre_materia" name="nombre_materia" placeholder="Escribe el nombre de la materia" required>

            <label for="nivel_id">Seleccionar Nivel:</label>
            <select id="nivel_id" name="nivel_id" required>
                <option value="">Selecciona un nivel</option>
                <?php foreach ($niveles_result as $nivel): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>">
                        <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Registrar Materia</button>
        </form>

        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <h2>Filtrar por Nivel</h2>
        <form action="" method="GET" class="formulario">
            <label for="nivel_id">Seleccionar Nivel:</label>
            <select id="nivel_id" name="nivel_id" onchange="this.form.submit()">
                <option value="">Selecciona un nivel</option>
                <?php foreach ($niveles_result as $nivel): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>" <?php echo ($nivel['nivel_id'] == $nivel_filtrado) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <h2>Materias Registradas</h2>
        <?php if (!empty($result)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de la Materia</th>
                        <th>Nivel</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['materia_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['nivel_nombre']); ?></td>
                            <td>
                                <a href="editar_materia.php?id=<?php echo $row['materia_id']; ?>">Editar</a> | 
                                <a href="?delete=<?php echo $row['materia_id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta materia?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay materias registradas.</p>
        <?php endif; ?>
    </main>
</body>
</html>