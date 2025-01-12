<?php
session_start();
include 'db.php';

// Verificar si el usuario está logueado y tiene permisos
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['administrador', 'director'])) {
    header("Location: login.php");
    exit;
}

// Verificar si se recibió el ID del alumno
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $alumno_id = $_GET['id'];

    // Primero, obtener el nombre del archivo de la foto para eliminarlo del servidor
    $query = "SELECT foto FROM alumnos WHERE alumno_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $alumno_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $alumno = $result->fetch_assoc();

    if ($alumno) {
        // Intentar eliminar la foto si existe
        $fotoPath = 'uploads/' . $alumno['foto'];
        if (file_exists($fotoPath) && is_file($fotoPath)) {
            unlink($fotoPath); // Elimina la foto del servidor
        }

        // Eliminar el registro del alumno en la base de datos
        $deleteQuery = "DELETE FROM alumnos WHERE alumno_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $alumno_id);

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
        // Redirigir con un mensaje de error si no se encuentra el alumno
        $_SESSION['mensaje'] = "Alumno no encontrado.";
        $_SESSION['mensaje_tipo'] = "error";
    }
} else {
    // Redirigir con un mensaje de error si no se recibe un ID válido
    $_SESSION['mensaje'] = "ID de alumno inválido.";
    $_SESSION['mensaje_tipo'] = "error";
}

// Redirigir de vuelta a la página de consulta
header("Location: consultar_alumnos.php");
exit;
?>
