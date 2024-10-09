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

// Obtener el ID del usuario de la sesión
session_start();
$id_usuario = $_SESSION["id"]; // Suponiendo que el ID de usuario está en la sesión

// Verifica que el ID del usuario esté establecido
if (!isset($id_usuario)) {
    echo json_encode(['error' => 'ID de usuario no encontrado en la sesión']);
    exit();
}

// Consulta para obtener los datos de la huerta, productor y junta local filtrados por el ID del usuario
$sql = "SELECT h.id_hue, u.nombre AS nombre_productor, jl.nombre AS nombre_junta_local, h.nombre AS nombre_huerta, h.localidad, h.centroide, 
               h.hectareas, h.pronostico_de_cosecha, h.longitud, h.altitud, h.altura_nivel_del_mar, 
               h.variedad, h.nomempresa, h.encargadoempresa, h.supervisorhuerta, h.anoplantacion, 
               h.arbolesporhectareas, h.totalarboles, h.etapafenologica, h.fechasv_01, h.fechasv_02, 
               h.rutaKML, h.fechaRegistro 
        FROM huertas h
        JOIN juntaslocales jl ON jl.idjuntaLocal = h.idjuntaLocal
        JOIN productores p ON p.id_productor = h.id_productor
        JOIN usuario u ON u.id_usuario = p.id_usuario
        WHERE h.id_productor = (
            SELECT p2.id_productor
            FROM productores p2
            WHERE p2.id_usuario = :id_usuario
        )";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT); // Vincular el parámetro

// Ejecutar la consulta
$stmt->execute();
$huertas = array();

// Verificar si hay resultados
if ($stmt !== false && $stmt->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $huertas[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver datos en formato JSON
echo json_encode($huertas);

?>




