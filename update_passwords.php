<?php
// Incluir conexión a la base de datos
require_once 'db.php';

// Consultar todas las contraseñas en texto plano
$sql = "SELECT usuario_id, contrasena FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuario_id = $row['usuario_id'];
        $contrasena_plana = $row['contrasena'];

        // Generar hash de la contraseña
        $hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);

        // Actualizar la contraseña en la base de datos
        $update_sql = "UPDATE usuarios SET contrasena = ? WHERE usuario_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $hash, $usuario_id);
        $stmt->execute();
    }
    echo "Contraseñas actualizadas correctamente.";
} else {
    echo "No se encontraron usuarios.";
}

$conn->close();
?>
