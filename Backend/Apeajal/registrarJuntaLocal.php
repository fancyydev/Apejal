<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root"; // Cambia esto si usas otro nombre de usuario
$password = ""; // Cambia esto si tienes una contraseña
$dbname = "materiaseca_apeajal";
$port = 3306; // Puerto de MySQL en XAMPP

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtener datos enviados por POST (JSON)
$data = json_decode(file_get_contents("php://input"));

// Mostrar valores obtenidos para verificar
echo "Datos recibidos:<br>";
echo "<pre>";
print_r($data);
echo "</pre>";

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

// Preparar la consulta SQL
$sql = "INSERT INTO juntaslocales (nombre, domicilio, telefono, correo, id_municipio, id_usuario, estatus) VALUES (?, ?, ?, ?, ?, 1, 'Activo')";
$stmt = $conn->prepare($sql);

// Verificar si la consulta se preparó correctamente
if (!$stmt) {
    die('Error al preparar la consulta: ' . $conn->error);
}

// Vincular parámetros
$stmt->bind_param("ssssi", $nombre, $domicilio, $telefono, $correo, $municipio);

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
