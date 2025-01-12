<?php
require 'db.php';

$nivel_id = $_GET['nivel_id'] ?? null;
if ($nivel_id) {
    $query = "SELECT p.profesor_id, u.nombre AS profesor_nombre 
              FROM profesores p
              JOIN usuarios u ON p.usuario_id = u.usuario_id
              WHERE p.nivel_id = ? 
              ORDER BY u.nombre";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $nivel_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">Selecciona un profesor</option>';
    while ($profesor = $result->fetch_assoc()) {
        echo '<option value="' . $profesor['profesor_id'] . '">' . htmlspecialchars($profesor['profesor_nombre']) . '</option>';
    }
    $stmt->close();
}
?>
