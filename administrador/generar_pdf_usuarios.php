<?php
// generar_pdf_usuarios.php

session_start();

// Verificar si el usuario está logueado y tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
require_once '../db.php';

// Incluir la librería FPDF
require_once '../librerias/fpdf/fpdf.php'; // Ruta correcta a fpdf.php

// Crear una clase personalizada para el PDF
class PDF extends FPDF {
    // Encabezado
    function Header() {
        // $this->Image('imagenes/logo.png', 10, 10, 30); // Logo (opcional)
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Reporte de Usuarios del Sistema'), 0, 1, 'C');
        $this->Ln(10); // Salto de línea
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15); // Posición a 1.5 cm desde el fondo
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

try {
    // Obtener la lista de usuarios con el nombre del rol
    $sql = "
        SELECT u.usuario_id, u.nombre, u.correo, r.nombre AS rol
        FROM usuarios u
        JOIN roles r ON u.rol_id = r.rol_id
    ";
    $stmt = $pdo->query($sql);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Crear una instancia de la clase personalizada
    $pdf = new PDF();
    $pdf->AliasNbPages(); // Habilitar el número total de páginas
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10); // Fuente Arial, tamaño 10

    // Título del PDF
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Reporte de Usuarios del Sistema'), 0, 1, 'C');
    $pdf->Ln(10); // Salto de línea

    // Encabezados de la tabla con fondo azul y texto blanco
    $pdf->SetFillColor(0, 102, 204); // Azul
    $pdf->SetTextColor(255, 255, 255); // Blanco
    $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(60, 10, iconv('UTF-8', 'windows-1252', 'Nombre'), 1, 0, 'C', true);
    $pdf->Cell(80, 10, iconv('UTF-8', 'windows-1252', 'Correo'), 1, 0, 'C', true);
    $pdf->Cell(30, 10, iconv('UTF-8', 'windows-1252', 'Rol'), 1, 1, 'C', true);

    // Restaurar el color del texto a negro
    $pdf->SetTextColor(0, 0, 0);

    // Contenido de la tabla con fondo gris claro en filas alternas
    $fill = false; // Alternar el relleno
    foreach ($usuarios as $usuario) {
        $pdf->SetFillColor($fill ? 240 : 255, 240, 240); // Gris claro o blanco

        // ID (no necesita MultiCell porque es un número corto)
        $pdf->Cell(20, 10, $usuario['usuario_id'], 1, 0, 'C', $fill);

        // Nombre (usar MultiCell para evitar desbordamiento)
        $pdf->SetXY(30, $pdf->GetY()); // Posicionar el cursor
        $pdf->MultiCell(60, 10, iconv('UTF-8', 'windows-1252', $usuario['nombre']), 1, 'L', $fill);

        // Correo (usar MultiCell para evitar desbordamiento)
        $pdf->SetXY(90, $pdf->GetY() - 10); // Ajustar la posición Y
        $pdf->MultiCell(80, 10, iconv('UTF-8', 'windows-1252', $usuario['correo']), 1, 'L', $fill);

        // Rol (usar MultiCell para evitar desbordamiento)
        $pdf->SetXY(170, $pdf->GetY() - 10); // Ajustar la posición Y
        $pdf->MultiCell(30, 10, iconv('UTF-8', 'windows-1252', $usuario['rol']), 1, 'C', $fill);

        $fill = !$fill; // Alternar el relleno
    }

    // Salida del PDF
    $pdf->Output('I', 'reporte_usuarios.pdf'); // 'D' fuerza la descarga del archivo
} catch (PDOException $e) {
    die("Error al generar el PDF: " . $e->getMessage());
}