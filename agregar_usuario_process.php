<?php
// Incluir conexión a la base de datos
require_once 'db.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol']; // Nombre del rol

    // Validar que los campos no estén vacíos
    if (!empty($nombre) && !empty($correo) && !empty($contrasena) && !empty($rol)) {
        // Encriptar la contraseña antes de almacenarla
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

        // Obtener el ID del rol basado en el nombre del rol
        $sql_rol = "SELECT rol_id FROM roles WHERE nombre = ?";
        $stmt_rol = $conn->prepare($sql_rol);
        $stmt_rol->bind_param("s", $rol);
        $stmt_rol->execute();
        $result_rol = $stmt_rol->get_result();

        if ($result_rol->num_rows > 0) {
            $row_rol = $result_rol->fetch_assoc();
            $rol_id = $row_rol['rol_id']; // Obtener el rol_id correspondiente

            // Preparar la consulta SQL para insertar el nuevo usuario
            $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol_id) VALUES (?, ?, ?, ?)";

            // Preparar la consulta
            if ($stmt = $conn->prepare($sql)) {
                // Vincular los parámetros
                $stmt->bind_param("sssi", $nombre, $correo, $hashed_password, $rol_id);

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
            echo "Rol no válido.";
        }

        // Cerrar la consulta de rol
        $stmt_rol->close();
    } else {
        echo "Todos los campos son requeridos.";
    }
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
