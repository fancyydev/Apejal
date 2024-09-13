<?php
header('Content-Type: application/json'); // Asegúrate de que el tipo de contenido es JSON

// Configuración de la base de datos
$servername = "localhost";
$username = "root"; // Ajusta esto según tu configuración
$password = ""; // Ajusta esto según tu configuración
$dbname = "materiaseca_apeajal";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

// Consulta para obtener los datos de la huerta, productor y junta local
$sql = "SELECT h.id_hue, u.nombre AS nombre_productor, j.nombre AS nombre_junta_local, h.nombre AS nombre_huerta, h.localidad, h.centroide, 
               h.hectareas, h.pronostico_de_cosecha, h.longitud, h.altitud, h.altura_nivel_del_mar, 
               h.variedad, h.nomempresa, h.encargadoempresa, h.supervisorhuerta, h.añoplantacion, 
               h.arbolesporhectareas, h.totalarboles, h.etapafenologica, h.fechasv_01, h.fechasv_02, 
               h.rutaKML, h.fechaRegistro 
        FROM huertas h
        JOIN productores p ON h.id_productor = p.id_productor
        JOIN usuario u ON p.id_usuario = u.id_usuario
        JOIN juntaslocales j ON h.id_juntalocal = j.idjuntalocal";

$result = $conn->query($sql);

$huertas = array();

if ($result->num_rows > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $result->fetch_assoc()) {
        $huertas[] = $row;
    }
} else {
    echo json_encode(['error' => 'No se encontraron datos']);
    exit();
}

// Devolver datos en formato JSON
echo json_encode($huertas);

// Cerrar conexión
$conn->close();
?>
