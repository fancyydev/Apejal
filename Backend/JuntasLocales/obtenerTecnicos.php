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


// Consulta para obtener los datos del técnico, usuario, y junta local
$sql = "SELECT t.id_tecnico, u.nombre AS nombre_usuario, u.correo, u.teléfono,t.carga_municipios,t.estatus, j.nombre AS nombre_junta
        FROM tecnico t
        JOIN usuario u ON t.id_usuario = u.id_usuario
        JOIN juntaslocales j ON t.idjuntalocal = j.idjuntalocal";

$result = $conn->query($sql);

$tecnicos = array();

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


        $tecnicos[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Devolver datos en formato JSON
echo json_encode($tecnicos);

// Cerrar conexión
$conn->close();
?>
