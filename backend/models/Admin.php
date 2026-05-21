<?php
class Admin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Iniciar sesión como Administrador
    public function login($email, $password) {
        $query = "SELECT * FROM ADMINISTRADORES WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $row['password_hash'])) {
                return ["status" => "success", "data" => $row];
            } else {
                return ["status" => "error", "mensaje" => "Contraseña incorrecta."];
            }
        }
        return ["status" => "error", "mensaje" => "Acceso denegado. Usuario no reconocido."];
    }

    // HU25: Obtener todos los usuarios y la cantidad de cuentas que tienen
    public function obtenerUsuarios() {
        $query = "SELECT u.id_usuario, u.nombre, u.apellido, u.email, u.activo, u.bloqueado, u.intentos_fallidos,
                COUNT(c.id_cuenta) as total_cuentas
                FROM USUARIOS u
                LEFT JOIN CUENTAS_BANCARIAS c ON u.id_usuario = c.id_usuario
                GROUP BY u.id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // HU26: Cambiar estado (Activar/Desactivar o Bloquear/Desbloquear)
    public function cambiarEstadoUsuario($id_usuario, $campo, $valor) {
        // $campo será 'activo' o 'bloqueado'
        $query = "UPDATE USUARIOS SET {$campo} = :valor WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        
        // Si el admin lo desbloquea manualmente, le limpiamos los intentos fallidos a 0
        if($campo == 'bloqueado' && $valor == 0) {
            $this->conn->query("UPDATE USUARIOS SET intentos_fallidos = 0 WHERE id_usuario = " . intval($id_usuario));
        }
        
        return $stmt->execute();
    }

    // HU27: Generar reportes y estadísticas globales del sistema
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM USUARIOS) as total_usuarios,
                    (SELECT COUNT(*) FROM CUENTAS_BANCARIAS) as total_cuentas,
                    (SELECT SUM(saldo) FROM CUENTAS_BANCARIAS) as capital_total,
                    (SELECT COUNT(*) FROM TRANSACCIONES) as total_transacciones";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>