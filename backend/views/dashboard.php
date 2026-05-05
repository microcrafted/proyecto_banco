<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';
require_once '../models/Cuenta.php';

$database = new Database();
$db = $database->getConnection();
$cuentaObj = new Cuenta($db);
$mis_cuentas = $cuentaObj->obtenerCuentas($_SESSION['id_usuario']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Dashboard</title>
</head>
<body>

<h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></h1>
<a href="../controllers/UsuarioController.php?action=logout">Cerrar Sesión</a>

<hr>

<?php if(isset($_GET['msg'])): ?>
    <p style="color: green;"><?php echo htmlspecialchars($_GET['msg']); ?></p>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php endif; ?>
// CREAR CUENTA (HU9) s
<h3>Crear nueva cuenta</h3>
<form method="POST" action="../controllers/UsuarioController.php?action=crearCuenta">
    <label>Tipo de cuenta:</label>
    <select name="tipo" required>
        <option value="ahorro">Ahorro</option>
        <option value="corriente">Corriente</option>
    </select>
    <button type="submit">Crear cuenta</button>
</form>

<hr>

// TABLA DE CUENTAS 
<h3>Mis Cuentas Bancarias</h3>
<table border="1" cellpadding="10" cellspacing="0">
    <tr style="background-color: #f2f2f2;">
        <th>Número de Cuenta</th>
        <th>Tipo</th>
        <th>Saldo Disponible</th>
        <th>Acciones</th>
    </tr>

    <?php if(count($mis_cuentas) > 0): ?>
        <?php foreach($mis_cuentas as $cta): ?>
        <tr>
            <td><?php echo htmlspecialchars($cta['num_cuenta']); ?></td>
            <td><?php echo ucfirst(htmlspecialchars($cta['tipo'])); ?></td>
            <td>$<?php echo number_format($cta['saldo'], 2); ?> MXN</td>

            <!-- 🔴 BOTÓN CERRAR CUENTA (HU10) -->
            <td>
                <form method="POST" action="../controllers/UsuarioController.php?action=cerrarCuenta" style="display:inline;">
                    <input type="hidden" name="id_cuenta" value="<?php echo $cta['id_cuenta']; ?>">
                    <button type="submit">Cerrar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">No tienes cuentas activas.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>