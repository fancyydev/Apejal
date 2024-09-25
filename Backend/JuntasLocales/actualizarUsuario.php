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
    $sql_productor = "UPDATE productores SET 
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
    // Insertar en la tabla 'tecnico'
    $ultimo_id = $conn->lastInsertId(); // Último ID generado
    $sql_tecnico = "INSERT INTO tecnico (id_usuario, idjuntaLocal, carga_municipios, estatus) VALUES (?, ?, ?, ?)";
    $stmt_tecnico = $conn->prepare($sql_tecnico);
    
    if (!$stmt_tecnico) {
        $errorInfo = $conn->errorInfo();
        die("Error en la preparación de la consulta de técnico: " . implode(", ", $errorInfo));
    }
    
    $stmt_tecnico->bindParam(1, $ultimo_id, PDO::PARAM_INT);
    $stmt_tecnico->bindParam(2, $jlT, PDO::PARAM_INT);
    $stmt_tecnico->bindParam(3, $municipio, PDO::PARAM_STR);
    $stmt_tecnico->bindParam(4, $estatusT, PDO::PARAM_STR);

    $resultado_tecnico = $stmt_tecnico->execute();
    
    if (!$resultado_tecnico) {
        $errorInfo = $stmt_tecnico->errorInfo();
        die("Error al insertar técnico: " . implode(", ", $errorInfo));
    }
}

// Cerrar conexión
$stmt_tecnico = null;
$conn = null;

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode(["success" => true]);

?>