<?php
// Incluir la librería TCPDF
require_once('../../vendor/autoload.php');


// Crear un nuevo PDF con orientación horizontal y tamaño A3
$pdf = new TCPDF('L', 'mm', 'A3', true, 'UTF-8', false);
$pdf->AddPage();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Autor');
$pdf->SetTitle('Reporte General');
$pdf->SetSubject('Reporte General de Huertas, Técnicos, Productores, Municipios y Laboratorios');
$pdf->SetKeywords('TCPDF, PDF, report, pdf, example');

// Establecer las propiedades del documento
$pdf->SetHeaderData('', 0, 'Reporte General', 'Huertas, Técnicos, Productores, Municipios y Laboratorios');
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 12));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', 8));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(15, 30, 15);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

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

// Sección de Huertas
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Huertas', 0, 1);
$pdf->SetFont('helvetica', '', 5);
$pdf->Cell(10, 10, 'ID Huerta', 1);
$pdf->Cell(20, 10, 'Nombre Productor', 1);
$pdf->Cell(20, 10, 'Nombre Huerta', 1);
$pdf->Cell(20, 10, 'Localidad', 1);
$pdf->Cell(10, 10, 'Centroide', 1);
$pdf->Cell(10, 10, 'Héctareas', 1);
$pdf->Cell(15, 10, 'Pronóstico', 1);
$pdf->Cell(15, 10, 'Longitud', 1);
$pdf->Cell(15, 10, 'Altitud', 1);
$pdf->Cell(15, 10, 'Altura Nivel Mar', 1);
$pdf->Cell(20, 10, 'Variedad', 1);
$pdf->Cell(20, 10, 'Nom Empresa', 1);
$pdf->Cell(20, 10, 'Encargado', 1);
$pdf->Cell(20, 10, 'Supervisor', 1);
$pdf->Cell(20, 10, 'Año', 1);
$pdf->Cell(20, 10, 'Árboles/Há', 1);
$pdf->Cell(20, 10, 'Total Árboles', 1);
$pdf->Cell(20, 10, 'Etapa', 1);
$pdf->Cell(20, 10, 'Fechas V. 01', 1);
$pdf->Cell(20, 10, 'Fechas V. 02', 1);
$pdf->Cell(20, 10, 'Fecha Registro', 1);
$pdf->Ln();

// Rellenar la tabla con los datos de Huertas
foreach ($data['huertas'] as $huerta) {
    $pdf->Cell(10, 5, $huerta['id_hue'], 1);
    $pdf->Cell(20, 5, $huerta['nombre_productor'], 1);
    $pdf->Cell(20, 5, $huerta['nombre_huerta'], 1);
    $pdf->Cell(20, 5, $huerta['localidad'], 1);
    $pdf->Cell(10, 5, $huerta['centroide'], 1);
    $pdf->Cell(10, 5, $huerta['hectareas'], 1);
    $pdf->Cell(15, 5, $huerta['pronostico_de_cosecha'], 1);
    $pdf->Cell(15, 5, $huerta['longitud'], 1);
    $pdf->Cell(15, 5, $huerta['altitud'], 1);
    $pdf->Cell(15, 5, $huerta['altura_nivel_del_mar'], 1);
    $pdf->Cell(20, 5, $huerta['variedad'], 1);
    $pdf->Cell(20, 5, $huerta['nomempresa'], 1);
    $pdf->Cell(20, 5, $huerta['encargadoempresa'], 1);
    $pdf->Cell(20, 5, $huerta['supervisorhuerta'], 1);
    $pdf->Cell(20, 5, $huerta['anoplantacion'], 1);
    $pdf->Cell(20, 5, $huerta['arbolesporhectareas'], 1);
    $pdf->Cell(20, 5, $huerta['totalarboles'], 1);
    $pdf->Cell(20, 5, $huerta['etapafenologica'], 1);
    $pdf->Cell(20, 5, $huerta['fechasv_01'], 1);
    $pdf->Cell(20, 5, $huerta['fechasv_02'], 1);
    $pdf->Cell(20, 5, $huerta['fechaRegistro'], 1);
    $pdf->Ln();
}

// Sección de Técnicos
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Técnicos', 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(30, 10, 'ID Técnico', 1);
$pdf->Cell(70, 10, 'Nombre', 1);
$pdf->Cell(70, 10, 'Correo', 1);
$pdf->Cell(40, 10, 'Teléfono', 1);
$pdf->Cell(30, 10, 'Carga Municipios', 1);
$pdf->Cell(30, 10, 'Estatus', 1);
$pdf->Cell(30, 10, 'Nombre Junta', 1);
$pdf->Ln();

// Rellenar la tabla con los datos de Técnicos
foreach ($data['tecnicos'] as $tecnico) {
    $pdf->Cell(30, 10, $tecnico['id_tecnico'], 1);
    $pdf->Cell(70, 10, $tecnico['nombre_usuario'], 1);
    $pdf->Cell(70, 10, $tecnico['correo'], 1);
    $pdf->Cell(40, 10, $tecnico['teléfono'], 1);
    $pdf->Cell(30, 10, $tecnico['carga_municipios'], 1);
    $pdf->Cell(30, 10, $tecnico['estatus'], 1);
    $pdf->Cell(30, 10, $tecnico['nombre_junta'], 1);
    $pdf->Ln();
}

// Sección de Productores
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Productores', 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(30, 10, 'ID Productor', 1);
$pdf->Cell(70, 10, 'Nombre', 1);
$pdf->Cell(70, 10, 'Correo', 1);
$pdf->Cell(40, 10, 'Teléfono', 1);
$pdf->Cell(70, 10, 'RFC', 1);
$pdf->Cell(70, 10, 'CURP', 1);
$pdf->Cell(30, 10, 'Estatus', 1);
$pdf->Cell(30, 10, 'Nombre Junta', 1);
$pdf->Ln();

// Rellenar la tabla con los datos de Productores
foreach ($data['productores'] as $productor) {
    $pdf->Cell(30, 10, $productor['id_productor'], 1);
    $pdf->Cell(70, 10, $productor['nombre'], 1);
    $pdf->Cell(70, 10, $productor['correo'], 1);
    $pdf->Cell(40, 10, $productor['teléfono'], 1);
    $pdf->Cell(70, 10, $productor['rfc'], 1);
    $pdf->Cell(70, 10, $productor['curp'], 1);
    $pdf->Cell(30, 10, $productor['estatus'], 1);
    $pdf->Cell(30, 10, $productor['nombre_junta'], 1);
    $pdf->Ln();
}

// Sección de Municipios
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Municipios', 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(30, 10, 'ID Municipio', 1);
$pdf->Cell(80, 10, 'Nombre', 1);
$pdf->Ln();

// Rellenar la tabla con los datos de Municipios
foreach ($data['municipios'] as $municipio) {
    $pdf->Cell(30, 10, $municipio['id_municipio'], 1);
    $pdf->Cell(80, 10, $municipio['nombre'], 1);
    $pdf->Ln();
}

// Sección de Laboratorios
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Reporte de Laboratorios', 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(30, 10, 'ID Laboratorio', 1);
$pdf->Cell(40, 10, 'Nombre', 1);
$pdf->Cell(80, 10, 'Correo', 1);
$pdf->Cell(40, 10, 'Teléfono', 1);
$pdf->Cell(30, 10, 'Estatus', 1);
$pdf->Cell(30, 10, 'Nombre Junta', 1);
$pdf->Ln();

// Rellenar la tabla con los datos de Laboratorios
foreach ($data['laboratorios'] as $laboratorio) {
    $pdf->Cell(30, 10, $laboratorio['id_laboratorio'], 1);
    $pdf->Cell(40, 10, $laboratorio['nombre_usuario'], 1);
    $pdf->Cell(80, 10, $laboratorio['correo'], 1);
    $pdf->Cell(40, 10, $laboratorio['teléfono'], 1);
    $pdf->Cell(30, 10, $laboratorio['estatus'], 1);
    $pdf->Cell(30, 10, $laboratorio['nombre_junta'], 1);
    $pdf->Ln();
}

// Cerrar conexión
$conn = null;

// Salida del PDF
$pdf->Output('Reporte_General_'.$id_usuario.'.pdf', 'I');
?>
