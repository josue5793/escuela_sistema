<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Función para manejar la carga de la imagen del logo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['logo'])) {
        // Obtener la imagen cargada
        $targetDir = "images/"; // Carpeta donde se guardarán los logos
        $targetFile = $targetDir . basename($_FILES["logo"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Verificar si el archivo es una imagen
        if (getimagesize($_FILES["logo"]["tmp_name"])) {
            // Verificar si el archivo ya existe
            if (file_exists($targetFile)) {
                echo "Lo siento, el archivo ya existe.";
            } else {
                // Intentar mover el archivo cargado
                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {
                    echo "El archivo " . basename($_FILES["logo"]["name"]) . " ha sido subido exitosamente.";
                } else {
                    echo "Hubo un error al subir el archivo.";
                }
            }
        } else {
            echo "Por favor, suba una imagen válida.";
        }
    }

    // Cambiar paleta de colores si es necesario
    if (isset($_POST['primary_color']) && isset($_POST['accent_color'])) {
        $primaryColor = $_POST['primary_color'];
        $accentColor = $_POST['accent_color'];
        
        // Guardar estos colores en un archivo o base de datos
        // (Este paso es solo para ejemplificar. Necesitarías guardar estos datos de forma persistente)
        file_put_contents('config/colors.json', json_encode([
            'primary_color' => $primaryColor,
            'accent_color' => $accentColor
        ]));

        echo "Paleta de colores actualizada exitosamente.";
    }
}

// Leer los colores configurados previamente
$colors = json_decode(file_get_contents('config/colors.json'), true);
$primaryColor = $colors['primary_color'] ?? '#007bff'; // Color primario por defecto
$accentColor = $colors['accent_color'] ?? '#6c757d';  // Color de acento por defecto
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Sitio</title>
    <link rel="stylesheet" href="CSS/administrador.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Administrar Sitio</h1>
            <div class="navbar-right">
                <a href="administrador.php" class="logout-button">Volver al Panel</a>
            </div>
        </div>
    </header>

    <main class="main-container">
        <section class="manage-logo">
            <h2>Cambiar Logo</h2>
            <form action="administrar_sitio.php" method="POST" enctype="multipart/form-data">
                <label for="logo">Selecciona el nuevo logo:</label>
                <input type="file" name="logo" id="logo" accept="image/*" required>
                <button type="submit">Subir Logo</button>
            </form>
        </section>

        <section class="manage-colors">
            <h2>Cambiar Paleta de Colores</h2>
            <form action="administrar_sitio.php" method="POST">
                <label for="primary_color">Color Primario:</label>
                <input type="color" name="primary_color" id="primary_color" value="<?php echo $primaryColor; ?>" required>
                
                <label for="accent_color">Color de Acento:</label>
                <input type="color" name="accent_color" id="accent_color" value="<?php echo $accentColor; ?>" required>
                
                <button type="submit">Actualizar Colores</button>
            </form>
        </section>
    </main>
</body>
</html>
