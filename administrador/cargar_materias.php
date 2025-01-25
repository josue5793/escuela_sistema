<?php
session_start();
require '../db.php';

$nivel_id = $_GET['nivel_id'] ?? '';

if ($nivel_id) {
    try {
        $stmt = $pdo->prepare("SELECT materia_id, nombre FROM materias WHERE nivel_id = :nivel_id");
        $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
        $stmt->execute();
        $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $options = '<option value="">Selecciona una materia</option>';
        foreach ($materias as $materia) {
            $options .= '<option value="' . $materia['materia_id'] . '">' . htmlspecialchars($materia['nombre']) . '</option>';
        }
        echo $options;
    } catch (PDOException $e) {
        die("Error al cargar materias: " . htmlspecialchars($e->getMessage()));
    }
} else {
    echo '<option value="">Selecciona una materia</option>';
}
?>