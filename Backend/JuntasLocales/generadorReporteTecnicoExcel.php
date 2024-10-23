<?php
// Iniciar el buffer de salida
ob_start();

try {
    require_once '../../vendor/autoload.php';
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear un nuevo documento de Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configuración del encabezado de la hoja
$sheet->setCellValue('A1', 'Reporte de Técnicos');
$sheet->mergeCells('A1:G1'); // Unir celdas para el título
$sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);

// Encabezados de la tabla
$sheet->setCellValue('A3', 'ID Técnico');
$sheet->setCellValue('B3', 'Nombre Usuario');
$sheet->setCellValue('C3', 'Correo');
$sheet->setCellValue('D3', 'Teléfono');
$sheet->setCellValue('E3', 'Carga Municipios');
$sheet->setCellValue('F3', 'Estatus');
$sheet->setCellValue('G3', 'Nombre Junta');

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

// Ejecutar la consulta de técnicos
$stmt = $conn->prepare("
  SELECT DISTINCT t.id_tecnico, u.nombre AS nombre_usuario, u.correo, u.teléfono, 
                t.carga_municipios, t.estatus, j.nombre AS nombre_junta
FROM tecnico t
JOIN juntaslocales j ON t.idjuntalocal = j.idjuntalocal
JOIN usuario u ON t.id_usuario = u.id_usuario
WHERE j.idjuntalocal = (
    SELECT j2.idjuntalocal 
    FROM juntaslocales j2
    WHERE j2.id_usuario = :id_usuario
);");
$stmt->execute([':id_usuario' => $id_usuario]);

// Rellenar los datos en las filas del Excel
$fila = 4; // La fila donde empiezan los datos
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $fila, $row['id_tecnico']);
    $sheet->setCellValue('B' . $fila, $row['nombre_usuario']);
    $sheet->setCellValue('C' . $fila, $row['correo']);
    $sheet->setCellValue('D' . $fila, $row['teléfono']);
    $sheet->setCellValue('E' . $fila, $row['carga_municipios']);
    $sheet->setCellValue('F' . $fila, $row['estatus']);
    $sheet->setCellValue('G' . $fila, $row['nombre_junta']);
    $fila++;
}

// Cerrar la conexión
$conn = null;

// Establecer encabezado para descargar el archivo como Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_Tecnicos.xlsx"');
header('Cache-Control: max-age=0');

// Escribir el archivo de salida
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
