<?php
require 'db.php'; // Asegúrate de que este archivo tiene la conexión PDO

$nivel_id = $_GET['nivel_id'] ?? null;

if ($nivel_id) {
    try {
        // Consulta para obtener las materias del nivel seleccionado
        $query = "SELECT materia_id, nombre 
                  FROM materias 
                  WHERE nivel_id = :nivel_id 
                  ORDER BY nombre";

        // Preparar la consulta
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT); // Enlazar el parámetro nivel_id
        $stmt->execute();

        // Verificar si hay resultados
        if ($stmt->rowCount() > 0) {
            echo '<option value="">Selecciona una materia</option>';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $row['materia_id'] . '">' . htmlspecialchars($row['nombre']) . '</option>';
            }
        } else {
            echo '<option value="">No hay materias disponibles para este nivel</option>';
        }
    } catch (PDOException $e) {
        echo '<option value="">Error al cargar materias: ' . htmlspecialchars($e->getMessage()) . '</option>';
    }
} else {
    echo '<option value="">Nivel no especificado</option>';
}
?>
