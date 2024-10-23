<?php
header('Content-Type: application/json'); // Asegúrate de que el tipo de contenido es JSON

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

// Consulta para obtener los datos
$sql = "SELECT jl.idjuntalocal, jl.nombre, u.nombre as nombre_admin, jl.correo, jl.teléfono, jl.domicilio, jl.carga_municipios, jl.estatus, jl.ruta_img 
        FROM juntaslocales jl
        JOIN usuario u ON u.id_usuario = jl.id_usuario";

$result = $conn->query($sql);

$juntalocal = array();

if ($result !== false && $result->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $juntalocal[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$result = null;

// Devolver datos en formato JSON
echo json_encode($juntalocal);

?>
