<?php
class Cuenta {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    // HU6: Crear cuenta bancaria automáticamente
    public function crearCuenta($id_usuario) {
        $num_cuenta = rand(1000000000, 9999999999);
        $tipo = 'ahorro'; 

        $query = "INSERT INTO CUENTAS_BANCARIAS (id_usuario, num_cuenta, tipo) VALUES (:id_usuario, :num_cuenta, :tipo)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->bindParam(":num_cuenta", $num_cuenta);
        $stmt->bindParam(":tipo", $tipo);

        return $stmt->execute();
    }

    // HU7: Ver cuentas
    public function obtenerCuentas($id_usuario) {
        $query = "SELECT num_cuenta, tipo, saldo FROM CUENTAS_BANCARIAS WHERE id_usuario = :id_usuario AND estado = 'activa'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>