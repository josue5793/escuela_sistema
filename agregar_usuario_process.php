<?php
// Incluir conexión a la base de datos
require_once 'db.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    // Validar que los campos no estén vacíos
    if (!empty($nombre) && !empty($correo) && !empty($contrasena) && !empty($rol)) {
        // Encriptar la contraseña antes de almacenarla
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

        // Preparar la consulta SQL para insertar el nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)";

        // Preparar la consulta
        if ($stmt = $conn->prepare($sql)) {
            // Vincular los parámetros
            $stmt->bind_param("ssss", $nombre, $correo, $hashed_password, $rol);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Si se insertó correctamente, redirigir a la gestión de usuarios
                header("Location: gestionar_usuarios.php?mensaje=Usuario%20agregado%20exitosamente");
exit;
            } else {
                echo "Error al agregar el usuario: " . $stmt->error;
            }

            // Cerrar la consulta
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conn->error;
        }
    } else {
        echo "Todos los campos son requeridos.";
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
