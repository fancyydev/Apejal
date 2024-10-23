<?php
include 'connectividad.php'; // Asegúrate de que la ruta sea correcta

$db = new DB_Connect();
$connection = $db->connect(); // Intenta conectar

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Conexión a la Base de Datos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Prueba de Conexión a la Base de Datos</h1>
    <?php if ($connection): ?>
        <p class="success">¡Conexión exitosa a la base de datos!</p>
    <?php else: ?>
        <p class="error">¡Error al conectar a la base de datos!</p>
    <?php endif; ?>
</body>
</html>
