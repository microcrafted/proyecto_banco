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
    <h3>Mis Cuentas Bancarias</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr style="background-color: #f2f2f2;">
            <th>Número de Cuenta</th>
            <th>Tipo</th>
            <th>Saldo Disponible</th>
        </tr>
        <?php if(count($mis_cuentas) > 0): ?>
            <?php foreach($mis_cuentas as $cta): ?>
            <tr>
                <td><?php echo htmlspecialchars($cta['num_cuenta']); ?></td>
                <td><?php echo ucfirst(htmlspecialchars($cta['tipo'])); ?></td>
                <td>$<?php echo number_format($cta['saldo'], 2); ?> MXN</td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No tienes cuentas activas.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>