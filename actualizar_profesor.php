<?php
include('db.php');
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['profesor_id'], $_POST['especialidad'], $_POST['telefono'])) {
        $profesor_id = intval($_POST['profesor_id']);
        $especialidad = trim($_POST['especialidad']);
        $telefono = trim($_POST['telefono']);

        try {
            $query = "UPDATE profesores SET especialidad = :especialidad, telefono = :telefono WHERE profesor_id = :profesor_id";
            $stmt = $pdo->prepare($query);
            $success = $stmt->execute([
                ':especialidad' => $especialidad,
                ':telefono' => $telefono,
                ':profesor_id' => $profesor_id
            ]);

            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Excepción: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
