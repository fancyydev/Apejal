<?php
header('Content-Type: application/json'); // Asegúrate de que el tipo de contenido es JSON

require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

session_start(); // Asegúrate de que se inicia la sesión
$id_usuario = $_SESSION["id"]; // Obtener el ID del usuario desde la sesión

// Verifica que el ID del usuario esté establecido
if (!isset($id_usuario)) {
    echo json_encode(['error' => 'ID de usuario no encontrado en la sesión']);
    exit();
}

// Crear conexión
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

// Consulta para obtener los datos del técnico filtrado por el ID del usuario
$sql = "SELECT t.id_tecnico, u.nombre AS nombre_usuario, u.correo, u.teléfono, t.carga_municipios, t.estatus, j.nombre AS nombre_junta
        FROM tecnico t
        JOIN juntaslocales j ON t.idjuntalocal = j.idjuntalocal
        JOIN usuario u ON j.id_usuario = u.id_usuario
        WHERE u.id_usuario = :id_usuario"; // Usamos un parámetro para la consulta

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT); // Vinculamos el parámetro

// Ejecutar la consulta
$stmt->execute();
$tecnicos = array();

if ($stmt !== false && $stmt->rowCount() > 0) {
    // Convertir los datos en un array asociativo
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // array para almacenar los nombres de los municipios
        $municipios_nombres = array();
        
        // Obtener los IDs de los municipios desde la consulta de arriba
        $ids_municipios = explode(',', $row['carga_municipios']);

        // Obtener los nombres de los municipios
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
    echo json_encode(['error' => 'No se encontraron técnicos para este usuario']);
    exit();
}

// Devolver datos en formato JSON
echo json_encode($tecnicos);

// Cerrar conexión
$conn = null;
?>
