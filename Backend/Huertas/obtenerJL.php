<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

// Crear conexión
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

$sql = "SELECT idjuntalocal, nombre FROM  juntaslocales";

$result = $conn->query($sql);

// Verificar si hay resultados
if ($result !== false && $result->rowCount() > 0) {
    $options = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $options[] = array(
            'idjuntalocal' => $row['idjuntalocal'],
            'nombre' => $row['nombre']
        );
    }
} else {
    $options = array('idjuntalocal' => '', 'nombre' => 'No hay productores disponibles');
}

// Cerrar la conexión
$conn = null;
$result = null;

// Devolver las opciones como respuesta (formato JSON)
echo json_encode($options);
?>
