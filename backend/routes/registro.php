<?php
require_once "../controllers/UsuarioController.php";

$conn = new mysqli("localhost", "root", "", "proyecto_victor");

$data = json_decode(file_get_contents("php://input"), true);

$controller = new UsuarioController($conn);

$respuesta = $controller->registrar($data);

echo json_encode($respuesta);
?>