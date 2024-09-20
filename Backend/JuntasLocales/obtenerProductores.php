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

// Consulta para obtener los datos
$sql = "SELECT p.id_productor, u.nombre, j.nombre AS nombre_junta, u.correo, u.teléfono, p.rfc, p.curp, p.estatus
        FROM productores p
        JOIN usuario u ON p.id_usuario = u.id_usuario
        JOIN juntaslocales j ON t.idjuntalocal = j.idjuntalocal";

$result = $conn->query($sql);

$productores = array();

if ($result !== false && $result->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $productores[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$result = null;

// Devolver datos en formato JSON
echo json_encode($productores);

?>
