<?php
header('Content-Type: application/json'); // Asegúrate de que el tipo de contenido es JSON

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

$sql = "SELECT 
    h.id_hue, 
    u.nombre AS nombre_productor, 
    jl.nombre AS nombre_junta_local, 
    h.nombre AS nombre_huerta, 
    h.localidad, 
    h.centroide, 
    h.hectareas, 
    h.pronostico_de_cosecha, 
    h.longitud, 
    h.altitud, 
    h.altura_nivel_del_mar, 
    h.variedad, 
    h.nomempresa, 
    h.encargadoempresa, 
    h.supervisorhuerta, 
    h.anoplantacion, 
    h.arbolesporhectareas, 
    h.totalarboles, 
    h.etapafenologica, 
    h.fechasv_01, 
    h.fechasv_02, 
    h.rutaKML, 
    h.fechaRegistro 
FROM huertas h
JOIN productores p ON p.id_productor = h.id_productor
JOIN usuario u ON u.id_usuario = p.id_usuario
JOIN juntaslocales jl ON jl.idjuntaLocal = h.idjuntaLocal
WHERE jl.idjuntalocal = (
    SELECT j2.idjuntalocal 
    FROM juntaslocales j2
    WHERE j2.id_usuario = :id_usuario
);
"; // Cerrar la consulta correctamente

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Vincular el parámetro correctamente
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT); 

// Ejecutar la consulta
if ($stmt->execute()) {
    $huertas = array();

    // Verificar si hay resultados
    if ($stmt->rowCount() > 0) {
        // Convertir los datos en un array asociativo
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Obtener el ID del municipio
            $localidadId = $row['localidad'];

            // Segunda consulta para obtener el nombre del municipio
            $sqlMunicipio = "SELECT nombre FROM municipio WHERE id_municipio = :id_municipio";
            $stmtMunicipio = $conn->prepare($sqlMunicipio);
            $stmtMunicipio->bindParam(':id_municipio', $localidadId, PDO::PARAM_INT);
            $stmtMunicipio->execute();

            // Obtener el nombre del municipio
            $municipio = $stmtMunicipio->fetch(PDO::FETCH_ASSOC);

            // Agregar el nombre del municipio a los datos de la huerta
            $row['nombre_municipio'] = $municipio['nombre'];

            // Añadir la huerta (con el nombre del municipio) al array de huertas
            $huertas[] = $row;
        }
        // Devolver datos en formato JSON
        echo json_encode($huertas);
    } else {
        echo json_encode(['error' => 'No se encontraron datos']);
    }
} else {
    // Manejar errores si la consulta no se ejecuta correctamente
    $errorInfo = $stmt->errorInfo();
    echo json_encode(['error' => 'Error en la consulta: ' . implode(", ", $errorInfo)]);
}

// Cerrar la conexión
$conn = null;
$stmt = null;

?>