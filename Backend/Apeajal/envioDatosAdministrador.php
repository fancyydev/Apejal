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


if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Consulta a la base de datos
    $stmt = $conn->prepare("
        SELECT u.id_usuario, u.nombre, tu.id_tipo, tu.descripcion as nombre_tipo, u.correo, u.teléfono, u.contraseña
        FROM usuario u 
        JOIN tipousuario tu ON tu.id_tipo = u.id_tipo
        WHERE u.id_usuario = :id_usuario");
    
    
    // Ejecutar la consulta
    if ($stmt->execute(['id_usuario' => $id_usuario])) {
        $administrador = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($administrador) {
            // Devuelve los datos del productor en formato JSON
            echo json_encode($administrador);
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