<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_grupo'])) {
    // Recoger los valores del formulario
    $id_grupo = $_POST['id_grupo'];
    $nivel_id = $_POST['nivel_id'];
    $grado = $_POST['grado'];
    $turno = $_POST['turno'];

    // Validación de los campos (puedes agregar más validaciones si es necesario)
    if (empty($nivel_id) || empty($grado) || empty($turno)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Preparar la consulta SQL para actualizar el grupo
    $sql = "
        UPDATE grupos 
        SET nivel_id = ?, grado = ?, turno = ? 
        WHERE id_grupo = ?";

    // Usar una declaración preparada para evitar inyecciones SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $nivel_id, $grado, $turno, $id_grupo);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir al administrador a la página de gestión de grupos después de guardar los cambios
        header("Location: gestionar_grupos.php");
        exit;
    } else {
        // Mostrar mensaje de error si la actualización falló
        echo "Error al guardar los cambios. Intente nuevamente.";
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    // Si no se ha enviado el formulario correctamente, redirigir al administrador a la página de grupos
    echo "No se han recibido los datos del formulario.";
    exit;
}
?>
