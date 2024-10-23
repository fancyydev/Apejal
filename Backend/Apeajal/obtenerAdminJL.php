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

// Obtener el ID del administrador desde los parámetros GET
$idAdminActual = isset($_GET['idAdminActual']) ? $_GET['idAdminActual'] : null;

// Modificar la consulta SQL para incluir al administrador seleccionado
$sql = "SELECT id_usuario, nombre FROM usuario WHERE id_tipo = 4 AND (id_usuario NOT IN (SELECT id_usuario FROM juntaslocales) OR id_usuario = :idAdminActual)";
$stmt = $conn->prepare($sql);

// Vincular el parámetro solo si se ha proporcionado un ID de administrador
if ($idAdminActual) {
    $stmt->bindParam(':idAdminActual', $idAdminActual, PDO::PARAM_INT);
}

// Ejecutar la consulta
$stmt->execute();

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
