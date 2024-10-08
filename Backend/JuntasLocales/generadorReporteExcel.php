<?php
// Incluir la librería PhpSpreadsheet
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

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

// Consultas y datos
$data = [];

// Consulta de huertas
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
$data['huertas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta de técnicos
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
$data['tecnicos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta de productores
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
$data['productores'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta de municipios
$stmt = $conn->prepare("
    SELECT m.id_municipio, m.nombre 
    FROM municipio m
    JOIN juntaslocales j ON FIND_IN_SET(m.id_municipio, j.carga_municipios)
    WHERE j.id_usuario = :id
    GROUP BY m.id_municipio, m.nombre;
");
$stmt->execute([':id' => $id_usuario]);
$data['municipios'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
$data['laboratorios'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear el reporte en el Excel
$row = 1;

// Sección de Huertas
$sheet->setCellValue('A'.$row, 'Reporte de Huertas');
$row++;
$sheet->setCellValue('A'.$row, 'ID Huerta');
$sheet->setCellValue('B'.$row, 'Nombre Productor');
$sheet->setCellValue('C'.$row, 'Nombre Huerta');
$sheet->setCellValue('D'.$row, 'Localidad');
$sheet->setCellValue('E'.$row, 'Centroide');
$sheet->setCellValue('F'.$row, 'Hectáreas');
$sheet->setCellValue('G'.$row, 'Pronóstico de Cosecha');
$sheet->setCellValue('H'.$row, 'Longitud');
$sheet->setCellValue('I'.$row, 'Altitud');
$sheet->setCellValue('J'.$row, 'Altura Nivel del Mar');
$sheet->setCellValue('K'.$row, 'Variedad');
$sheet->setCellValue('L'.$row, 'Nombre Empresa');
$sheet->setCellValue('M'.$row, 'Encargado Empresa');
$sheet->setCellValue('N'.$row, 'Supervisor Huerta');
$sheet->setCellValue('O'.$row, 'Año Plantación');
$sheet->setCellValue('P'.$row, 'Árboles por Hectáreas');
$sheet->setCellValue('Q'.$row, 'Total Árboles');
$sheet->setCellValue('R'.$row, 'Etapa Fenológica');
$sheet->setCellValue('S'.$row, 'Fechas V1');
$sheet->setCellValue('T'.$row, 'Fechas V2');
$sheet->setCellValue('U'.$row, 'Ruta KML');
$sheet->setCellValue('V'.$row, 'Fecha Registro');
$row++;

// Rellenar la tabla con los datos de Huertas
foreach ($data['huertas'] as $huerta) {
    $sheet->setCellValue('A'.$row, $huerta['id_hue']);
    $sheet->setCellValue('B'.$row, $huerta['nombre_productor']);
    $sheet->setCellValue('C'.$row, $huerta['nombre_huerta']);
    $sheet->setCellValue('D'.$row, $huerta['localidad']);
    $sheet->setCellValue('E'.$row, $huerta['centroide']);
    $sheet->setCellValue('F'.$row, $huerta['hectareas']);
    $sheet->setCellValue('G'.$row, $huerta['pronostico_de_cosecha']);
    $sheet->setCellValue('H'.$row, $huerta['longitud']);
    $sheet->setCellValue('I'.$row, $huerta['altitud']);
    $sheet->setCellValue('J'.$row, $huerta['altura_nivel_del_mar']);
    $sheet->setCellValue('K'.$row, $huerta['variedad']);
    $sheet->setCellValue('L'.$row, $huerta['nomempresa']);
    $sheet->setCellValue('M'.$row, $huerta['encargadoempresa']);
    $sheet->setCellValue('N'.$row, $huerta['supervisorhuerta']);
    $sheet->setCellValue('O'.$row, $huerta['anoplantacion']);
    $sheet->setCellValue('P'.$row, $huerta['arbolesporhectareas']);
    $sheet->setCellValue('Q'.$row, $huerta['totalarboles']);
    $sheet->setCellValue('R'.$row, $huerta['etapafenologica']);
    $sheet->setCellValue('S'.$row, $huerta['fechasv_01']);
    $sheet->setCellValue('T'.$row, $huerta['fechasv_02']);
    $sheet->setCellValue('U'.$row, $huerta['rutaKML']);
    $sheet->setCellValue('V'.$row, $huerta['fechaRegistro']);
    $row++;
}

// Sección de Técnicos
$sheet->setCellValue('A'.$row, 'Reporte de Técnicos');
$row++;
$sheet->setCellValue('A'.$row, 'ID Técnico');
$sheet->setCellValue('B'.$row, 'Nombre');
$sheet->setCellValue('C'.$row, 'Correo');
$sheet->setCellValue('D'.$row, 'Teléfono');
$sheet->setCellValue('E'.$row, 'Carga Municipios');
$sheet->setCellValue('F'.$row, 'Estatus');
$sheet->setCellValue('G'.$row, 'Nombre Junta');
$row++;

// Rellenar la tabla con los datos de Técnicos
foreach ($data['tecnicos'] as $tecnico) {
    $sheet->setCellValue('A'.$row, $tecnico['id_tecnico']);
    $sheet->setCellValue('B'.$row, $tecnico['nombre_usuario']);
    $sheet->setCellValue('C'.$row, $tecnico['correo']);
    $sheet->setCellValue('D'.$row, $tecnico['teléfono']);
    $sheet->setCellValue('E'.$row, $tecnico['carga_municipios']);
    $sheet->setCellValue('F'.$row, $tecnico['estatus']);
    $sheet->setCellValue('G'.$row, $tecnico['nombre_junta']);
    $row++;
}

// Sección de Productores
$sheet->setCellValue('A'.$row, 'Reporte de Productores');
$row++;
$sheet->setCellValue('A'.$row, 'ID Productor');
$sheet->setCellValue('B'.$row, 'Nombre');
$sheet->setCellValue('C'.$row, 'Correo');
$sheet->setCellValue('D'.$row, 'Teléfono');
$sheet->setCellValue('E'.$row, 'RFC');
$sheet->setCellValue('F'.$row, 'CURP');
$sheet->setCellValue('G'.$row, 'Estatus');
$sheet->setCellValue('H'.$row, 'Nombre Junta');
$row++;

// Rellenar la tabla con los datos de Productores
foreach ($data['productores'] as $productor) {
    $sheet->setCellValue('A'.$row, $productor['id_productor']);
    $sheet->setCellValue('B'.$row, $productor['nombre']);
    $sheet->setCellValue('C'.$row, $productor['correo']);
    $sheet->setCellValue('D'.$row, $productor['teléfono']);
    $sheet->setCellValue('E'.$row, $productor['rfc']);
    $sheet->setCellValue('F'.$row, $productor['curp']);
    $sheet->setCellValue('G'.$row, $productor['estatus']);
    $sheet->setCellValue('H'.$row, $productor['nombre_junta']);
    $row++;
}

// Sección de Municipios
$sheet->setCellValue('A'.$row, 'Reporte de Municipios');
$row++;
$sheet->setCellValue('A'.$row, 'ID Municipio');
$sheet->setCellValue('B'.$row, 'Nombre');
$row++;

// Rellenar la tabla con los datos de Municipios
foreach ($data['municipios'] as $municipio) {
    $sheet->setCellValue('A'.$row, $municipio['id_municipio']);
    $sheet->setCellValue('B'.$row, $municipio['nombre']);
    $row++;
}

// Sección de Laboratorios
$sheet->setCellValue('A'.$row, 'Reporte de Laboratorios');
$row++;
$sheet->setCellValue('A'.$row, 'ID Laboratorio');
$sheet->setCellValue('B'.$row, 'Nombre');
$sheet->setCellValue('C'.$row, 'Correo');
$sheet->setCellValue('D'.$row, 'Teléfono');
$sheet->setCellValue('E'.$row, 'Estatus');
$sheet->setCellValue('F'.$row, 'Nombre Junta');
$row++;

// Rellenar la tabla con los datos de Laboratorios
foreach ($data['laboratorios'] as $laboratorio) {
    $sheet->setCellValue('A'.$row, $laboratorio['id_laboratorio']);
    $sheet->setCellValue('B'.$row, $laboratorio['nombre_usuario']);
    $sheet->setCellValue('C'.$row, $laboratorio['correo']);
    $sheet->setCellValue('D'.$row, $laboratorio['teléfono']);
    $sheet->setCellValue('E'.$row, $laboratorio['estatus']);
    $sheet->setCellValue('F'.$row, $laboratorio['nombre_junta']);
    $row++;
}

// Guardar el archivo Excel
$writer = new Xlsx($spreadsheet);
$filename = "Reporte_General_".$id_usuario.".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer->save("php://output");

// Cerrar conexión
$conn = null;
?>
