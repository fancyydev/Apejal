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

// Obtener el ID del técnico desde la sesión o una solicitud
session_start();
$id_tecnico = $_SESSION["id_tecnico"]; // Suponiendo que el ID de técnico está en la sesión

// Verificar si el ID del técnico está establecido
if (!isset($id_tecnico)) {
    echo json_encode(['error' => 'ID del técnico no encontrado en la sesión']);
    exit();
}

// Consulta para obtener las solicitudes relacionadas con el técnico
$sql = "SELECT s.id_solicitud, s.Fecha_programada, s.Hora_programada, s.status, 
               t.nombre AS nombre_tecnico, h.nombre AS nombre_huerta
        FROM solicitudes s
        JOIN tecnico t ON s.id_tecnico = t.id_tecnico
        JOIN huertas h ON s.id_hue = h.id_hue
        WHERE s.id_tecnico = :id_tecnico";

// Preparar la consulta
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_tecnico', $id_tecnico, PDO::PARAM_INT);

// Ejecutar la consulta
$stmt->execute();
$solicitudes = array();

// Verificar si hay resultados
if ($stmt !== false && $stmt->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $solicitudes[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron solicitudes']);
    exit();
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver los datos en formato JSON
echo json_encode($solicitudes);

?>
