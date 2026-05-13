<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once '../../backend/config/database.php';
require_once '../../backend/models/Cuenta.php';
require_once '../../backend/models/Usuario.php'; // <-- 1. Agregamos el modelo Usuario

$database = new Database();
$db = $database->getConnection();

$cuentaObj = new Cuenta($db);
$usuarioObj = new Usuario($db); // <-- 2. Instanciamos al usuario

$mis_cuentas = $cuentaObj->obtenerCuentas($_SESSION['id_usuario']);
$datos_usuario = $usuarioObj->obtenerDatosPerfil($_SESSION['id_usuario']); // <-- 3. Magia MVC
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
        body {
            background-color: #f4f6f8;
        }

        .brand-logo {
            padding-left: 20px !important;
            font-weight: bold;
        }

        .account-card {
            border-top: 4px solid #1565c0;
        }

        .perfil-card {
            border-top: 4px solid #00897b;
        }

        .action-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .action-form input {
            margin-bottom: 0 !important;
            height: 2rem !important;
        }
    </style>
</head>

<body>

    <nav class="blue darken-3">
        <div class="nav-wrapper">
            <a href="#" class="brand-logo">
                <i class="material-icons">account_balance</i>
                Búho Bank
            </a>

            <ul class="right hide-on-med-and-down">
                <li>
                    <a href="../../backend/controllers/UsuarioController.php?action=logout"
                        class="waves-effect waves-light btn red darken-1">

                        <i class="material-icons left">exit_to_app</i>
                        Cerrar Sesión
                    </a>
                </li>
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
            <div class="col s12">
                <div class="card white perfil-card" style="border-top: 4px solid #00897b;">
                    <div class="card-content">
                        <span class="card-title" style="font-weight: bold;"><i class="material-icons left">person</i> Mis Datos Personales</span>
                        <form method="POST" action="../../backend/controllers/UsuarioController.php?action=editarPerfil">
                            <div class="row" style="margin-bottom: 0;">
                                <div class="input-field col s12 m4">
                                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($datos_usuario['nombre']); ?>" required>
                                    <label for="nombre" class="active">Nombre</label>
                                </div>
                                <div class="input-field col s12 m4">
                                    <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($datos_usuario['apellido']); ?>" required>
                                    <label for="apellido" class="active">Apellido</label>
                                </div>
                                <div class="input-field col s12 m4">
                                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($datos_usuario['email']); ?>" required>
                                    <label for="email" class="active">Correo Electrónico</label>
                                </div>
                            </div>
                            <button class="btn teal darken-1 waves-effect waves-light" type="submit">
                                <i class="material-icons left">save</i> Guardar Cambios
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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
                <div class="card white account-card" style="border-top: 4px solid #1565c0;">
                    <div class="card-content">
                        <span class="card-title" style="font-weight: bold;"><i class="material-icons left">account_balance_wallet</i> Mis Cuentas</span>
                        <table class="highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>N° de Cuenta</th>
                                    <th>Tipo</th>
                                    <th>Saldo Disponible</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($mis_cuentas) > 0): ?>
                                    <?php foreach($mis_cuentas as $cta): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($cta['num_cuenta']); ?></strong></td>
                                        <td><span class="new badge blue darken-1" data-badge-caption=""><?php echo ucfirst(htmlspecialchars($cta['tipo'])); ?></span></td>
                                        <td class="green-text text-darken-2" style="font-size: 1.2rem; font-weight: bold;">$<?php echo number_format($cta['saldo'], 2); ?> MXN</td>
                                        <td style="display: flex; gap: 10px;">
                                            <a href="historial.php?id_cuenta=<?php echo $cta['id_cuenta']; ?>" class="btn-small blue darken-2 waves-effect waves-light" title="Ver Historial">
                                                <i class="material-icons">history</i>
                                            </a>

                                            <form method="POST" action="../../backend/controllers/UsuarioController.php?action=cerrarCuenta" onsubmit="return confirm('HU10/HU15: ¿Estás seguro de que deseas CERRAR esta cuenta definitivamente?');">
                                                <input type="hidden" name="id_cuenta" value="<?php echo $cta['id_cuenta']; ?>">
                                                <button class="btn-small red darken-2 waves-effect waves-light" type="submit" title="Cerrar Cuenta"><i class="material-icons">cancel</i></button>
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
        <h5 style="margin-top: 30px; font-weight: bold; color: #1565c0;">Operaciones Rápidas</h5>
        <div class="row">
            
            <div class="col s12 m4">
                <div class="card white" style="border-top: 4px solid #43a047;">
                    <div class="card-content">
                        <span class="card-title" style="font-size: 1.2rem; font-weight: bold;"><i class="material-icons left green-text">arrow_downward</i> Depositar</span>
                        <form method="POST" action="../../backend/controllers/UsuarioController.php?action=depositar" onsubmit="return confirm('¿Confirmas el depósito?');">
                            <div class="input-field">
                                <select name="id_cuenta" required>
                                    <option value="" disabled selected>Selecciona tu cuenta</option>
                                    <?php foreach($mis_cuentas as $cta): ?>
                                        <option value="<?php echo $cta['id_cuenta']; ?>"><?php echo $cta['num_cuenta'] . ' - $' . number_format($cta['saldo'], 2); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Cuenta Destino</label>
                            </div>
                            <div class="input-field">
                                <input type="number" name="monto" id="monto_dep" step="0.01" min="0.01" required>
                                <label for="monto_dep">Monto a depositar</label>
                            </div>
                            <div class="row" style="margin-bottom: 0;">
                                <div class="col s6">
                                    <button class="btn-flat red-text waves-effect waves-red col s12" type="reset" onclick="M.toast({html: 'Depósito cancelado', classes: 'red rounded'})">Cancelar</button>
                                </div>
                                <div class="col s6">
                                    <button class="btn green darken-1 waves-effect waves-light col s12" type="submit" style="padding: 0;">Depositar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="card white" style="border-top: 4px solid #fb8c00;">
                    <div class="card-content">
                        <span class="card-title" style="font-size: 1.2rem; font-weight: bold;"><i class="material-icons left orange-text">arrow_upward</i> Retirar</span>
                        <form method="POST" action="../../backend/controllers/UsuarioController.php?action=retirar" onsubmit="return confirm('¿Confirmas el retiro?');">
                            <div class="input-field">
                                <select name="id_cuenta" required>
                                    <option value="" disabled selected>Selecciona tu cuenta</option>
                                    <?php foreach($mis_cuentas as $cta): ?>
                                        <option value="<?php echo $cta['id_cuenta']; ?>"><?php echo $cta['num_cuenta'] . ' - $' . number_format($cta['saldo'], 2); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Cuenta Origen</label>
                            </div>
                            <div class="input-field">
                                <input type="number" name="monto" id="monto_ret" step="0.01" min="0.01" required>
                                <label for="monto_ret">Monto a retirar</label>
                            </div>
                            <div class="row" style="margin-bottom: 0;">
                                <div class="col s6">
                                    <button class="btn-flat red-text waves-effect waves-red col s12" type="reset" onclick="M.toast({html: 'Retiro cancelado', classes: 'red rounded'})">Cancelar</button>
                                </div>
                                <div class="col s6">
                                    <button class="btn orange darken-1 waves-effect waves-light col s12" type="submit" style="padding: 0;">Retirar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="card white" style="border-top: 4px solid #1e88e5;">
                    <div class="card-content">
                        <span class="card-title" style="font-size: 1.2rem; font-weight: bold;"><i class="material-icons left blue-text">swap_horiz</i> Transferir</span>
                        <form method="POST" action="../../backend/controllers/UsuarioController.php?action=transferir" onsubmit="return confirm('¿Confirmas la transferencia?');">
                            <div class="input-field">
                                <select name="id_cuenta_origen" required>
                                    <option value="" disabled selected>Cuenta a retirar</option>
                                    <?php foreach($mis_cuentas as $cta): ?>
                                        <option value="<?php echo $cta['id_cuenta']; ?>"><?php echo $cta['num_cuenta'] . ' - $' . number_format($cta['saldo'], 2); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Cuenta Origen</label>
                            </div>
                            <div class="input-field">
                                <input type="text" name="num_cuenta_destino" id="destino_transf" required>
                                <label for="destino_transf">N° Cuenta Destino</label>
                            </div>
                            <div class="input-field">
                                <input type="number" name="monto" id="monto_transf" step="0.01" min="0.01" required>
                                <label for="monto_transf">Monto a transferir</label>
                            </div>
                            <div class="row" style="margin-bottom: 0;">
                                <div class="col s6">
                                    <button class="btn-flat red-text waves-effect waves-red col s12" type="reset" onclick="M.toast({html: 'Transferencia cancelada', classes: 'red rounded'})">Cancelar</button>
                                </div>
                                <div class="col s6">
                                    <button class="btn blue darken-1 waves-effect waves-light col s12" type="submit" style="padding: 0;">Transferir</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/materialize.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);
        });
    </script>
</body>
</html>