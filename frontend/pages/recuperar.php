<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Búho Bank</title>
    <link rel="stylesheet" href="../css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style> body { background-color: #f4f6f8; } </style>
</head>
<body class="valign-wrapper" style="height: 100vh;">
    <div class="container">
        <div class="row">
            <div class="col s12 m6 offset-m3">
                <div class="card white">
                    <div class="card-content">
                        <span class="card-title center-align"><i class="material-icons large blue-text">lock_reset</i><br>Recuperar Contraseña</span>
                        <p class="center-align grey-text" style="margin-bottom: 20px;">Ingresa tu correo registrado y generaremos una contraseña temporal segura.</p>

                        <?php if(isset($_GET['error'])): ?>
                            <div class="card-panel red white-text center-align" style="padding: 10px;"><?php echo htmlspecialchars($_GET['error']); ?></div>
                        <?php endif; ?>
                        
                        <?php if(isset($_GET['msg'])): ?>
                            <div class="card-panel green darken-1 white-text center-align" style="padding: 15px; font-weight: bold; font-size: 16px;">
                                <?php echo htmlspecialchars($_GET['msg']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="../../backend/controllers/UsuarioController.php?action=recuperar">
                            <div class="input-field">
                                <i class="material-icons prefix">email</i>
                                <input id="email" type="email" name="email" required>
                                <label for="email">Correo Electrónico</label>
                            </div>
                            <button class="btn blue darken-2 waves-effect waves-light col s12" type="submit" style="margin-top: 10px;">Generar Nueva Contraseña</button>
                        </form>

                        <div class="center-align" style="margin-top: 30px;">
                            <a href="login.php" class="grey-text"><i class="material-icons left" style="font-size: 16px;">arrow_back</i> Volver al inicio de sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/materialize.min.js"></script>
</body>
</html>