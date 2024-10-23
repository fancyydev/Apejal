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

// Agregar información de la junta local a la hoja de cálculo
if ($junta) {
    $sheet->setCellValue('A1', 'Reporte de Junta Local');
    $sheet->setCellValue('A3', 'Nombre: ' . $junta['nombre']);
    $sheet->setCellValue('A4', 'Domicilio: ' . $junta['domicilio']);
    $sheet->setCellValue('A5', 'Teléfono: ' . $junta['teléfono']);
    $sheet->setCellValue('A6', 'Correo: ' . $junta['correo']);
    $sheet->setCellValue('A7', 'Estatus: ' . $junta['estatus']);
}

// --- Sección 1: Reporte de Huertas ---
$sheet->setCellValue('A9', 'Reporte de Huertas');

// Encabezados de la tabla
$headers = ['ID Huerta', 'Nombre Productor', 'Nombre Huerta', 'Localidad', 'Centroide', 'Héctareas', 'Pronóstico', 'Longitud', 'Altitud', 'Altura Nivel Mar', 'Variedad', 'Nom Empresa', 'Encargado', 'Supervisor', 'Año', 'Árboles/Há', 'Total Árboles', 'Etapa', 'Fechas V. 01', 'Fechas V. 02', 'Fecha Registro'];
$column = 'A';
$row = 10;

foreach ($headers as $header) {
    $sheet->setCellValue($column.$row, $header);
    $column++;
}

// Ejecutar la consulta de huertas
$stmt = $conn->prepare("SELECT h.id_hue, u.nombre AS nombre_productor, h.nombre AS nombre_huerta, m.nombre AS localidad, 
               h.centroide, h.hectareas, h.pronostico_de_cosecha, h.longitud, h.altitud, 
               h.altura_nivel_del_mar, h.variedad, h.nomempresa, h.encargadoempresa, 
               h.supervisorhuerta, h.anoplantacion, h.arbolesporhectareas, h.totalarboles, 
               h.etapafenologica, h.fechasv_01, h.fechasv_02, h.rutaKML, h.fechaRegistro 
        FROM huertas h
        JOIN juntaslocales jl ON h.idjuntaLocal = jl.idjuntaLocal
        JOIN productores p ON h.id_productor = p.id_productor
        JOIN usuario u ON p.id_usuario = u.id_usuario
        JOIN municipio m ON h.localidad = m.id_municipio
        WHERE jl.id_usuario = :id_usuario");
$stmt->execute([':id_usuario' => $id_usuario]);

// Rellenar los datos de las huertas
$row = 11;
while ($huerta = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $column = 'A';
    foreach ($huerta as $data) {
        $sheet->setCellValue($column.$row, $data);
        $column++;
    }
    $row++;
}

// Cerrar la conexión
$conn = null;

// Generar el archivo Excel
$writer = new Xlsx($spreadsheet);
$filename = 'reporte_Huertas.xlsx';

// Cabeceras para descargar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
