<?php
session_start();
require_once 'db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Variables para filtros
$nivelSeleccionado = $_GET['nivel'] ?? '';
$grupoSeleccionado = $_GET['grupo'] ?? '';
$searchField = $_GET['field'] ?? '';
$searchValue = $_GET['search'] ?? '';
$exactMatch = isset($_GET['exact']);

try {
    // Consulta base
    $query = "SELECT a.*, n.nivel_nombre, CONCAT(g.grado, ' ', g.turno) AS grupo 
              FROM alumnos a
              LEFT JOIN niveles n ON a.nivel_id = n.nivel_id
              LEFT JOIN grupos g ON a.grupo_id = g.id_grupo";

    // Filtros dinámicos
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

    if ($searchField && $searchValue) {
        $operator = $exactMatch ? '=' : 'LIKE';
        $searchValue = $exactMatch ? $searchValue : "%$searchValue%";
        $filters[] = "$searchField $operator ?";
        $params[] = $searchValue;
    }

    if ($filters) {
        $query .= " WHERE " . implode(' AND ', $filters);
    }

    $query .= " ORDER BY n.nivel_nombre, g.grado, g.turno, a.apellidos";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener niveles y grupos para los controles
    $niveles = $pdo->query("SELECT * FROM niveles")->fetchAll(PDO::FETCH_ASSOC);
    $grupos = $pdo->query("SELECT * FROM grupos")->fetchAll(PDO::FETCH_ASSOC);

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
            <a href="logout.php" class="logout-button">Cerrar Sesión</a>
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
        
        <a href="administrador.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Panel Administrador</span>
        </a>
    </div>
    <section class="filter-section">
        <h2>Filtrar por Nivel y Grupo</h2>
        <form method="GET" action="">
            <label for="nivel">Nivel:</label>
            <select name="nivel" id="nivel" onchange="this.form.submit()">
                <option value="">Todos</option>
                <?php foreach ($niveles as $nivel): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>" <?php echo $nivelSeleccionado == $nivel['nivel_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="grupo">Grupo:</label>
            <select name="grupo" id="grupo" onchange="this.form.submit()">
                <option value="">Todos</option>
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?php echo $grupo['id_grupo']; ?>" <?php echo $grupoSeleccionado == $grupo['id_grupo'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($grupo['grado'] . ' ' . $grupo['turno']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="field">Buscar por:</label>
            <select name="field" id="field">
                <option value="a.nombres" <?php echo $searchField === 'a.nombres' ? 'selected' : ''; ?>>Nombres</option>
                <option value="a.apellidos" <?php echo $searchField === 'a.apellidos' ? 'selected' : ''; ?>>Apellidos</option>
            </select>
            <input type="text" name="search" placeholder="Buscar..." value="<?php echo htmlspecialchars(str_replace('%', '', $searchValue)); ?>">
            <label><input type="checkbox" name="exact" <?php echo $exactMatch ? 'checked' : ''; ?>> Coincidencia exacta</label>
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
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Fecha de Nacimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr onclick="showModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                        <td><img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto del Alumno" width="50"></td>
                        <td><?php echo htmlspecialchars($row['matricula']); ?></td>
                        <td><?php echo htmlspecialchars($row['apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombres']); ?></td>
                        <td><?php echo htmlspecialchars($row['grupo']); ?></td>
                        <td><?php echo htmlspecialchars($row['nivel_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                        <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_nacimiento']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron alumnos con los criterios seleccionados.</p>
    <?php endif; ?>

    <!-- Modal -->
    <div class="modal-overlay" id="modal-overlay"></div>
    <div class="modal" id="modal">
        <button class="modal-close" onclick="closeModal()">×</button>
        <div class="modal-header">Detalles del Alumno</div>
        <div class="modal-content" id="modal-content"></div> <!-- Cambiado -->
    </div>
</main>

<script>
    function showModal(data) {
        const modal = document.getElementById('modal');
        const modalOverlay = document.getElementById('modal-overlay');
        const modalContent = document.getElementById('modal-content');

        modalContent.innerHTML = `
            <div>
                <img src="uploads/${data.foto}" alt="Foto del Alumno" class="modal-img"> <!-- Cambiado -->
            </div>
            <div class="modal-details"> <!-- Cambiado -->
                <p><strong>Matrícula:</strong> ${data.matricula}</p>
                <p><strong>Apellidos:</strong> ${data.apellidos}</p>
                <p><strong>Nombres:</strong> ${data.nombres}</p>
                <p><strong>Grupo:</strong> ${data.grupo}</p>
                <p><strong>Nivel:</strong> ${data.nivel_nombre}</p>
                <p><strong>Dirección:</strong> ${data.direccion}</p>
                <p><strong>Teléfono:</strong> ${data.telefono}</p>
                <p><strong>Fecha de Nacimiento:</strong> ${data.fecha_nacimiento}</p>
            </div>
        `;

        modal.style.display = 'block';
        modalOverlay.style.display = 'block';
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
        document.getElementById('modal-overlay').style.display = 'none';
    }
</script>

</body>
</html>
