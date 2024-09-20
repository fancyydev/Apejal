<?php
require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");

// Obtener datos del formulario
$tipo = $_POST['tipo'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$correo = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$contra = $_POST['contra'] ?? '';
$rfc = $_POST['rfc'] ?? '';
$curp = $_POST['curp'] ?? '';
$jl = $_POST['jl'] ?? '';
$estatus = $_POST["status"] ?? '';
$estatusT = $_POST["statusT"] ?? '';
$jlT = $_POST['jlT'] ?? '';
$municipiosSeleccionados = isset($_POST['municipio']) ? $_POST['municipio'] : [];
$municipio = implode(',', $municipiosSeleccionados);

// Validar campos obligatorios
$errores = [];
if (empty($nombre)) {
    $errores[] = "El nombre es obligatorio.";
}
if (empty($tipo)) {
    $errores[] = "El tipo de usuario es obligatorio.";
}
if (empty($correo)) {
    $errores[] = "El correo es obligatorio.";
}
if (empty($telefono)) {
    $errores[] = "El teléfono es obligatorio.";
}
if (empty($contra)) {
    $errores[] = "La contraseña es obligatoria.";
}

// Si hay errores, devolver respuesta JSON
if (!empty($errores)) {
    header('Content-Type: application/json', true, 400);
    echo json_encode(["success" => false, "errors" => $errores]);
    exit; // Terminar el script
}

// Insertar en tabla usuario
$sql_usuario = "INSERT INTO usuario (id_tipo, nombre, correo, teléfono, contraseña)
                VALUES (?, ?, ?, ?, ?)";

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


// Conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    // Manejo del error de conexión aquí
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}


$stmt = $conn->prepare($sql_usuario);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . implode(", ", $conn->errorInfo()));
}

$stmt->bindParam(1, $id_tipo, PDO::PARAM_INT);
$stmt->bindParam(2, $nombre, PDO::PARAM_STR);
$stmt->bindParam(3, $correo, PDO::PARAM_STR);
$stmt->bindParam(4, $telefono, PDO::PARAM_STR);
$stmt->bindParam(5, $contra, PDO::PARAM_STR);

$resultado = $stmt->execute();

if ($resultado) {    
    // Si el usuario es productor, insertar en tabla productores
    if ($tipo == "productor") {
        //Ultimo id es el que se generá automaticamente con el auto increment en la tabla usurio
        //Esto con la finalidad de que cuando se vaya a la tabla productor ese id que se genero se asocie con id_usuario en la tabla
        $ultimo_id = $conn->lastInsertId();

        $sql_productor = "INSERT INTO productores (id_usuario, rfc, estatus, curp, idjuntalocal) VALUES (?, ?, ?, ?, ?)";
        $stmt_productor = $conn->prepare($sql_productor);
        if (!$stmt_productor) {
            die("Error en la preparación de la consulta de productor: " . implode(", ", $conn->errorInfo()));
        }
        
        $stmt_productor->bindParam(1, $ultimo_id, PDO::PARAM_INT);
        $stmt_productor->bindParam(2, $rfc, PDO::PARAM_STR);
        $stmt_productor->bindParam(3, $estatus, PDO::PARAM_STR);
        $stmt_productor->bindParam(4, $curp, PDO::PARAM_STR);
        $stmt_productor->bindParam(5, $jl, PDO::PARAM_INT);
        
        $resultado_productor = $stmt_productor->execute();
    } else if($tipo == "tecnico") {
        //Ultimo id es el que se generá automaticamente con el auto increment en la tabla usurio
        //Esto con la finalidad de que cuando se vaya a la tabla tecnico ese id que se genero se asocie con id_usuario en la tabla
        $ultimo_id = $conn->lastInsertId();
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
