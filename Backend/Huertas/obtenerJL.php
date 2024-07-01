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

$sql = "SELECT idjuntalocal, nombre FROM  juntaslocales";

$result = $conn->query($sql);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    $options = array();
    while ($row = $result->fetch_assoc()) {
        $options[] = array(
            'idjuntalocal' => $row['idjuntalocal'],
            'nombre' => $row['nombre']
        );
    }
} else {
    $options = array('idjuntalocal' => '', 'nombre' => 'No hay productores disponibles');
}

$conn->close();

// Devolver las opciones como respuesta (formato JSON)
echo json_encode($options);
?>
