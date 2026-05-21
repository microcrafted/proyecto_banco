<!-- 👻 -->

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

    // HU2, HU21 y HU24: Login con bloqueo de seguridad
    public function login($email, $password) {
        // Buscamos al usuario por su correo (solo si está activo)
        $query = "SELECT * FROM USUARIOS WHERE email = :email AND activo = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query); // Usando tu variable $this->conn
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // 1. Validar si la cuenta ya está bloqueada
            if ($row['bloqueado'] == 1) {
                return ["status" => "error", "mensaje" => "Tu cuenta ha sido bloqueada por seguridad. Contacta al administrador."];
            }
            
            // 2. Validar contraseña usando password_hash
            if (password_verify($password, $row['password_hash'])) {
                // Si entra exitosamente, limpiamos su historial de fallos a 0
                $reset = $this->conn->prepare("UPDATE USUARIOS SET intentos_fallidos = 0 WHERE id_usuario = :id");
                $reset->bindParam(':id', $row['id_usuario']);
                $reset->execute();
                
                return ["status" => "success", "data" => $row];
            } else {
                // Si la contraseña es incorrecta, incrementamos los intentos
                $intentos = $row['intentos_fallidos'] + 1;
                $bloqueado = ($intentos >= 3) ? 1 : 0; // Si llega a 3, se bloquea (1)
                
                $update = $this->conn->prepare("UPDATE USUARIOS SET intentos_fallidos = :intentos, bloqueado = :bloqueado WHERE id_usuario = :id");
                $update->bindParam(':intentos', $intentos);
                $update->bindParam(':bloqueado', $bloqueado);
                $update->bindParam(':id', $row['id_usuario']);
                $update->execute();
                
                $mensaje = ($bloqueado == 1) ? "Cuenta bloqueada por múltiples intentos fallidos." : "Contraseña incorrecta. Intento $intentos de 3.";
                return ["status" => "error", "mensaje" => $mensaje];
            }
        }
        
        return ["status" => "error", "mensaje" => "El correo no está registrado o la cuenta está inactiva."];
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

    // HU4: Obtener datos actuales para el formulario de edición
    public function obtenerDatosPerfil($id_usuario) {
        $query = "SELECT nombre, apellido, email FROM USUARIOS WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // HU5: Recuperar contraseña (Generar temporal)
    public function recuperarPassword($email) {
        // Verificar si el correo existe
        $query = "SELECT id_usuario FROM USUARIOS WHERE email = :email AND activo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id_usuario = $row['id_usuario'];

            // Generar contraseña temporal aleatoria de 8 caracteres
            $password_temporal = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
            
            // Encriptarla para la base de datos
            $password_hash = password_hash($password_temporal, PASSWORD_BCRYPT);

            // Actualizar contraseña y limpiar bloqueos previos
            $update = $this->conn->prepare("UPDATE USUARIOS SET password_hash = :hash, intentos_fallidos = 0, bloqueado = 0 WHERE id_usuario = :id");
            $update->bindParam(':hash', $password_hash);
            $update->bindParam(':id', $id_usuario);

            if($update->execute()) {
                return ["status" => "success", "temporal" => $password_temporal];
            }
        }
        return ["status" => "error", "mensaje" => "No encontramos ninguna cuenta activa con ese correo."];
    }
}
?>