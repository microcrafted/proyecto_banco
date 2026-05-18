<?php
session_start();
require_once '../config/database.php';
require_once '../models/Admin.php';

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';

// LOGIN DE ADMINISTRADOR
if ($action == 'login' && $_POST) {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    $respuesta = $admin->login($email, $password);

    if ($respuesta['status'] === 'success') {
        $datos = $respuesta['data'];
        
        // Usamos variables de sesión diferentes para no mezclarlas con las de clientes
        $_SESSION['id_admin'] = $datos['id_admin'];
        $_SESSION['nombre_admin'] = $datos['nombre'] . ' ' . $datos['apellido'];
        
        header("Location: ../../frontend/pages/admin.php");
    } else {
        header("Location: ../../frontend/pages/admin_login.php?error=" . urlencode($respuesta['mensaje']));
    }
    exit;
}

// LOGOUT DE ADMINISTRADOR
if ($action == 'logout') {
    unset($_SESSION['id_admin']);
    unset($_SESSION['nombre_admin']);
    header("Location: ../../frontend/pages/admin_login.php");
    exit;
}

// HU26: Modificar el estado de un usuario
if ($action == 'toggleEstado' && isset($_GET['id']) && isset($_GET['campo']) && isset($_GET['valor'])) {
    
    $id = intval($_GET['id']);
    $campo = $_GET['campo']; // 'activo' o 'bloqueado'
    $valor = intval($_GET['valor']); // 1 o 0

    // Solo permitimos modificar estas dos columnas por seguridad
    if ($campo === 'activo' || $campo === 'bloqueado') {
        if ($admin->cambiarEstadoUsuario($id, $campo, $valor)) {
            header("Location: ../../frontend/pages/admin.php?msg=Estado actualizado correctamente");
        } else {
            header("Location: ../../frontend/pages/admin.php?error=Error al actualizar el estado");
        }
    }
    exit;
}
?>
