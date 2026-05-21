<?php
session_start();
if(!isset($_SESSION['id_admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once '../../backend/config/database.php';
require_once '../../backend/models/Admin.php';

$database = new Database();
$db = $database->getConnection();
$adminObj = new Admin($db);

$usuarios = $adminObj->obtenerUsuarios();
$stats = $adminObj->obtenerEstadisticas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Búho Bank</title>
    <link rel="stylesheet" href="../css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style> body { background-color: #212121; color: white; } .card { color: black; } </style>
</head>
<body>

    <nav class="grey darken-4">
        <div class="nav-wrapper container">
            <a href="#" class="brand-logo"><i class="material-icons">security</i> Admin Panel</a>
            <ul id="nav-mobile" class="right">
                <li><a href="../../backend/controllers/AdminController.php?action=logout" class="waves-effect waves-light btn red"><i class="material-icons left">exit_to_app</i>Salir</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 30px;">
        <div class="row">
            <div class="col s12 m3"><div class="card blue white-text center-align"><div class="card-content"><h5><?php echo $stats['total_usuarios']; ?></h5><p>Usuarios</p></div></div></div>
            <div class="col s12 m3"><div class="card green white-text center-align"><div class="card-content"><h5><?php echo $stats['total_cuentas']; ?></h5><p>Cuentas</p></div></div></div>
            <div class="col s12 m3"><div class="card orange white-text center-align"><div class="card-content"><h5>$<?php echo number_format($stats['capital_total'], 2); ?></h5><p>Capital Total</p></div></div></div>
            <div class="col s12 m3"><div class="card purple white-text center-align"><div class="card-content"><h5><?php echo $stats['total_transacciones']; ?></h5><p>Transacciones</p></div></div></div>
        </div>

        <div class="card white">
            <div class="card-content">
                <span class="card-title">Gestión de Clientes</span>
                <table class="striped responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Nombre</th><th>Email</th><th>Cuentas</th><th>Intentos Fallidos</th><th>Estado (HU26)</th><th>Bloqueo Seguridad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td><?php echo $u['id_usuario']; ?></td>
                            <td><?php echo $u['nombre'] . ' ' . $u['apellido']; ?></td>
                            <td><?php echo $u['email']; ?></td>
                            <td><?php echo $u['total_cuentas']; ?></td>
                            <td><?php echo $u['intentos_fallidos']; ?> / 3</td>
                            
                            <td>
                                <?php if($u['activo'] == 1): ?>
                                    <a href="../../backend/controllers/AdminController.php?action=toggleEstado&id=<?php echo $u['id_usuario']; ?>&campo=activo&valor=0" class="btn-small green waves-effect" title="Desactivar Cuenta">Activo</a>
                                <?php else: ?>
                                    <a href="../../backend/controllers/AdminController.php?action=toggleEstado&id=<?php echo $u['id_usuario']; ?>&campo=activo&valor=1" class="btn-small grey waves-effect" title="Activar Cuenta">Inactivo</a>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if($u['bloqueado'] == 1): ?>
                                    <a href="../../backend/controllers/AdminController.php?action=toggleEstado&id=<?php echo $u['id_usuario']; ?>&campo=bloqueado&valor=0" class="btn-small red waves-effect" title="Clic para Desbloquear"><i class="material-icons left">lock</i>Bloqueado</a>
                                <?php else: ?>
                                    <a href="../../backend/controllers/AdminController.php?action=toggleEstado&id=<?php echo $u['id_usuario']; ?>&campo=bloqueado&valor=1" class="btn-small green waves-effect" title="Clic para Bloquear"><i class="material-icons left">lock_open</i>Normal</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="../js/materialize.min.js"></script>
</body>
</html>