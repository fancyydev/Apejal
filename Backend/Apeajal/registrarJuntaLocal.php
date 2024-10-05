<?php

require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

$nombre = $_POST['nombre'];
$domicilio = $_POST['domicilio'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$municipiosSeleccionados = isset($_POST['municipios']) ? $_POST['municipios'] : '';
$municipio = $municipiosSeleccionados;
$admin = $_POST['admin'];
$status = $_POST['status'];
//$carpetaDestinoBase = "../imagenesJuntaLocal/";
$carpetaDestinoBase = $_SERVER['DOCUMENT_ROOT'] . "/proyectoApeajal/APEJAL/Assets/Img/imagenesJuntasLocales/";

$rutaIMG = '';

try {
    $conexion = new DB_Connect();
    $conn = $conexion->connect();

} catch (PDOException $e) {
    echo json_encode(["error" => true, "messages" => ["Error en la base de datos : " . $e->getMessage()]]);
    exit();
}

if (isset($_FILES["file"])) {
    $nombreArchivo = $_FILES["file"]["name"];
    $origen = $_FILES["file"]["tmp_name"];

    // Crear la carpeta base  si no existe
    if (!file_exists($carpetaDestinoBase)) {
        if (!mkdir($carpetaDestinoBase, 0777, true)) {
            echo json_encode(["error" => true, "messages" => ["Error al crear la carpeta base: $carpetaDestinoBase"]]);
            exit(); 
        }
    }

    // Crear la carpeta específica para la huerta si no existe
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

    // Mover el archivo KML a la carpeta destino
    $destino = $carpetaDestinoFechaHora . $nombreArchivo;
    if (!move_uploaded_file($origen, $destino)) {
        echo json_encode(["error" => true, "messages" => ["Error al mover el archivo KML a: $destino"]]);
        exit(); 
    } else {
        $rutaIMG = $destino;
    }
} else {
    echo json_encode(["error" => true, "messages" => ["No se ha recibido un archivo KML."]]);
    exit();
}

// Preparar la consulta SQL
$sql = "INSERT INTO juntaslocales (carga_municipios, id_usuario, nombre, domicilio, teléfono, correo, estatus, ruta_img) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";


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
    
    $resultado = $stmt->execute();

    if ($resultado) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => true, "messages" => ["Error al insertar los datos en la base de datos: " . implode(", ", $stmt_huertas->errorInfo())]]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => true, "messages" => ["Error en la base de datos: " . $e->getMessage()]]);
}

// Cerrar consulta y conexión
$stmt->null;
$conn->null;

?>
