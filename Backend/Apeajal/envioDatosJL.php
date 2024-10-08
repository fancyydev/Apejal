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

if (isset($_GET['idjuntalocal'])) {
    $id_juntalocal = $_GET['idjuntalocal'];

    // Consulta a la base de datos
    $stmt = $conn->prepare("
        SELECT 
        jl.idjuntalocal, jl.carga_municipios, jl.id_usuario, jl.nombre, jl.domicilio, jl.teléfono, jl.correo, jl.estatus, jl.ruta_img,
        u.nombre AS nombre_admin,
        GROUP_CONCAT(m.nombre SEPARATOR ', ') AS municipios
        FROM juntaslocales jl
        JOIN usuario u ON jl.id_usuario = u.id_usuario
        LEFT JOIN municipio m ON FIND_IN_SET(m.id_municipio, jl.carga_municipios)
        WHERE jl.idjuntalocal = :idjuntalocal
        GROUP BY jl.idjuntalocal
    ");
    
    // Ejecutar la consulta
    if ($stmt->execute(['idjuntalocal' => $id_juntalocal])) {
        $juntalocal = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($juntalocal) {
            // Devuelve los datos del productor en formato JSON
            echo json_encode($juntalocal);
        } else {
            echo json_encode(['error' => 'Junta Local no encontrada']);
        }
    } else {
        // Manejo de error en la ejecución de la consulta
        echo json_encode(['error' => 'Error en la consulta']);
    }
} else {
    echo json_encode(['error' => 'ID de tecnico no proporcionado']);
}



// Cerrar la conexión
$conn = null;
$stmt = null;

?>