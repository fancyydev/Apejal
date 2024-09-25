<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once($_SERVER['DOCUMENT_ROOT']."/proyectoApeajal/APEJAL/Backend/DataBase/connectividad.php");



// Obtener datos del formulario
$tipo = $_POST['tipo'] ?? '';
$id_productor = $_POST['id_productor'] ?? '';
$id_tecnico = $_POST['id_tecnico'] ?? '';
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
$jlT = (int) ($_POST['jlT'] ?? 0);
$municipiosSeleccionados = isset($_POST['municipio']) ? $_POST['municipio'] : [];
$municipio = implode(',', $municipiosSeleccionados);

// Conexión a la base de datos
$conexion = new DB_Connect();
$conn = $conexion->connect();

// Verificar la conexión a la base de datos
if ($conn->errorCode() !== "00000") {
    $errorInfo = $conn->errorInfo();
    die("Conexión fallida: " . implode(", ", $errorInfo));
}

// Actualizar tabla 'usuario'
$sql_usuario = "UPDATE usuario SET 
                    nombre = '$nombre', 
                    correo = '$correo', 
                    teléfono = '$telefono', 
                    contraseña = '$contra' 
                WHERE id_usuario = $id_usuario";
                
$resultado_usuario = $conn->exec($sql_usuario);

if ($resultado_usuario === false) {
    $errorInfo = $conn->errorInfo();
    die("Error al actualizar usuario: " . implode(", ", $errorInfo));
}

// Actualizar tabla 'productores' si el tipo es productor
if ($tipo == "productor") {
    $sql_productor = "UPDATE productor SET 
                        rfc = '$rfc', 
                        estatus = '$estatus', 
                        curp = '$curp', 
                        idjuntalocal = $juntalocal 
                    WHERE id_productor = $id_productor";
    
    $resultado_productor = $conn->exec($sql_productor);
    
    if ($resultado_productor === false) {
        $errorInfo = $conn->errorInfo();
        die("Error al actualizar productor: " . implode(", ", $errorInfo));
    }
} else if ($tipo == "tecnico") {
    $sql_tecnico = "UPDATE tecnico SET
                      idjuntaLocal = $jlT, 
                      carga_municipios = '$municipio', 
                      estatus = '$estatusT'
                    WHERE id_tecnico = $id_tecnico";
    
    $resultado_tecnico = $conn->exec($sql_tecnico);
    
    if ($resultado_tecnico === false) {
        $errorInfo = $conn->errorInfo();
        die("Error al actualizar tecnico " . implode(", ", $errorInfo));
    }
}

// Cerrar conexión
$stmt_tecnico = null;
$conn = null;

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode(["success" => true]);

?>