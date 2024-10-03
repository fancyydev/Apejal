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


if (isset($_GET['id_laboratorio'])) {
    $id_laboratorio = $_GET['id_laboratorio'];

    $archivo = 'datos_formulario.txt';

// Abrir el archivo para escribir, si no existe, se creará
$archivoTxt = fopen($archivo, 'a'); // 'a' para agregar datos sin borrar lo anterior

// Preparar los datos para escribir en el archivo
$contenido = "ID Laboratorio: $id_laboratorio\n";

// Escribir los datos en el archivo
fwrite($archivoTxt, $contenido);

// Cerrar el archivo
fclose($archivoTxt);


    // Consulta a la base de datos
    $stmt = $conn->prepare("
        SELECT u.id_usuario,u.id_tipo, u.nombre, u.correo, u.teléfono, u.contraseña, l.estatus, l.idjuntalocal, jl.nombre AS nombre_junta  
        FROM laboratorio l
        JOIN usuario u ON l.id_usuario = u.id_usuario 
        LEFT JOIN juntaslocales jl ON l.idjuntalocal = jl.idjuntalocal
        WHERE l.id_laboratorio = :id_laboratorio
    ");
    
    
    // Ejecutar la consulta
    if ($stmt->execute(['id_laboratorio' => $id_laboratorio])) {
        $laboratorio = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($laboratorio) {
            // Devuelve los datos del productor en formato JSON
            echo json_encode($laboratorio);
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