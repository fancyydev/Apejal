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

// Obtener el ID del usuario de la sesión
session_start();
$id_usuario = $_SESSION["id"]; // Suponiendo que el ID de usuario está en la sesión

// Verifica que el ID del usuario esté establecido
if (!isset($id_usuario)) {
    echo json_encode(['error' => 'ID de usuario no encontrado en la sesión']);
    exit();
}

$sql = "SELECT h.id_hue, h.nombre 
        FROM huertas h
        LEFT JOIN solicitudes s ON h.id_hue = s.id_hue AND s.status IN ('pendiente', 'activa')
        WHERE h.id_productor = (
            SELECT p2.id_productor
            FROM productores p2
            WHERE p2.id_usuario = :id_usuario
        ) AND s.id_hue IS NULL"; // Asegúrate de que la huerta no esté asociada a ninguna solicitud pendiente o activa


// Preparar la consulta
$stmt = $conn->prepare($sql);

// Enlazar el parámetro
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

// Ejecutar la consulta
$stmt->execute();

// Verificar si hay resultados
if ($stmt->rowCount() > 0) {
    $options = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options[] = array(
            'id_hue' => $row['id_hue'],
            'nombre' => $row['nombre']
        );
    }
} else {
    $options = array('id_hue' => '', 'nombre' => 'No hay huertas disponibles');
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver las opciones como respuesta (formato JSON)
echo json_encode($options);
?>
