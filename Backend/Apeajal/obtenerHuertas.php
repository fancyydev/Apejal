<?php
header('Content-Type: application/json'); // Asegúrate de que el tipo de contenido es JSON

require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");


// Crear conexión
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

// Consulta para obtener los datos de la huerta, productor y junta local
$sql = "SELECT h.id_hue, u.nombre AS nombre_productor, h.nombre AS nombre_huerta, h.localidad, h.centroide, 
               h.hectareas, h.pronostico_de_cosecha, h.longitud, h.altitud, h.altura_nivel_del_mar, 
               h.variedad, h.nomempresa, h.encargadoempresa, h.supervisorhuerta, h.añoplantacion, 
               h.arbolesporhectareas, h.totalarboles, h.etapafenologica, h.fechasv_01, h.fechasv_02, 
               h.rutaKML, h.fechaRegistro 
        FROM huertas h
        JOIN productores p ON h.id_productor = p.id_productor
        JOIN usuario u ON p.id_usuario = u.id_usuario";

$result = $conn->query($sql);

$huertas = array();

// Verificar si hay resultados
if ($result !== false && $result->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $huertas[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$result = null;

// Devolver datos en formato JSON
echo json_encode($huertas);

?>




