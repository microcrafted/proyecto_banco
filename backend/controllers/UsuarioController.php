<?php
session_start();

require_once '../config/database.php';
require_once '../models/Usuario.php';
require_once '../models/Cuenta.php';

$database = new Database();
$db = $database->getConnection();

$usuario = new Usuario($db);
$cuenta = new Cuenta($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';


// REGISTRO FETCH API
$data = json_decode(file_get_contents("php://input"), true);

if ($action == 'registrar' && $data) {

    $nombre = htmlspecialchars(trim($data['nombre']));
    $apellido = htmlspecialchars(trim($data['apellido']));
    $email = htmlspecialchars(trim($data['email']));
    $password = $data['password'];

    $nuevo_id = $usuario->registrar($nombre, $apellido, $email, $password);

    if ($nuevo_id) {

        $respuesta_cuenta = $cuenta->crearNuevaCuenta($nuevo_id, 'ahorro');

        if ($respuesta_cuenta['status'] === 'success') {

            $num_cta = $respuesta_cuenta['data']['numero_cuenta'];

            echo json_encode([
                "ok" => true,
                "mensaje" => "Registro exitoso. Se generó tu cuenta: " . $num_cta
            ]);

        } else {

            echo json_encode([
                "ok" => false,
                "mensaje" => "Error al generar la cuenta."
            ]);
        }

    } else {

        echo json_encode([
            "ok" => false,
            "mensaje" => "El correo ya está registrado."
        ]);
    }

    exit;
}

// LOGIN (Con HU24: Bloqueo de seguridad)
if ($action == 'login' && $_POST) {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $respuesta = $usuario->login($email, $password);

    if ($respuesta['status'] === 'success') {
        $datos = $respuesta['data'];
        
        $_SESSION['id_usuario'] = $datos['id_usuario'];
        $_SESSION['nombre_completo'] = $datos['nombre'] . ' ' . $datos['apellido'];

        header("Location: ../../frontend/pages/dashboard.php");
    } else {
        header("Location: ../../frontend/pages/login.php?error=" . urlencode($respuesta['mensaje']));
    }
    exit;
}

// HU5: Recuperar contraseña
if ($action == 'recuperar' && $_POST) {
    $email = htmlspecialchars(trim($_POST['email']));

    $respuesta = $usuario->recuperarPassword($email);

    if ($respuesta['status'] === 'success') {
        $mensaje = "Éxito. Tu contraseña temporal es: " . $respuesta['temporal'];
        header("Location: ../../frontend/pages/recuperar.php?msg=" . urlencode($mensaje));
    } else {
        header("Location: ../../frontend/pages/recuperar.php?error=" . urlencode($respuesta['mensaje']));
    }
    exit;
}

// LOGOUT
if ($action == 'logout') {

    session_destroy();

    header("Location: ../../frontend/pages/login.php");

    exit;
}

// CREAR CUENTA
if ($action == 'crearCuenta' && $_POST) {

    $tipo = $_POST['tipo'];

    $id_usuario = $_SESSION['id_usuario'];

    $respuesta = $cuenta->crearNuevaCuenta($id_usuario, $tipo);

    if ($respuesta['status'] === 'success') {

        header("Location: ../../frontend/pages/dashboard.php?msg=Cuenta creada correctamente");

    } else {

        header("Location: ../../frontend/pages/dashboard.php?error=" . urlencode($respuesta['mensaje']));
    }

    exit;
}


// CERRAR CUENTA
if ($action == 'cerrarCuenta' && $_POST) {

    $id_cuenta = $_POST['id_cuenta'];

    $id_usuario = $_SESSION['id_usuario'];

    $respuesta = $cuenta->cerrarCuenta($id_cuenta, $id_usuario);

    if ($respuesta['status'] === 'success') {

        header("Location: ../../frontend/pages/dashboard.php?msg=Cuenta cerrada correctamente");

    } else {

        header("Location: ../../frontend/pages/dashboard.php?error=" . urlencode($respuesta['mensaje']));
    }

    exit;
}


// EDITAR PERFIL
if ($action == 'editarPerfil' && $_POST) {

    $id_usuario = $_SESSION['id_usuario'];

    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellido = htmlspecialchars(trim($_POST['apellido']));
    $email = htmlspecialchars(trim($_POST['email']));

    // Validaciones
    if(empty($nombre) || empty($apellido) || empty($email)) {

        header("Location: ../../frontend/pages/dashboard.php?error=Todos los campos son obligatorios.");
        exit;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        header("Location: ../../frontend/pages/dashboard.php?error=Correo inválido.");
        exit;
    }

    $respuesta = $usuario->actualizarPerfil(
        $id_usuario,
        $nombre,
        $apellido,  
        $email
    );

    if($respuesta['status'] === 'success') {

        $_SESSION['nombre_completo'] = $nombre . ' ' . $apellido;

        header("Location: ../../frontend/pages/dashboard.php?msg=" . urlencode($respuesta['mensaje']));

    } else {

        header("Location: ../../frontend/pages/dashboard.php?error=" . urlencode($respuesta['mensaje']));
    }

    exit;
}

// HU11 Depositar
if ($action == 'depositar' && $_POST) {
    $id_cuenta = $_POST['id_cuenta'];
    $monto = floatval($_POST['monto']);
    
    $respuesta = $cuenta->depositar($id_cuenta, $monto);
    
    if ($respuesta['status'] === 'success') {
        header("Location: ../../frontend/pages/dashboard.php?msg=" . urlencode($respuesta['mensaje']));
    } else {
        header("Location: ../../frontend/pages/dashboard.php?error=" . urlencode($respuesta['mensaje']));
    }
    exit;
}

// HU12 Retirar
if ($action == 'retirar' && $_POST) {
    $id_cuenta = $_POST['id_cuenta'];
    $monto = floatval($_POST['monto']);
    
    $respuesta = $cuenta->retirar($id_cuenta, $monto);
    
    if ($respuesta['status'] === 'success') {
        header("Location: ../../frontend/pages/dashboard.php?msg=" . urlencode($respuesta['mensaje']));
    } else {
        header("Location: ../../frontend/pages/dashboard.php?error=" . urlencode($respuesta['mensaje']));
    }
    exit;
}

// HU13 Transferir
if ($action == 'transferir' && $_POST) {
    $id_cuenta_origen = $_POST['id_cuenta_origen'];
    $num_cuenta_destino = htmlspecialchars(trim($_POST['num_cuenta_destino']));
    $monto = floatval($_POST['monto']);
    
    $respuesta = $cuenta->transferir($id_cuenta_origen, $num_cuenta_destino, $monto);
    
    if ($respuesta['status'] === 'success') {
        header("Location: ../../frontend/pages/dashboard.php?msg=" . urlencode($respuesta['mensaje']));
    } else {
        header("Location: ../../frontend/pages/dashboard.php?error=" . urlencode($respuesta['mensaje']));
    }
    exit;
}

// HU20 Exportar Historial a CSV (Excel)
if ($action == 'exportarHistorial' && isset($_GET['id_cuenta'])) {
    
    $id_cuenta = $_GET['id_cuenta'];
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

    $historial = $cuenta->obtenerHistorialFiltrado($id_cuenta, $tipo, $fecha_inicio, $fecha_fin);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Buhobank_Historial_Cuenta_' . $id_cuenta . '.csv');
    $output = fopen('php://output', 'w');
    
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, array('Fecha y Hora', 'Operación', 'Detalle', 'Monto'));

    if(count($historial) > 0) {
        foreach ($historial as $mov) {
            $es_ingreso = ($mov['id_cuenta_destino'] == $id_cuenta || $mov['tipo_operacion'] == 'deposito');
            $signo = $es_ingreso ? '+' : '-';
            
            fputcsv($output, array(
                date('d/m/Y H:i', strtotime($mov['fecha'])),
                ucfirst($mov['tipo_operacion']),
                'Transacción #' . $mov['id_transaccion'],
                $signo . '$' . number_format($mov['monto'], 2)
            ));
        }
    } else {
        fputcsv($output, array('No hay movimientos en este periodo.'));
    }
    fclose($output);
    exit;
}
?>