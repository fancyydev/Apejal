<?php
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

// Conexión a la base de datos con PDO
$conexion = new DB_Connect();
$conn = $conexion->connect();

if (!$conn) {
    die("Conexión fallida: " . $conexion->connect_error);
}
// Imprimir los valores de POST para verificar si están llegando correctamente
echo "<pre>";
echo "POST Data: \n";
print_r($_POST);
echo "</pre>";
// Obtiene los datos de la solicitud POST
$id_hue = $_POST['id_hue'] ?? null;
$idjuntalocal = $_POST['idjuntalocal'] ?? null;
$nombre = $_POST['nombre'] ?? '';
$localidad = $_POST['localidad'] ?? '';
$centroide = $_POST['centroide'] ?? '';
$hectareas = $_POST['hectareas'] ?? 0;
$pronostico_de_cosecha = $_POST['pronostico_de_cosecha'] ?? '';
$longitud = $_POST['longitud'] ?? '';
$altitud = $_POST['altitud'] ?? '';
$altura_nivel_del_mar = $_POST['altura_nivel_del_mar'] ?? '';
$variedad = $_POST['variedad'] ?? '';
$nomempresa = $_POST['nomempresa'] ?? '';
$encargadoempresa = $_POST['encargadoempresa'] ?? '';
$supervisorhuerta = $_POST['supervisorhuerta'] ?? '';
$anoplantacion = $_POST['anoplantacion'] ?? 0;
$arbolesporhectareas = $_POST['arbolesporhectareas'] ?? 0;
$totalarboles = $_POST['totalarboles'] ?? 0;
$etapafenologica = $_POST['etapafenologica'] ?? '';
$fechasv_01 = $_POST['fechasv_01'] ?? null;
$fechasv_02 = $_POST['fechasv_02'] ?? null;
$rutaKML = $_POST['rutaKML'] ?? '';

// Verifica si se proporcionó el ID de la huerta
if ($id_hue === null) {
    echo json_encode(['message' => 'El ID de la huerta es requerido.']);
    exit();
}

// Imprimir los valores antes de ejecutar la consulta para verificar si están bien asignados
echo "<pre>";
echo "Valores recibidos: \n";
echo "ID Huerta: $id_hue\n";
echo "ID jl: $idjuntalocal\n";
echo "Nombre: $nombre\n";
echo "Localidad: $localidad\n";
echo "Centroide: $centroide\n";
echo "Hectáreas: $hectareas\n";
echo "Pronóstico de Cosecha: $pronostico_de_cosecha\n";
echo "Longitud: $longitud\n";
echo "Altitud: $altitud\n";
echo "Altura Nivel del Mar: $altura_nivel_del_mar\n";
echo "Variedad: $variedad\n";
echo "Nombre Empresa: $nomempresa\n";
echo "Encargado Empresa: $encargadoempresa\n";
echo "Supervisor Huerta: $supervisorhuerta\n";
echo "Año Plantación: $anoplantacion\n";
echo "Árboles por Hectárea: $arbolesporhectareas\n";
echo "Total Árboles: $totalarboles\n";
echo "Etapa Fenológica: $etapafenologica\n";
echo "Fecha SV_01: $fechasv_01\n";
echo "Fecha SV_02: $fechasv_02\n";
echo "Ruta KML: $rutaKML\n";
echo "</pre>";

// Consulta para actualizar los datos de la huerta
$sql = "UPDATE huertas SET
    nombre = :nombre,
    idjuntalocal = :idjuntalocal,
    localidad = :localidad,
    centroide = :centroide,
    hectareas = :hectareas,
    pronostico_de_cosecha = :pronostico_de_cosecha,
    longitud = :longitud,
    altitud = :altitud,
    altura_nivel_del_mar = :altura_nivel_del_mar,
    variedad = :variedad,
    nomempresa = :nomempresa,
    encargadoempresa = :encargadoempresa,
    supervisorhuerta = :supervisorhuerta,
    anoplantacion = :anoplantacion,
    arbolesporhectareas = :arbolesporhectareas,
    totalarboles = :totalarboles,
    etapafenologica = :etapafenologica,
    fechasv_01 = :fechasv_01,
    fechasv_02 = :fechasv_02,
    rutaKML = :rutaKML
WHERE id_hue = :id_hue";

try {
    $stmt = $conn->prepare($sql);

    // Asigna los valores a los parámetros con bindValue
    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':idjuntalocal', $idjuntalocal);
    $stmt->bindValue(':localidad', $localidad);
    $stmt->bindValue(':centroide', $centroide);
    $stmt->bindValue(':hectareas', $hectareas);
    $stmt->bindValue(':pronostico_de_cosecha', $pronostico_de_cosecha);
    $stmt->bindValue(':longitud', $longitud);
    $stmt->bindValue(':altitud', $altitud);
    $stmt->bindValue(':altura_nivel_del_mar', $altura_nivel_del_mar);
    $stmt->bindValue(':variedad', $variedad);
    $stmt->bindValue(':nomempresa', $nomempresa);
    $stmt->bindValue(':encargadoempresa', $encargadoempresa);
    $stmt->bindValue(':supervisorhuerta', $supervisorhuerta);
    $stmt->bindValue(':anoplantacion', $anoplantacion);
    $stmt->bindValue(':arbolesporhectareas', $arbolesporhectareas);
    $stmt->bindValue(':totalarboles', $totalarboles);
    $stmt->bindValue(':etapafenologica', $etapafenologica);
    $stmt->bindValue(':fechasv_01', $fechasv_01);
    $stmt->bindValue(':fechasv_02', $fechasv_02);
    $stmt->bindValue(':rutaKML', $rutaKML);
    $stmt->bindValue(':id_hue', $id_hue);

    // Ejecuta la consulta
    $stmt->execute();
    echo json_encode(['message' => 'Huerta actualizada con éxito.']);
} catch (PDOException $e) {
    echo json_encode(['message' => 'Error al actualizar la huerta: ' . $e->getMessage(), 'errorCode' => $e->getCode()]);
}

$conn = null; // Cierra la conexión
?>
