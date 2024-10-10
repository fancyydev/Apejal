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


$sql = "SELECT l.id_laboratorio, u.nombre AS nombre_usuario, u.correo, u.teléfono, 
                l.estatus, j.nombre AS nombre_junta
        FROM laboratorio l
        JOIN usuario u ON l.id_usuario = u.id_usuario 
        JOIN juntaslocales j ON l.idjuntalocal = j.idjuntalocal
        WHERE j.idjuntalocal = (
            SELECT j2.idjuntalocal 
            FROM juntaslocales j2
            WHERE j2.id_usuario = :id_usuario
        )";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT); // Vincular el parámetro

// Ejecutar la consulta
$stmt->execute();
$personalLaboratorio = array();

// Verificar si hay resultados
if ($stmt !== false && $stmt->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $personalLaboratorio[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver datos en formato JSON
echo json_encode($personalLaboratorio);
    
?>
