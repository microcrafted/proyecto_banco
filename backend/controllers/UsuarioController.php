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


// LOGIN
if ($action == 'login' && $_POST) {

    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $datos = $usuario->login($email, $password);

    if ($datos) {

        $_SESSION['id_usuario'] = $datos['id_usuario'];
        $_SESSION['nombre_completo'] = $datos['nombre'] . ' ' . $datos['apellido'];

        header("Location: ../../frontend/pages/dashboard.php");

    } else {

        header("Location: ../../frontend/pages/login.php?error=Credenciales inválidas.");
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
?>