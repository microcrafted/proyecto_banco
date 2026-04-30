<?php
require_once "../models/Usuario.php";

class UsuarioController {

    private $usuario;

    public function __construct($conexion) {
        $this->usuario = new Usuario($conexion);
    }

    public function registrar($data) {

        if (!isset($data['nombre'], $data['apellido'], $data['email'], $data['password'])) {
            return ["ok" => false, "mensaje" => "Datos incompletos"];
        }

        return $this->usuario->registrar(
            $data['nombre'],
            $data['apellido'],
            $data['email'],
            $data['password']
        );
    }
}
?>