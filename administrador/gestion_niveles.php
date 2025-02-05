<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once '../db.php';

// Mensajes de éxito o error
$mensaje = '';

// Procesar el formulario de agregar nivel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_nivel'])) {
    $nivel_nombre = trim($_POST['nivel_nombre']);

    if (!empty($nivel_nombre)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO niveles (nivel_nombre) VALUES (:nivel_nombre)");
            $stmt->execute(['nivel_nombre' => $nivel_nombre]);
            $mensaje = "Nivel agregado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al agregar el nivel: " . $e->getMessage();
        }
    } else {
        $mensaje = "El nombre del nivel no puede estar vacío.";
    }
}

// Procesar la eliminación de un nivel
if (isset($_GET['eliminar'])) {
    $nivel_id = $_GET['eliminar'];

    // Verificar si el nivel tiene dependencias antes de eliminarlo
    try {
        // Verificar si hay grupos asociados al nivel
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM grupos WHERE nivel_id = :nivel_id");
        $stmt->execute(['nivel_id' => $nivel_id]);
        $grupos_asociados = $stmt->fetchColumn();

        if ($grupos_asociados > 0) {
            $mensaje = "No se puede eliminar el nivel porque tiene grupos asociados.";
        } else {
            // Eliminar el nivel si no tiene dependencias
            $stmt = $pdo->prepare("DELETE FROM niveles WHERE nivel_id = :nivel_id");
            $stmt->execute(['nivel_id' => $nivel_id]);
            $mensaje = "Nivel eliminado correctamente.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar el nivel: " . $e->getMessage();
    }
}

// Procesar la edición de un nivel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_nivel'])) {
    $nivel_id = $_POST['nivel_id'];
    $nivel_nombre = trim($_POST['nivel_nombre']);

    if (!empty($nivel_nombre)) {
        try {
            $stmt = $pdo->prepare("UPDATE niveles SET nivel_nombre = :nivel_nombre WHERE nivel_id = :nivel_id");
            $stmt->execute(['nivel_nombre' => $nivel_nombre, 'nivel_id' => $nivel_id]);
            $mensaje = "Nivel actualizado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al actualizar el nivel: " . $e->getMessage();
        }
    } else {
        $mensaje = "El nombre del nivel no puede estar vacío.";
    }
}

// Obtener la lista de niveles
try {
    $stmt = $pdo->query("SELECT * FROM niveles");
    $niveles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = "Error al obtener los niveles: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Niveles</title>
    <link rel="stylesheet" href="CSS/gestion_niveles2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>Gestión de Niveles</h1>
        <div class="navbar-right">
            <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </div>
</header>
<main class="main-container">
    <!-- Sección de bienvenida -->
    <section class="welcome-section">
        <h2>Administración de Niveles</h2>
        <p>Desde aquí puedes gestionar los niveles del sistema.</p>
    </section>

    <!-- Botones de control -->
    <div class="button-container">
        <a href="gestion_niveles.php" class="control-button">
            <i class="bi bi-list-ul"></i>
            <span>Gestión de Niveles</span>
        </a>
        <a href="administrador_dashboard.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Panel Administrador</span>
        </a>
    </div>

    <!-- Mostrar mensajes de éxito o error -->
    <?php if (!empty($mensaje)): ?>
        <div class="<?= strpos($mensaje, 'Error') !== false ? 'error-message' : 'success-message' ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar un nuevo nivel -->
    <h2>Agregar Nuevo Nivel</h2>
    <form method="POST" action="">
        <label for="nivel_nombre">Nombre del Nivel:</label>
        <input type="text" id="nivel_nombre" name="nivel_nombre" required>
        <button type="submit" name="agregar_nivel">Agregar</button>
    </form>

    <!-- Lista de niveles existentes -->
    <h2>Lista de Niveles</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($niveles as $nivel): ?>
                <tr>
                    <td><?= htmlspecialchars($nivel['nivel_id']) ?></td>
                    <td><?= htmlspecialchars($nivel['nivel_nombre']) ?></td>
                    <td>
                        <!-- Formulario para editar un nivel -->
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="nivel_id" value="<?= $nivel['nivel_id'] ?>">
                            <input type="text" name="nivel_nombre" value="<?= htmlspecialchars($nivel['nivel_nombre']) ?>" required>
                            <button type="submit" name="editar_nivel">Editar</button>
                        </form>
                        <!-- Enlace para eliminar un nivel -->
                        <a href="?eliminar=<?= $nivel['nivel_id'] ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este nivel?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>