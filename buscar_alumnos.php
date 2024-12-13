<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión y tiene un rol válido
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'], ['administrador', 'director'])) {
    header("Location: login.php?error=acceso_denegado");
    exit;
}

// Definir la ruta de inicio según el rol del usuario
$inicio_ruta = ($_SESSION['rol'] === 'administrador') ? 'administrador.php' : 'director.php';

// Incluir conexión a la base de datos
require_once 'db.php';

// Obtener la búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Consultar la base de datos
$alumnos = [];
if (!empty($busqueda)) {
    $sql = "SELECT * FROM alumnos WHERE 
            matricula LIKE ? OR 
            apellido_paterno LIKE ? OR 
            apellido_materno LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_busqueda = '%' . $busqueda . '%';
    $stmt->bind_param("sss", $like_busqueda, $like_busqueda, $like_busqueda);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Alumnos</title>
    <link rel="stylesheet" href="CSS/gestionar_alumnos.css">
</head>
<body>
    <!-- Menú de navegación -->
    <nav>
        <div>
            <a href="<?php echo $inicio_ruta; ?>">Inicio</a>
            <a href="gestionar_profesores.php">Profesores</a>
            <a href="gestionar_alumnos.php">Alumnos</a>
        </div>
        <div>
            <a href="cerrar_sesion.php">Cerrar Sesión</a>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container">
        <h1>Resultados de la Búsqueda</h1>
        <form method="GET" action="buscar_alumnos.php">
            <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Matrícula o apellidos">
            <button type="submit">Buscar</button>
        </form>
        
        <?php if (empty($alumnos)): ?>
            <p>No se encontraron alumnos con el criterio proporcionado.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Matrícula</th>
                        <th>Nombre Completo</th>
                        <th>Grado Escolar</th>
                        <th>Teléfono</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos as $alumno): ?>
                        <tr>
                            <td>
                                <?php if (!empty($alumno['foto'])): ?>
                                    <img src="<?php echo htmlspecialchars($alumno['foto']); ?>" alt="Foto" class="foto-alumno">
                                <?php else: ?>
                                    <img src="img/default_avatar.png" alt="Sin foto" class="foto-alumno">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($alumno['matricula']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['nombres'] . ' ' . $alumno['apellido_paterno'] . ' ' . $alumno['apellido_materno']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['grado_escolar']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['telefono']); ?></td>
                            <td>
                                <a href="editar_alumno.php?matricula=<?php echo $alumno['matricula']; ?>">Editar</a> | 
                                <a href="eliminar_alumno.php?matricula=<?php echo $alumno['matricula']; ?>" onclick="return confirm('¿Está seguro de que desea eliminar este alumno?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
