<?php
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

$sql = "SELECT id_usuario, nombre FROM usuario WHERE id_tipo = 1";

$result = $conn->query($sql);

// Verificar si hay resultados
if ($result !== false && $result->rowCount() > 0) {
    $options = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $options[] = array(
            'id_usuario' => $row['id_usuario'],
            'nombre' => $row['nombre']
        );
    }
} else {
    $options = array('id_usuario' => '', 'nombre' => 'No hay productores disponibles');
}

// Cerrar la conexión
$conn = null;
$result = null;

// Devolver las opciones como formato JSON
echo json_encode($options);
?>
