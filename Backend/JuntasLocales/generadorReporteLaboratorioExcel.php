<?php
// Incluir la librería PhpSpreadsheet
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Iniciar la sesión
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION["id"])) {
    die("Acceso denegado.");
}

// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Obtener el ID del usuario de la sesión
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

// Espaciado entre datos de la junta y el reporte de laboratorios
$sheet->setCellValue('A8', 'Reporte de Laboratorios');
$sheet->setCellValue('A9', 'ID Lab');
$sheet->setCellValue('B9', 'Nombre Usuario');
$sheet->setCellValue('C9', 'Correo');
$sheet->setCellValue('D9', 'Teléfono');
$sheet->setCellValue('E9', 'Estatus');
$sheet->setCellValue('F9', 'Nombre Junta');

// Ejecutar la consulta para obtener laboratorios
$stmt = $conn->prepare("
    SELECT DISTINCT l.id_laboratorio, u.nombre AS nombre_usuario, u.correo, u.teléfono, 
                    l.estatus, j.nombre AS nombre_junta
    FROM laboratorio l
    JOIN usuario u ON l.id_usuario = u.id_usuario 
    JOIN juntaslocales j ON l.idjuntalocal = j.idjuntalocal
    WHERE j.idjuntalocal = (
        SELECT j2.idjuntalocal 
        FROM juntaslocales j2
        WHERE j2.id_usuario = :id_usuario
    )
");
$stmt->execute([':id_usuario' => $id_usuario]);

// Rellenar las filas con los datos de los laboratorios
$fila = 10; // Comienza en la fila 10 ya que las filas 1 a 9 tienen los encabezados
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $fila, $row['id_laboratorio']);
    $sheet->setCellValue('B' . $fila, $row['nombre_usuario']);
    $sheet->setCellValue('C' . $fila, $row['correo']);
    $sheet->setCellValue('D' . $fila, $row['teléfono']);
    $sheet->setCellValue('E' . $fila, $row['estatus']);
    $sheet->setCellValue('F' . $fila, $row['nombre_junta']);
    $fila++;
}

// Establecer el nombre del archivo
$nombreArchivo = 'reporte_laboratorios.xlsx';

// Establecer las cabeceras HTTP para que el archivo se descargue como Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

// Limpiar el buffer de salida
ob_end_clean();

// Crear el archivo Excel y enviarlo al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Cerrar la conexión
$conn = null;
exit; // Terminar el script
?>
