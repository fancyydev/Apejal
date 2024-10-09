<?php
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

try {
    // Conexión a la base de datos con PDO
    $conexion = new DB_Connect();
    $conn = $conexion->connect();

    // Verifica si la conexión fue exitosa
    if (!$conn) {
        throw new Exception("Conexión fallida.");
    }

    // Obtiene los datos de la solicitud POST
    $id_hue = $_POST['id_hue'] ?? null;
    if ($id_hue === null) {
        echo json_encode(['message' => 'El ID de la huerta es requerido.']);
        exit();
    }

    // Otras variables
    $idjuntalocal = $_POST['idjuntalocal'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $localidad = $_POST['localidad'] ?? '';
    $centroide = $_POST['centroide'] ?? '';
    $hectareas = $_POST['hectareas'] ?? 0;
    $pronostico_de_cosecha = $_POST['pronostico_de_cosecha'] ?? '';
    $longitud = $_POST['longitud'] ?? '';
    $altitud = $_POST['altitud'] ?? '';
    $altura_nivel_del_mar = $_POST['altura_nivel_del_mar'] ?? '';
    $variedad = $_POST['variedad'] ?? '';
    $nomempresa = $_POST['nomempresa'] ?? '';
    $encargadoempresa = $_POST['encargadoempresa'] ?? '';
    $supervisorhuerta = $_POST['supervisorhuerta'] ?? '';
    $anoplantacion = $_POST['anoplantacion'] ?? '';
    $arbolesporhectareas = $_POST['arbolesporhectareas'] ?? '';
    $totalarboles = $_POST['totalarboles'] ?? '';
    $etapafenologica = $_POST['etapafenologica'] ?? '';
    $fechasv_01 = $_POST['fechasv_01'] ?? null;
    $fechasv_02 = $_POST['fechasv_02'] ?? null;
    $rutaKML = $_POST['rutaKML'] ?? ''; // Ruta KML actual

    // Si hay un archivo KML subido
    if (isset($_FILES["file"])) {
        $nombreArchivo = $_FILES["file"]["name"];
        $origen = $_FILES["file"]["tmp_name"];
    
        // Validar el tipo de archivo para asegurar que sea un KML
        $fileType = mime_content_type($origen);
        if ($fileType !== 'application/vnd.google-earth.kml+xml' && pathinfo($nombreArchivo, PATHINFO_EXTENSION) !== 'kml') {
            echo json_encode(['error' => true, 'message' => 'El archivo no es un KML válido.']);
            exit();
        }
    
        // Crear la carpeta base KMLS si no existe
        $carpetaDestinoBase = $_SERVER['DOCUMENT_ROOT'] . "/proyectoApeajal/APEJAL/Assets/KMLS/";
        if (!file_exists($carpetaDestinoBase)) {
            if (!mkdir($carpetaDestinoBase, 0777, true)) {
                throw new Exception("Error al crear la carpeta base.");
            }
        }
    
        // Crear la carpeta específica para la huerta si no existe
        $carpetaDestinoHuerta = $carpetaDestinoBase . $id_hue . '/';
        if (!file_exists($carpetaDestinoHuerta)) {
            if (!mkdir($carpetaDestinoHuerta, 0777, true)) {
                throw new Exception("Error al crear la carpeta de la huerta.");
            }
        }
    
        // Establecer la zona horaria
        date_default_timezone_set('America/Mexico_City');
    
        // Obtener la fecha y hora actual
        $fecha = date("d-m-y");
        $hora = date("H-i");
    
        // Crear la carpeta con formato id_hue_F-fecha_H-hora
        $carpetaDestinoFechaHora = $carpetaDestinoHuerta . $id_hue . "_F-$fecha" . "_H-$hora/";
        if (!file_exists($carpetaDestinoFechaHora)) {
            if (!mkdir($carpetaDestinoFechaHora, 0777, true)) {
                throw new Exception("Error al crear la carpeta con fecha y hora.");
            }
        }
    
        // Mover el archivo KML subido a la carpeta específica con fecha y hora
        $destino = $carpetaDestinoFechaHora . $nombreArchivo;
        if (!move_uploaded_file($origen, $destino)) {
            throw new Exception("Error al mover el archivo KML.");
        }
    
        // Actualiza la ruta KML
        $rutaKML = $destino;
    }
    
    // Actualizar los datos de la huerta en la base de datos
    $sql = "UPDATE huertas SET
        nombre = :nombre,
        idjuntalocal = :idjuntalocal,
        localidad = :localidad,
        centroide = :centroide,
        hectareas = :hectareas,
        pronostico_de_cosecha = :pronostico_de_cosecha,
        longitud = :longitud,
        altitud = :altitud,
        altura_nivel_del_mar = :altura_nivel_del_mar,
        variedad = :variedad,
        nomempresa = :nomempresa,
        encargadoempresa = :encargadoempresa,
        supervisorhuerta = :supervisorhuerta,
        anoplantacion = :anoplantacion,
        arbolesporhectareas = :arbolesporhectareas,
        totalarboles = :totalarboles,
        etapafenologica = :etapafenologica,
        fechasv_01 = :fechasv_01,
        fechasv_02 = :fechasv_02,
        rutaKML = :rutaKML
    WHERE id_hue = :id_hue";

    $stmt = $conn->prepare($sql);

    // Asignar valores
    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':idjuntalocal', $idjuntalocal);
    $stmt->bindValue(':localidad', $localidad);
    $stmt->bindValue(':centroide', $centroide);
    $stmt->bindValue(':hectareas', $hectareas);
    $stmt->bindValue(':pronostico_de_cosecha', $pronostico_de_cosecha);
    $stmt->bindValue(':longitud', $longitud);
    $stmt->bindValue(':altitud', $altitud);
    $stmt->bindValue(':altura_nivel_del_mar', $altura_nivel_del_mar);
    $stmt->bindValue(':variedad', $variedad);
    $stmt->bindValue(':nomempresa', $nomempresa);
    $stmt->bindValue(':encargadoempresa', $encargadoempresa);
    $stmt->bindValue(':supervisorhuerta', $supervisorhuerta);
    $stmt->bindValue(':anoplantacion', $anoplantacion);
    $stmt->bindValue(':arbolesporhectareas', $arbolesporhectareas);
    $stmt->bindValue(':totalarboles', $totalarboles);
    $stmt->bindValue(':etapafenologica', $etapafenologica);
    $stmt->bindValue(':fechasv_01', $fechasv_01);
    $stmt->bindValue(':fechasv_02', $fechasv_02);
    $stmt->bindValue(':rutaKML', $rutaKML);
    $stmt->bindValue(':id_hue', $id_hue);

    // Ejecutar la consulta
    $stmt->execute();
    echo json_encode(['message' => 'Huerta actualizada con éxito.']);

} catch (PDOException $e) {
    echo json_encode(['message' => 'Error al actualizar la huerta: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['message' => $e->getMessage()]);
} finally {
    $conn = null; // Cierra la conexión
}
?>
