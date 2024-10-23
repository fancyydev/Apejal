<?php
header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON

require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");
// Iniciar la sesión
session_start();
$id_usuario = isset($_SESSION["id"]) ? $_SESSION["id"] : null; // Suponiendo que el ID del productor está en la sesión

// Verificar si el ID del productor está establecido
if (!$id_usuario) {
    echo json_encode(['error' => 'ID del productor no encontrado en la sesión']);
    exit();
}

// Crear conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Consulta SQL para obtener las solicitudes del productor
$sql = "SELECT s.id_solicitud, s.status, h.nombre AS nombre_huerta, s.fecha_programada, u.nombre AS nombre_tecnico
        FROM solicitudes s
        JOIN huertas h ON s.id_hue = h.id_hue
        LEFT JOIN tecnico t ON t.id_tecnico = s.id_tecnico
        LEFT JOIN usuario u ON u.id_usuario = t.id_usuario
        WHERE h.id_productor = (
            SELECT p2.id_productor
            FROM productores p2
            WHERE p2.id_usuario = :id_usuario
        )";

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Enlazar el parámetro
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

// Ejecutar la consulta
$stmt->execute();

// Verificar si hay resultados
$solicitudes = array();
if ($stmt->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $solicitudes[] = $row;
    }
    // Devolver los datos en formato JSON
    echo json_encode(['solicitudes' => $solicitudes]);
} else {
    // No se encontraron solicitudes
    echo json_encode(['solicitudes' => [], 'message' => 'No se encontraron solicitudes para este productor.']);
}

// Cerrar la conexión
$conn = null;
$stmt = null;

?>
