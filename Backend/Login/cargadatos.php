<?php
// Incluye la conexión a la base de datos
require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

session_start(); // Asegúrate de que la sesión esté iniciada

try {
    $db = new DB_Connect();
    $conexion = $db->connect();
    
    // Verifica que el ID de usuario esté configurado
    if (!isset($_SESSION['id'])) {
        echo json_encode(['error' => true, 'mensaje' => 'ID de usuario no está en la sesión']);
        exit();
    }

    $sql = "SELECT nombre, correo, teléfono FROM usuario WHERE id_usuario = :id_usuario";
    $query = $conexion->prepare($sql);
    $query->bindParam(':id_usuario', $_SESSION['id']);
    $query->execute();
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode(['error' => false, 'usuario' => $usuario]);
    } else {
        echo json_encode(['error' => true, 'mensaje' => 'Usuario no encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => true, 'mensaje' => 'Error en la consulta: ' . $e->getMessage()]);
}
?>
