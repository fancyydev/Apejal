<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

// Conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

if (isset($_GET['id_hue'])) {
    $id_hue = $_GET['id_hue'];

    // Consulta a la base de datos
    $stmt = $conn->prepare("
        SELECT h.id_hue, h.id_productor, h.nombre, h.localidad, h.centroide, h.hectareas, 
               h.pronostico_de_cosecha, h.longitud, h.altitud, h.altura_nivel_del_mar, 
               h.variedad, h.nomempresa, h.encargadoempresa, h.supervisorhuerta, 
               h.anoplantacion, h.arbolesporhectareas, h.totalarboles, h.etapafenologica, 
               h.fechasv_01, h.fechasv_02, h.rutaKML, h.fechaRegistro, h.idjuntalocal
        FROM huertas h
        WHERE h.id_hue = :id_hue
    ");
    
    // Ejecutar la consulta
    if ($stmt->execute(['id_hue' => $id_hue])) {
        $huerta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($huerta) {
            // Devuelve los datos de la huerta en formato JSON
            echo json_encode($huerta);
        } else {
            echo json_encode(['error' => 'Huerta no encontrada']);
        }
    } else {
        // Manejo de error en la ejecución de la consulta
        echo json_encode(['error' => 'Error en la consulta']);
    }
} else {
    echo json_encode(['error' => 'ID de huerta no proporcionado']);
}

// Cerrar la conexión
$conn = null;
$stmt = null;

?>
