<?php
require 'db.php';

$nivel_id = $_GET['nivel_id'] ?? null;
if ($nivel_id) {
    $query = "SELECT m.materia_id, m.nombre 
              FROM materias m
              WHERE m.nivel_id = ? 
              ORDER BY m.nombre";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $nivel_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">Selecciona una materia</option>';
    while ($materia = $result->fetch_assoc()) {
        echo '<option value="' . $materia['materia_id'] . '">' . htmlspecialchars($materia['nombre']) . '</option>';
    }
    $stmt->close();
}
?>
