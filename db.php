<?php
error_reporting(E_ALL); // Mostrar todos los errores
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$database = "escuela_sistema";

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Establecer el modo de error a excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexión exitosa"; // Descomentar para verificar que la conexión es exitosa
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
