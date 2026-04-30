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

// REGISTRO
if ($action == 'registrar' && $_POST) {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellido = htmlspecialchars(trim($_POST['apellido']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $nuevo_id = $usuario->registrar($nombre, $apellido, $email, $password);
    
    if ($nuevo_id) {
        $respuesta_cuenta = $cuenta->crearNuevaCuenta($nuevo_id, 'ahorro'); 
        
        if ($respuesta_cuenta['status'] === 'success') {
            $num_cta = $respuesta_cuenta['data']['numero_cuenta'];
            header("Location: ../views/login.php?msg=Registro exitoso. Se genero tu cuenta: " . $num_cta);
        } else {
            header("Location: ../views/login.php?error=Registro exitoso, pero hubo un error al generar la cuenta bancaria.");
        }
    } else {
        header("Location: ../views/registro.php?error=El correo ya esta registrado.");
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