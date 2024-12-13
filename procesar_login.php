<?php
session_start();
require_once 'db.php'; // Incluye la conexión a la base de datos

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Preparar la consulta para buscar el usuario por correo
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña ingresada contra el hash almacenado
        if (password_verify($password, $usuario['contrasena'])) {
            // Guardar datos del usuario en sesión
            $_SESSION['usuario_id'] = $usuario['usuario_id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirigir según el rol del usuario
            $nombre = htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); // Sanitiza el nombre
            $rol = $usuario['rol'];

            if ($rol === 'administrador') {
                echo "
                    <script>
                        alert('Bienvenido, $nombre. Eres Administrador.');
                        window.location.href = 'administrador.php';
                    </script>
                ";
            } elseif ($rol === 'profesor') {
                echo "
                    <script>
                        alert('Bienvenido, $nombre. Eres Profesor.');
                        window.location.href = 'profesor.php';
                    </script>
                ";
            } elseif ($rol === 'director') {
                echo "
                    <script>
                        alert('Bienvenido, $nombre. Eres Director.');
                        window.location.href = 'director.php';
                    </script>
                ";
            } else {
                echo "
                    <script>
                        alert('Rol no reconocido.');
                        window.location.href = 'login.php';
                    </script>
                ";
            }
            exit();
        } else {
            // Contraseña incorrecta
            echo "
                <script>
                    alert('Contraseña incorrecta. Por favor, inténtalo de nuevo.');
                    window.location.href = 'login.php';
                </script>
            ";
        }
    } else {
        // Correo no encontrado
        echo "
            <script>
                alert('El correo ingresado no está registrado.');
                window.location.href = 'login.php';
            </script>
        ";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "
        <script>
            alert('Acceso no autorizado.');
            window.location.href = 'login.php';
        </script>
    ";
}
?>
