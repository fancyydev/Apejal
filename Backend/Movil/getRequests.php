<?php
    require_once('./connectividad.php');
    //require_once('../DataBase/connectividad.php');
    $conexion = new DB_Connect();
    $conn = $conexion->connect();

    // Verificar la conexión a la base de datos
    if ($conn->errorCode() !== "00000") {
        // Manejo del error de conexión aquí
        $errorInfo = $conn->errorInfo();
        die("Conexión fallida: " . implode(", ", $errorInfo));
    }

    $id = $_GET['id_solicitud'];

    $request = [];
    $sql = "SELECT * FROM solicitudes WHERE id_tecnico = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($id));
    
    while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
        array_push($request, $row);
    }

    $huertas = [];
    foreach($request as $value){
        $hue = $value['id_hue'];
        $sql = "SELECT * FROM huertas WHERE id_hue = ?";
        $stm = $conn->prepare($sql);
        $stm->execute(array($hue));
        $huerta = $stm->fetch(PDO::FETCH_ASSOC);
        if(!(in_array($huerta, $huertas))){
            array_push($huertas, $huerta);
        }
    }

    echo json_encode(
        array(
            "solicitudes" => $request,
            "huertas" => $huertas
        )
    )

?>