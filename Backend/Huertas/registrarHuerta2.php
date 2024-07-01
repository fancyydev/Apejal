<?php
require_once($_SERVER['DOCUMENT_ROOT']."/APEJAL/Backend/DataBase/connectividad.php");

$id_huerta = $_POST['hue'];
$id_productor = $_POST['propietario'];
$id_juntalocal = $_POST['jl'];
$nombre = $_POST['nomHuerta'];
$localidad = $_POST['ciudad'];
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
$totalarboles = $_POST['totArboles'];
$etapafenologica = $_POST['etaFenologica'];
$fechasv_01 = $_POST['fechaSV1'];
$fechasv_02 = $_POST['fechaSV2'];

$carpetaDestinoBase = "../kmls/";
$rutaKML = '';

if (isset($_FILES["file"])) {
    $nombreArchivo = $_FILES["file"]["name"];
    $origen = $_FILES["file"]["tmp_name"];
    
    // Crear la carpeta base si no existe
    if (!file_exists($carpetaDestinoBase)) {
        mkdir($carpetaDestinoBase, 0777, true);
    }

    // Crear la carpeta específica para la huerta si no existe
    $carpetaDestinoHuerta = $carpetaDestinoBase . $nombre . '/';
    if (!file_exists($carpetaDestinoHuerta)) {
        mkdir($carpetaDestinoHuerta, 0777, true);
    }

    $destino = $carpetaDestinoHuerta . $nombreArchivo;
    if (move_uploaded_file($origen, $destino)) {
        $rutaKML = $destino;
        $sql = "INSERT INTO huertas 
        (id_hue, id_productor, id_juntalocal, nombre, localidad, centroide, hectareas, pronostico_de_cosecha, longitud, altitud, altura_nivel_del_mar, variedad, nomempresa, encargadoempresa, supervisorhuerta, añoplantacion, arbolesporhectareas, totalarboles, etapafenologica, fechasv_01, fechasv_02, rutaKML)
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


        $conexion = new DB_Connect();
        $conn = $conexion->connect();

        if ($conn->errorCode() !== "00000") {
            // Manejo del error de conexión aquí
            $errorInfo = $conn->errorInfo();
            die("Conexión fallida: " . implode(", ", $errorInfo));
        }

        $stmt = $conn->prepare($sql);

        // Ejemplo de preparación y ejecución de consulta para la tabla huertas
        $stmt_huertas = $conn->prepare($sql);
        $stmt_huertas->bindParam(1, $id_huerta, PDO::PARAM_STR);
        $stmt_huertas->bindParam(2, $id_productor, PDO::PARAM_INT);
        $stmt_huertas->bindParam(3, $id_juntalocal, PDO::PARAM_INT);
        $stmt_huertas->bindParam(4, $nombre, PDO::PARAM_STR);
        $stmt_huertas->bindParam(5, $localidad, PDO::PARAM_STR);
        $stmt_huertas->bindParam(6, $centroide, PDO::PARAM_STR);
        $stmt_huertas->bindParam(7, $hectareas, PDO::PARAM_INT);
        $stmt_huertas->bindParam(8, $pronostico_de_cosecha, PDO::PARAM_INT);
        $stmt_huertas->bindParam(9, $longitud, PDO::PARAM_STR);
        $stmt_huertas->bindParam(10, $altitud, PDO::PARAM_STR);
        $stmt_huertas->bindParam(11, $altura_nivel_del_mar, PDO::PARAM_STR);
        $stmt_huertas->bindParam(12, $variedad, PDO::PARAM_STR);
        $stmt_huertas->bindParam(13, $nomempresa, PDO::PARAM_STR);
        $stmt_huertas->bindParam(14, $encargadoempresa, PDO::PARAM_STR);
        $stmt_huertas->bindParam(15, $supervisorhuerta, PDO::PARAM_STR);
        $stmt_huertas->bindParam(16, $añoplantacion, PDO::PARAM_INT);
        $stmt_huertas->bindParam(17, $arbolesporhectareas, PDO::PARAM_INT);
        $stmt_huertas->bindParam(18, $totalarboles, PDO::PARAM_STR);
        $stmt_huertas->bindParam(19, $etapafenologica, PDO::PARAM_STR);
        $stmt_huertas->bindParam(20, $fechasv_01, PDO::PARAM_STR);
        $stmt_huertas->bindParam(21, $fechasv_02, PDO::PARAM_STR);
        $stmt_huertas->bindParam(22, $rutaKML, PDO::PARAM_STR);    
    
        $resultado = $stmt_huertas->execute();

        if ($resultado) {  

            $stmt = null;
            $conn = null;
        
            // Enviar respuesta JSON
            header('Content-Type: application/json');
            echo json_encode(["success" => true]);
        } else {
            die("Error al ejecutar la consulta de inserción: " . implode(", ", $stmt->errorInfo()));
            header('Content-Type: application/json');
            echo json_encode(["error" => true]);
        }
    }
}




?>


