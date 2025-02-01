<?php
// Incluir conexión a la base de datos
require_once 'db.php';

// Encabezado para devolver una respuesta en formato JSON
header('Content-Type: application/json');

// Verificar que se ha recibido el parámetro 'nivel_id'
if (!isset($_GET['nivel_id']) || !is_numeric($_GET['nivel_id'])) {
    echo json_encode(["error" => "Parámetro nivel_id no válido o ausente."]);
    exit;
}

$nivel_id = (int) $_GET['nivel_id'];

try {
    // Consulta para obtener los grupos correspondientes al nivel_id
    $query_grupos = "SELECT id_grupo, CONCAT(grado, ' ', turno) AS grupo FROM grupos WHERE nivel_id = :nivel_id";
    $stmt = $pdo->prepare($query_grupos);

    // Ejecutar la consulta
    $stmt->execute(['nivel_id' => $nivel_id]);

    $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se encontraron grupos
    if (empty($grupos)) {
        echo json_encode(["error" => "No se encontraron grupos para el nivel seleccionado."]);
    } else {
        echo json_encode($grupos); // Devolver los grupos en formato JSON
    }
} catch (PDOException $e) {
    // Captura de errores y respuesta en formato JSON
    error_log("Error en obtener_grupos.php: " . $e->getMessage()); // Registrar error en el log del servidor
    echo json_encode(["error" => "Ocurrió un error al obtener los grupos."]);
}
