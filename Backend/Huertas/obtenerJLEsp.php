<?php

session_start();

require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

if (!isset($_SESSION['id'])) {
    die("Error: No se ha iniciado sesión.");
}

$id_usuario = $_SESSION['id'];

// Crear conexión
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

// Modificar la consulta para seleccionar solo la junta local del usuario
$sql = "SELECT idjuntalocal, nombre FROM juntaslocales WHERE id_usuario = :id_usuario";

// Preparar la consulta
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

// Verificar si hay resultados
if ($stmt !== false && $stmt->rowCount() > 0) {
    $options = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options[] = array(
            'idjuntalocal' => $row['idjuntalocal'],
            'nombre' => $row['nombre']
        );
    }
} else {
    $options = array('idjuntalocal' => '', 'nombre' => 'No hay junta local disponible para este usuario');
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver las opciones como respuesta (formato JSON)
echo json_encode($options);

?>
