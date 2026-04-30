<?php
class Usuario {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    // HU1: Registro (Incluye el campo apellido de tu BD)
    public function registrar($nombre, $apellido, $email, $password) {
        $query = "INSERT INTO USUARIOS (nombre, apellido, email, password_hash) VALUES (:nombre, :apellido, :email, :password_hash)";
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
            // El código de error 1062 en MySQL significa "Entrada duplicada"
            if($e->errorInfo[1] == 1062) {
                return false; 
            }
        }
        return false;
    }

    // HU2 y HU21: Login y validación 
    public function login($email, $password) {
        $query = "SELECT id_usuario, nombre, apellido, password_hash FROM USUARIOS WHERE email = :email AND activo = 1 AND bloqueado = 0";
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
}
?>