<?php
require '../Database/connectividad.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $db = new DB_Connect();
    $conn = $db->connect();

    try {
        $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE correo = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(50));
            $expira = date("U") + 1800; // 30 minutos de expiración

            $stmt = $conn->prepare("INSERT INTO password_resets (correo, token, expira) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expira]);

            $url = "http://tu_dominio.com/restablecer_form.php?token=$token";
            $subject = 'Recuperar Contraseña';
            $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: $url";
            $headers = 'From: tu_email@example.com';

            mail($email, $subject, $message, $headers);

            echo "Se ha enviado un correo electrónico con las instrucciones para recuperar tu contraseña.";
        } else {
            echo "El correo electrónico no está registrado.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $conn = null;
}
?>
