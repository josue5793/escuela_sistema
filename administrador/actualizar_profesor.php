<?php
session_start();
include('../db.php');

// Verificar si el usuario está logueado y tiene el rol de administrador o director
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'director')) {
    die("Acceso denegado.");
}

// Obtener datos del formulario
$profesor_id = $_POST['profesor_id'] ?? null;
$especialidad = $_POST['especialidad'] ?? '';
$telefono = $_POST['telefono'] ?? '';

if (!$profesor_id) {
    die("ID de profesor no especificado.");
}

// Validar los datos
if (empty($especialidad) || empty($telefono)) {
    die("Todos los campos son obligatorios.");
}

// Validar el formato del teléfono
if (!preg_match('/^\d{10}$/', $telefono)) {
    die("El teléfono debe contener 10 dígitos.");
}

// Actualizar los datos del profesor en la base de datos
try {
    $query = "UPDATE profesores SET especialidad = :especialidad, telefono = :telefono WHERE profesor_id = :profesor_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'especialidad' => $especialidad,
        'telefono' => $telefono,
        'profesor_id' => $profesor_id
    ]);

    echo "Profesor actualizado correctamente.";
} catch (PDOException $e) {
    die("Error al actualizar el profesor: " . $e->getMessage());
}
?>