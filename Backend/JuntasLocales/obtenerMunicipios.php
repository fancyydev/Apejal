<?php

//ESTO NO ES RECOMENDABLE POR SEGURIDAD
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// obtenerMunicipios.php
$servername = "localhost";
$username = "root"; // Cambia esto si usas otro nombre de usuario
$password = ""; // Cambia esto si tienes una contraseña
$dbname = "materiaseca_apeajal";
$port = 3306; // Puerto de MySQL en XAMPP

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_municipio, nombre FROM municipio";
$result = $conn->query($sql);

$municipios = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $municipios[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($municipios);
?>
