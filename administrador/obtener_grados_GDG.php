<?php
require_once '../db.php';

$nivel = $_GET['nivel'] ?? '';

if (empty($nivel)) {
    echo json_encode(['error' => 'El parámetro nivel es requerido.']);
    exit;
}

try {
    $sql = "
        SELECT DISTINCT grado 
        FROM grupos g
        JOIN niveles n ON g.nivel_id = n.nivel_id
        WHERE n.nivel_nombre = :nivel
        ORDER BY grado ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nivel', $nivel, PDO::PARAM_STR);
    $stmt->execute();
    $grados = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($grados);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar los grados: ' . $e->getMessage()]);
}
?>