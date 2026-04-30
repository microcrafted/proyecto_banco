<?php
class Usuario {

    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function registrar($nombre, $apellido, $email, $password) {

        // Verificar si existe el correo 
        $sql = "SELECT id_usuario FROM USUARIOS WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return ["ok" => false, "mensaje" => "El correo ya está registrado"];
        }

        // Encriptar contraseña por seguridad
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar usuario
        $sql = "INSERT INTO USUARIOS (nombre, apellido, email, password_hash)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $apellido, $email, $password_hash);

        if ($stmt->execute()) {
            return ["ok" => true, "mensaje" => "Usuario registrado correctamente"];
        }

        return ["ok" => false, "mensaje" => "Error al registrar"];
    }
}
?>