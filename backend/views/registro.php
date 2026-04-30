<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Banco Digital</title>
</head>
<body>
    <h2>Abrir Cuenta Bancaria</h2>
    <?php if(isset($_GET['error'])) echo "<p style='color:red;'>".$_GET['error']."</p>"; ?>
    
    <form action="../controllers/UsuarioController.php?action=registrar" method="POST">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>
        
        <label>Apellido:</label><br>
        <input type="text" name="apellido" required><br><br>
        
        <label>Correo Electrónico:</label><br>
        <input type="email" name="email" required><br><br>
        
        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">Registrarme</button>
    </form>
    <br><a href="login.php">Ya tengo cuenta (Iniciar sesión)</a>
</body>
</html>