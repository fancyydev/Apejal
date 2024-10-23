<?php
// Conexión a la base de datos
require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

// Verificar si los datos necesarios se han enviado
if (!isset($_POST['id_solicitud']) || !isset($_POST['status']) || !isset($_POST['tecnico']) || !isset($_POST['fecha_programada'])) {
    echo json_encode(['error' => 'Faltan datos para la actualización']);
    exit();
}

// Obtener los datos del formulario
$id_solicitud = $_POST['id_solicitud'];
$id_tecnico = $_POST['tecnico'];
$status = $_POST['status'];
$fecha_programada = $_POST['fecha_programada'];

// Crear una conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar si la conexión es exitosa
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión
    $errorInfo = $conn->errorInfo();
    echo json_encode(['error' => 'Error en la conexión a la base de datos: ' . implode(", ", $errorInfo)]);
    exit();
}

// Preparar la consulta SQL para actualizar la solicitud
$sql = "UPDATE solicitudes 
        SET id_tecnico = :id_tecnico, 
            status = :status, 
            fecha_programada = :fecha_programada 
        WHERE id_solicitud = :id_solicitud";

$stmt = $conn->prepare($sql);

// Asignar valores a los parámetros de la consulta
$stmt->bindParam(':id_solicitud', $id_solicitud, PDO::PARAM_INT);
$stmt->bindParam(':id_tecnico', $id_tecnico, PDO::PARAM_INT);
$stmt->bindParam(':status', $status, PDO::PARAM_STR);
$stmt->bindParam(':fecha_programada', $fecha_programada, PDO::PARAM_STR);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(['success' => 'Solicitud actualizada correctamente']);
} else {
    $errorInfo = $stmt->errorInfo();
    echo json_encode(['error' => 'Error al actualizar la solicitud: ' . implode(", ", $errorInfo)]);
}

// Cerrar la conexión
$conn = null;
?>
