<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
require_once 'db.php';

// Verificar si se ha recibido el ID del usuario a eliminar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de usuario no especificado o inválido.";
    exit;
}

$usuario_id = (int) $_GET['id'];

// Prevenir la eliminación del propio administrador que está logueado
if ($usuario_id === $_SESSION['usuario_id']) {
    echo "No puedes eliminar tu propia cuenta.";
    exit;
}

try {
    // Preparar la consulta para eliminar al usuario
    $sql = "DELETE FROM usuarios WHERE usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql);

    // Asignar valores a los parámetros
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        header("Location: gestionar_usuarios.php?mensaje=usuario_eliminado");
        exit;
    } else {
        echo "Error al eliminar el usuario. Por favor, inténtalo nuevamente.";
    }
} catch (PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
}
?>
