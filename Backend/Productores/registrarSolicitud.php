<?php
session_start();

// Verifica que el usuario ha iniciado sesión y tiene el tipo correcto
if (!isset($_SESSION['id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    // Redirige al usuario a la página de inicio de sesión si no tiene permiso
    header('Location: ../../index.html');
    exit();
}

// Conectar a la base de datos
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

// Obtener datos del formulario
$id_hue = isset($_POST['huerta']) ? $_POST['huerta'] : null;
$status = 'pendiente'; 
$id_tecnico = null; 
$fecha_programada = null; 

// Validar que el id_hue no sea nulo
if (is_null($id_hue)) {
    echo json_encode(['error' => 'Huerta no válida']);
    exit();
}

// Preparar la consulta de inserción
$sql = "INSERT INTO solicitudes (id_hue, id_tecnico, status, fecha_programada) VALUES (:id_hue, :id_tecnico, :status, :fecha_programada)";

$stmt = $conn->prepare($sql);

// Enlazar los parámetros
$stmt->bindParam(':id_hue', $id_hue, PDO::PARAM_INT);
$stmt->bindParam(':id_tecnico', $id_tecnico, PDO::PARAM_INT);
$stmt->bindParam(':status', $status, PDO::PARAM_STR);
$stmt->bindParam(':fecha_programada', $fecha_programada, PDO::PARAM_NULL); // Enlazando NULL

// Ejecutar la consulta
if ($stmt->execute()) {
    echo json_encode(['success' => 'Solicitud registrada exitosamente']);
} else {
    echo json_encode(['error' => 'Error al registrar la solicitud']);
}

// Cerrar la conexión
$conn = null;
$stmt = null;
?>