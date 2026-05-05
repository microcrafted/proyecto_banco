<?php
class Cuenta {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    //HU6 
    public function crearNuevaCuenta($idUsuario, $tipoDeCuenta) {
        //CTA + Año + 6 dígitos
        $numeroDeCuentaUnico = "CTA-" . date("Y") . "-" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $query = "INSERT INTO CUENTAS_BANCARIAS (id_usuario, num_cuenta, tipo, saldo, estado) 
                VALUES (:id_usuario, :num_cuenta, :tipo, 0.00, 'activa')";
            
            $sentencia = $this->conexion->prepare($query);
            
            $sentencia->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $sentencia->bindParam(':num_cuenta', $numeroDeCuentaUnico, PDO::PARAM_STR);
            $sentencia->bindParam(':tipo', $tipoDeCuenta, PDO::PARAM_STR);

            if ($sentencia->execute()) {
                return [
                    "status" => "success",
                    "mensaje" => "Cuenta creada exitosamente.",
                    "data" => [
                        "numero_cuenta" => $numeroDeCuentaUnico,
                        "tipo" => $tipoDeCuenta
                    ]
                ];
            } else {
                return ["status" => "error", "mensaje" => "No se pudo crear la cuenta."];
            }

        } catch (PDOException $excepcion) {
            return ["status" => "error", "mensaje" => "Error de BD: " . $excepcion->getMessage()];
        }
    }

    //HU7 - Dashboard
    public function obtenerCuentas($id_usuario) {
        $query = "SELECT num_cuenta, tipo, saldo FROM CUENTAS_BANCARIAS WHERE id_usuario = :id_usuario AND estado = 'activa'";
        $stmt = $this->conexion->prepare($query); // Usamos $conexion para respetar el código de Mich
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>