<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inicie sesión para acceder al Sistema de Control Escolar del Centro Escolar Veracruz.">
    <title>Iniciar sesión - Sistema de Control Escolar</title>
    <link rel="stylesheet" href="CSS/login2.css"> <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Íconos Font Awesome -->
</head>
<body>
    <!-- Contenedor Principal -->
    <div class="container">
        <!-- Logo Header -->
        <header class="logo-header">
            <img id="logo" src="images/CEV.png" alt="Logo Centro Escolar Veracruz" class="logo">
            <h1>Sistema de Control Escolar</h1>
            <p><strong>Centro Escolar Veracruz</strong></p>
            <p>Escuela Particular Incorporada.</p>
        </header>

        <!-- Sección de Login -->
        <div class="login-box">
            <h2>Iniciar sesión</h2>

            <!-- Mostrar mensajes de error o éxito -->
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']); // Limpiar mensaje después de mostrarlo
            }
            if (isset($_SESSION['success'])) {
                echo "<p class='success-message'>" . $_SESSION['success'] . "</p>";
                unset($_SESSION['success']); // Limpiar mensaje después de mostrarlo
            }
            ?>

            <form action="procesar_login.php" method="POST">
                <label for="email">
                    <i class="fas fa-envelope"></i> Correo electrónico:
                </label>
                <input type="email" id="email" name="email" placeholder="Ingresa tu correo" required>

                <label for="password">
                    <i class="fas fa-lock"></i> Contraseña:
                </label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                </button>
            </form>
            <!-- Botón para regresar a la página de inicio -->
            <a href="index.html" class="back-button">
                <i class="fas fa-arrow-left"></i> Regresar a Inicio
            </a>
        </div>
    </div>

    <!-- Pie de Página -->
    <footer>
        <p>&copy; 2024 Centro Escolar Veracruz. Todos los derechos reservados.</p>
        <p>
            <a href="politica-privacidad.html">Política de Privacidad</a> |
            <a href="terminos-condiciones.html">Términos y Condiciones</a>
        </p>
    </footer>
</body>
</html>
