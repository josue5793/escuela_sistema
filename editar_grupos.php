<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profesor_id = intval($_POST['profesor_id']);
    $especialidad = $_POST['especialidad'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    $query = "UPDATE profesores SET especialidad = :especialidad, telefono = :telefono WHERE profesor_id = :profesor_id";
    $stmt = $pdo->prepare($query);
    $success = $stmt->execute([':especialidad' => $especialidad, ':telefono' => $telefono, ':profesor_id' => $profesor_id]);

    echo json_encode(['success' => $success]);
}
?>
