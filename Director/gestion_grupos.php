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

// Obtener parámetros de filtrado
$filtro_grado = $_GET['grado'] ?? '';
$filtro_turno = $_GET['turno'] ?? '';

try {
    // Consulta base para obtener grupos del nivel del director
    $sql = "
        SELECT g.id_grupo, n.nivel_nombre, g.grado, g.turno 
        FROM grupos g
        JOIN niveles n ON g.nivel_id = n.nivel_id
        WHERE g.nivel_id = :nivel_id
    ";

    // Aplicar filtros si están presentes
    if (!empty($filtro_grado)) {
        $sql .= " AND g.grado = :grado";
    }
    if (!empty($filtro_turno)) {
        $sql .= " AND g.turno = :turno";
    }

    // Ordenar los grupos por grado
    $sql .= " ORDER BY g.grado ASC";

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nivel_id', $nivel_id_director, PDO::PARAM_INT);

    if (!empty($filtro_grado)) {
        $stmt->bindParam(':grado', $filtro_grado, PDO::PARAM_STR);
    }
    if (!empty($filtro_turno)) {
        $stmt->bindParam(':turno', $filtro_turno, PDO::PARAM_STR);
    }

    $stmt->execute();
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener grados y turnos según el nivel del director
    $sql_grados = "
        SELECT DISTINCT grado 
        FROM grupos 
        WHERE nivel_id = :nivel_id
        ORDER BY grado ASC
    ";
    $stmt_grados = $pdo->prepare($sql_grados);
    $stmt_grados->bindParam(':nivel_id', $nivel_id_director, PDO::PARAM_INT);
    $stmt_grados->execute();
    $grados = $stmt_grados->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($filtro_grado)) {
        $sql_turnos = "
            SELECT DISTINCT turno 
            FROM grupos 
            WHERE nivel_id = :nivel_id AND grado = :grado
        ";
        $stmt_turnos = $pdo->prepare($sql_turnos);
        $stmt_turnos->bindParam(':nivel_id', $nivel_id_director, PDO::PARAM_INT);
        $stmt_turnos->bindParam(':grado', $filtro_grado, PDO::PARAM_STR);
        $stmt_turnos->execute();
        $turnos = $stmt_turnos->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (PDOException $e) {
    die("Error al obtener los grupos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos - Director</title>
    <link rel="stylesheet" href="CSS/gestion_grupos3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Grupos</h1>
            <div class="navbar-right">
                <span>Director: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenedor de botones -->
    <div class="button-container">
        <!-- Botón para ir al Panel del Director -->
        <a href="dashboard_director.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Panel Director</span>
        </a>

        <!-- Botón para agregar un nuevo grupo (solo para el nivel del director) -->
        <a href="agregar_grupo.php" class="control-button">
            <i class="bi bi-plus-circle"></i>
            <span>Agregar Nuevo Grupo</span>
        </a>
    </div>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Formulario de filtrado -->
        <form method="GET" action="gestion_grupos.php" class="filter-form">
            <div class="filter-group">
                <label for="grado">Grado:</label>
                <select name="grado" id="grado">
                    <option value="">Todos</option>
                    <?php foreach ($grados as $grado): ?>
                        <option value="<?php echo htmlspecialchars($grado); ?>" 
                            <?php echo ($filtro_grado === $grado) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($grado); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <button type="submit" class="filter-button">Filtrar</button>
        </form>

        <!-- Tabla de grupos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nivel</th>
                    <th>Grado</th>
                    <th>Turno</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($grupos)): ?>
                    <?php foreach ($grupos as $grupo): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grupo['id_grupo']); ?></td>
                            <td><?php echo htmlspecialchars($grupo['nivel_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($grupo['grado']); ?></td>
                            <td><?php echo htmlspecialchars($grupo['turno']); ?></td>
                            <td>
                                <a href="editar_grupo.php?id=<?php echo $grupo['id_grupo']; ?>" class="action-button edit">
                                    <i class="bi bi-pencil"></i> Editar
                                </a> |
                                <a href="eliminar_grupo.php?id=<?php echo $grupo['id_grupo']; ?>" class="action-button delete" onclick="return confirm('¿Estás seguro de eliminar este grupo?');">
                                    <i class="bi bi-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay grupos registrados en este nivel.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- Script para manejar la dinámica de los filtros -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const gradoSelect = document.getElementById('grado');
            const turnoSelect = document.getElementById('turno');

            // Habilitar/deshabilitar turnos según el grado seleccionado
            gradoSelect.addEventListener('change', function () {
                if (gradoSelect.value) {
                    turnoSelect.disabled = false;
                    turnoSelect.innerHTML = '<option value="">Cargando...</option>';

                    // Obtener turnos según el grado seleccionado
                    fetch(`obtener_turnos.php?grado=${encodeURIComponent(gradoSelect.value)}`)
                        .then(response => response.json())
                        .then(data => {
                            turnoSelect.innerHTML = '<option value="">Todos</option>';
                            data.forEach(turno => {
                                turnoSelect.innerHTML += `<option value="${turno}">${turno}</option>`;
                            });
                        });
                } else {
                    turnoSelect.disabled = true;
                    turnoSelect.innerHTML = '<option value="">Todos</option>';
                }
            });
        });
    </script>
</body>
</html>