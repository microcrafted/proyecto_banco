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

//REGISTRO (Frontend con Fetch API)
//leemos datos JSON que envía el javascript
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
            //en lugar de redireccionar, respondemos con JSON
            echo json_encode(["ok" => true, "mensaje" => "Registro exitoso. Se generó tu cuenta: " . $num_cta]);
        } else {
            echo json_encode(["ok" => false, "mensaje" => "Registro exitoso, pero hubo un error al generar la cuenta bancaria."]);
        }
    } else {
        echo json_encode(["ok" => false, "mensaje" => "El correo ya está registrado."]);
    }
    exit;
}

//LOGIN
if ($action == 'login' && $_POST) {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $datos = $usuario->login($email, $password);
    
    if ($datos) {
        $_SESSION['id_usuario'] = $datos['id_usuario'];
        $_SESSION['nombre_completo'] = $datos['nombre'] . ' ' . $datos['apellido'];
        header("Location: ../views/dashboard.php");
    } else {
        header("Location: ../views/login.php?error=Credenciales invalidas o usuario inactivo.");
    }
    exit;
}

//LOGOUT
if ($action == 'logout') {
    session_destroy();
    header("Location: ../views/login.php");
    exit;
}
?>