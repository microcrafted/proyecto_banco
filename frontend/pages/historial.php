<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id_cuenta'])) {
    header("Location: dashboard.php");
    exit;
}

$id_cuenta = $_GET['id_cuenta'];

require_once '../../backend/config/database.php';
require_once '../../backend/models/Cuenta.php';

$database = new Database();
$db = $database->getConnection();
$cuentaObj = new Cuenta($db);

// Atrapamos los filtros que vienen por GET (si existen)
$tipo_filtro = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Llamamos a la nueva función filtrada
$historial = $cuentaObj->obtenerHistorialFiltrado($id_cuenta, $tipo_filtro, $fecha_inicio, $fecha_fin);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Movimientos - Búho Bank</title>
    <link rel="stylesheet" href="../css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { background-color: #f4f6f8; }
        .history-card { border-top: 4px solid #1565c0; margin-top: 30px;}
        .deposito { color: #43a047; font-weight: bold; }
        .retiro { color: #e53935; font-weight: bold; }
    </style>
</head>
<body>

    <nav class="blue darken-3">
        <div class="nav-wrapper container">
            <a href="dashboard.php" class="brand-logo"><i class="material-icons">account_balance</i> Búho Bank</a>
            <ul id="nav-mobile" class="right">
                <li><a href="dashboard.php" class="waves-effect waves-light btn blue lighten-1"><i class="material-icons left">arrow_back</i>Volver al Dashboard</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <div class="card white history-card">
                    <div class="card-content">
                        <span class="card-title" style="font-weight: bold; margin-bottom: 20px;"><i class="material-icons left">receipt</i> Historial de Movimientos</span>
                        
                        <form method="GET" action="historial.php" class="row valign-wrapper" style="margin-bottom: 20px; background-color: #f4f6f8; padding: 15px; border-radius: 8px;">
                            <input type="hidden" name="id_cuenta" value="<?php echo htmlspecialchars($id_cuenta); ?>">
                            
                            <div class="input-field col s12 m3">
                                <select name="tipo">
                                    <option value="todos" <?php echo ($tipo_filtro == 'todos') ? 'selected' : ''; ?>>Todas las operaciones</option>
                                    <option value="deposito" <?php echo ($tipo_filtro == 'deposito') ? 'selected' : ''; ?>>Depósitos</option>
                                    <option value="retiro" <?php echo ($tipo_filtro == 'retiro') ? 'selected' : ''; ?>>Retiros</option>
                                    <option value="transferencia" <?php echo ($tipo_filtro == 'transferencia') ? 'selected' : ''; ?>>Transferencias</option>
                                </select>
                                <label>Tipo de Operación</label>
                            </div>
                            
                            <div class="input-field col s12 m2">
                                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                                <label for="fecha_inicio" class="active">Desde</label>
                            </div>
                            
                            <div class="input-field col s12 m2">
                                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
                                <label for="fecha_fin" class="active">Hasta</label>
                            </div>
                            
                            <div class="col s12 m2 center-align">
                                <button class="btn blue darken-2 waves-effect waves-light" type="submit" style="width: 100%;">
                                    <i class="material-icons">search</i>
                                </button>
                            </div>

                            <div class="col s12 m3 center-align">
                                <a href="../../backend/controllers/UsuarioController.php?action=exportarHistorial&id_cuenta=<?php echo $id_cuenta; ?>&tipo=<?php echo $tipo_filtro; ?>&fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>" class="btn green darken-2 waves-effect waves-light" style="width: 100%;" title="Exportar a Excel">
                                    <i class="material-icons left">file_download</i> Excel
                                </a>
                            </div>
                        </form>
                        
                        <table class="striped responsive-table" style="margin-top: 20px;">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Operación</th>
                                    <th>Detalle (Origen/Destino)</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($historial) > 0): ?>
                                    <?php foreach($historial as $mov): 
                                        // Lógica para saber si es ingreso o egreso de esta cuenta
                                        $es_ingreso = ($mov['id_cuenta_destino'] == $id_cuenta || $mov['tipo_operacion'] == 'deposito');
                                        $clase_color = $es_ingreso ? 'deposito' : 'retiro';
                                        $signo = $es_ingreso ? '+' : '-';
                                    ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha'])); ?></td>
                                        <td><span class="chip"><?php echo ucfirst($mov['tipo_operacion']); ?></span></td>
                                        <td class="grey-text">
                                            Transacción #<?php echo $mov['id_transaccion']; ?>
                                        </td>
                                        <td class="<?php echo $clase_color; ?>">
                                            <?php echo $signo; ?> $<?php echo number_format($mov['monto'], 2); ?> MXN
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="center-align grey-text">No hay movimientos que coincidan con tu búsqueda.</td>
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
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);
        });
    </script>
</body>
</html>u