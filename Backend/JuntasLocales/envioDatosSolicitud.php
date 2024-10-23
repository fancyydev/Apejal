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

if (isset($_GET['id_solicitud'])) {
    $id_solicitud = $_GET['id_solicitud'];

    // Consulta a la base de datos
    $stmt = $conn->prepare("SELECT s.id_solicitud, s.status, up.nombre AS nombre_productor, h.nombre AS nombre_huerta, s.fecha_programada, ut.nombre AS nombre_tecnico, t.id_tecnico
        FROM solicitudes s
        JOIN huertas h ON s.id_hue = h.id_hue
        JOIN juntaslocales jl ON jl.idjuntalocal = h.idjuntalocal 
        LEFT JOIN tecnico t ON t.id_tecnico = s.id_tecnico
        LEFT JOIN usuario ut ON ut.id_usuario = t.id_usuario
        JOIN productores p ON p.id_productor = h.id_productor
        JOIN usuario up ON up.id_usuario = p.id_usuario
        WHERE s.id_solicitud = :id_solicitud");
    
    
    // Ejecutar la consulta
    if ($stmt->execute(['id_solicitud' => $id_solicitud])) {
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($solicitud) {
            // Devuelve los datos del productor en formato JSON
            echo json_encode($solicitud);
        } else {
            echo json_encode(['error' => 'Solicitud no encontrada']);
        }
    } else {
        // Manejo de error en la ejecución de la consulta
        echo json_encode(['error' => 'Error en la consulta']);
    }
} else {
    echo json_encode(['error' => 'ID de la solicitud no proporcionado']);
}



// Cerrar la conexión
$conn = null;
$stmt = null;

?>