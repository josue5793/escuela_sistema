<?php
include('db.php');
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador o director
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'administrador' && $_SESSION['rol'] !== 'director')) {
    header("Location: login.php");
    exit;
}

// Obtener los profesores, niveles y materias asignadas categorizadas por nivel
$query_profesores = "
    SELECT 
        p.profesor_id, 
        u.nombre, 
        p.especialidad, 
        p.telefono,
        GROUP_CONCAT(DISTINCT n.nivel_nombre ORDER BY n.nivel_nombre ASC) AS niveles,
        GROUP_CONCAT(DISTINCT CONCAT(n.nivel_nombre, ': ', m.nombre) ORDER BY n.nivel_nombre, m.nombre SEPARATOR '|') AS materias
    FROM profesores p
    JOIN usuarios u ON p.usuario_id = u.usuario_id
    LEFT JOIN profesor_nivel pn ON p.profesor_id = pn.profesor_id
    LEFT JOIN niveles n ON pn.nivel_id = n.nivel_id
    LEFT JOIN profesor_materia pm ON p.profesor_id = pm.profesor_id
    LEFT JOIN materias m ON pm.materia_id = m.materia_id AND m.nivel_id = n.nivel_id
    GROUP BY p.profesor_id";
$stmt_profesores = $pdo->query($query_profesores);
$result_profesores = $stmt_profesores->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Profesores</title>
    <link rel="stylesheet" href="css/consulta_profesores2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Barra de navegación superior -->
    <header class="navbar">
        <div class="navbar-container">
            <h1>Consulta de Profesores</h1>
            <div class="navbar-right">
                <span>Bienvenid@: <?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></span>
                <a href="logout.php" class="logout-button">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Botones de control -->
    <div class="button-container">
        <a href="administrador.php" class="control-button">
            <i class="bi bi-house-door"></i>
            <span>Regresar al panel de administrador</span>
        </a>
        <a href="asignar_niveles.php" class="control-button">
            <i class="bi bi-pen"></i>
            <span>Asignación de Niveles</span>
        </a>
        <a href="asignar_materias.php" class="control-button">
            <i class="bi bi-book"></i>
            <span>Asignar Materias</span>
        </a>
    </div>

    <!-- Contenido Principal -->
    <main class="main-container">
        <section class="welcome-section">
            <h2>Consulta de Docentes</h2>
            <p>Información de los profesores, sus especialidades, niveles y materias asignadas.</p>
        </section>

        <!-- Tabla de profesores -->
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Teléfono</th>
                    <th>Niveles Asignados</th>
                    <th>Materias Asignadas</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result_profesores as $profesor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($profesor['nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($profesor['especialidad'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($profesor['telefono'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($profesor['niveles'] ?? ''); ?></td>
                        <td>
                            <?php
                            $materias_por_nivel = explode('|', $profesor['materias'] ?? '');
                            foreach ($materias_por_nivel as $materia_nivel) {
                                echo htmlspecialchars($materia_nivel) . '<br>';
                            }
                            ?>
                        </td>
                        <td>
                            <button class="edit-btn" 
                                    data-profesor-id="<?php echo htmlspecialchars($profesor['profesor_id'] ?? ''); ?>" 
                                    data-nombre="<?php echo htmlspecialchars($profesor['nombre'] ?? ''); ?>" 
                                    data-especialidad="<?php echo htmlspecialchars($profesor['especialidad'] ?? ''); ?>" 
                                    data-telefono="<?php echo htmlspecialchars($profesor['telefono'] ?? ''); ?>">
                                Editar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <!-- Modal para editar profesor -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Profesor</h2>
            <form id="form-editar">
                <input type="hidden" name="profesor_id" id="profesor_id">
                <label for="especialidad">Especialidad:</label>
                <input type="text" name="especialidad" id="especialidad" required>

                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" required>

                <button type="submit">Actualizar</button>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById('editModal');
        var span = document.getElementsByClassName('close')[0];

        // Abrir modal
        $(document).on('click', '.edit-btn', function() {
            var profesorId = $(this).data('profesor-id');
            var especialidad = $(this).data('especialidad');
            var telefono = $(this).data('telefono');

            $('#profesor_id').val(profesorId);
            $('#especialidad').val(especialidad);
            $('#telefono').val(telefono);

            modal.style.display = "block";
        });

        // Cerrar modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
