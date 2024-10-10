<?php

header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON

require_once($_SERVER['DOCUMENT_ROOT'] . "/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

// Iniciar la sesión
session_start();
$id_usuario = isset($_SESSION["id"]) ? $_SESSION["id"] : null; // Suponiendo que el ID del usuario está en la sesión

// Verificar si el ID del usuario está establecido
if (!$id_usuario) {
    echo json_encode(['error' => 'ID del usuario no encontrado en la sesión']);
    exit();
}

// Crear conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

$id_solicitud = isset($_GET['id_solicitud']) ? $_GET['id_solicitud'] : null;

if (!$id_solicitud) {
    echo json_encode(['error' => 'ID de la solicitud no recibido']);
    exit();
}

// Preparar la consulta SQL
// Preparar la consulta SQL

// Preparar la consulta SQL
$sql = "SELECT t.id_tecnico, u.nombre 
        FROM tecnico t
        JOIN juntaslocales j ON j.idjuntalocal = t.idjuntalocal
        JOIN usuario u ON u.id_usuario = t.id_usuario
        WHERE j.idjuntalocal = (
            SELECT j2.idjuntalocal 
            FROM juntaslocales j2
            JOIN solicitudes s on s.id_solicitud = :id_solicitud
            JOIN huertas h ON h.id_hue = s.id_hue
            WHERE j2.idjuntalocal = h.idjuntalocal
        )";



$stmt = $conn->prepare($sql);

// Asignar el valor del parámetro id_solicitud
$stmt->bindParam(':id_solicitud', $id_solicitud, PDO::PARAM_INT);

// Ejecutar la consulta
$stmt->execute();

// Verificar si hay resultados
$options = array();
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options[] = array(
            'id_tecnico' => $row['id_tecnico'], // Corregí el campo a 'id_tecnico'
            'nombre' => $row['nombre']
        );
    }
} else {
    $options = array('id_tecnico' => '', 'nombre' => 'No hay técnicos disponibles');
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver las opciones como respuesta (formato JSON)
echo json_encode($options);

?>
