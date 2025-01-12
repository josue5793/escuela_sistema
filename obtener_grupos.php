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
    $query_grupos = "SELECT id_grupo, grado, turno FROM grupos WHERE nivel_id = ?";
    $stmt = $conn->prepare($query_grupos);

    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $nivel_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $grupos = [];

    while ($row = $result->fetch_assoc()) {
        $grupos[] = $row;
    }

    // Verificar si se encontraron grupos
    if (empty($grupos)) {
        echo json_encode(["error" => "No se encontraron grupos para el nivel seleccionado."]);
    } else {
        echo json_encode($grupos); // Devolver los grupos en formato JSON
    }

    $stmt->close();
} catch (Exception $e) {
    // Captura de errores y respuesta en formato JSON
    error_log("Error en obtener_grupos.php: " . $e->getMessage()); // Registrar error en el log del servidor
    echo json_encode(["error" => "Ocurrió un error al obtener los grupos."]);
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
