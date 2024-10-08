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
session_start();
$id = $_SESSION["id"];
$sql = "
    SELECT m.id_municipio, m.nombre 
    FROM municipio m
    JOIN juntaslocales j ON FIND_IN_SET(m.id_municipio, j.carga_municipios)
    WHERE j.id_usuario = :id
    GROUP BY m.id_municipio, m.nombre;
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT); // Asignar el ID a la consulta
$stmt->execute();

$municipios = array();

if ($stmt->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $municipios[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver datos en formato JSON
echo json_encode($municipios);
?>
