<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profesor_id = $_POST['profesor_id'];
    $especialidad = trim($_POST['especialidad']);

    $query = "UPDATE profesores SET especialidad = :especialidad WHERE profesor_id = :profesor_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':especialidad' => $especialidad, ':profesor_id' => $profesor_id]);

    echo "Especialidad actualizada correctamente.";
} else {
    echo "Método no permitido.";
}
?>
