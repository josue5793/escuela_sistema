<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Verificar si se ha pasado un ID de grupo por la URL
if (isset($_GET['id'])) {
    $id_grupo = $_GET['id'];

    try {
        // Obtener los datos del grupo desde la base de datos
        $sql = "
            SELECT g.id_grupo, n.nivel_nombre, g.grado, g.turno, g.nivel_id
            FROM grupos g
            JOIN niveles n ON g.nivel_id = n.nivel_id
            WHERE g.id_grupo = ?
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_grupo]);
        $grupo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$grupo) {
            echo "Grupo no encontrado.";
            exit;
        }

        // Obtener los niveles disponibles para el select
        $sql_niveles = "SELECT * FROM niveles";
        $niveles_stmt = $pdo->query($sql_niveles);
        $niveles = $niveles_stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Error al obtener los datos del grupo: " . $e->getMessage());
    }
} else {
    echo "No se ha especificado un ID de grupo.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Grupo</title>
    <link rel="stylesheet" href="CSS/gestionar_grupos2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Grupos</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenedor de botones -->
    <div class="button-container">
        <!-- Botón para ir al Panel del Administrador -->
        <a href="administrador.php" class="control-button">
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
        <h1>Editar Grupo</h1>
        <form action="guardar_edicion_grupo.php" method="POST">
            <input type="hidden" name="id_grupo" value="<?= htmlspecialchars($grupo['id_grupo']) ?>">

            <label for="nivel">Nivel</label>
            <select name="nivel_id" id="nivel">
                <?php foreach ($niveles as $nivel): ?>
                    <option value="<?= htmlspecialchars($nivel['nivel_id']) ?>" <?= $grupo['nivel_id'] == $nivel['nivel_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nivel['nivel_nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="grado">Grado</label>
            <input type="text" name="grado" id="grado" value="<?= htmlspecialchars($grupo['grado']) ?>" required>

            <label for="turno">Turno</label>
            <input type="text" name="turno" id="turno" value="<?= htmlspecialchars($grupo['turno']) ?>" required>

            <button type="submit">Guardar Cambios</button>
        </form>
    </main>
</body>
</html>
