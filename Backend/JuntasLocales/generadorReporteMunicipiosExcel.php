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

// Agregar información de la junta local al Excel
if ($junta) {
    $sheet->setCellValue('A1', 'Reporte de Junta Local');
    $sheet->setCellValue('A2', 'Nombre: ' . $junta['nombre']);
    $sheet->setCellValue('A3', 'Domicilio: ' . $junta['domicilio']);
    $sheet->setCellValue('A4', 'Teléfono: ' . $junta['teléfono']);
    $sheet->setCellValue('A5', 'Correo: ' . $junta['correo']);
    $sheet->setCellValue('A6', 'Estatus: ' . $junta['estatus']);
}

// Espaciado entre datos de la junta y el reporte de municipios
$sheet->setCellValue('A8', 'Reporte de Municipios');
$sheet->setCellValue('A9', 'ID Municipio');
$sheet->setCellValue('B9', 'Nombre Municipio');

// Consultar los municipios que pertenecen a las juntas locales del usuario
$stmt = $conn->prepare("
    SELECT m.id_municipio, m.nombre 
    FROM municipio m
    JOIN juntaslocales j ON FIND_IN_SET(m.id_municipio, j.carga_municipios)
    WHERE j.id_usuario = :id
    GROUP BY m.id_municipio, m.nombre;
");
$stmt->execute([':id' => $id_usuario]);

// Rellenar la tabla con los resultados de la consulta
$fila = 10; // Comienza en la fila 10 para los datos
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $fila, $row['id_municipio']);
    $sheet->setCellValue('B' . $fila, $row['nombre']);
    $fila++;
}

// Establecer el nombre del archivo
$nombreArchivo = 'reporte_municipios.xlsx';

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
