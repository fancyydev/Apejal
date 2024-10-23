<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

$idjuntalocal = $_POST['idjuntalocal'];
$nombre = $_POST['nombre'];
$domicilio = $_POST['domicilio'];
$telefono = $_POST['telefono'];
$rutaDefault = $_POST['archivo'];
$correo = $_POST['correo'];
$municipiosSeleccionados = isset($_POST['municipios']) ? $_POST['municipios'] : '';
$municipio = $municipiosSeleccionados;
$admin = $_POST['admin'];
$status = $_POST['status'];
$editarFoto = isset($_POST['editarFoto']) ? $_POST['editarFoto'] : 'no';
//$carpetaDestinoBase = "../imagenesJuntaLocal/";
$carpetaDestinoBase = $_SERVER['DOCUMENT_ROOT'] . "/Apejal/Assets/Img/imagenesJuntasLocales/";

$rutaIMG = $rutaDefault;

try {
    $conexion = new DB_Connect();
    $conn = $conexion->connect();
} catch (PDOException $e) {
    echo json_encode(["error" => true, "messages" => ["Error en la base de datos : " . $e->getMessage()]]);
    exit();
}

// Si se debe editar la foto
if ($editarFoto === 'si' && isset($_FILES["file"])) {
    $nombreArchivo = $_FILES["file"]["name"];
    $origen = $_FILES["file"]["tmp_name"];

    // Crear la carpeta base si no existe
    if (!file_exists($carpetaDestinoBase)) {
        if (!mkdir($carpetaDestinoBase, 0777, true)) {
            echo json_encode(["error" => true, "messages" => ["Error al crear la carpeta base: $carpetaDestinoBase"]]);
            exit(); 
        }
    }

    // Crear la carpeta específica para la junta local si no existe
    $carpetaDestinoIMG = $carpetaDestinoBase . $nombre . '/';
    if (!file_exists($carpetaDestinoIMG)) {
        if (!mkdir($carpetaDestinoIMG, 0777, true)) {
            echo json_encode(["error" => true, "messages" => ["Error al crear la carpeta de la imagen: $carpetaDestinoIMG"]]);
            exit(); 
        }
    }

    // Establecer la zona horaria
    date_default_timezone_set('America/Mexico_City');

    // Obtener la fecha y hora actual
    $fecha = date("d-m-y"); // Formato: 20-09-24
    $hora = date("H-i");    // Formato: 19-10

    // Crear la carpeta con formato nombre_F-fecha_H-hora
    $carpetaDestinoFechaHora = $carpetaDestinoIMG . $nombre . "_F-$fecha" . "_H-$hora/";
    if (!file_exists($carpetaDestinoFechaHora)) {
        if (!mkdir($carpetaDestinoFechaHora, 0777, true)) {
            echo json_encode(["error" => true, "messages" => ["Error al crear la carpeta con fecha y hora: $carpetaDestinoFechaHora"]]);
            exit();
        }
    }

    // Mover la imagen a la carpeta destino
    $destino = $carpetaDestinoFechaHora . $nombreArchivo;
    if (!move_uploaded_file($origen, $destino)) {
        echo json_encode(["error" => true, "messages" => ["Error al mover el archivo KML a: $destino"]]);
        exit(); 
    } else {
        $rutaIMG = $destino;
    }
}

// Preparar la consulta SQL para actualizar los datos
$sql = "UPDATE juntaslocales 
        SET carga_municipios = ?, id_usuario = ?, nombre = ?, domicilio = ?, teléfono = ?, correo = ?, estatus = ?, ruta_img = ? 
        WHERE idjuntalocal = ?";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $municipio, PDO::PARAM_STR);
    $stmt->bindParam(2, $admin, PDO::PARAM_INT);
    $stmt->bindParam(3, $nombre, PDO::PARAM_STR);
    $stmt->bindParam(4, $domicilio, PDO::PARAM_STR);
    $stmt->bindParam(5, $telefono, PDO::PARAM_STR);
    $stmt->bindParam(6, $correo, PDO::PARAM_STR);
    $stmt->bindParam(7, $status, PDO::PARAM_STR);
    $stmt->bindParam(8, $rutaIMG, PDO::PARAM_STR);
    $stmt->bindParam(9, $idjuntalocal, PDO::PARAM_INT);
    
    $resultado = $stmt->execute();

    if ($resultado) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => true, "messages" => ["Error al actualizar los datos en la base de datos: " . implode(", ", $stmt->errorInfo())]]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => true, "messages" => ["Error en la base de datos: " . $e->getMessage()]]);
}

// Cerrar consulta y conexión
$stmt = null;
$conn = null;

?>
