<?php
require_once '../db.php';

$nivel_id = $_GET['nivel_id'] ?? '';

if (empty($nivel_id)) {
    echo json_encode(['error' => 'El parámetro nivel_id es requerido.']);
    exit;
}

try {
    // Consulta para obtener los grupos del nivel seleccionado
    $sql = "
        SELECT id_grupo, grado, turno 
        FROM grupos 
        WHERE nivel_id = :nivel_id
        ORDER BY grado ASC, turno ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
    $stmt->execute();
    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($grupos)) {
        echo json_encode(['error' => 'No se encontraron grupos para el nivel seleccionado.']);
    } else {
        echo json_encode($grupos);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>