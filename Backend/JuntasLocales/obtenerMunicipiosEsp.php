<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

// Crear conexión
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

if (isset($_POST['idJuntaLocal'])) {
    $idJuntaLocal = $_POST['idJuntaLocal'];

    // Obtener los IDs de los municipios asociados a la junta local
    $stmt = $conn->prepare("SELECT carga_municipios FROM juntaslocales WHERE idjuntalocal = ?");
    $stmt->execute([$idJuntaLocal]);
    $junta = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($junta) {
        $municipioIds = $junta['carga_municipios'];
        
        // Convertir la cadena de IDs en un array
        $idArray = (explode(',', $municipioIds));

        // Obtener los nombres de los municipios
        if (!empty($idArray)) {
            $placeholders = implode(',', array_fill(0, count($idArray), '?'));
            $stmt = $conn->prepare("SELECT id_municipio, nombre FROM municipio WHERE id_municipio IN ($placeholders)");
            $stmt->execute($idArray);
            $municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $municipios = [];
        }

        // Retornar los municipios como JSON
        echo json_encode($municipios);
    } else {
        // Si no se encontró la junta local
        echo json_encode([]);
    }
} else {
    // Si no se envió el ID de la junta local
    echo json_encode([]);
}

// Cerrar conexión
$conn = null;
?>
