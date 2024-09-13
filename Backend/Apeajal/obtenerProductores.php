<?php
header('Content-Type: application/json'); // Asegúrate de que el tipo de contenido es JSON

//USAR EL CONECTAR PARA REUTILIZAR CODIGO
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

// Consulta para obtener los datos
$sql = "SELECT p.id_productor, u.nombre, u.correo, u.teléfono, p.rfc, p.curp, p.estatus
        FROM productores p
        JOIN usuario u ON p.id_usuario = u.id_usuario";
$result = $conn->query($sql);

$productores = array();

if ($result->num_rows > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $result->fetch_assoc()) {
        $productores[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Devolver datos en formato JSON
echo json_encode($productores);

// Cerrar conexión
$conn->close();
?>
