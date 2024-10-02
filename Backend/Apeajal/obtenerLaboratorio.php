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

$sql = "SELECT l.id_laboratorio, u.nombre AS nombre_usuario, u.correo, u.teléfono, l.estatus, j.nombre AS nombre_junta
        FROM laboratorio l
        JOIN usuario u ON l.id_usuario = u.id_usuario
        JOIN juntaslocales j ON l.idjuntalocal = j.idjuntalocal";

$result = $conn->query($sql);

$personalLaboratorio = array();

if ($result !== false && $result->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $personalLaboratorio[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$result = null;

// Devolver datos en formato JSON
echo json_encode($personalLaboratorio);

?>
