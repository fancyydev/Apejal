<?php

header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON

require_once($_SERVER['DOCUMENT_ROOT'] . "/Apejal/Backend/DataBase/connectividad.php");

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

// Preparar la consulta SQL
$sql = "SELECT t.id_tecnico, u.nombre 
        FROM tecnico t
        JOIN juntaslocales j ON j.idjuntalocal = t.idjuntalocal
        JOIN usuario u ON u.id_usuario = t.id_usuario
        WHERE j.idjuntalocal = (
            SELECT j2.idjuntalocal 
            FROM juntaslocales j2
            WHERE j2.id_usuario = :id_usuario
        )";

$stmt = $conn->prepare($sql);

// Asignar el valor del parámetro de la sesión a la consulta preparada
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

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
