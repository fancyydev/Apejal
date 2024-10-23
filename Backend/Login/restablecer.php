<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Apejal/Backend/DataBase/connectividad.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $db = new DB_Connect();
    $conn = $db->connect();

    try {
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expira >= ?");
        $expira = date("U");
        $stmt->execute([$token, $expira]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $email = $row['email'];

            $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
            $stmt->execute([$new_password, $email]);

            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);

            echo "Tu contraseña ha sido actualizada.";
        } else {
            echo "El enlace ha expirado o es inválido.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $conn = null;
}
?>
