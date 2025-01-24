<?php
session_start();
include('db.php');

// Verificar si el usuario está logueado y tiene el rol de profesor
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: login.php");
    exit;
}

try {
    // Obtener el ID del profesor desde la sesión
    $usuario_id = $_SESSION['usuario_id'];

    // Consultar los niveles asignados al profesor
    $stmt_niveles = $pdo->prepare("
        SELECT DISTINCT n.nivel_id, n.nivel_nombre
        FROM profesor_nivel pn
        JOIN niveles n ON pn.nivel_id = n.nivel_id
        JOIN profesores p ON pn.profesor_id = p.profesor_id
        WHERE p.usuario_id = :usuario_id
    ");
    $stmt_niveles->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_niveles->execute();
    $niveles = $stmt_niveles->fetchAll(PDO::FETCH_ASSOC);

    // Si se seleccionó un nivel, consultar las materias asignadas al profesor en ese nivel
    $materias = [];
    if (isset($_POST['nivel_id'])) {
        $nivel_id = $_POST['nivel_id'];

        $stmt_materias = $pdo->prepare("
            SELECT DISTINCT m.materia_id, m.nombre AS materia_nombre
            FROM profesor_materia pm
            JOIN materias m ON pm.materia_id = m.materia_id
            JOIN profesores p ON pm.profesor_id = p.profesor_id
            WHERE m.nivel_id = :nivel_id AND p.usuario_id = :usuario_id
        ");
        $stmt_materias->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
        $stmt_materias->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_materias->execute();
        $materias = $stmt_materias->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error al recuperar información: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Niveles y Materias</title>
    <link rel="stylesheet" href="CSS/materia_detalle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Gestión de Materias</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="main-container">
        <!-- Mostrar Niveles -->
        <section class="niveles-asignados">
            <h2>Niveles Asignados</h2>
            <form method="POST" action="">
                <label for="nivel_id">Selecciona un Nivel:</label>
                <select name="nivel_id" id="nivel_id" required>
                    <option value="" disabled selected>Selecciona un nivel</option>
                    <?php foreach ($niveles as $nivel): ?>
                        <option value="<?php echo htmlspecialchars($nivel['nivel_id']); ?>"
                            <?php echo isset($nivel_id) && $nivel_id == $nivel['nivel_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Mostrar Materias</button>
            </form>
        </section>

        <!-- Mostrar Materias -->
        <?php if (isset($nivel_id)): ?>
            <section class="materias-asignadas">
                <h2>Materias Asignadas en el Nivel Seleccionado</h2>
                <?php if (count($materias) > 0): ?>
                    <ul class="materias-lista">
                        <?php foreach ($materias as $materia): ?>
                            <li>
                                <h3><?php echo htmlspecialchars($materia['materia_nombre']); ?></h3>
                                <a href="detalle_materia.php?materia_id=<?php echo $materia['materia_id']; ?>">Ver Detalles</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No hay materias asignadas en este nivel.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
