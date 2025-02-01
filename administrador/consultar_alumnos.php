<?php
session_start();
require_once '../db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Variables para filtros (inicializadas con valores predeterminados)
$nivelSeleccionado = $_GET['nivel'] ?? ''; // Valor predeterminado: cadena vacía
$grupoSeleccionado = $_GET['grupo'] ?? ''; // Valor predeterminado: cadena vacía
$mostrarTodos = isset($_GET['mostrar_todos']);
$busqueda = $_GET['busqueda'] ?? '';  // Nueva variable para la búsqueda
$result = [];

try {
    // Obtener niveles
    $niveles = $pdo->query("SELECT * FROM niveles")->fetchAll(PDO::FETCH_ASSOC);

    // Si se ha seleccionado un nivel, obtener los grupos correspondientes
    $grupos = [];
    if ($nivelSeleccionado) {
        $stmtGrupos = $pdo->prepare("SELECT * FROM grupos WHERE nivel_id = ?");
        $stmtGrupos->execute([$nivelSeleccionado]);
        $grupos = $stmtGrupos->fetchAll(PDO::FETCH_ASSOC);
    }

    // Consultar registros si se solicitan
    if ($nivelSeleccionado || $grupoSeleccionado || $mostrarTodos || $busqueda) {
        // Consulta base
        $query = "SELECT a.*, n.nivel_nombre, CONCAT(g.grado, ' ', g.turno) AS grupo 
                  FROM alumnos a
                  LEFT JOIN niveles n ON a.nivel_id = n.nivel_id
                  LEFT JOIN grupos g ON a.grupo_id = g.id_grupo";

        $filters = [];
        $params = [];

        if ($nivelSeleccionado) {
            $filters[] = "a.nivel_id = ?";
            $params[] = $nivelSeleccionado;
        }

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
            $query .= " WHERE " . implode(' AND ', $filters);
        }

        $query .= " ORDER BY n.nivel_nombre, g.grado, g.turno, a.apellidos";

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
    <link rel="stylesheet" href="CSS/consultar_alumnos2.css">
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
        <a href="administrador_dashboard.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Panel Administrador</span>
        </a>
        <a href="promover_alumnos.php" class="control-button">
            <i class="bi bi-arrow-up-circle"></i>
            <span>Promover Alumnos</span>
        </a>
    </div>

    <section class="filter-section">
        <h2>Filtrar por Nivel y Grupo</h2>
        <form method="GET" action="">
            <label for="nivel">Nivel:</label>
            <select name="nivel" id="nivel" onchange="this.form.submit()">
                <option value="">Seleccione un nivel</option>
                <?php foreach ($niveles as $nivel): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>" <?php echo $nivelSeleccionado == $nivel['nivel_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($nivelSeleccionado): ?>
                <label for="grupo">Grupo:</label>
                <select name="grupo" id="grupo" onchange="this.form.submit()">
                    <option value="">Todos los grupos</option>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?php echo $grupo['id_grupo']; ?>" <?php echo $grupoSeleccionado == $grupo['id_grupo'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($grupo['grado'] . ' ' . $grupo['turno']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

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

    <?php if ($mostrarTodos): ?>
        <?php
        // Agrupar resultados por nivel y grupo
        $clasificados = [];
        foreach ($result as $row) {
            $clasificados[$row['nivel_nombre']][$row['grupo']][] = $row;
        }
        ?>
        
        <?php foreach ($clasificados as $nivel => $grupos): ?>
            <h2>Nivel: <?php echo htmlspecialchars($nivel); ?></h2>
            <?php foreach ($grupos as $grupo => $alumnos): ?>
                <h3>Grupo: <?php echo htmlspecialchars($grupo); ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Matrícula</th>
                            <th>Apellidos</th>
                            <th>Nombres</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumnos as $alumno): ?>
                            <tr data-alumno='<?php echo htmlspecialchars(json_encode($alumno)); ?>'>
                                <td><img src="../uploads/<?php echo htmlspecialchars($alumno['foto']); ?>" alt="Foto del Alumno" width="50"></td>
                                <td><?php echo htmlspecialchars($alumno['matricula']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['apellidos']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['nombres']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['direccion']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['fecha_nacimiento']); ?></td>
                                <td>
                                    <a href="editar_alumno.php?id=<?php echo $alumno['alumno_id']; ?>" class="action-button">Editar</a>
                                    <a href="eliminar_alumno.php?id=<?php echo $alumno['alumno_id']; ?>" class="action-button" onclick="return confirm('¿Estás seguro de eliminar a este alumno?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php elseif (!empty($result)): ?>
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
                        <td><?php echo htmlspecialchars($row['grupo']); ?></td>
                        <td><?php echo htmlspecialchars($row['nivel_nombre']); ?></td>
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