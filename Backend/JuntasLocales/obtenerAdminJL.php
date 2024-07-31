<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "materiaseca_apeajal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT id_usuario, nombre FROM usuario WHERE id_tipo = 4";

$result = $conn->query($sql);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    $options = array();
    while ($row = $result->fetch_assoc()) {
        $options[] = array(
            'id_usuario' => $row['id_usuario'],
            'nombre' => $row['nombre']
        );
    }
} else {
    $options = array('id_usuario' => '', 'nombre' => 'No hay administradores disponibles');
}

$conn->close();

// Devolver las opciones como formato JSON
echo json_encode($options);
?>
