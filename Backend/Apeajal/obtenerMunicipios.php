<?php
// Conexión a la base de datos (reemplaza con tus datos de conexión)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "materiaseca_apeajal";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT id_municipio, nombre FROM  municipio";

$result = $conn->query($sql);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    $options = array();
    while ($row = $result->fetch_assoc()) {
        $options[] = array(
            'id_municipio' => $row['id_municipio'],
            'nombre' => $row['nombre']
        );
    }
} else {
    $options = array('id_municipio' => '', 'nombre' => 'No hay municipios disponibles');
}

$conn->close();

// Devolver las opciones como respuesta (formato JSON)
echo json_encode($options);
?>
