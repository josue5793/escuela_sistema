<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: login.php");
    exit;
}

require_once '../db.php';

// Obtener el nivel_id del director
$director_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT nivel_id FROM profesores WHERE usuario_id = ?");
$stmt->execute([$director_id]);
$director = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$director) {
    die("No se encontró información del director.");
}

$nivel_id_director = $director['nivel_id'];

// Función para generar matrícula
function generarMatricula($pdo) {
    $query = "SELECT matricula FROM alumnos WHERE matricula LIKE 'A%' ORDER BY alumno_id DESC LIMIT 1";
    $stmt = $pdo->query($query);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $ultimaMatricula = $resultado['matricula'];
        $ultimoNumero = intval(substr($ultimaMatricula, 1));
        $nuevoNumero = $ultimoNumero + 1;
    } else {
        $nuevoNumero = 1;
    }

    return 'A' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT); // A00001, A00002, etc.
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = generarMatricula($pdo); // Llamamos a la función para generar la matrícula
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $direccion = $_POST['direccion'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $grupo_id = $_POST['grupo_id']; // El grupo seleccionado en el formulario

    // Subida de foto
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // Definir el nombre del archivo
        $foto = $_FILES['foto']['name'];
        $fotoTmpPath = $_FILES['foto']['tmp_name'];
        $uploadDir = 'uploads/';

        // Verificar que el archivo sea una imagen
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['foto']['type'], $allowedTypes)) {
            // Mover el archivo a la carpeta uploads
            $destination = $uploadDir . $foto;
            if (move_uploaded_file($fotoTmpPath, $destination)) {
                echo "Foto subida correctamente.";
            } else {
                echo "Error al subir la foto.";
                $foto = null; // Si hubo error, no asignamos foto
            }
        } else {
            echo "Tipo de archivo no permitido. Debe ser una imagen JPG, PNG o GIF.";
        }
    }

    try {
        // Insertamos los datos del alumno
        $stmt = $pdo->prepare("INSERT INTO alumnos (matricula, nombres, apellidos, direccion, telefono, fecha_nacimiento, foto, grupo_id, nivel_id) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$matricula, $nombres, $apellidos, $direccion, $telefono, $fecha_nacimiento, $foto, $grupo_id, $nivel_id_director]);

        echo "Alumno registrado correctamente. Matrícula: $matricula";
    } catch (PDOException $e) {
        die("Error al registrar el alumno: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Alumno</title>
    <link rel="stylesheet" href="CSS/agregar_alumno.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>Agregar un alumno nuevo</h1>
        <div class="navbar-right">
            <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </div>
</header>
<main class="main-container">
    <!-- Sección de bienvenida -->
    <section class="welcome-section">
        <h2>Administración de alumnos</h2>
        <p>Desde aquí puedes gestionar la información de cada alumno.</p>
    </section>

    <!-- Botones de control -->
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
    </div>

    <h1>Registrar Alumno</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="nombres">Nombres:</label>
        <input type="text" name="nombres" id="nombres" required><br><br>

        <label for="apellidos">Apellidos:</label>
        <input type="text" name="apellidos" id="apellidos" required><br><br>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" id="direccion"><br><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono"><br><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"><br><br>

        <!-- Campo para seleccionar el grupo -->
        <label for="grupo_id">Grupo:</label>
        <select name="grupo_id" id="grupo_id" required>
            <?php
            // Obtener los grupos del nivel del director
            $stmt = $pdo->prepare("SELECT id_grupo, grado, turno FROM grupos WHERE nivel_id = ?");
            $stmt->execute([$nivel_id_director]);
            $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($grupos) {
                foreach ($grupos as $grupo) {
                    echo "<option value='{$grupo['id_grupo']}'>{$grupo['grado']} - {$grupo['turno']}</option>";
                }
            } else {
                echo "<option value=''>No hay grupos disponibles</option>";
            }
            ?>
        </select><br><br>

        <label for="foto">Foto del Alumno:</label>
        <input type="file" name="foto" id="foto" accept="image/*"><br><br>

        <button type="submit">Registrar</button>
    </form>
</main>
</body>
</html>