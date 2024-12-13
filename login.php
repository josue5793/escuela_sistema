<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="CSS/style.css"> <!-- Enlace al archivo de estilos -->
</head>
<body>
    <!-- Menú Superior -->
    <header>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="login.php">Iniciar sesión</a></li>
                <li><a href="#">Otro Enlace</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenedor Principal -->
    <div class="main-container">
        <div class="login-box">
            <h2>Iniciar sesión</h2>
            <form action="procesar_login.php" method="POST">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Iniciar sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
