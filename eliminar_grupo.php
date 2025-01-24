<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Verificar si se ha pasado el ID del grupo a eliminar
if (isset($_GET['id'])) {
    $id_grupo = intval($_GET['id']); // Asegurarse de que el ID sea un número entero

    try {
        // Verificar si el grupo existe antes de eliminarlo
        $sql = "SELECT id_grupo FROM grupos WHERE id_grupo = :id_grupo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // El grupo existe, proceder a eliminarlo
            $sql_delete = "DELETE FROM grupos WHERE id_grupo = :id_grupo";
            $stmt_delete = $pdo->prepare($sql_delete);
            $stmt_delete->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);

            if ($stmt_delete->execute()) {
                // Redirigir a la página de gestión de grupos después de eliminar
                header("Location: gestionar_grupos.php");
                exit;
            } else {
                // Si no se puede eliminar el grupo, mostrar un error
                echo "Error al eliminar el grupo.";
            }
        } else {
            echo "El grupo no existe.";
        }
    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
} else {
    echo "No se ha especificado un ID de grupo para eliminar.";
}
?>
