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

// Consulta para obtener los datos de la huerta, productor y junta local
$sql = "SELECT h.id_hue, u.nombre AS nombre_productor, h.nombre AS nombre_huerta, jl.nombre as nombre_junta, h.localidad, h.centroide, 
               h.hectareas, h.pronostico_de_cosecha, h.longitud, h.altitud, h.altura_nivel_del_mar, 
               h.variedad, h.nomempresa, h.encargadoempresa, h.supervisorhuerta, h.anoplantacion, 
               h.arbolesporhectareas, h.totalarboles, h.etapafenologica, h.fechasv_01, h.fechasv_02, 
               h.rutaKML, h.fechaRegistro 
        FROM huertas h
        JOIN productores p ON h.id_productor = p.id_productor
        JOIN usuario u ON p.id_usuario = u.id_usuario
        JOIN juntaslocales jl on jl.idjuntalocal = h.idjuntalocal";

$result = $conn->query($sql);

$huertas = array();

// Verificar si hay resultados
if ($result !== false && $result->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Cerrar la conexión
$conn = null;
$result = null;

// Devolver datos en formato JSON
echo json_encode($huertas);

?>




