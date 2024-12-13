<?php
error_reporting(E_ALL); // Mostrar todos los errores
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$database = "escuela_sistema";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error al conectar con la base de datos: " . $conn->connect_error);
} else {
    echo ".";
}
?>
