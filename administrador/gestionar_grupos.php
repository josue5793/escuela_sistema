<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once '../db.php';

// Obtener parámetros de filtrado
$filtro_nivel = $_GET['nivel'] ?? '';
$filtro_grado = $_GET['grado'] ?? '';
$filtro_turno = $_GET['turno'] ?? '';

try {
    // Consulta base para obtener grupos
    $sql = "
        SELECT g.id_grupo, n.nivel_nombre, g.grado, g.turno 
        FROM grupos g
        JOIN niveles n ON g.nivel_id = n.nivel_id
        WHERE 1=1
    ";

    // Aplicar filtros si están presentes
    if (!empty($filtro_nivel) && $filtro_nivel !== 'Todos') { // Excluimos "Todos"
        $sql .= " AND n.nivel_nombre = :nivel";
    }
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

    if (!empty($filtro_nivel) && $filtro_nivel !== 'Todos') { // Excluimos "Todos"
        $stmt->bindParam(':nivel', $filtro_nivel, PDO::PARAM_STR);
    }
    if (!empty($filtro_grado)) {
        $stmt->bindParam(':grado', $filtro_grado, PDO::PARAM_STR);
    }
    if (!empty($filtro_turno)) {
        $stmt->bindParam(':turno', $filtro_turno, PDO::PARAM_STR);
    }

    $stmt->execute();
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener valores únicos para los filtros
    $sql_niveles = "SELECT DISTINCT nivel_nombre FROM niveles";
    $niveles = $pdo->query($sql_niveles)->fetchAll(PDO::FETCH_COLUMN);

    // Obtener grados y turnos según el nivel seleccionado
    $grados = [];
    $turnos = [];
    if (!empty($filtro_nivel) && $filtro_nivel !== 'Todos') { // Excluimos "Todos"
        $sql_grados = "
            SELECT DISTINCT grado 
            FROM grupos g
            JOIN niveles n ON g.nivel_id = n.nivel_id
            WHERE n.nivel_nombre = :nivel
            ORDER BY grado ASC
        ";
        $stmt_grados = $pdo->prepare($sql_grados);
        $stmt_grados->bindParam(':nivel', $filtro_nivel, PDO::PARAM_STR);
        $stmt_grados->execute();
        $grados = $stmt_grados->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($filtro_grado)) {
            $sql_turnos = "
                SELECT DISTINCT turno 
                FROM grupos g
                JOIN niveles n ON g.nivel_id = n.nivel_id
                WHERE n.nivel_nombre = :nivel AND g.grado = :grado
            ";
            $stmt_turnos = $pdo->prepare($sql_turnos);
            $stmt_turnos->bindParam(':nivel', $filtro_nivel, PDO::PARAM_STR);
            $stmt_turnos->bindParam(':grado', $filtro_grado, PDO::PARAM_STR);
            $stmt_turnos->execute();
            $turnos = $stmt_turnos->fetchAll(PDO::FETCH_COLUMN);
        }
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
    <title>Gestión de Grupos</title>
    <link rel="stylesheet" href="CSS/gestionar_grupos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Grupos</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenedor de botones -->
    <div class="button-container">
        <!-- Botón para ir al Panel del Administrador -->
        <a href="administrador_dashboard.php" class="control-button">
            <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
            <span>Panel Administrador</span>
        </a>

        <!-- Botón para agregar un nuevo grupo -->
        <a href="agregar_grupo.php" class="control-button">
            <i class="bi bi-plus-circle"></i> <!-- Ícono de agregar -->
            <span>Agregar Nuevo Grupo</span>
        </a>
    </div>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Formulario de filtrado -->
        <form method="GET" action="gestionar_grupos.php" class="filter-form">
            <div class="filter-group">
                <label for="nivel">Nivel:</label>
                <select name="nivel" id="nivel" required>
                    <option value="">Todos</option> <!-- Opción "Todos" -->
                    <?php foreach ($niveles as $nivel): ?>
                        <option value="<?php echo htmlspecialchars($nivel); ?>" 
                            <?php echo ($filtro_nivel === $nivel) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($nivel); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="grado">Grado:</label>
                <select name="grado" id="grado" <?php echo (empty($filtro_nivel) || $filtro_nivel === 'Todos') ? 'disabled' : ''; ?>>
                    <option value="">Todos</option>
                    <?php foreach ($grados as $grado): ?>
                        <option value="<?php echo htmlspecialchars($grado); ?>" 
                            <?php echo ($filtro_grado === $grado) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($grado); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="turno">Turno:</label>
                <select name="turno" id="turno" <?php echo empty($filtro_grado) ? 'disabled' : ''; ?>>
                    <option value="">Todos</option>
                    <?php foreach ($turnos as $turno): ?>
                        <option value="<?php echo htmlspecialchars($turno); ?>" 
                            <?php echo ($filtro_turno === $turno) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($turno); ?>
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
                <?php
                if (!empty($grupos)) {
                    foreach ($grupos as $row) {
                        echo "<tr>
                                <td>{$row['id_grupo']}</td>
                                <td>{$row['nivel_nombre']}</td>
                                <td>{$row['grado']}</td>
                                <td>{$row['turno']}</td>
                                <td>
                                    <a href='editar_grupos.php?id={$row['id_grupo']}' class='action-button edit'>
                                        <i class='bi bi-pencil'></i> Editar
                                    </a> |
                                    <a href=\"eliminar_grupo.php?id={$row['id_grupo']}\" class='action-button delete' onclick=\"return confirm('¿Estás seguro de eliminar este grupo?');\">
                                        <i class='bi bi-trash'></i> Eliminar
                                    </a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay grupos registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <!-- Script para manejar la dinámica de los filtros -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nivelSelect = document.getElementById('nivel');
            const gradoSelect = document.getElementById('grado');
            const turnoSelect = document.getElementById('turno');

            // Habilitar/deshabilitar grados y turnos según el nivel seleccionado
            nivelSelect.addEventListener('change', function () {
                if (nivelSelect.value && nivelSelect.value !== 'Todos') {
                    gradoSelect.disabled = false;
                    turnoSelect.disabled = true; // Deshabilitar turno hasta que se seleccione un grado
                    gradoSelect.innerHTML = '<option value="">Cargando...</option>';

                    // Obtener grados según el nivel seleccionado
                    fetch(`obtener_grados.php?nivel=${encodeURIComponent(nivelSelect.value)}`)
                        .then(response => response.json())
                        .then(data => {
                            gradoSelect.innerHTML = '<option value="">Todos</option>';
                            data.forEach(grado => {
                                gradoSelect.innerHTML += `<option value="${grado}">${grado}</option>`;
                            });
                        });
                } else {
                    gradoSelect.disabled = true;
                    turnoSelect.disabled = true;
                    gradoSelect.innerHTML = '<option value="">Todos</option>';
                    turnoSelect.innerHTML = '<option value="">Todos</option>';
                }
            });

            // Habilitar/deshabilitar turnos según el grado seleccionado
            gradoSelect.addEventListener('change', function () {
                if (gradoSelect.value) {
                    turnoSelect.disabled = false;
                    turnoSelect.innerHTML = '<option value="">Cargando...</option>';

                    // Obtener turnos según el nivel y grado seleccionados
                    fetch(`obtener_turnos.php?nivel=${encodeURIComponent(nivelSelect.value)}&grado=${encodeURIComponent(gradoSelect.value)}`)
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