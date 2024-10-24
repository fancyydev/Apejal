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
        //array para almacenar los nombres de los municipios
        $municipios_nombres = array();
        
        //Obtener los IDs de los municipios desde la consulta de arriba
        $ids_municipios = explode(',', $row['carga_municipios']);

        //obtener los nombres de los municipios
        $municipios_sql = "SELECT nombre FROM municipio WHERE id_municipio IN (" . implode(',', $ids_municipios) . ")";
        $municipios_result = $conn->query($municipios_sql);

        if ($municipios_result->rowCount() > 0) {
            while ($municipio = $municipios_result->fetch(PDO::FETCH_ASSOC)) {
                $municipios_nombres[] = $municipio['nombre'];  // Agregar el nombre del municipio al array
            }
        }

        // Agregar los nombres de los municipios a los datos del técnico
        $row['nombres_municipios'] = $municipios_nombres;
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