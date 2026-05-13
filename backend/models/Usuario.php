<?php
class Usuario {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // HU1: Registro
    public function registrar($nombre, $apellido, $email, $password) {

        $query = "INSERT INTO USUARIOS 
                  (nombre, apellido, email, password_hash) 
                  VALUES 
                  (:nombre, :apellido, :email, :password_hash)";

        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":apellido", $apellido);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password_hash", $password_hash);

        try {

            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }

        } catch(PDOException $e) {

            if($e->errorInfo[1] == 1062) {
                return false;
            }
        }

        return false;
    }

    // HU2 y HU21: Login
    public function login($email, $password) {

        $query = "SELECT id_usuario, nombre, apellido, password_hash 
                  FROM USUARIOS 
                  WHERE email = :email 
                  AND activo = 1 
                  AND bloqueado = 0";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':email', $email);

        $stmt->execute();

        if($stmt->rowCount() > 0) {

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if(password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }

        return false;
    }

    // HU: Editar perfil
    public function actualizarPerfil($id_usuario, $nombre, $apellido, $email) {

        // Validar correo repetido
        $verificar = "SELECT id_usuario 
                      FROM USUARIOS 
                      WHERE email = :email 
                      AND id_usuario != :id_usuario";

        $stmt = $this->conn->prepare($verificar);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_usuario', $id_usuario);

        $stmt->execute();

        if($stmt->rowCount() > 0) {

            return [
                "status" => "error",
                "mensaje" => "El correo ya está registrado."
            ];
        }

        // Actualizar datos
        $query = "UPDATE USUARIOS
                  SET nombre = :nombre,
                      apellido = :apellido,
                      email = :email
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_usuario', $id_usuario);

        if($stmt->execute()) {

            return [
                "status" => "success",
                "mensaje" => "Perfil actualizado correctamente."
            ];
        }

        return [
            "status" => "error",
            "mensaje" => "No se pudieron actualizar los datos."
        ];
    }
}
?>