<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once '../../backend/config/database.php';
require_once '../../backend/models/Cuenta.php';

$database = new Database();
$db = $database->getConnection();

$cuentaObj = new Cuenta($db);
$mis_cuentas = $cuentaObj->obtenerCuentas($_SESSION['id_usuario']);

// DATOS DEL USUARIO
$queryUser = "SELECT nombre, apellido, email 
              FROM USUARIOS 
              WHERE id_usuario = :id_usuario";

$stmtUser = $db->prepare($queryUser);
$stmtUser->bindParam(':id_usuario', $_SESSION['id_usuario']);
$stmtUser->execute();

$datos_usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);
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

        <h4>
            Bienvenido,
            <span class="blue-text text-darken-3">
                <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?>
            </span>
        </h4>

        <!-- MENSAJES -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="card-panel green lighten-4 green-text text-darken-4">
                <i class="material-icons left">check_circle</i>
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="card-panel red lighten-4 red-text text-darken-4">
                <i class="material-icons left">error</i>
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="row">

            <!-- PERFIL -->
            <div class="col s12">
                <div class="card white perfil-card">
                    <div class="card-content">

                        <span class="card-title" style="font-weight: bold;">
                            <i class="material-icons left">person</i>
                            Mis Datos Personales
                        </span>

                        <form method="POST"
                            action="../../backend/controllers/UsuarioController.php?action=editarPerfil">

                            <div class="row">

                                <div class="input-field col s12 m4">
                                    <input type="text"
                                        name="nombre"
                                        id="nombre"
                                        value="<?php echo htmlspecialchars($datos_usuario['nombre']); ?>"
                                        required>

                                    <label for="nombre" class="active">Nombre</label>
                                </div>

                                <div class="input-field col s12 m4">
                                    <input type="text"
                                        name="apellido"
                                        id="apellido"
                                        value="<?php echo htmlspecialchars($datos_usuario['apellido']); ?>"
                                        required>

                                    <label for="apellido" class="active">Apellido</label>
                                </div>

                                <div class="input-field col s12 m4">
                                    <input type="email"
                                        name="email"
                                        id="email"
                                        value="<?php echo htmlspecialchars($datos_usuario['email']); ?>"
                                        required>

                                    <label for="email" class="active">
                                        Correo Electrónico
                                    </label>
                                </div>

                            </div>

                            <button class="btn teal darken-1 waves-effect waves-light"
                                type="submit">

                                <i class="material-icons left">save</i>
                                Guardar Cambios
                            </button>

                        </form>
                    </div>
                </div>
            </div>

            <!-- NUEVA CUENTA -->
            <div class="col s12 m4">
                <div class="card white">
                    <div class="card-content">

                        <span class="card-title" style="font-weight: bold;">
                            <i class="material-icons left">add_circle_outline</i>
                            Nueva Cuenta
                        </span>

                        <form method="POST"
                            action="../../backend/controllers/UsuarioController.php?action=crearCuenta">

                            <div class="input-field">
                                <select name="tipo" required>
                                    <option value="" disabled selected>
                                        Elige una opción
                                    </option>

                                    <option value="ahorro">Ahorro</option>
                                    <option value="corriente">Corriente</option>
                                </select>

                                <label>Tipo de cuenta</label>
                            </div>

                            <button class="btn blue darken-3 waves-effect waves-light col s12"
                                type="submit">

                                Crear
                            </button>

                        </form>
                    </div>
                </div>
            </div>

            <!-- MIS CUENTAS -->
            <div class="col s12 m8">
                <div class="card white account-card">
                    <div class="card-content">

                        <span class="card-title" style="font-weight: bold;">
                            <i class="material-icons left">account_balance_wallet</i>
                            Mis Cuentas
                        </span>
                        <table class="highlight responsive-table">

                            <thead>
                                <tr>
                                    <th>N° Cuenta</th>
                                    <th>Tipo</th>
                                    <th>Saldo Disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($mis_cuentas) > 0): ?>

                                    <?php foreach ($mis_cuentas as $cta): ?>

                                        <tr>
                                            <td>
                                                <strong>
                                                    <?php echo htmlspecialchars($cta['num_cuenta']); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="new badge blue darken-1"
                                                    data-badge-caption="">

                                                    <?php echo ucfirst(htmlspecialchars($cta['tipo'])); ?>

                                                </span>
                                            </td>
                                            <td class="green-text text-darken-2"
                                                style="font-size: 1.2rem; font-weight: bold;">

                                                $<?php echo number_format($cta['saldo'], 2); ?> MXN
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="3"
                                            class="center-align grey-text">

                                            No tienes cuentas activas.
                                        </td>
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
        document.addEventListener('DOMContentLoaded', function () {
            const elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);
        });
    </script>
</body>
</html>