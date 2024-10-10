<?php
header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON

require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

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
$sql = "SELECT s.id_solicitud, s.status, up.nombre AS nombre_productor, jl.nombre as nombre_junta, h.nombre AS nombre_huerta, s.fecha_programada, ut.nombre AS nombre_tecnico
        FROM solicitudes s
        JOIN huertas h ON s.id_hue = h.id_hue
        JOIN juntaslocales jl ON jl.idjuntalocal = h.idjuntalocal 
        LEFT JOIN tecnico t ON t.id_tecnico = s.id_tecnico
        LEFT JOIN usuario ut ON ut.id_usuario = t.id_usuario
        JOIN productores p ON p.id_productor = h.id_productor
        JOIN usuario up ON up.id_usuario = p.id_usuario
        ORDER BY 
            CASE 
                WHEN s.status = 'pendiente' THEN 1 
                ELSE 2 
            END";


// Preparar la consulta
$stmt = $conn->prepare($sql);
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
    echo json_encode(['solicitudes' => [], 'message' => 'No se encontraron solicitudes para esta junta local.']);
}

// Cerrar la conexión
$conn = null;
$stmt = null;

?>
