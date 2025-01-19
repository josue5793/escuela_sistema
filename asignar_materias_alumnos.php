<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require 'db.php'; // Conexión a la base de datos

$mensaje = "";

// Eliminar una asignación
if (isset($_GET['delete'])) {
    $id_asignacion = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM grupo_materia WHERE id = :id");
        $stmt->bindParam(':id', $id_asignacion, PDO::PARAM_INT);
        $stmt->execute();
        $mensaje = "Asignación eliminada correctamente.";
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar la asignación: " . htmlspecialchars($e->getMessage());
    }
}

// Procesar formulario para asignar materia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nivel_id = $_POST['nivel_id'];
    $grupo_id = $_POST['grupo_id'];
    $materia_id = $_POST['materia_id'];

    if ($nivel_id && $grupo_id && $materia_id) {
        try {
            // Validar que la materia no esté ya asignada al grupo
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM grupo_materia WHERE grupo_id = :grupo_id AND materia_id = :materia_id");
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                $mensaje = "La materia ya está asignada a este grupo.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO grupo_materia (grupo_id, materia_id) VALUES (:grupo_id, :materia_id)");
                $stmt->bindParam(':grupo_id', $grupo_id);
                $stmt->bindParam(':materia_id', $materia_id);
                $stmt->execute();
                $mensaje = "Materia asignada correctamente.";
            }
        } catch (PDOException $e) {
            $mensaje = "Error al asignar la materia: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $mensaje = "Por favor, complete todos los campos.";
    }
}

// Obtener niveles
try {
    $niveles_result = $pdo->query("SELECT nivel_id, nivel_nombre FROM niveles");
    $niveles = $niveles_result->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener niveles: " . htmlspecialchars($e->getMessage()));
}

// Obtener asignaciones categorizadas por nivel y grado
try {
    $stmt = $pdo->query("SELECT gm.id, g.grado, g.turno, m.nombre AS materia, n.nivel_nombre, n.nivel_id
                         FROM grupo_materia gm
                         JOIN grupos g ON gm.grupo_id = g.id_grupo
                         JOIN materias m ON gm.materia_id = m.materia_id
                         JOIN niveles n ON g.nivel_id = n.nivel_id
                         ORDER BY n.nivel_id, g.grado, g.turno");
    $asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener asignaciones: " . htmlspecialchars($e->getMessage()));
}

// Agrupar asignaciones por nivel y grado
$datosAgrupados = [];
foreach ($asignaciones as $asignacion) {
    $nivel = $asignacion['nivel_nombre'];
    $grado = $asignacion['grado'] . ' - ' . $asignacion['turno'];
    if (!isset($datosAgrupados[$nivel])) {
        $datosAgrupados[$nivel] = [];
    }
    if (!isset($datosAgrupados[$nivel][$grado])) {
        $datosAgrupados[$nivel][$grado] = [];
    }
    $datosAgrupados[$nivel][$grado][] = $asignacion;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Materias a Grupos</title>
    <link rel="stylesheet" href="CSS/asignar_materias_a_grupos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Filtrar materias asignadas
            $('#buscar').on('input', function() {
                var filtro = $(this).val().toLowerCase();
                $('.nivel').each(function() {
                    var visible = false;
                    $(this).find('.grado').each(function() {
                        var texto = $(this).text().toLowerCase();
                        if (texto.includes(filtro)) {
                            $(this).show();
                            visible = true;
                        } else {
                            $(this).hide();
                        }
                    });
                    $(this).toggle(visible);
                });
            });
        });
    </script>
</head>
<body>
    <main class="main-container">
        <header>
            <h1>Asignar Materias a Grupos</h1>
        </header>

        <section>
            <form action="" method="POST">
                <label for="nivel_id">Nivel:</label>
                <select id="nivel_id" name="nivel_id" required>
                    <option value="">Selecciona un nivel</option>
                    <?php foreach ($niveles as $nivel): ?>
                        <option value="<?php echo $nivel['nivel_id']; ?>">
                            <?php echo htmlspecialchars($nivel['nivel_nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="grupo_id">Grupo:</label>
                <select id="grupo_id" name="grupo_id" required>
                    <option value="">Selecciona un grupo</option>
                </select>

                <label for="materia_id">Materia:</label>
                <select id="materia_id" name="materia_id" required>
                    <option value="">Selecciona una materia</option>
                </select>

                <button type="submit">Asignar Materia</button>
            </form>
        </section>

        <section>
            <h2>Materias Asignadas</h2>
            <input type="text" id="buscar" placeholder="Buscar por nivel, grado o grupo">
            <div id="materias-container">
                <?php foreach ($datosAgrupados as $nivel => $grados): ?>
                    <div class="nivel">
                        <h3><?php echo htmlspecialchars($nivel); ?></h3>
                        <?php foreach ($grados as $grado => $materias): ?>
                            <div class="grado">
                                <h4><?php echo htmlspecialchars($grado); ?></h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Materia</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($materias as $materia): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($materia['materia']); ?></td>
                                                <td>
                                                    <a href="?delete=<?php echo $materia['id']; ?>" onclick="return confirm('¿Eliminar esta asignación?')">Eliminar</a>
                                                    <a href="editar_asignacion.php?id=<?php echo $materia['id']; ?>">Editar</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if ($mensaje): ?>
            <div class="mensaje"> <?php echo htmlspecialchars($mensaje); ?> </div>
        <?php endif; ?>
    </main>
</body>
</html>
