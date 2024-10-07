<?php
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

// Conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if (!$conn) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtiene los datos de la solicitud POST
$id_hue = $_POST['id_hue'] ?? null;
// ... (otros campos) ...

// Verifica si se proporcionó el ID de la huerta
if ($id_hue === null) {
    echo json_encode(['message' => 'El ID de la huerta es requerido.']);
    exit();
}

// Consulta para actualizar los datos de la huerta
$sql = "UPDATE huertas SET
    nombre = :nombre,
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
    añoplantacion = :añoplantacion,
    arbolesporhectareas = :arbolesporhectareas,
    totalarboles = :totalarboles,
    etapafenologica = :etapafenologica,
    fechasv_01 = :fechasv_01,
    fechasv_02 = :fechasv_02,
    rutaKML = :rutaKML
WHERE id_hue = :id_hue";

$stmt = $conn->prepare($sql); // Cambiado a $conn

// Asigna los valores a los parámetros
$stmt->bindParam(':nombre', $nombre);
// ... (otros campos) ...
$stmt->bindParam(':id_hue', $id_hue);

try {
    // Ejecuta la consulta
    $stmt->execute();
    echo json_encode(['message' => 'Huerta actualizada con éxito.']);
} catch (PDOException $e) {
    echo json_encode(['message' => 'Error al actualizar la huerta: ' . $e->getMessage(), 'errorCode' => $e->getCode()]);
}

$conn = null; // Cierra la conexión
?>
    