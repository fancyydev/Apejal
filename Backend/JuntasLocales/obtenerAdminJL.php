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

$sql = "SELECT id_usuario, nombre FROM usuario WHERE id_tipo = 4";
$stmt = $conn->query($sql);

// Verificar si la consulta fue exitosa
if ($stmt !== false) {
    $options = array();
    // Verificar si hay resultados
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $options[] = array(
                'id_usuario' => $row['id_usuario'],
                'nombre' => $row['nombre']
            );
        }
    } else {
        $options[] = array('id_usuario' => '', 'nombre' => 'No hay administradores disponibles');
    }
} else {
    // Manejar error en la consulta SQL
    die("Error en la consulta: " . implode(", ", $conn->errorInfo()));
}

// Cerrar la conexión
$conn = null;
$stmt = null;

// Devolver las opciones en formato JSON
echo json_encode($options);
?>
