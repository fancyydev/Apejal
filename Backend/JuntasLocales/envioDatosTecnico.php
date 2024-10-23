<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

// Conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

if (isset($_GET['id_tecnico'])) {
    $id_tecnico = $_GET['id_tecnico'];

    // Consulta a la base de datos
    $stmt = $conn->prepare("
        SELECT 
        u.id_usuario, u.id_tipo, u.nombre, u.correo, u.teléfono, u.contraseña, 
        t.idjuntalocal, t.carga_municipios, t.estatus, 
        jl.nombre AS nombre_junta,
        GROUP_CONCAT(m.nombre SEPARATOR ', ') AS municipios
        FROM tecnico t
        JOIN usuario u ON t.id_usuario = u.id_usuario
        LEFT JOIN juntaslocales jl ON t.idjuntalocal = jl.idjuntalocal
        LEFT JOIN municipio m ON FIND_IN_SET(m.id_municipio, t.carga_municipios)
        WHERE t.id_tecnico = :id_tecnico
        GROUP BY t.id_tecnico
    ");
    
    
    // Ejecutar la consulta
    if ($stmt->execute(['id_tecnico' => $id_tecnico])) {
        $tecnico = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tecnico) {
            // Devuelve los datos del productor en formato JSON
            echo json_encode($tecnico);
        } else {
            echo json_encode(['error' => 'Productor no encontrado']);
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