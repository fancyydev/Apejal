<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");


// Conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

// Obtener datos enviados por POST (JSON)
$data = json_decode(file_get_contents("php://input"));

// Verificar si los datos se reciben correctamente
if (!$data) {
    die('Error: No se recibieron datos correctamente.');
}

// Obtener valores
$nombre = $data->nombre;
$domicilio = $data->domicilio;
$telefono = $data->telefono;
$correo = $data->correo;
$municipio = $data->municipio;
$admin = $data->admin;
$status = $data->status;

// Preparar la consulta SQL
$sql = "INSERT INTO juntaslocales (id_municipio, id_usuario, nombre, domicilio, teléfono, correo, estatus) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Verificar si la consulta se preparó correctamente
if (!$stmt) {
    die("Error en la preparación de la consulta: " . implode(", ", $conn->errorInfo()));
}

// Vincular parámetros
$stmt->bindParam(1, $municipio);
$stmt->bindParam(2, $admin);
$stmt->bindParam(3, $nombre);
$stmt->bindParam(4, $domicilio);
$stmt->bindParam(5, $telefono);
$stmt->bindParam(6, $correo);
$stmt->bindParam(7, $status);

// Array para la respuesta JSON
$response = array();

// Ejecutar consulta
if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Junta local registrada exitosamente';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error al registrar la junta local: ' . $stmt->error;
}

// Cerrar consulta y conexión
$stmt->close();
$conn->close();

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
