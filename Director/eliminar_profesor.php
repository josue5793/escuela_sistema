<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de director
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'director') {
    header("Location: ../login.php");
    exit;
}

// Incluir la conexión a la base de datos
require_once '../db.php';

// Obtener el profesor_id desde la URL
if (!isset($_GET['profesor_id'])) {
    header("Location: consultar_profesores.php");
    exit;
}

$profesor_id = $_GET['profesor_id'];

// Eliminar el profesor
try {
    $stmt = $pdo->prepare("DELETE FROM profesores WHERE profesor_id = :profesor_id");
    $stmt->execute([':profesor_id' => $profesor_id]);

    // Redirigir con un mensaje de éxito
    header("Location: consultar_profesores.php?mensaje=Profesor eliminado correctamente");
    exit;
} catch (PDOException $e) {
    die("Error al eliminar el profesor: " . $e->getMessage());
}