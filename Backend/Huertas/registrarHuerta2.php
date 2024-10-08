<?php
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

$id_huerta = $_POST['hue'];
$id_productor = $_POST['propietario'];
$nombre = $_POST['nomHuerta'];
$localidad = $_POST['municipios'];
$jl = $_POST['jl'];
$centroide = $_POST['centroide'];
$hectareas = $_POST['hectareas'];
$pronostico_de_cosecha = $_POST['proCosecha'];
$longitud = $_POST['longitud'];
$altitud = $_POST['altitud'];
$altura_nivel_del_mar = $_POST['altNivMar'];
$variedad = $_POST['variedad'];
$nomempresa = $_POST['nomEmpresa'];
$encargadoempresa = $_POST['encEmpresa'];
$supervisorhuerta = $_POST['supHue'];
$añoplantacion = $_POST['aPlantacion'];
$arbolesporhectareas = $_POST['arbolH'];
$totalarboles = $_POST['totalArboles'];
$etapafenologica = $_POST['etaFenologica'];
$fechasv_01 = $_POST['fechaSV1'];
$fechasv_02 = $_POST['fechaSV2'];
$fechaReg = $_POST['fechaReg'];

//$carpetaDestinoBase = "../kmls/";
$carpetaDestinoBase = $_SERVER['DOCUMENT_ROOT'] . "/proyectoApeajal/APEJAL/Assets/KMLS/";


$rutaKML = '';


try {
    $conexion = new DB_Connect();
    $conn = $conexion->connect();

    // Consulta para verificar si el id_huerta ya existe
    $sqlCheck = "SELECT COUNT(*) FROM huertas WHERE id_hue = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(1, $id_huerta, PDO::PARAM_STR); 
    $stmtCheck->execute();
    $huertaExistente = $stmtCheck->fetchColumn();

    if ($huertaExistente > 0) {
        echo json_encode(["error" => true, "messages" => ["El HUE '$id_huerta' ya está registrado."]]);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(["error" => true, "messages" => ["Error en la base de datos al verificar id_huerta: " . $e->getMessage()]]);
    exit();
}

if (isset($_FILES["file"])) {
    $nombreArchivo = $_FILES["file"]["name"];
    $origen = $_FILES["file"]["tmp_name"];

    // Crear la carpeta base KMLS si no existe
    if (!file_exists($carpetaDestinoBase)) {
        if (!mkdir($carpetaDestinoBase, 0777, true)) {
            echo json_encode(["error" => true, "messages" => ["Error al crear la carpeta base: $carpetaDestinoBase"]]);
            exit(); 
        }
    }

    // Crear la carpeta específica para la huerta si no existe
    $carpetaDestinoHuerta = $carpetaDestinoBase . $id_huerta . '/';
    if (!file_exists($carpetaDestinoHuerta)) {
        if (!mkdir($carpetaDestinoHuerta, 0777, true)) {
            echo json_encode(["error" => true, "messages" => ["Error al crear la carpeta de la huerta: $carpetaDestinoHuerta"]]);
            exit(); 
        }
    }

    // Establecer la zona horaria
    date_default_timezone_set('America/Mexico_City');

    // Obtener la fecha y hora actual
    $fecha = date("d-m-y"); // Formato: 20-09-24
    $hora = date("H-i");    // Formato: 19-10

    // Crear la carpeta con formato id_huerta_F-fecha_H-hora
    $carpetaDestinoFechaHora = $carpetaDestinoHuerta . $id_huerta . "_F-$fecha" . "_H-$hora/";
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
        $rutaKML = $destino;
    }
} else {
    echo json_encode(["error" => true, "messages" => ["No se ha recibido un archivo KML."]]);
    exit();
}


// Insertar los datos en la base de datos si no hay errores
$sql = "INSERT INTO huertas 
        (id_hue, id_productor, nombre, localidad, centroide, hectareas, pronostico_de_cosecha, longitud, altitud, altura_nivel_del_mar, variedad, nomempresa, encargadoempresa, supervisorhuerta, anoplantacion, arbolesporhectareas, totalarboles, etapafenologica, fechasv_01, fechasv_02, rutaKML, fechaRegistro, idjuntalocal)
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

try {
    $stmt_huertas = $conn->prepare($sql);
    $stmt_huertas->bindParam(1, $id_huerta, PDO::PARAM_STR); // Usar PDO::PARAM_STR para varchar
    $stmt_huertas->bindParam(2, $id_productor, PDO::PARAM_INT);
    $stmt_huertas->bindParam(3, $nombre, PDO::PARAM_STR);
    $stmt_huertas->bindParam(4, $localidad, PDO::PARAM_STR);
    $stmt_huertas->bindParam(5, $centroide, PDO::PARAM_STR);
    $stmt_huertas->bindParam(6, $hectareas, PDO::PARAM_INT);
    $stmt_huertas->bindParam(7, $pronostico_de_cosecha, PDO::PARAM_INT);
    $stmt_huertas->bindParam(8, $longitud, PDO::PARAM_STR);
    $stmt_huertas->bindParam(9, $altitud, PDO::PARAM_STR);
    $stmt_huertas->bindParam(10, $altura_nivel_del_mar, PDO::PARAM_STR);
    $stmt_huertas->bindParam(11, $variedad, PDO::PARAM_STR);
    $stmt_huertas->bindParam(12, $nomempresa, PDO::PARAM_STR);
    $stmt_huertas->bindParam(13, $encargadoempresa, PDO::PARAM_STR);
    $stmt_huertas->bindParam(14, $supervisorhuerta, PDO::PARAM_STR);
    $stmt_huertas->bindParam(15, $añoplantacion, PDO::PARAM_INT);
    $stmt_huertas->bindParam(16, $arbolesporhectareas, PDO::PARAM_INT);
    $stmt_huertas->bindParam(17, $totalarboles, PDO::PARAM_STR);
    $stmt_huertas->bindParam(18, $etapafenologica, PDO::PARAM_STR);
    $stmt_huertas->bindParam(19, $fechasv_01, PDO::PARAM_STR);
    $stmt_huertas->bindParam(20, $fechasv_02, PDO::PARAM_STR);
    $stmt_huertas->bindParam(21, $rutaKML, PDO::PARAM_STR);
    $stmt_huertas->bindParam(22, $fechaReg, PDO::PARAM_STR);
    $stmt_huertas->bindParam(23, $jl, PDO::PARAM_INT);

    $resultado = $stmt_huertas->execute();

    if ($resultado) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => true, "messages" => ["Error al insertar los datos en la base de datos: " . implode(", ", $stmt_huertas->errorInfo())]]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => true, "messages" => ["Error en la base de datos: " . $e->getMessage()]]);
}
?>
