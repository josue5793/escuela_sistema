<?php
require_once '../db.php';

$nivel = $_GET['nivel'] ?? '';
$grado = $_GET['grado'] ?? '';

try {
    $sql = "
        SELECT DISTINCT turno 
        FROM grupos g
        JOIN niveles n ON g.nivel_id = n.nivel_id
        WHERE n.nivel_nombre = :nivel AND g.grado = :grado
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nivel', $nivel, PDO::PARAM_STR);
    $stmt->bindParam(':grado', $grado, PDO::PARAM_STR);
    $stmt->execute();
    $turnos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($turnos);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>