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

// Variables para filtros (inicializadas con valores predeterminados)
$grupoSeleccionado = $_GET['grupo'] ?? ''; // Valor predeterminado: cadena vacía
$mostrarTodos = isset($_GET['mostrar_todos']);
$busqueda = $_GET['busqueda'] ?? '';  // Nueva variable para la búsqueda
$result = [];

try {
    // Obtener los grupos correspondientes al nivel del director
    $stmtGrupos = $pdo->prepare("SELECT * FROM grupos WHERE nivel_id = ?");
    $stmtGrupos->execute([$nivelDirector]);
    $grupos = $stmtGrupos->fetchAll(PDO::FETCH_ASSOC);

    // Consultar registros si se solicitan
    if ($grupoSeleccionado || $mostrarTodos || $busqueda) {
        // Consulta base
        $query = "SELECT a.*, n.nivel_nombre, CONCAT(g.grado, ' ', g.turno) AS grupo 
                  FROM alumnos a
                  LEFT JOIN niveles n ON a.nivel_id = n.nivel_id
                  LEFT JOIN grupos g ON a.grupo_id = g.id_grupo
                  WHERE a.nivel_id = ?"; // Solo alumnos del nivel del director

        $filters = [];
        $params = [$nivelDirector]; // Añadir el nivel del director como primer parámetro

        if ($grupoSeleccionado) {
            $filters[] = "a.grupo_id = ?";
            $params[] = $grupoSeleccionado;
        }

        if ($busqueda) {
            $filters[] = "(a.nombres LIKE ? OR a.apellidos LIKE ?)";
            $params[] = "%$busqueda%";  // Búsqueda por aproximación
            $params[] = "%$busqueda%";  // Búsqueda por aproximación
        }

        if ($filters) {
            $query .= " AND " . implode(' AND ', $filters);
        }

        $query .= " ORDER BY g.grado, g.turno, a.apellidos";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Error al consultar los alumnos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Alumnos</title>
    <link rel="stylesheet" href="CSS/gestion_alumnos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<header class="navbar">
    <div class="navbar-container">
        <h1>Consultar Alumnos</h1>
        <div class="navbar-right">
            <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </div>
</header>

<main class="main-container">
    <div class="button-container">
        <a href="agregar_alumno.php" class="control-button">
            <i class="bi bi-person"></i>
            <span>Agregar Alumno</span>
        </a>
        <a href="consultar_alumnos.php" class="control-button">
            <i class="bi bi-person-badge"></i>
            <span>Consultar Alumnos</span>
        </a>
        <a href="dashboard_director.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Panel del director</span>
        </a>
        <a href="promover_alumnos.php" class="control-button">
            <i class="bi bi-arrow-up-circle"></i>
            <span>Promover Alumnos</span>
        </a>
    </div>

    <section class="filter-section">
        <h2>Filtrar por Grupo</h2>
        <form method="GET" action="">
            <label for="grupo">Grupo:</label>
            <select name="grupo" id="grupo" onchange="this.form.submit()">
                <option value="">Todos los grupos</option>
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?php echo $grupo['id_grupo']; ?>" <?php echo $grupoSeleccionado == $grupo['id_grupo'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($grupo['grado'] . ' ' . $grupo['turno']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="mostrar_todos" value="1">Mostrar Todos</button>
        </form>
    </section>

    <!-- Formulario de búsqueda -->
    <section class="search-section">
        <h2>Buscar por Nombre o Apellidos</h2>
        <form method="GET" action="">
            <input type="text" name="busqueda" placeholder="Buscar nombre o apellido" value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">Buscar</button>
        </form>
    </section>

    <?php if (!empty($result)): ?>
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Matrícula</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>Grupo</th>
                    <th>Nivel</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr data-alumno='<?php echo htmlspecialchars(json_encode($row)); ?>'>
                        <td><img src="../uploads/<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto" width="50"></td>
                        <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($row['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombres']); ?></td>
                        <td><?php echo htmlspecialchars($row['grupo']); ?></td> <!-- Nombre del grupo -->
                        <td><?php echo htmlspecialchars($row['nivel_nombre']); ?></td> <!-- Nombre del nivel -->
                        <td>
                            <a href="editar_alumno.php?id=<?php echo $row['alumno_id']; ?>" class="action-button">Editar</a>
                            <a href="eliminar_alumno.php?id=<?php echo $row['alumno_id']; ?>" class="action-button" onclick="return confirm('¿Estás seguro de eliminar este alumno?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron resultados.</p>
    <?php endif; ?>
</main>

<!-- Modal de Detalles del Alumno -->
<div id="modal" class="modal">
    <div class="modal-content">
        <div class="modal-left">
            <img id="modal-student-photo" src="" alt="Foto del Alumno">
        </div>
        <div class="modal-right">
            <h2 id="modal-student-name"></h2>
            <p><strong>Matrícula:</strong> <span id="modal-student-id"></span></p>
            <p><strong>Apellidos:</strong> <span id="modal-student-lastname"></span></p>
            <p><strong>Dirección:</strong> <span id="modal-student-address"></span></p>
            <p><strong>Teléfono:</strong> <span id="modal-student-phone"></span></p>
            <p><strong>Fecha de Nacimiento:</strong> <span id="modal-student-dob"></span></p>
        </div>
        <span class="close" onclick="closeModal()">&times;</span>
    </div>
</div>

<script>
// Event delegation para manejar clics en las filas de la tabla
document.querySelector('tbody').addEventListener('click', function(event) {
    const row = event.target.closest('tr');
    if (row) {
        const alumno = JSON.parse(row.getAttribute('data-alumno'));
        showModal(alumno);
    }
});

function showModal(alumno) {
    document.getElementById("modal-student-photo").src = "../uploads/" + alumno.foto;
    document.getElementById("modal-student-id").textContent = alumno.matricula;
    document.getElementById("modal-student-name").textContent = alumno.nombres + " " + alumno.apellidos;
    document.getElementById("modal-student-lastname").textContent = alumno.apellidos;
    document.getElementById("modal-student-address").textContent = alumno.direccion;
    document.getElementById("modal-student-phone").textContent = alumno.telefono;
    document.getElementById("modal-student-dob").textContent = alumno.fecha_nacimiento;

    document.getElementById("modal").style.display = "flex";
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

window.onclick = function(event) {
    if (event.target == document.getElementById("modal")) {
        closeModal();
    }
}
</script>

</body>
</html>