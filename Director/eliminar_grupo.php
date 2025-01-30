<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

require_once '../db.php';

// Obtener el ID del grupo a eliminar
$grupo_id = $_GET['id'] ?? null;

if (!$grupo_id) {
    header("Location: gestion_grupos.php");
    exit;
}

// Eliminar el grupo
try {
    $stmt = $pdo->prepare("DELETE FROM grupos WHERE id_grupo = :id_grupo");
    $stmt->execute([':id_grupo' => $grupo_id]);

    header("Location: gestion_grupos.php");
    exit;
} catch (PDOException $e) {
    die("Error al eliminar el grupo: " . $e->getMessage());
}
?>