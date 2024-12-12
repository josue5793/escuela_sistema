<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Verificar si se ha recibido el ID del usuario a eliminar
if (!isset($_GET['id'])) {
    echo "ID de usuario no especificado.";
    exit;
}

$usuario_id = $_GET['id'];

// Prevenir la eliminación del propio administrador que está logueado
if ($usuario_id == $_SESSION['usuario_id']) {
    echo "No puedes eliminar tu propia cuenta.";
    exit;
}

// Eliminar el usuario de la base de datos
$sql = "DELETE FROM usuarios WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);

if ($stmt->execute()) {
    header("Location: gestionar_usuarios.php?mensaje=usuario_eliminado");
    exit;
} else {
    echo "Error al eliminar el usuario: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
