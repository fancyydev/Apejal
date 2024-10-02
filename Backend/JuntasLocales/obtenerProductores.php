<?php
session_start(); // Asegúrate de que la sesión esté iniciada
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

// Asegurarse de que el ID de la sesión está disponible
if (!isset($_SESSION["id"])) {
    echo json_encode(['error' => 'ID de usuario no disponible']);
    exit();
}

// Consulta para obtener los datos filtrados por el ID de usuario
$id = $_SESSION["id"];
$sql = "SELECT p.id_productor, u.nombre, j.nombre AS nombre_junta, u.correo, u.teléfono, p.rfc, p.curp, p.estatus
        FROM productores p
        JOIN usuario u ON p.id_usuario = u.id_usuario
        JOIN juntaslocales j ON p.idjuntalocal = j.idjuntalocal
        WHERE u.id_usuario = :id"; // Uso de parámetro nombrado

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT); // Asignar el ID a la consulta
$stmt->execute();

$productores = array();

if ($stmt->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $productores[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver datos en formato JSON
echo json_encode($productores);
?>
