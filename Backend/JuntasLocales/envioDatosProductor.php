<?php
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

// Conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

if (isset($_GET['id_productor'])) {
    $id_productor = $_GET['id_productor'];

    // Consulta a la base de datos
    $stmt = $conn->prepare("
        SELECT u.id_usuario,u.id_tipo, u.nombre, u.correo, u.teléfono, u.contraseña, p.rfc, p.curp, p.estatus, p.idjuntalocal, jl.nombre AS nombre_junta  
        FROM productores p
        JOIN usuario u ON p.id_usuario = u.id_usuario 
        LEFT JOIN juntaslocales jl ON p.idjuntalocal = jl.idjuntalocal
        WHERE p.id_productor = :id_productor
    ");
    
    
    // Ejecutar la consulta
    if ($stmt->execute(['id_productor' => $id_productor])) {
        $productor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($productor) {
            // Devuelve los datos del productor en formato JSON
            echo json_encode($productor);
        } else {
            echo json_encode(['error' => 'Productor no encontrado']);
        }
    } else {
        // Manejo de error en la ejecución de la consulta
        echo json_encode(['error' => 'Error en la consulta']);
    }
} else {
    echo json_encode(['error' => 'ID de productor no proporcionado']);
}



// Cerrar la conexión
$conn = null;
$stmt = null;

?>