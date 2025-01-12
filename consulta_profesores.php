<?php
session_start();

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Función auxiliar para escapar datos
function escapar_datos($dato) {
    return htmlspecialchars($dato ?? '', ENT_QUOTES, 'UTF-8');
}

// Obtener los filtros de búsqueda si están definidos
$busqueda_nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$busqueda_nivel = isset($_GET['nivel']) ? (int)$_GET['nivel'] : 0;

// Construir la consulta para obtener profesores, sus niveles y materias
$query_profesores = "
    SELECT 
        p.profesor_id, 
        u.nombre AS profesor_nombre, 
        u.correo AS profesor_correo, 
        p.telefono AS profesor_telefono, 
        p.especialidad, 
        n.nivel_nombre, 
        n.nivel_id, 
        GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.nombre SEPARATOR ', ') AS materias
    FROM profesores p
    LEFT JOIN usuarios u ON p.usuario_id = u.usuario_id
    LEFT JOIN profesor_nivel pn ON p.profesor_id = pn.profesor_id
    LEFT JOIN niveles n ON pn.nivel_id = n.nivel_id
    LEFT JOIN profesor_materia pm ON p.profesor_id = pm.profesor_id
    LEFT JOIN materias m ON pm.materia_id = m.materia_id AND m.nivel_id = n.nivel_id
    WHERE 1=1
";

$params = [];
if (!empty($busqueda_nombre)) {
    $query_profesores .= " AND u.nombre LIKE ?";
    $params[] = '%' . $busqueda_nombre . '%';
}
if ($busqueda_nivel > 0) {
    $query_profesores .= " AND n.nivel_id = ?";
    $params[] = $busqueda_nivel;
}

$query_profesores .= " GROUP BY p.profesor_id, n.nivel_id ORDER BY u.nombre, n.nivel_nombre";

// Preparar la consulta
$stmt = $conn->prepare($query_profesores);
if ($params) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Obtener los niveles para el filtro
$query_niveles = "SELECT nivel_id, nivel_nombre FROM niveles";
$result_niveles = mysqli_query($conn, $query_niveles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Profesores</title>
    <link rel="stylesheet" href="CSS/gestionar_profesores2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Consulta de Profesores</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo escapar_datos($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <main class="main-container">
        <section class="welcome-section">
            <h2>Profesores</h2>
            <p>Consulta los profesores, sus niveles y materias asignadas.</p>
        </section>

        <div class="button-container">
        <a href="administrador.php" class="control-button">
                <i class="bi bi-house-door"></i>
                <span>Regresar</span>
            </a>
            <a href="consulta_profesores.php" class="control-button">
                <i class="bi bi-person-lines-fill"></i>
                <span>Consulta de profesores y especialidad</span>
            </a>

            <!-- Botón para asignación de niveles -->
            <a href="asignar_niveles.php" class="control-button">
                <i class="bi bi-pen"></i>
                <span>Asignación de Niveles</span>
            </a>

            <!-- Botón para asignar materias -->
            <a href="asignar_materias.php" class="control-button">
                <i class="bi bi-book"></i>
                <span>Asignar Materias</span>
            </a>
        </div>

        <form method="GET" class="search-form">
            <label for="nombre">Buscar por nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo escapar_datos($busqueda_nombre); ?>">

            <label for="nivel">Filtrar por nivel:</label>
            <select id="nivel" name="nivel">
                <option value="0">Todos los niveles</option>
                <?php while ($nivel = mysqli_fetch_assoc($result_niveles)) : ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>" <?php echo $busqueda_nivel == $nivel['nivel_id'] ? 'selected' : ''; ?>>
                        <?php echo escapar_datos($nivel['nivel_nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Buscar</button>
        </form>

        <?php if ($resultado && $resultado->num_rows > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>Nivel</th>
                        <th>Materias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($profesor = $resultado->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo escapar_datos($profesor['profesor_nombre']); ?></td>
                            <td><?php echo escapar_datos($profesor['profesor_correo']); ?></td>
                            <td><?php echo escapar_datos($profesor['profesor_telefono']); ?></td>
                            <td><?php echo escapar_datos($profesor['especialidad']); ?></td>
                            <td><?php echo escapar_datos($profesor['nivel_nombre']); ?></td>
                            <td><?php echo escapar_datos($profesor['materias'] ?: 'Sin materias asignadas'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No se encontraron resultados para la búsqueda realizada.</p>
        <?php endif; ?>
    </main>
</body>
</html>
