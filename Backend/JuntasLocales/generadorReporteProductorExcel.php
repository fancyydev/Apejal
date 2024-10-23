<?php
// Iniciar el buffer de salida
ob_start();

try {
    require_once '../../vendor/autoload.php';
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

// Incluir PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear un nuevo objeto de hoja de cálculo
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configuración del documento
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

// Agregar información de la junta local al Excel
if ($junta) {
    $sheet->setCellValue('A1', 'Reporte de Junta Local');
    $sheet->setCellValue('A2', 'Nombre: ' . $junta['nombre']);
    $sheet->setCellValue('A3', 'Domicilio: ' . $junta['domicilio']);
    $sheet->setCellValue('A4', 'Teléfono: ' . $junta['teléfono']);
    $sheet->setCellValue('A5', 'Correo: ' . $junta['correo']);
    $sheet->setCellValue('A6', 'Estatus: ' . $junta['estatus']);
}

// Ejecutar la consulta de productores
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
$sheet->setCellValue('A8', 'Reporte de Productores');

// Encabezados de la tabla de productores
$sheet->setCellValue('A9', 'ID Productor');
$sheet->setCellValue('B9', 'Nombre');
$sheet->setCellValue('C9', 'Correo');
$sheet->setCellValue('D9', 'Teléfono');
$sheet->setCellValue('E9', 'RFC');
$sheet->setCellValue('F9', 'CURP');
$sheet->setCellValue('G9', 'Estatus');
$sheet->setCellValue('H9', 'Nombre Junta');

// Rellenar los datos de los productores
$rowNum = 10; // Comenzar desde la fila 10
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $rowNum, $row['id_productor']);
    $sheet->setCellValue('B' . $rowNum, $row['nombre']);
    $sheet->setCellValue('C' . $rowNum, $row['correo']);
    $sheet->setCellValue('D' . $rowNum, $row['teléfono']);
    $sheet->setCellValue('E' . $rowNum, $row['rfc']);
    $sheet->setCellValue('F' . $rowNum, $row['curp']);
    $sheet->setCellValue('G' . $rowNum, $row['estatus']);
    $sheet->setCellValue('H' . $rowNum, $row['nombre_junta']);
    $rowNum++;
}

// Cerrar la conexión
$conn = null;

// Establecer el nombre del archivo y la cabecera de descarga
$filename = 'reporte_productores.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Guardar el archivo y forzar la descarga
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
