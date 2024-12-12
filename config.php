<?php
$host = 'localhost'; // Nombre del host
$usuario = 'root'; // Usuario de la base de datos
$contrasena = ''; // Contraseña (por defecto es vacía en XAMPP)
$base_de_datos = 'escuela_sistema'; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar si hay algún error de conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
