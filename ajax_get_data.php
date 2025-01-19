<?php
session_start();
require 'db.php'; // Conexión a la base de datos

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit;
}

$response = [];

// Obtener grupos o materias según la solicitud
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'get_grupos' && isset($_GET['nivel_id'])) {
        // Obtener grupos del nivel seleccionado
        $nivel_id = $_GET['nivel_id'];
        try {
            $stmt = $pdo->prepare("SELECT id_grupo, grado, turno FROM grupos WHERE nivel_id = :nivel_id");
            $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
            $stmt->execute();
            $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['grupos'] = $grupos;
        } catch (PDOException $e) {
            $response['error'] = "Error al obtener los grupos: " . htmlspecialchars($e->getMessage());
        }
    }

    if ($_GET['action'] === 'get_materias' && isset($_GET['nivel_id'])) {
        // Obtener materias del nivel seleccionado
        $nivel_id = $_GET['nivel_id'];
        try {
            $stmt = $pdo->prepare("SELECT materia_id, nombre FROM materias WHERE nivel_id = :nivel_id");
            $stmt->bindParam(':nivel_id', $nivel_id, PDO::PARAM_INT);
            $stmt->execute();
            $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response['materias'] = $materias;
        } catch (PDOException $e) {
            $response['error'] = "Error al obtener las materias: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Devolver la respuesta como JSON
echo json_encode($response);
