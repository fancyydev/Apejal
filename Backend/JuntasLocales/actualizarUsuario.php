<?php
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

// Obtener datos del formulario
$tipo = $_POST['tipo'] ?? '';
$id_productor = $_POST['id_productor'] ?? '';
$id_usuario = $_POST['id_usuario'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$contra = $_POST['contra'] ?? '';
$rfc = $_POST['rfc'] ?? '';
$curp = $_POST['curp'] ?? '';
$juntalocal = (int) ($_POST['jl'] ?? 0);
$estatus = $_POST["status"] ?? '';
$estatusT = $_POST["statusT"] ?? '';
$jlT = $_POST['jlT'] ?? '';
$municipiosSeleccionados = isset($_POST['municipio']) ? $_POST['municipio'] : [];
$municipio = implode(',', $municipiosSeleccionados);


$archivo = 'datos_formulario.txt';

// Abrir el archivo para escribir, si no existe, se creará
$archivoTxt = fopen($archivo, 'a'); // 'a' para agregar datos sin borrar lo anterior




// Si hay errores, devolver respuesta JSON
if (!empty($errores)) {
    header('Content-Type: application/json', true, 400);
    echo json_encode(["success" => false, "errors" => $errores]);
    exit; // Terminar el script
}

// Establecer id_tipo basado en el tipo de usuario seleccionado
$id_tipo = 0;
switch ($tipo) {
    case "tecnico":
        $id_tipo = 2;
        break;
    case "personalLab":
        $id_tipo = 3;
        break;
    case "productor":
        $id_tipo = 1;
        break;
    case "adminJL":
        $id_tipo = 4;
        break;
    default:
        die("Tipo de usuario no válido");
}

// Preparar los datos para escribir en el archivo
$contenido = "tipo: $id_tipo\n" .
             "id usuario: $id_usuario\n" .
             "id productor: $id_productor\n" .
             "nombre: $nombre\n" .
             "correo: $correo\n" .
             "telefono: $telefono\n" .
             "contraseña: $contra\n" .
             "estatus: $estatus\n" .
             "junta local: $juntalocal\n" .
             "rfc: $rfc\n" .
             "curp: $curp\nn";

// Escribir los datos en el archivo
fwrite($archivoTxt, $contenido);

// Cerrar el archivo
fclose($archivoTxt);

// Conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}


$sql_usuario = "UPDATE usuario SET 
                    nombre = '$nombre', 
                    correo = '$correo', 
                    teléfono = '$telefono', 
                    contraseña = '$contra' 
                WHERE id_usuario = $id_usuario";

$resultado = $conn->exec($sql_usuario);

if ($resultado) {    
    // Si el usuario es productor, insertar en tabla productores
    if ($tipo == "productor") {
        $sql_productor = "UPDATE productores SET 
                            rfc = '$rfc', 
                            estatus = '$estatus', 
                            curp = '$curp', 
                            idjuntalocal = $juntalocal 
                        WHERE id_productor = $id_productor";
        $resultado_productor = $conn->exec($sql_productor);
        if (!$resultado_productor) {
            die("Error en la preparación de la consulta de productor: " . implode(", ", $conn->errorInfo()));
        }
        
    } else if($tipo == "tecnico") {
        //Ultimo id es el que se generá automaticamente con el auto increment en la tabla usurio
        //Esto con la finalidad de que cuando se vaya a la tabla tecnico ese id que se genero se asocie con id_usuario en la tabla
        $ultimo_id = $conn->lastInsertId();
        $sql_tecnico = "INSERT INTO tecnico (id_usuario, idjuntaLocal, carga_municipios, estatus) VALUES (?, ?, ?, ?)";
        $stmt_tecnico = $conn->prepare($sql_tecnico);
        if (!$stmt_tecnico) {
            die("Error en la preparación de la consulta de productor: " . implode(", ", $conn->errorInfo()));
        }
        $stmt_tecnico->bindParam(1, $ultimo_id, PDO::PARAM_INT);
        $stmt_tecnico->bindParam(2, $jlT, PDO::PARAM_INT);
        $stmt_tecnico->bindParam(3, $municipio, PDO::PARAM_STR);
        $stmt_tecnico->bindParam(4, $estatusT, PDO::PARAM_STR);

        $resultado_tecnico = $stmt_tecnico->execute();
    }

    // Cerrar statement y conexión
    $stmt = null;
    $conn = null;

    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al ejecutar la consulta: " . implode(", ", $errorInfo)]);
    die("Error al ejecutar la consulta de inserción: " . implode(", ", $stmt->errorInfo()));
}
?>