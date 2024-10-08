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

// --- Sección 1: Reporte de Huertas ---
$pdf->Cell(0, 10, 'Reporte de Huertas', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 5); // Tamaño de fuente para encabezados

// Encabezados de la tabla
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

// Variable para ajustar el tamaño de fuente de los datos
$tamañoFuenteDatos = 5; // Cambia este valor para ajustar el tamaño de fuente de los datos

// Cambiar el tamaño de fuente para las filas de datos
$pdf->SetFont('helvetica', '', $tamañoFuenteDatos); // Tamaño de fuente para los datos

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
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->Cell(10, 5, $row['id_hue'], 1);
    $pdf->Cell(20, 5, $row['nombre_productor'], 1);
    $pdf->Cell(20, 5, $row['nombre_huerta'], 1);
    $pdf->Cell(20, 5, $row['localidad'], 1);
    $pdf->Cell(10, 5, $row['centroide'], 1);
    $pdf->Cell(10, 5, $row['hectareas'], 1);
    $pdf->Cell(15, 5, $row['pronostico_de_cosecha'], 1);
    $pdf->Cell(15, 5, $row['longitud'], 1);
    $pdf->Cell(15, 5, $row['altitud'], 1);
    $pdf->Cell(15, 5, $row['altura_nivel_del_mar'], 1);
    $pdf->Cell(20, 5, $row['variedad'], 1);
    $pdf->Cell(20, 5, $row['nomempresa'], 1);
    $pdf->Cell(20, 5, $row['encargadoempresa'], 1);
    $pdf->Cell(20, 5, $row['supervisorhuerta'], 1);
    $pdf->Cell(20, 5, $row['anoplantacion'], 1);
    $pdf->Cell(20, 5, $row['arbolesporhectareas'], 1);
    $pdf->Cell(20, 5, $row['totalarboles'], 1);
    $pdf->Cell(20, 5, $row['etapafenologica'], 1);
    $pdf->Cell(20, 5, $row['fechasv_01'], 1);
    $pdf->Cell(20, 5, $row['fechasv_02'], 1);
    $pdf->Cell(20, 5, $row['fechaRegistro'], 1);
    $pdf->Ln();
}

// Cerrar la conexión
$conn = null;

// Generar el PDF
$pdf->Output('reporte_Huertas.pdf', 'I');
?>
