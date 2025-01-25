<?php
session_start();
require '../db.php';

$nivel_id = $_GET['nivel_id'] ?? '';

if ($nivel_id) {
    try {
        $stmt = $pdo->prepare("SELECT id_grupo, grado, turno FROM grupos WHERE nivel_id = :nivel_id");
        $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
        $stmt->execute();
        $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $options = '<option value="">Selecciona un grupo</option>';
        foreach ($grupos as $grupo) {
            $options .= '<option value="' . $grupo['id_grupo'] . '">' . htmlspecialchars($grupo['grado'] . ' - ' . $grupo['turno']) . '</option>';
        }
        echo $options;
    } catch (PDOException $e) {
        die("Error al cargar grupos: " . htmlspecialchars($e->getMessage()));
    }
} else {
    echo '<option value="">Selecciona un grupo</option>';
}
?>