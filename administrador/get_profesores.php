<?php
require '../db.php'; // Se incluye el archivo con la conexión PDO

$nivel_id = $_GET['nivel_id'] ?? null; // Obtener el nivel_id de la solicitud GET

if ($nivel_id) {
    try {
        // Preparar la consulta con marcadores de posición
        $query = "SELECT p.profesor_id, u.nombre AS profesor_nombre 
                  FROM profesores p
                  JOIN usuarios u ON p.usuario_id = u.usuario_id
                  JOIN profesor_nivel pn ON p.profesor_id = pn.profesor_id
                  WHERE pn.nivel_id = :nivel_id 
                  ORDER BY u.nombre";
        $stmt = $pdo->prepare($query); // Preparar la consulta
        $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT); // Enlazar el parámetro nivel_id

        $stmt->execute(); // Ejecutar la consulta

        // Generar las opciones para el dropdown
        echo '<option value="">Selecciona un profesor</option>';
        while ($profesor = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $profesor['profesor_id'] . '">' . htmlspecialchars($profesor['profesor_nombre']) . '</option>';
        }
    } catch (PDOException $e) {
        // En caso de error, mostrar un mensaje genérico
        echo '<option value="">Error al cargar profesores: ' . htmlspecialchars($e->getMessage()) . '</option>';
    }
} else {
    // Si no se recibe nivel_id, mostrar un mensaje en el dropdown
    echo '<option value="">Nivel no especificado</option>';
}
?>
