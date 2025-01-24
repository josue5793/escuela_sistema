<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

ob_start(); // Captura cualquier salida accidental

require_once '../db.php';
require_once '../librerias/fpdf/fpdf.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, utf8_decode('Reporte General de Alumnos'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function TableHeader() {
        $this->SetFillColor(0, 102, 204);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(20, 10, 'Foto', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Matrícula', 1, 0, 'C', true);
        $this->Cell(70, 10, 'Nombre', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Grupo', 1, 1, 'C', true);
        $this->SetTextColor(0, 0, 0);
    }
}

try {
    $sql = "
        SELECT a.*, n.nivel_nombre, CONCAT(g.grado, ' ', g.turno) AS grupo 
        FROM alumnos a
        LEFT JOIN niveles n ON a.nivel_id = n.nivel_id
        LEFT JOIN grupos g ON a.grupo_id = g.id_grupo
        ORDER BY g.grado, g.turno, a.apellidos
    ";
    $stmt = $pdo->query($sql);
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $alumnosPorGrado = [];
    foreach ($alumnos as $alumno) {
        $grado = $alumno['grupo'];
        $alumnosPorGrado[$grado][] = $alumno;
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial', '', 10);

    foreach ($alumnosPorGrado as $grado => $alumnos) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Nivel: ' . $alumnos[0]['nivel_nombre']), 0, 1, 'L');
        $pdf->Cell(0, 10, utf8_decode('Grupo: ' . $grado), 0, 1, 'L');
        $pdf->Ln(5);
        $pdf->TableHeader();

        $fill = false;
        foreach ($alumnos as $alumno) {
            if ($pdf->GetY() > 250) {
                $pdf->AddPage();
                $pdf->TableHeader();
            }

            $fotoPath = '../uploads/' . $alumno['foto'];
            $pdf->SetFillColor($fill ? 240 : 255, 240, 240);

            if (!empty($alumno['foto']) && file_exists($fotoPath)) {
                $pdf->Cell(20, 15, '', 1, 0, 'C', $fill);
                $pdf->Image($fotoPath, $pdf->GetX() - 20 + 2.5, $pdf->GetY() + 2.5, 15, 15);
            } else {
                $pdf->Cell(20, 15, 'Sin foto', 1, 0, 'C', $fill);
            }

            $pdf->Cell(30, 15, utf8_decode($alumno['matricula']), 1, 0, 'C', $fill);
            $pdf->Cell(70, 15, utf8_decode($alumno['nombres'] . ' ' . $alumno['apellidos']), 1, 0, 'L', $fill);
            $pdf->Cell(50, 15, utf8_decode($alumno['grupo']), 1, 1, 'C', $fill);
            $fill = !$fill;
        }
    }

    ob_end_clean(); // Limpia cualquier salida accidental antes de generar el PDF
    $pdf->Output('I', 'reporte_alumnos_general.pdf');
} catch (PDOException $e) {
    ob_end_clean(); // Limpia cualquier salida antes de mostrar errores
    die("Error al generar el reporte: " . $e->getMessage());
}
