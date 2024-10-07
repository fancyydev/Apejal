<?php
try {
    require_once '../../vendor/autoload.php';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear un nuevo archivo Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
header('Content-Type: application/json'); // Asegúrate de que el tipo de contenido es JSON

require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");
// Obtener el ID del usuario de la sesión
session_start();
$id_usuario = $_SESSION["id"]; // Suponiendo que el ID de usuario está en la sesión

// Crear conexión
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die(json_encode(["error" => "Conexión fallida: " . implode(", ", $errorInfo)]));
}

// Obtener datos de la junta local
$stmt = $conn->prepare("SELECT nombre, domicilio, teléfono, correo, estatus, ruta_img FROM juntaslocales WHERE id_usuario = :id_usuario");
$stmt->execute([':id_usuario' => $id_usuario]);
$junta = $stmt->fetch(PDO::FETCH_ASSOC);

// Enviar los datos como respuesta JSON
if ($junta) {
    echo json_encode($junta);
} else {
    echo json_encode(["error" => "No se encontraron datos para el usuario."]);
}

if ($junta) {
    // Insertar el logo
    if (file_exists($junta['ruta_img'])) {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath($junta['ruta_img']); // Ruta del logo
        $drawing->setCoordinates('A1'); // Coordenadas de la imagen en la hoja
        $drawing->setWidthAndHeight(100, 100);
        $drawing->setWorksheet($sheet);
    }

    // Insertar los datos de la junta local
    $sheet->setCellValue('B1', 'Junta Local: ' . $junta['nombre']);
    $sheet->setCellValue('B2', 'Domicilio: ' . $junta['domicilio']);
    $sheet->setCellValue('B3', 'Teléfono: ' . $junta['teléfono']);
    $sheet->setCellValue('B4', 'Correo: ' . $junta['correo']);
    $sheet->setCellValue('B5', 'Estatus: ' . $junta['estatus']);
}

// Espacio antes del reporte general
$rowNum = 7; // Saltamos unas líneas

// --- Sección 1: Reporte de Huertas ---
// Definir encabezados
$sheet->setCellValue('A' . $rowNum, 'Reporte de Huertas');
$sheet->setCellValue('A' . ($rowNum + 1), 'ID Huerta');
$sheet->setCellValue('B' . ($rowNum + 1), 'Nombre Productor');
$sheet->setCellValue('C' . ($rowNum + 1), 'Nombre Huerta');
// Continúa con los encabezados...

// Ejecutar la consulta de huertas y rellenar los datos
$stmt = $conn->prepare("SELECT h.id_hue, u.nombre AS nombre_productor, h.nombre AS nombre_huerta, m.nombre AS localidad, h.centroide, h.hectareas, h.pronostico_de_cosecha, h.longitud, h.altitud, h.altura_nivel_del_mar, h.variedad, h.nomempresa, h.encargadoempresa, h.supervisorhuerta, h.anoplantacion, h.arbolesporhectareas, h.totalarboles, h.etapafenologica, h.fechasv_01, h.fechasv_02, h.rutaKML, h.fechaRegistro FROM huertas h JOIN juntaslocales jl ON h.idjuntaLocal = jl.idjuntaLocal JOIN productores p ON h.id_productor = p.id_productor JOIN usuario u ON p.id_usuario = u.id_usuario JOIN municipio m ON h.localidad = m.id_municipio WHERE jl.id_usuario = :id_usuario");
$stmt->execute([':id_usuario' => $id_usuario]);

$rowNum += 2;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sheet->setCellValue('A' . $rowNum, $row['id_hue']);
    $sheet->setCellValue('B' . $rowNum, $row['nombre_productor']);
    $sheet->setCellValue('C' . $rowNum, $row['nombre_huerta']);
    // Continúa agregando las demás columnas...
    $rowNum++;
}

// --- Sección 2: Reporte de Municipios ---
// Resto del código para el reporte de municipios, productores, solicitudes y técnicos...

// Guardar el archivo Excel
$writer = new Xlsx($spreadsheet);
$filename = 'Reporte_General.xlsx';

// Cabeceras para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
