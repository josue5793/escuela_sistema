<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nuevo Usuario</title>
       <link rel="stylesheet" href="css/agregar_usuario.css">
</head>
<body>

    <!-- Menú Superior -->
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="gestionar_usuarios.php">Gestión de Usuarios</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenedor Principal -->
    <div class="main-container">
        <div class="form-container">
            <h2>Agregar Nuevo Usuario</h2>
            <form action="agregar_usuario_process.php" method="POST">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>

                <label for="correo">Correo electrónico:</label>
                <input type="email" name="correo" id="correo" required>

                <label for="contrasena">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" required>

                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="administrador">Administrador</option>
                    <option value="profesor">Profesor</option>
                    <option value="director">Director</option>
                </select>

                <button type="submit">Agregar Usuario</button>
            </form>
        </div>
    </div>

</body>
</html>
