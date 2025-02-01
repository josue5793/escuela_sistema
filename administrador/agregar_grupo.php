<?php
session_start();

// Verificar si el usuario ha iniciado sesión y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
require_once '../db.php';

// Inicializar variables
$errores = [];
$mensaje = "";
$nivel_id = $_POST['nivel_id'] ?? '';
$grado = $_POST['grado'] ?? '';
$turno = $_POST['turno'] ?? '';

try {
    // Obtener lista de niveles desde la base de datos
    $sql_niveles = "SELECT nivel_id, nivel_nombre FROM niveles";
    $stmt_niveles = $pdo->query($sql_niveles);
    $niveles = $stmt_niveles->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar datos
        if (empty($nivel_id)) {
            $errores[] = "Debe seleccionar un nivel.";
        }
        if (empty($grado)) {
            $errores[] = "El campo 'Grado' es obligatorio.";
        }
        if (empty($turno)) {
            $errores[] = "El campo 'Turno' es obligatorio.";
        }

        // Insertar grupo si no hay errores
        if (empty($errores)) {
            $sql = "INSERT INTO grupos (nivel_id, grado, turno) VALUES (:nivel_id, :grado, :turno)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
            $stmt->bindParam(':grado', $grado, PDO::PARAM_STR);
            $stmt->bindParam(':turno', $turno, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $mensaje = "Grupo agregado exitosamente.";
                $nivel_id = $grado = $turno = ''; // Limpiar los campos
            } else {
                $errores[] = "Error al agregar el grupo. Intente nuevamente.";
            }
        }
    }
} catch (PDOException $e) {
    $errores[] = "Error en la base de datos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Grupo</title>
    <link rel="stylesheet" href="css/agregar_grupo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para los íconos -->
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Agregar Nuevo Grupo</h1>
            <div class="navbar-right">
                <span>Administrador: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Botón de Panel del Administrador fuera de la barra de navegación -->
    

    <!-- Contenedor Principal -->
    <main class="main-container">
    <div class="admin-panel-button-container">
                <a href="administrador_dashboard.php" class="control-button">
            <i class="bi bi-house-door"></i> <!-- Ícono de casa -->
            <span>Panel Administrador</span>
        </a>


    </div>
        <div class="form-container">
            <h2>Formulario para Agregar Nuevo Grupo</h2>
            <?php if (!empty($errores)): ?>
                <div class="error-messages">
                    <?php foreach ($errores as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!empty($mensaje)): ?>
                <div class="success-message">
                    <p><?php echo htmlspecialchars($mensaje); ?></p>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nivel_id">Nivel:</label>
                    <select name="nivel_id" id="nivel_id" required>
                        <option value="" disabled selected>Seleccione un nivel</option>
                        <?php foreach ($niveles as $nivel): ?>
                            <option value="<?php echo htmlspecialchars($nivel['nivel_id']); ?>" 
                                <?php echo ($nivel_id == $nivel['nivel_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="grado">Grado:</label>
                    <input type="text" name="grado" id="grado" value="<?php echo htmlspecialchars($grado); ?>" required>
                </div>

                <div class="form-group">
                    <label for="turno">Turno:</label>
                    <input type="text" name="turno" id="turno" value="<?php echo htmlspecialchars($turno); ?>" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Agregar Grupo</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>