<!DOCTYPE html>
<html>
<head>
    <title>Restablecer Contraseña</title>
</head>
<body>
    <h2>Restablecer Contraseña</h2>
    <form action="restablecer.php" method="post">
        <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
        <label for="password">Nueva Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Restablecer</button>
    </form>
</body>
</html>
