<?php
session_start();
require_once 'db.php'; // Incluye la conexión a la base de datos

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y validar los datos del formulario
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Validación de formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Correo electrónico no válido.";
        header("Location: login.php");
        exit;
    }

    try {
        // Preparar la consulta para buscar el usuario por correo
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':correo', $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar la contraseña ingresada contra el hash almacenado
            if (password_verify($password, $usuario['contrasena'])) {
                // Guardar datos del usuario en sesión
                $_SESSION['usuario_id'] = $usuario['usuario_id'];
                $_SESSION['nombre'] = $usuario['nombre'];

                // Si el sistema está usando 'rol_id', obtén el rol de la tabla 'roles'
                $rol_id = $usuario['rol_id'];
                $sql_rol = "SELECT nombre FROM roles WHERE rol_id = :rol_id";
                $stmt_rol = $pdo->prepare($sql_rol);
                $stmt_rol->bindParam(':rol_id', $rol_id, PDO::PARAM_INT);
                $stmt_rol->execute();
                $rol = $stmt_rol->fetch(PDO::FETCH_ASSOC)['nombre'];

                if ($rol) {
                    $_SESSION['rol'] = $rol; // Almacenar el rol en la sesión

                    // Redirigir según el rol del usuario
                    $nombre = htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8');

                    if ($rol === 'administrador') {
                        $_SESSION['success'] = "Bienvenido, $nombre. Eres Administrador.";
                        header("Location: administrador\administrador_dashboard.php");
                        exit;
                    } elseif ($rol === 'profesor') {
                        $_SESSION['success'] = "Bienvenido, $nombre. Eres Profesor.";
                        header("Location: profesor\dashboard_profesor.php");
                        exit;
                    } elseif ($rol === 'director') {
                        $_SESSION['success'] = "Bienvenido, $nombre. Eres Director.";
                        header("Location: director\director.php");
                        exit;
                    } else {
                        $_SESSION['error'] = "Rol no reconocido.";
                        header("Location: login.php");
                        exit;
                    }
                } else {
                    $_SESSION['error'] = "Rol no encontrado.";
                    header("Location: login.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = "Contraseña incorrecta. Por favor, inténtalo de nuevo.";
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "El correo ingresado no está registrado.";
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al procesar el inicio de sesión: " . $e->getMessage();
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Acceso no autorizado.";
    header("Location: login.php");
    exit;
}
?>
