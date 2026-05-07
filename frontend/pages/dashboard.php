<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once '../../backend/config/database.php';
require_once '../../backend/models/Cuenta.php';

$database = new Database();
$db = $database->getConnection();
$cuentaObj = new Cuenta($db);
$mis_cuentas = $cuentaObj->obtenerCuentas($_SESSION['id_usuario']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Búho Bank</title>
    <link rel="stylesheet" href="../css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { background-color: #f4f6f8; }
        .brand-logo { padding-left: 20px !important; font-weight: bold; }
        .account-card { border-top: 4px solid #1565c0; }
        /* Flexbox para que los mini-formularios se vean ordenados */
        .action-form { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .action-form input { margin-bottom: 0 !important; height: 2rem !important; }
    </style>
</head>
<body>

    <nav class="blue darken-3">
        <div class="nav-wrapper">
            <a href="#" class="brand-logo"><i class="material-icons">account_balance</i> Búho Bank</a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <li><a href="../../backend/controllers/UsuarioController.php?action=logout" class="waves-effect waves-light btn red darken-1"><i class="material-icons left">exit_to_app</i>Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 30px;">
        
        <h4>Bienvenido, <span class="blue-text text-darken-3"><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></span></h4>

        <?php if(isset($_GET['msg'])): ?>
            <div class="card-panel green lighten-4 green-text text-darken-4" style="padding: 10px; border-radius: 4px;">
                <i class="material-icons left">check_circle</i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="card-panel red lighten-4 red-text text-darken-4" style="padding: 10px; border-radius: 4px;">
                <i class="material-icons left">error</i> <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col s12 m4">
                <div class="card white">
                    <div class="card-content">
                        <span class="card-title" style="font-weight: bold;"><i class="material-icons left">add_circle_outline</i> Nueva Cuenta</span>
                        <form method="POST" action="../../backend/controllers/UsuarioController.php?action=crearCuenta">
                            <div class="input-field">
                                <select name="tipo" required>
                                    <option value="" disabled selected>Elige una opción</option>
                                    <option value="ahorro">Ahorro</option>
                                    <option value="corriente">Corriente</option>
                                </select>
                                <label>Tipo de cuenta</label>
                            </div>
                            <button class="btn blue darken-3 waves-effect waves-light col s12" type="submit" style="border-radius: 4px;">Crear</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col s12 m8">
                <div class="card white account-card">
                    <div class="card-content">
                        <span class="card-title" style="font-weight: bold;"><i class="material-icons left">account_balance_wallet</i> Mis Cuentas</span>
                        <table class="highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>N° de Cuenta</th>
                                    <th>Tipo</th>
                                    <th>Saldo Disponible</th>
                                    <th>Operaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($mis_cuentas) > 0): ?>
                                    <?php foreach($mis_cuentas as $cta): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($cta['num_cuenta']); ?></strong></td>
                                        <td><span class="new badge blue darken-1" data-badge-caption=""><?php echo ucfirst(htmlspecialchars($cta['tipo'])); ?></span></td>
                                        <td class="green-text text-darken-2" style="font-size: 1.2rem; font-weight: bold;">$<?php echo number_format($cta['saldo'], 2); ?> MXN</td>
                                        
                                        <td>
                                            <form method="POST" action="../../backend/controllers/UsuarioController.php?action=depositar" class="action-form" onsubmit="return confirm('HU15: ¿Confirmas el depósito en esta cuenta?');">
                                                <input type="hidden" name="id_cuenta" value="<?php echo $cta['id_cuenta']; ?>">
                                                <input type="number" name="monto" placeholder="Monto" step="0.01" min="0.01" required style="width: 90px;">
                                                <button class="btn-small green darken-1 waves-effect waves-light" type="submit" title="Depositar"><i class="material-icons">add</i></button>
                                            </form>

                                            <form method="POST" action="../../backend/controllers/UsuarioController.php?action=retirar" class="action-form" onsubmit="return confirm('HU15: ¿Confirmas el retiro de esta cuenta?');">
                                                <input type="hidden" name="id_cuenta" value="<?php echo $cta['id_cuenta']; ?>">
                                                <input type="number" name="monto" placeholder="Monto" step="0.01" min="0.01" required style="width: 90px;">
                                                <button class="btn-small orange darken-2 waves-effect waves-light" type="submit" title="Retirar"><i class="material-icons">remove</i></button>
                                            </form>

                                            <form method="POST" action="../../backend/controllers/UsuarioController.php?action=transferir" class="action-form" onsubmit="return confirm('HU15: ¿Confirmas la transferencia a la cuenta destino?');">
                                                <input type="hidden" name="id_cuenta_origen" value="<?php echo $cta['id_cuenta']; ?>">
                                                <input type="text" name="num_cuenta_destino" placeholder="N° Destino" required style="width: 110px;">
                                                <input type="number" name="monto" placeholder="Monto" step="0.01" min="0.01" required style="width: 80px;">
                                                <button class="btn-small blue waves-effect waves-light" type="submit" title="Transferir"><i class="material-icons">swap_horiz</i></button>
                                            </form>

                                            <form method="POST" action="../../backend/controllers/UsuarioController.php?action=cerrarCuenta" style="margin-top: 15px;" onsubmit="return confirm('HU10/HU15: ¿Estás seguro de que deseas CERRAR esta cuenta definitivamente?');">
                                                <input type="hidden" name="id_cuenta" value="<?php echo $cta['id_cuenta']; ?>">
                                                <button class="btn-small red darken-2 waves-effect waves-light" type="submit" style="width: 100%;"><i class="material-icons left">cancel</i>Cerrar Cuenta</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="center-align grey-text">No tienes cuentas activas. ¡Crea una para empezar!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/materialize.min.js"></script>
    <script>
        // Materialize requiere inicializar los elementos <select> con JS para que se vean bonitos
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);
        });
    </script>
</body>
</html>