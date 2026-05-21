<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo - Búho Bank</title>
    <link rel="stylesheet" href="../css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style> body { background-color: #212121; } </style>
</head>
<body class="valign-wrapper" style="height: 100vh;">
    <div class="container">
        <div class="row">
            <div class="col s12 m6 offset-m3">
                <div class="card grey darken-3 white-text">
                    <div class="card-content">
                        <span class="card-title center-align"><i class="material-icons large">security</i><br>Acceso Restringido</span>
                        <p class="center-align grey-text text-lighten-1">Portal exclusivo para personal de Búho Bank</p>
                        
                        <?php if(isset($_GET['error'])): ?>
                            <div class="card-panel red darken-4 white-text center-align" style="padding: 10px;"><?php echo htmlspecialchars($_GET['error']); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="../../backend/controllers/AdminController.php?action=login" style="margin-top: 20px;">
                            <div class="input-field">
                                <i class="material-icons prefix white-text">email</i>
                                <input id="email" type="email" name="email" class="white-text" required>
                                <label for="email" class="white-text">Correo Corporativo</label>
                            </div>
                            <div class="input-field">
                                <i class="material-icons prefix white-text">lock</i>
                                <input id="password" type="password" name="password" class="white-text" required>
                                <label for="password" class="white-text">Contraseña</label>
                            </div>
                            <button class="btn blue darken-2 waves-effect waves-light col s12" type="submit">Iniciar Sesión</button>
                        </form>
                        <div class="center-align" style="margin-top: 25px;">
                            <a href="login.php" class="btn-flat grey-text text-lighten-1 waves-effect">
                                <i class="material-icons left">arrow_back</i>Volver al Portal de Clientes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/materialize.min.js"></script>
</body>
</html>
