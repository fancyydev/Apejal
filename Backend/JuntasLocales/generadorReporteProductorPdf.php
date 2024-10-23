<?php
// Iniciar el buffer de salida
ob_start();

try {
    require_once '../../vendor/autoload.php';
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

// Incluir TCPDF
use TCPDF;

// Crear un nuevo PDF con orientación horizontal y tamaño A3
$pdf = new TCPDF('L', 'mm', 'A3', true, 'UTF-8', false);
$pdf->AddPage();

// Configuración del documento
$pdf->SetFont('helvetica', '', 10); // Tamaño de fuente para encabezados

// Obtener el ID del usuario de la sesión
session_start();
$id_usuario = $_SESSION["id"];

// Conectar a la base de datos
require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar conexión
if ($conn->errorCode() !== "00000") {
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

// Obtener datos de la junta local
$stmt = $conn->prepare("SELECT nombre, domicilio, teléfono, correo, estatus, ruta_img FROM juntaslocales WHERE id_usuario = :id_usuario");
$stmt->execute([':id_usuario' => $id_usuario]);
$junta = $stmt->fetch(PDO::FETCH_ASSOC);

// Agregar información de la junta local al PDF
if ($junta) {
    $pdf->Cell(0, 10, 'Reporte de Junta Local', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->Cell(0, 10, 'Nombre: ' . $junta['nombre'], 0, 1);
    $pdf->Cell(0, 10, 'Domicilio: ' . $junta['domicilio'], 0, 1);
    $pdf->Cell(0, 10, 'Teléfono: ' . $junta['teléfono'], 0, 1);
    $pdf->Cell(0, 10, 'Correo: ' . $junta['correo'], 0, 1);
    $pdf->Cell(0, 10, 'Estatus: ' . $junta['estatus'], 0, 1);
    $pdf->Ln(10);
}


// Variable para ajustar el tamaño de fuente de los datos
$tamañoFuenteDatos = 5; // Cambia este valor para ajustar el tamaño de fuente de los datos

// Cambiar el tamaño de fuente para las filas de datos
$pdf->SetFont('helvetica', '', $tamañoFuenteDatos); // Tamaño de fuente para los datos

// Ejecutar la consulta de técnicos
$stmt = $conn->prepare("
    SELECT p.id_productor, u.nombre AS nombre, u.correo, u.teléfono, p.rfc, p.curp, p.estatus, j.nombre AS nombre_junta
    FROM productores p
    JOIN huertas h ON p.id_productor = h.id_productor
    JOIN juntaslocales j ON h.idjuntalocal = j.idjuntalocal
    JOIN usuario u ON p.id_usuario = u.id_usuario
    WHERE j.idjuntalocal = (
        SELECT j2.idjuntalocal 
        FROM juntaslocales j2
        WHERE j2.id_usuario = :id_usuario
    )
    GROUP BY p.id_productor, u.nombre, u.correo, u.teléfono, p.rfc, p.curp, p.estatus, j.nombre
");
$stmt->execute([':id_usuario' => $id_usuario]);

// --- Sección 2: Reporte de Productores ---
$pdf->Cell(0, 10, 'Reporte de Productores', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 5); // Tamaño de fuente para encabezados

// Encabezados de la tabla de productores
$pdf->Cell(10, 10, 'ID Productor', 1);
$pdf->Cell(20, 10, 'Nombre', 1);
$pdf->Cell(30, 10, 'Correo', 1);
$pdf->Cell(20, 10, 'Teléfono', 1);
$pdf->Cell(20, 10, 'RFC', 1);
$pdf->Cell(20, 10, 'CURP', 1);
$pdf->Cell(20, 10, 'Estatus', 1);
$pdf->Cell(30, 10, 'Nombre Junta', 1);
$pdf->Ln();

// Cambiar el tamaño de fuente para las filas de datos
$pdf->SetFont('helvetica', '', 5); // Tamaño de fuente para los datos

// Rellenar los datos de los productores
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(10, 5, $row['id_productor'], 1);
    $pdf->Cell(20, 5, $row['nombre'], 1);
    $pdf->Cell(30, 5, $row['correo'], 1);
    $pdf->Cell(20, 5, $row['teléfono'], 1);
    $pdf->Cell(20, 5, $row['rfc'], 1);
    $pdf->Cell(20, 5, $row['curp'], 1);
    $pdf->Cell(20, 5, $row['estatus'], 1);
    $pdf->Cell(30, 5, $row['nombre_junta'], 1);
    $pdf->Ln();
}

// Cerrar la conexión
$conn = null;

// Generar el PDF
$pdf->Output('reporte_productores.pdf', 'I');
?>

