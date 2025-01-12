<?php
include 'db.php';

header('Content-Type: application/json'); // Aseguramos que la respuesta sea JSON

if (isset($_GET['nivel_id'])) {
    $nivel_id = $_GET['nivel_id'];

    try {
        // Preparar la consulta para obtener los grupos correspondientes al nivel seleccionado
        $query = "SELECT id_grupo, grado, turno FROM grupos WHERE nivel_id = :nivel_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
        $stmt->execute();

        // Crear un array para almacenar los grupos
        $grupos = [];
        while ($grupo = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $grupos[] = $grupo;
        }

        // Enviar la respuesta en formato JSON
        echo json_encode($grupos);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error al cargar los grupos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Nivel no especificado"]);
}
?>
