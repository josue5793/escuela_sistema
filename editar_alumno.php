<?php
session_start();
include 'db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$alumno_id = $_GET['id'] ?? null;
if (!$alumno_id) {
    die("ID de alumno no especificado.");
}

// Obtener los datos del alumno
$query = "SELECT * FROM alumnos WHERE alumno_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $alumno_id);
$stmt->execute();
$result = $stmt->get_result();
$alumno = $result->fetch_assoc();

if (!$alumno) {
    die("Alumno no encontrado.");
}

// Obtener niveles
$niveles = $conn->query("SELECT * FROM niveles");

// Manejar la actualización del alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $nivel_id = $_POST['nivel_id'];
    $grupo_id = $_POST['grupo_id'];

    // Manejar la subida de la nueva foto
    $fotoRuta = $alumno['foto']; // Mantener la foto existente por defecto
    if (!empty($_FILES['foto']['name'])) {
        $foto = $_FILES['foto'];
        $fotoNombre = time() . '_' . basename($foto['name']);
        $fotoDestino = "uploads/" . $fotoNombre;

        if (move_uploaded_file($foto['tmp_name'], $fotoDestino)) {
            // Eliminar la foto anterior si existe
            if (!empty($alumno['foto']) && file_exists("uploads/" . $alumno['foto'])) {
                unlink("uploads/" . $alumno['foto']);
            }
            $fotoRuta = $fotoNombre; // Actualizar la ruta de la nueva foto
        }
    }

    // Actualizar datos del alumno
    $query = "UPDATE alumnos SET nombres = ?, apellidos = ?, direccion = ?, telefono = ?, fecha_nacimiento = ?, nivel_id = ?, grupo_id = ?, foto = ? WHERE alumno_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssiisi", $nombres, $apellidos, $direccion, $telefono, $fecha_nacimiento, $nivel_id, $grupo_id, $fotoRuta, $alumno_id);

    if ($stmt->execute()) {
        header("Location: consultar_alumnos.php?success=1");
        exit;
    } else {
        $error = "Error al actualizar el alumno.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alumno</title>
    <link rel="stylesheet" href="CSS/editar_alumno.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Editar Alumno</h1>
            <a href="consultar_alumnos.php" class="back-button">Volver</a>
        </div>
    </header>

    <main class="main-container">
        <h2>Actualizar información del alumno</h2>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($alumno['matricula']); ?>" disabled>

            <label for="nombres">Nombres:</label>
            <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($alumno['nombres']); ?>" required>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($alumno['apellidos']); ?>" required>

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($alumno['direccion']); ?>">

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($alumno['telefono']); ?>">

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($alumno['fecha_nacimiento']); ?>">

            <label for="nivel_id">Nivel:</label>
            <select name="nivel_id" id="nivel_id" required>
                <?php while ($nivel = $niveles->fetch_assoc()): ?>
                    <option value="<?php echo $nivel['nivel_id']; ?>" <?php echo $nivel['nivel_id'] == $alumno['nivel_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="grupo_id">Grupo:</label>
            <select name="grupo_id" id="grupo_id" required>
                <?php 
                $grupoQuery = "SELECT id_grupo, CONCAT(grado, ' ', turno) AS grupo FROM grupos WHERE nivel_id = ?";
                $stmt = $conn->prepare($grupoQuery);
                $stmt->bind_param("i", $alumno['nivel_id']);
                $stmt->execute();
                $grupoResult = $stmt->get_result();
                while ($grupo = $grupoResult->fetch_assoc()): ?>
                    <option value="<?php echo $grupo['id_grupo']; ?>" <?php echo $grupo['id_grupo'] == $alumno['grupo_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($grupo['grupo']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="foto">Foto (opcional):</label>
            <input type="file" id="foto" name="foto" accept="image/*">
            <?php if (!empty($alumno['foto'])): ?>
                <p>Foto actual:</p>
                <img src="uploads/<?php echo htmlspecialchars($alumno['foto']); ?>" alt="Foto del alumno" width="100">
            <?php endif; ?>

            <button type="submit">Actualizar</button>
        </form>
    </main>

    <script>
        document.getElementById('nivel_id').addEventListener('change', function () {
            const nivelId = this.value;
            const grupoSelect = document.getElementById('grupo_id');

            // Realizar una solicitud AJAX para obtener los grupos correspondientes
            fetch('get_grupos.php?nivel_id=' + nivelId)
                .then(response => response.json())
                .then(data => {
                    // Limpiar el contenido del select de grupos
                    grupoSelect.innerHTML = '';

                    // Agregar las opciones de los grupos obtenidos
                    data.forEach(grupo => {
                        const option = document.createElement('option');
                        option.value = grupo.id_grupo;
                        option.textContent = grupo.grupo;
                        grupoSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al obtener los grupos:', error));
        });
    </script>
</body>
</html>
