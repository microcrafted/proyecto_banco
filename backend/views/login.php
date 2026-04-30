<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Banco Digital</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if(isset($_GET['msg'])) echo "<p style='color:green;'>".$_GET['msg']."</p>"; ?>
    <?php if(isset($_GET['error'])) echo "<p style='color:red;'>".$_GET['error']."</p>"; ?>

    <form action="../controllers/UsuarioController.php?action=login" method="POST">
        <label>Correo Electrónico:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Entrar</button>
    </form>
    <br><a href="registro.php">No tengo cuenta (Registrarme)</a>
</body>
</html>