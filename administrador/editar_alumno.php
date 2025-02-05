<?php
session_start();
require_once '../db.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener el ID del alumno desde la URL
$alumno_id = $_GET['id'] ?? null;
if (!$alumno_id) {
    die("ID de alumno no especificado.");
}

// Obtener los datos del alumno
try {
    $query = "SELECT * FROM alumnos WHERE alumno_id = :alumno_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['alumno_id' => $alumno_id]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        die("Alumno no encontrado.");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos del alumno: " . $e->getMessage());
}

// Obtener niveles y grupos para el formulario
try {
    $niveles = $pdo->query("SELECT * FROM niveles")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener grupos del nivel actual del alumno
    $gruposQuery = "SELECT * FROM grupos WHERE nivel_id = :nivel_id";
    $gruposStmt = $pdo->prepare($gruposQuery);
    $gruposStmt->execute(['nivel_id' => $alumno['nivel_id']]);
    $grupos = $gruposStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener niveles o grupos: " . $e->getMessage());
}

// Manejar la actualización del alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario y permitir NULL en campos opcionales
    $nombres = !empty($_POST['nombres']) ? $_POST['nombres'] : null;
    $apellidos = !empty($_POST['apellidos']) ? $_POST['apellidos'] : null;
    $direccion = !empty($_POST['direccion']) ? $_POST['direccion'] : null;
    $telefono = !empty($_POST['telefono']) ? $_POST['telefono'] : null;
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $nivel_id = !empty($_POST['nivel_id']) ? $_POST['nivel_id'] : null;
    $grupo_id = !empty($_POST['grupo_id']) ? $_POST['grupo_id'] : null;

    // Validaciones
    $error = null;
    if (!$nombres || !$apellidos) {
        $error = "Los campos de nombres y apellidos son obligatorios.";
    } elseif ($telefono && !preg_match('/^[0-9]{10}$/', $telefono)) {
        $error = "El teléfono debe contener 10 dígitos.";
    }

    // Manejar la subida de la nueva foto
    $fotoRuta = $alumno['foto']; // Mantener la foto existente por defecto
    if (!$error && !empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto'];
        $fotoNombre = time() . '_' . basename($foto['name']);
        $fotoDestino = "../uploads/" . $fotoNombre;

        // Validar que el archivo sea una imagen
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($foto['type'], $allowedTypes)) {
            $error = "Solo se permiten imágenes (JPEG, PNG, GIF).";
        }

        // Validar el tamaño del archivo (máximo 2 MB)
        $maxSize = 2 * 1024 * 1024; // 2 MB
        if ($foto['size'] > $maxSize) {
            $error = "El archivo es demasiado grande. El tamaño máximo es 2 MB.";
        }

        // Mover la foto al directorio de uploads
        if (!$error && move_uploaded_file($foto['tmp_name'], $fotoDestino)) {
            // Eliminar la foto anterior si existe
            if (!empty($alumno['foto']) && file_exists("../uploads/" . $alumno['foto'])) {
                unlink("../uploads/" . $alumno['foto']);
            }
            $fotoRuta = $fotoNombre; // Actualizar la ruta de la nueva foto
        }
    }

    // Actualizar los datos del alumno en la base de datos
    if (!$error) {
        try {
            $query = "UPDATE alumnos SET 
                nombres = :nombres, 
                apellidos = :apellidos, 
                direccion = :direccion, 
                telefono = :telefono, 
                fecha_nacimiento = :fecha_nacimiento, 
                nivel_id = :nivel_id, 
                grupo_id = :grupo_id, 
                foto = :foto 
                WHERE alumno_id = :alumno_id";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'direccion' => $direccion,
                'telefono' => $telefono,
                'fecha_nacimiento' => $fecha_nacimiento,
                'nivel_id' => $nivel_id,
                'grupo_id' => $grupo_id,
                'foto' => $fotoRuta,
                'alumno_id' => $alumno_id
            ]);

            // Redirigir a la página de consulta de alumnos con un mensaje de éxito
            header("Location: consultar_alumnos.php?success=1");
            exit;
        } catch (PDOException $e) {
            $error = "Error al actualizar los datos del alumno: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alumno</title>
    <link rel="stylesheet" href="CSS/editar_alumno2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <h1>Esditar la informacion del alumno</h1>
        <div class="navbar-right">
            <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
            <a href="../logout.php" class="logout-button">Cerrar Sesión</a>
        </div>
    </div>


</header> 

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

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="nombres">Nombres:</label>
        <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($alumno['nombres']); ?>" required>

        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($alumno['apellidos']); ?>" required>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($alumno['direccion'] ?? ''); ?>">

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($alumno['telefono'] ?? ''); ?>">

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($alumno['fecha_nacimiento'] ?? ''); ?>">

        <label for="foto">Foto:</label>
        <input type="file" id="foto" name="foto" accept="image/*">
        <?php if (!empty($alumno['foto'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($alumno['foto']); ?>" alt="Foto del alumno" width="100">
        <?php endif; ?>

        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
