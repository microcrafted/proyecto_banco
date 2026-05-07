<?php
// frontend/pages/login.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Búho Bank</title>
    <link rel="stylesheet" href="../css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { background-color: #f4f6f8; display: flex; min-height: 100vh; flex-direction: column; justify-content: center; }
        .login-card { padding: 30px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border-top: 4px solid #1565c0; }
        .brand-logo-custom { color: #1565c0; font-weight: bold; text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row" style="margin-bottom: 0;">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                
                <?php if(isset($_GET['msg'])): ?>
                    <div class="card-panel green lighten-4 green-text text-darken-4 center-align" style="border-radius: 4px; padding: 10px;">
                        <i class="material-icons tiny">check_circle</i> <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php endif; ?>
                <?php if(isset($_GET['error'])): ?>
                    <div class="card-panel red lighten-4 red-text text-darken-4 center-align" style="border-radius: 4px; padding: 10px;">
                        <i class="material-icons tiny">error</i> <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="card white login-card">
                    <div class="card-content">
                        <h4 class="brand-logo-custom"><i class="material-icons prefix">account_balance</i> Búho Bank</h4>
                        <p class="center-align grey-text" style="margin-bottom: 25px;">Ingresa a tu banca en línea</p>
                        
                        <form action="../../backend/controllers/UsuarioController.php?action=login" method="POST">
                            <div class="input-field">
                                <i class="material-icons prefix">email</i>
                                <input id="email" type="email" name="email" required>
                                <label for="email">Correo Electrónico</label>
                            </div>

                            <div class="input-field">
                                <i class="material-icons prefix">lock</i>
                                <input id="password" type="password" name="password" required>
                                <label for="password">Contraseña</label>
                            </div>

                            <button class="btn waves-effect waves-light blue darken-3 col s12" type="submit" style="margin-top: 20px; border-radius: 4px; height: 45px; font-weight: bold;">
                                Iniciar Sesión <i class="material-icons right">login</i>
                            </button>
                        </form>
                    </div>
                    <div class="card-action center-align" style="border-top: none; padding-top: 0;">
                        <a href="index.html" class="blue-text text-darken-3">¿No tienes cuenta? Regístrate aquí</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="../js/materialize.min.js"></script>
</body>
</html>