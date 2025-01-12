<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require 'db.php'; // Asegúrate de que este archivo contiene la conexión adecuada como se describe arriba

// Inicializar mensaje de éxito o error
$mensaje = "";

// Procesar el formulario de alta de materia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario
    $nombre_materia = trim($_POST['nombre_materia']);
    $nivel_id = $_POST['nivel_id']; // Recibir el nivel seleccionado

    // Validar que el nombre no esté vacío y que el nivel esté seleccionado
    if (empty($nombre_materia)) {
        $mensaje = "El nombre de la materia es obligatorio.";
    } elseif (empty($nivel_id)) {
        $mensaje = "El nivel de la materia es obligatorio.";
    } else {
        // Preparar la consulta para insertar la materia con el nivel seleccionado
        $stmt = $conn->prepare("INSERT INTO materias (nombre, nivel_id) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("si", $nombre_materia, $nivel_id);
            if ($stmt->execute()) {
                $mensaje = "Materia registrada exitosamente.";
            } else {
                $mensaje = "Error al registrar la materia: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensaje = "Error en la consulta: " . $conn->error;
        }
    }
}

// Obtener todos los niveles para el formulario de búsqueda y para el formulario de alta
$niveles_result = $conn->query("SELECT nivel_id, nivel_nombre FROM niveles");

// Procesar la búsqueda por nivel
$nivel_filtrado = isset($_GET['nivel_id']) ? $_GET['nivel_id'] : ''; // Obtener el nivel seleccionado para filtrar

// Modificar la consulta para filtrar por nivel si se seleccionó uno
$query = "SELECT m.materia_id, m.nombre, n.nivel_nombre FROM materias m JOIN niveles n ON m.nivel_id = n.nivel_id";

if (!empty($nivel_filtrado)) {
    $query .= " WHERE m.nivel_id = ?";
}

// Obtener las materias registradas con el filtro si aplica
$stmt = $conn->prepare($query);

if (!empty($nivel_filtrado)) {
    $stmt->bind_param("i", $nivel_filtrado); // Filtrar por nivel_id
}

$stmt->execute();
$result = $stmt->get_result();
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
    <!-- Menú de navegación -->
   
    <main class="main-container">
        <header class="navbar">
            <div class="navbar-container">
                <h1>Agregar Materias</h1>
                <div class="navbar-right">
                    <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    <a href="logout.php" class="logout-button">Cerrar Sesión</a>
                </div>
            </div>
        </header>
        <section class="welcome-section">
            <h2>Crear nuevas materias</h2>
            <p>Consulta y actualiza la información de las materias</p>
        </section>

        <a href="administrador.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Panel Administrador</span>
        </a>

        <!-- Contenido principal -->
        <h1>Alta de Materias</h1>

        <!-- Formulario de registro -->
        <form action="" method="POST" class="formulario">
            <label for="nombre_materia">Nombre de la Materia:</label>
            <input type="text" id="nombre_materia" name="nombre_materia" placeholder="Escribe el nombre de la materia" required>

            <label for="nivel_id">Seleccionar Nivel:</label>
            <select id="nivel_id" name="nivel_id" required>
                <option value="">Selecciona un nivel</option>
                <?php
                // Recorrer los niveles y mostrar como opciones
                $niveles_result->data_seek(0); // Resetear el puntero de resultados
                while ($nivel = $niveles_result->fetch_assoc()): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>" <?php echo ($nivel['nivel_id'] == $nivel_filtrado) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Registrar Materia</button>
        </form>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (!empty($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <!-- Formulario de búsqueda por nivel -->
        <h2>Filtrar por Nivel</h2>
        <form action="" method="GET" class="formulario">
            <label for="nivel_id">Seleccionar Nivel:</label>
            <select id="nivel_id" name="nivel_id" onchange="this.form.submit()">
                <option value="">Selecciona un nivel</option>
                <?php
                // Recorrer los niveles y mostrar como opciones
                $niveles_result->data_seek(0); // Resetear el puntero de resultados
                while ($nivel = $niveles_result->fetch_assoc()): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>" <?php echo ($nivel['nivel_id'] == $nivel_filtrado) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <!-- Tabla de materias registradas -->
        <h2>Materias Registradas</h2>
        <?php if ($result->num_rows > 0): ?>
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
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['materia_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['nivel_nombre']); ?></td>
                            <td>
                                <!-- Enlaces de editar y eliminar -->
                                <a href="editar_materia.php?id=<?php echo $row['materia_id']; ?>">Editar</a> | 
                                <a href="?delete=<?php echo $row['materia_id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta materia?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay materias registradas.</p>
        <?php endif; ?>
    </main>
</body>
</html>
