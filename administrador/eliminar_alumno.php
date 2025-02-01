<?php
session_start();
include '../db.php';  // Incluir el archivo de conexión correctamente

// Verificar si el usuario está logueado y tiene permisos
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['administrador', 'director'])) {
    header("Location: login.php");
    exit;
}

// Verificar si se recibió el ID del alumno
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $alumno_id = $_GET['id'];

    // Obtener los datos del alumno
    $query = "SELECT * FROM alumnos WHERE alumno_id = ?";
    $stmt = $pdo->prepare($query);  // Cambié $conn por $pdo
    $stmt->bindParam(1, $alumno_id, PDO::PARAM_INT);  // Bind con PDO
    $stmt->execute();
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($alumno) {
        // Si el formulario fue enviado para confirmar la eliminación
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'sí') {
                // Intentar eliminar la foto si existe
                $fotoPath = 'uploads/' . $alumno['foto'];
                if (file_exists($fotoPath) && is_file($fotoPath)) {
                    unlink($fotoPath); // Elimina la foto del servidor
                }

                // Eliminar el registro del alumno en la base de datos
                $deleteQuery = "DELETE FROM alumnos WHERE alumno_id = ?";
                $deleteStmt = $pdo->prepare($deleteQuery);  // Cambié $conn por $pdo
                $deleteStmt->bindParam(1, $alumno_id, PDO::PARAM_INT);  // Bind con PDO

                if ($deleteStmt->execute()) {
                    // Redirigir con un mensaje de éxito
                    $_SESSION['mensaje'] = "Alumno eliminado con éxito.";
                    $_SESSION['mensaje_tipo'] = "success";
                } else {
                    // Redirigir con un mensaje de error
                    $_SESSION['mensaje'] = "Error al eliminar el alumno.";
                    $_SESSION['mensaje_tipo'] = "error";
                }
            } else {
                // Si se cancela la eliminación
                $_SESSION['mensaje'] = "Eliminación cancelada.";
                $_SESSION['mensaje_tipo'] = "info";
            }

            // Redirigir de vuelta a la página de consulta
            header("Location: consultar_alumnos.php");
            exit;
        }
    } else {
        // Redirigir con un mensaje de error si no se encuentra el alumno
        $_SESSION['mensaje'] = "Alumno no encontrado.";
        $_SESSION['mensaje_tipo'] = "error";
        header("Location: consultar_alumnos.php");
        exit;
    }
} else {
    // Redirigir con un mensaje de error si no se recibe un ID válido
    $_SESSION['mensaje'] = "ID de alumno inválido.";
    $_SESSION['mensaje_tipo'] = "error";
    header("Location: consultar_alumnos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Alumno</title>
    <link rel="stylesheet" href="CSS/eliminar_alumno.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Eliminar Alumno</h1>
            <a href="consultar_alumnos.php" class="back-button">Volver</a>
        </div>
    </header>

    <main class="main-container">
        <h2>¿Estás seguro de que deseas eliminar al siguiente alumno?</h2>

        <div class="alumno-info">
            <p><strong>Matrícula:</strong> <?php echo htmlspecialchars($alumno['matricula']); ?></p>
            <p><strong>Nombres:</strong> <?php echo htmlspecialchars($alumno['nombres']); ?></p>
            <p><strong>Apellidos:</strong> <?php echo htmlspecialchars($alumno['apellidos']); ?></p>
            <p><strong>Foto:</strong></p>
            <?php if (!empty($alumno['foto'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($alumno['foto']); ?>" alt="Foto del alumno" width="100">
            <?php endif; ?>
        </div>

        <form method="POST">
            <p>Esta acción no se puede deshacer.</p>
            <button type="submit" name="confirmar" value="sí" class="confirm-button">Sí, eliminar</button>
            <button type="submit" name="confirmar" value="no" class="cancel-button">Cancelar</button>
        </form>
    </main>

    <script>
        // Puedes agregar algún script si es necesario en el futuro
    </script>
</body>
</html>
