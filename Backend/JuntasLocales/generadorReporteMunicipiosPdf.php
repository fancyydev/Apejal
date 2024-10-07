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

// Crear un nuevo PDF con orientación horizontal y tamaño A4
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->AddPage();

// Configuración del documento
$pdf->SetFont('helvetica', '', 12); // Tamaño de fuente para encabezados

// Obtener el ID del usuario de la sesión
session_start();
$id_usuario = $_SESSION["id"];

// Conectar a la base de datos
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");
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


// Título del reporte
$pdf->Cell(0, 10, 'Reporte de Municipios', 0, 1, 'C');
$pdf->Ln(10);

// Encabezados de la tabla
$pdf->SetFont('helvetica', '', 10); // Cambiar tamaño de fuente para los encabezados
$pdf->Cell(20, 10, 'ID Municipio', 1);
$pdf->Cell(100, 10, 'Nombre Municipio', 1);
$pdf->Ln(); // Nueva línea

// Consultar los municipios que pertenecen a las juntas locales del usuario
$stmt = $conn->prepare("
    SELECT m.id_municipio, m.nombre 
    FROM municipio m
    JOIN juntaslocales j ON FIND_IN_SET(m.id_municipio, j.carga_municipios)
    WHERE j.id_usuario = :id
    GROUP BY m.id_municipio, m.nombre;
");
$stmt->execute([':id' => $id_usuario]);

// Cambiar el tamaño de fuente para los datos
$pdf->SetFont('helvetica', '', 9); // Tamaño de fuente para los datos

// Llenar la tabla con los resultados de la consulta
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(20, 10, $row['id_municipio'], 1);
    $pdf->Cell(100, 10, $row['nombre'], 1);
    $pdf->Ln(); // Nueva línea
}

// Cerrar la conexión
$conn = null;

// Generar el PDF
$pdf->Output('reporte_municipios.pdf', 'I');
?>
