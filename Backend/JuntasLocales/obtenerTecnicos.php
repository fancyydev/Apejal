<?php
header('Content-Type: application/json'); // Asegúrate de que el tipo de contenido es JSON

// Configuración de la base de datos
$servername = "localhost";
$username = "root"; // Ajusta esto según tu configuración
$password = ""; // Ajusta esto según tu configuración
$dbname = "materiaseca_apeajal";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

// Consulta para obtener los datos del técnico, usuario, y junta local
$sql = "SELECT t.id_tecnico, u.nombre AS nombre_usuario, u.correo, u.teléfono, j.nombre AS nombre_junta
        FROM tecnico t
        JOIN usuario u ON t.id_usuario = u.id_usuario
        JOIN juntaslocales j ON t.idjuntalocal = j.idjuntalocal";

$result = $conn->query($sql);

$tecnicos = array();

if ($result->num_rows > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $result->fetch_assoc()) {
        $tecnicos[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Devolver datos en formato JSON
echo json_encode($tecnicos);

// Cerrar conexión
$conn->close();
?>
